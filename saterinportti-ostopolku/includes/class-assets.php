<?php
/**
 * CSS/JS-assettien lataus.
 *
 * Lataa liity.css + liity.js kaikilla mu-pluginin sivupohjilla
 * (/liity, /jatka, /maksu, /vahvistus).
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Assets {

	public function register(): void {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function enqueue(): void {
		if ( ! $this->should_load() ) {
			return;
		}

		/**
		 * Bööna-brändi käyttää Figmasta löytyviä fontteja:
		 * - Google Sans Flex (otsikot, body, CTA-napit)
		 * - Inter (footerin sisältö)
		 * Säterinportti-brändi perii edelleen teemasta — fontit ladataan
		 * mutta käytetään vain `body.brand-boona`-tilassa CSS:n kautta.
		 */
		wp_enqueue_style(
			'saterinportti-ostopolku-fonts',
			'https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wdth,wght@7..72,75..125,100..900&family=Inter:wght@400;700&display=swap',
			[],
			null
		);

		wp_enqueue_style(
			'saterinportti-ostopolku-liity',
			SATERINPORTTI_OSTOPOLKU_URL . 'assets/css/liity.css',
			[ 'saterinportti-ostopolku-fonts' ],
			SATERINPORTTI_OSTOPOLKU_VERSION
		);

		wp_enqueue_script(
			'saterinportti-ostopolku-liity',
			SATERINPORTTI_OSTOPOLKU_URL . 'assets/js/liity.js',
			[],
			SATERINPORTTI_OSTOPOLKU_VERSION,
			true
		);

		// Välitä Packages-data JS:lle
		$plugin       = Plugin::instance();
		$checkout_url = function_exists( 'wc_get_checkout_url' )
			? wc_get_checkout_url()
			: home_url( '/checkout/' );
		$data = [
			'pricing'     => $plugin->packages->get_pricing(),
			'commits'     => $plugin->packages->get_commitments(),
			'tarjous'     => $plugin->packages->get_tarjous(),
			'fbBaseUrl'   => apply_filters( 'saterinportti_ostopolku_fb_base_url', get_option( Admin_Settings::OPTION_FB_BASE, '' ) ),
			'checkoutUrl' => apply_filters( 'saterinportti_ostopolku_checkout_url', $checkout_url ),
		];
		wp_add_inline_script(
			'saterinportti-ostopolku-liity',
			'window.SP_OSTOPOLKU = ' . wp_json_encode( $data ) . ';',
			'before'
		);
	}

	private function should_load(): bool {
		$post = get_queried_object();
		if ( ! $post instanceof \WP_Post ) {
			return false;
		}

		// Ladataan jos slug on jokin neljästä, TAI sivupohja on valittu.
		$pages = Page_Template::pages();
		if ( isset( $pages[ $post->post_name ] ) ) {
			return true;
		}

		$selected = get_page_template_slug( $post->ID );
		foreach ( $pages as $info ) {
			if ( $info['template'] === $selected ) {
				return true;
			}
		}

		return false;
	}
}
