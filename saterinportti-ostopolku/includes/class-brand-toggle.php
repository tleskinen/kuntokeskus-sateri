<?php
/**
 * Brand-toggle widget (DEMO / PROTOTYPE).
 *
 * Liukuva widget alaoikealla, joka vaihtaa Säterinportin ja Böönan brändin
 * välillä. Vaihto tapahtuu CSS-muuttujilla ja `body.brand-{key}` -luokalla,
 * joten visuaalinen tyyli päivittyy ilman uudelleenlatausta.
 *
 * Tämä on prototyyppi-vaiheen testausväline. Tuotannossa piilotetaan
 * filtterillä:
 *   add_filter( 'saterinportti_ostopolku_show_brand_toggle', '__return_false' );
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Brand_Toggle {

	public function register(): void {
		add_action( 'wp_footer', [ $this, 'render' ], 100 );
		add_filter( 'body_class', [ $this, 'add_body_class' ] );
	}

	/**
	 * Lisätään `brand-saterinportti`-luokka oletuksena bodyyn, jotta CSS:n
	 * default-arvoilla on jokin luokka johon kytkeytyä. JS overrideaa tämän
	 * tarvittaessa `brand-boona`:lla localStoragesta.
	 */
	public function add_body_class( array $classes ): array {
		if ( ! $this->is_enabled() ) {
			return $classes;
		}
		$classes[] = 'brand-saterinportti';
		return $classes;
	}

	public function render(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		?>
		<div class="sp-brand-toggle" role="radiogroup" aria-label="Brändin valinta">
			<button type="button" data-sp-brand="saterinportti" role="radio" aria-pressed="true">Säteri</button>
			<button type="button" data-sp-brand="boona" role="radio" aria-pressed="false">Bööna</button>
		</div>
		<script>
		(function () {
			'use strict';
			var KEY = 'sp_brand';
			var DEFAULT = 'saterinportti';
			var BRANDS = ['saterinportti', 'boona'];

			function applyBrand(brand) {
				if (BRANDS.indexOf(brand) === -1) brand = DEFAULT;
				BRANDS.forEach(function (b) {
					document.body.classList.toggle('brand-' + b, b === brand);
				});
				document.querySelectorAll('.sp-brand-toggle [data-sp-brand]').forEach(function (btn) {
					btn.setAttribute('aria-pressed', btn.getAttribute('data-sp-brand') === brand ? 'true' : 'false');
				});
			}

			// Alkutila localStoragesta (jos saatavilla)
			var saved = DEFAULT;
			try { saved = localStorage.getItem(KEY) || DEFAULT; } catch (e) {}
			applyBrand(saved);

			// Klikkauskäsittelijät
			document.querySelectorAll('.sp-brand-toggle [data-sp-brand]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var brand = btn.getAttribute('data-sp-brand');
					try { localStorage.setItem(KEY, brand); } catch (e) {}
					applyBrand(brand);
				});
			});
		})();
		</script>
		<?php
	}

	private function is_enabled(): bool {
		/**
		 * Brand-toggle näkyvyys. Tuotanto pystyy piilottamaan tämän:
		 *   add_filter( 'saterinportti_ostopolku_show_brand_toggle', '__return_false' );
		 */
		return (bool) apply_filters( 'saterinportti_ostopolku_show_brand_toggle', true );
	}
}
