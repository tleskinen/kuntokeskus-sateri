<?php
/**
 * Admin-asetukset: sivujen luonti, tarjous-tila, FB base URL.
 *
 * Asetukset → Säterinportti Ostopolku.
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Admin_Settings {

	const PAGE_SLUG    = 'saterinportti-ostopolku';
	const OPTION_GROUP = 'saterinportti_ostopolku_settings';
	const OPTION_FB_BASE = 'saterinportti_ostopolku_fb_base_url';

	public function register(): void {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_post_saterinportti_create_pages', [ $this, 'handle_create_pages' ] );
		add_action( 'admin_notices', [ $this, 'maybe_show_create_notice' ] );
	}

	public function register_menu(): void {
		add_options_page(
			'Säterinportti / Ostopolku',
			'Säterinportti Ostopolku',
			'manage_options',
			self::PAGE_SLUG,
			[ $this, 'render_page' ]
		);
	}

	public function register_settings(): void {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_FB_BASE,
			[
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			]
		);
		register_setting(
			self::OPTION_GROUP,
			Packages::OPTION_TARJOUS,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_tarjous' ],
				'default'           => [],
			]
		);
	}

	public function sanitize_tarjous( $value ): array {
		if ( ! is_array( $value ) ) {
			return [];
		}
		$applies = isset( $value['applies_to'] ) && is_array( $value['applies_to'] )
			? array_values( array_filter( array_map( 'sanitize_key', $value['applies_to'] ) ) )
			: [];
		return [
			'active'             => ! empty( $value['active'] ),
			'label'              => sanitize_text_field( $value['label'] ?? '' ),
			'first_month_price'  => isset( $value['first_month_price'] ) ? (int) $value['first_month_price'] : 0,
			'applies_to'         => $applies,
			'avainkortti_free'   => ! empty( $value['avainkortti_free'] ),
			'ends_at_label'      => sanitize_text_field( $value['ends_at_label'] ?? '' ),
		];
	}

	public function handle_create_pages(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Ei oikeuksia.' );
		}
		check_admin_referer( 'saterinportti_create_pages' );

		$created = Page_Template::create_pages();
		set_transient( 'saterinportti_pages_created', $created, 30 );

		wp_safe_redirect( admin_url( 'options-general.php?page=' . self::PAGE_SLUG . '&pages_created=1' ) );
		exit;
	}

	public function maybe_show_create_notice(): void {
		// Näytä huomautus admin-puolella jos sivuja ei vielä ole.
		if ( ! current_user_can( 'manage_options' ) ) return;
		$missing = [];
		foreach ( array_keys( Page_Template::pages() ) as $slug ) {
			if ( ! get_page_by_path( $slug ) ) {
				$missing[] = $slug;
			}
		}
		if ( empty( $missing ) ) return;

		$url = wp_nonce_url(
			admin_url( 'admin-post.php?action=saterinportti_create_pages' ),
			'saterinportti_create_pages'
		);
		?>
		<div class="notice notice-info">
			<p>
				<strong>Säterinportti Ostopolku:</strong>
				puuttuvat sivut: <code><?php echo esc_html( implode( ', /', $missing ) ); ?></code>.
				<a href="<?php echo esc_url( $url ); ?>" class="button button-primary" style="margin-left:.5rem;">Luo puuttuvat sivut</a>
			</p>
		</div>
		<?php
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) return;

		$tarjous = get_option( Packages::OPTION_TARJOUS, [] );
		if ( ! is_array( $tarjous ) ) $tarjous = [];

		$created_now = get_transient( 'saterinportti_pages_created' );
		if ( $created_now ) delete_transient( 'saterinportti_pages_created' );
		?>
		<div class="wrap">
			<h1>Säterinportti / Ostopolku</h1>

			<?php if ( ! empty( $_GET['pages_created'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p>Sivut luotu onnistuneesti.</p>
				</div>
			<?php endif; ?>

			<h2>Sivut</h2>
			<p>Mu-plugin lisää neljä sivupohjaa:</p>
			<table class="widefat striped" style="max-width: 720px;">
				<thead>
					<tr><th>Sivu</th><th>Slug</th><th>Tila</th></tr>
				</thead>
				<tbody>
					<?php foreach ( Page_Template::pages() as $slug => $info ) :
						$page = get_page_by_path( $slug );
						?>
						<tr>
							<td><?php echo esc_html( $info['title'] ); ?></td>
							<td><code>/<?php echo esc_html( $slug ); ?></code></td>
							<td>
								<?php if ( $page ) : ?>
									<span style="color: #2a7f4a">✓ Olemassa</span>
									<a href="<?php echo esc_url( get_permalink( $page ) ); ?>" target="_blank">Avaa →</a>
								<?php else : ?>
									<span style="color: #b04">Puuttuu</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p>
				<a class="button button-primary"
				   href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=saterinportti_create_pages' ), 'saterinportti_create_pages' ) ); ?>">
					Luo puuttuvat sivut
				</a>
			</p>

			<hr style="margin: 2rem 0;">

			<form method="post" action="options.php">
				<?php settings_fields( self::OPTION_GROUP ); ?>

				<h2>Tarjous</h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">Tarjous aktiivinen</th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[active]" value="1" <?php checked( ! empty( $tarjous['active'] ) ); ?>>
								Näytä tarjous /liity- ja /jatka- ja /maksu-sivuilla
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="t-label">Tarjouksen otsikko</label></th>
						<td>
							<input id="t-label" type="text" class="regular-text" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[label]" value="<?php echo esc_attr( $tarjous['label'] ?? 'Treenit huhtikuun loppuun' ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="t-price">1. kk hinta (€)</label></th>
						<td>
							<input id="t-price" type="number" class="small-text" min="0" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[first_month_price]" value="<?php echo esc_attr( $tarjous['first_month_price'] ?? 1 ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row">Koskee sopimustyyppejä</th>
						<td>
							<?php $applies = $tarjous['applies_to'] ?? [ 'etuasiakkuus', 'joustojasenyys' ]; ?>
							<label><input type="checkbox" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[applies_to][]" value="etuasiakkuus" <?php checked( in_array( 'etuasiakkuus', $applies, true ) ); ?>> Etuasiakkuus</label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[applies_to][]" value="vuosisaasto" <?php checked( in_array( 'vuosisaasto', $applies, true ) ); ?>> Vuosisäästö</label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[applies_to][]" value="joustojasenyys" <?php checked( in_array( 'joustojasenyys', $applies, true ) ); ?>> Joustojäsenyys</label>
						</td>
					</tr>
					<tr>
						<th scope="row">Avainkortti veloituksetta tarjouksen aikana</th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[avainkortti_free]" value="1" <?php checked( ! empty( $tarjous['avainkortti_free'] ) ); ?>> Kyllä</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="t-ends">Tarjouksen kesto (teksti)</label></th>
						<td>
							<input id="t-ends" type="text" class="regular-text" name="<?php echo esc_attr( Packages::OPTION_TARJOUS ); ?>[ends_at_label]" value="<?php echo esc_attr( $tarjous['ends_at_label'] ?? 'huhtikuun loppuun' ); ?>" placeholder="esim. huhtikuun loppuun">
							<p class="description">Käytetään alaviitteessä: "Tarjous päättyy <em>huhtikuun loppuun</em>."</p>
						</td>
					</tr>
				</table>

				<hr>

				<h2>FitnessBooker / verkkokauppa</h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="fb-base">Pohja-URL ostopolulle</label></th>
						<td>
							<input id="fb-base" name="<?php echo esc_attr( self::OPTION_FB_BASE ); ?>" type="url" class="regular-text" value="<?php echo esc_attr( get_option( self::OPTION_FB_BASE, '' ) ); ?>" placeholder="https://kuntokeskussaterinportti.fi/verkkokauppa/">
							<p class="description">Käytetään fiboproduct- ja add-to-cart-URL:eihin. Tyhjä → käytetään /verkkokauppa/.</p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
