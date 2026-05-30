<?php
/**
 * Fiboproduct-parametrin säilytys rekisteröinnin/kirjautumisen yli.
 *
 * Ongelma: käyttäjä klikkaa /liity-sivulla tuotekorttia → päätyy rekisteröintiin/
 * kirjautumiseen → ?fiboproduct=<id> -parametri katoaa → tuote ei avaudu oston
 * jälkeen.
 *
 * Ratkaisu: talletetaan fiboproduct cookieen (vieras) tai usermetaan (kirjautunut)
 * heti kun se näkyy URL:ssa. Hookataan wp_login ja user_register niin, että käyttäjä
 * ohjataan FitnessBookerin ostopolulle oikealla tuotteella.
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Fiboproduct {

	const COOKIE_NAME     = 'saterinportti_fiboproduct';
	const USERMETA_KEY    = '_saterinportti_fiboproduct';
	const COOKIE_LIFETIME = HOUR_IN_SECONDS; // Riittää ostopolkuun, ei jätä roskaa.

	const QUERY_PARAM = 'fiboproduct';

	/**
	 * FitnessBookerin ostopolun URL. Vaihdetaan tarvittaessa admin-optioksi.
	 * Placeholder; säädetään oikea URL kun integraatio on selvillä.
	 */
	const DEFAULT_FB_PURCHASE_URL = 'https://saterinportti.fitnessbooker.fi/';

	public function register(): void {
		add_action( 'init', [ $this, 'capture_from_query' ], 1 );
		add_action( 'wp_login', [ $this, 'on_login' ], 10, 2 );
		add_action( 'user_register', [ $this, 'on_register' ], 10, 1 );
		add_action( 'wp_head', [ $this, 'emit_session_bridge' ], 1 );
	}

	/**
	 * Tallentaa ?fiboproduct= cookieen (vieras) tai usermetaan (kirjautunut).
	 */
	public function capture_from_query(): void {
		if ( ! isset( $_GET[ self::QUERY_PARAM ] ) ) {
			return;
		}

		$raw = wp_unslash( $_GET[ self::QUERY_PARAM ] );
		$id  = $this->sanitize_id( $raw );

		if ( '' === $id ) {
			return;
		}

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), self::USERMETA_KEY, $id );
		} else {
			$this->set_cookie( $id );
		}
	}

	/**
	 * Login-hookki: siirretään mahdollinen cookie-arvo usermetaan ja ohjataan
	 * käyttäjä ostopolulle jos fiboproduct on tiedossa.
	 */
	public function on_login( string $user_login, \WP_User $user ): void {
		$id = $this->read_cookie();

		if ( '' !== $id ) {
			update_user_meta( $user->ID, self::USERMETA_KEY, $id );
			$this->clear_cookie();
		} else {
			$id = (string) get_user_meta( $user->ID, self::USERMETA_KEY, true );
		}

		if ( '' === $id ) {
			return;
		}

		$this->redirect_to_purchase( $id );
	}

	/**
	 * Rekisteröinti-hookki: otetaan cookie talteen uuden käyttäjän usermetaan.
	 * Redirect tapahtuu yleensä user_register-hookia seuraavalla pyynnöllä;
	 * siksi sama looginen ohjaus kuin on_login:ssa.
	 */
	public function on_register( int $user_id ): void {
		$id = $this->read_cookie();

		if ( '' === $id ) {
			return;
		}

		update_user_meta( $user_id, self::USERMETA_KEY, $id );
		$this->clear_cookie();
	}

	/**
	 * Luo pienen JS-sillan: jos selaimessa on sessionStorage-arvo fiboproductille,
	 * mutta URL:ssä ei, ohjataan takaisin ostopolulle. Tämä pelastaa tilanteet,
	 * joissa 3rd-party-redirect pudottaa query-parametrin matkan varrella.
	 */
	public function emit_session_bridge(): void {
		$param   = esc_js( self::QUERY_PARAM );
		$fb_base = esc_js( $this->get_purchase_base_url() );
		?>
		<script>
		(function () {
			try {
				var u = new URL(window.location.href);
				var fromUrl = u.searchParams.get('<?php echo $param; ?>');
				if (fromUrl) {
					sessionStorage.setItem('saterinportti_fiboproduct', fromUrl);
				}
			} catch (e) { /* no-op */ }
		})();
		</script>
		<?php
	}

	/**
	 * Palauttaa tallennetun fiboproduct-ID:n: usermeta voittaa cookien.
	 */
	public function get_stored_id( ?int $user_id = null ): string {
		if ( null === $user_id && is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}

		if ( $user_id ) {
			$meta = (string) get_user_meta( $user_id, self::USERMETA_KEY, true );
			if ( '' !== $meta ) {
				return $meta;
			}
		}

		return $this->read_cookie();
	}

	public function build_purchase_url( string $fiboproduct_id ): string {
		$id = $this->sanitize_id( $fiboproduct_id );
		if ( '' === $id ) {
			return $this->get_purchase_base_url();
		}
		return add_query_arg( self::QUERY_PARAM, $id, $this->get_purchase_base_url() );
	}

	private function redirect_to_purchase( string $id ): void {
		if ( headers_sent() ) {
			return;
		}
		$url = $this->build_purchase_url( $id );

		/**
		 * Filter: mahdollistaa ohjauksen kustomoinnin (esim. oma vahvistussivu).
		 */
		$url = apply_filters( 'saterinportti_ostopolku_login_redirect', $url, $id );

		wp_safe_redirect( $url );
		exit;
	}

	private function get_purchase_base_url(): string {
		$configured = get_option( 'saterinportti_ostopolku_fb_base_url', '' );
		return is_string( $configured ) && '' !== $configured ? $configured : self::DEFAULT_FB_PURCHASE_URL;
	}

	private function sanitize_id( $raw ): string {
		if ( ! is_scalar( $raw ) ) {
			return '';
		}
		$clean = preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $raw );
		return is_string( $clean ) ? $clean : '';
	}

	private function set_cookie( string $id ): void {
		if ( headers_sent() ) {
			return;
		}
		setcookie(
			self::COOKIE_NAME,
			$id,
			[
				'expires'  => time() + self::COOKIE_LIFETIME,
				'path'     => COOKIEPATH ?: '/',
				'domain'   => COOKIE_DOMAIN,
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			]
		);
		$_COOKIE[ self::COOKIE_NAME ] = $id;
	}

	private function read_cookie(): string {
		if ( empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return '';
		}
		return $this->sanitize_id( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) );
	}

	private function clear_cookie(): void {
		if ( headers_sent() ) {
			unset( $_COOKIE[ self::COOKIE_NAME ] );
			return;
		}
		setcookie(
			self::COOKIE_NAME,
			'',
			[
				'expires'  => time() - 3600,
				'path'     => COOKIEPATH ?: '/',
				'domain'   => COOKIE_DOMAIN,
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			]
		);
		unset( $_COOKIE[ self::COOKIE_NAME ] );
	}
}
