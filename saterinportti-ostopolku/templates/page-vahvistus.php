<?php
/**
 * Template Name: Säterinportti — /vahvistus
 *
 * Tilauksen vahvistussivu, näkyy maksun onnistumisen jälkeen.
 *
 * @package Saterinportti\Ostopolku
 */

defined( 'ABSPATH' ) || exit;

get_header();

$liity_url = ( $p = get_page_by_path( 'liity' ) ) ? get_permalink( $p ) : home_url( '/liity' );
?>

<main class="sp-liity">
	<div class="sp-container">
		<div style="max-width: 720px; margin: 0 auto;">

			<div class="sp-confirm-hero">
				<div class="sp-success-icon" aria-hidden="true">✓</div>
				<h1 class="sp-page-title">Tervetuloa Säterinporttiin!</h1>
				<p class="sp-page-lede">Tilauksesi on vahvistettu ja maksu vastaanotettu.</p>
				<div class="sp-confirm-email" id="sp-confirm-email">PIN-koodi lähetetty: example@kayttaja.fi</div>
			</div>

			<section class="sp-summary" style="position: static; margin-bottom: 2rem;" aria-labelledby="sp-confirm-order-title">
				<h2 id="sp-confirm-order-title" class="sp-summary-title">Tilauksesi</h2>

				<div class="sp-summary-product">
					<span class="sp-summary-name" id="sp-sum-name">Joustojäsenyys</span>
					<span class="sp-summary-meta" id="sp-sum-scope">Kuntosali + jumpat · toistaiseksi voimassa</span>
				</div>

				<div class="sp-summary-rows" id="sp-sum-rows">
					<!-- JS täyttää -->
				</div>

				<div class="sp-summary-total">
					<span class="sp-summary-total-label">Maksettu</span>
					<span class="sp-summary-total-value" id="sp-sum-total">1 €</span>
				</div>

				<p class="sp-summary-note">
					<span class="sp-tarjous-star">*</span>
					Sopimus jatkuu normaalihintaisena ensimmäisen kuukauden jälkeen.
					Voit irtisanoa milloin vain kuukauden irtisanomisajalla.
				</p>
			</section>

			<section aria-labelledby="sp-next-title" style="margin-bottom: 3rem;">
				<h2 id="sp-next-title" class="sp-section-title" style="text-align:left">Mitä seuraavaksi?</h2>
				<ol class="sp-next-steps">
					<li class="sp-next-step">
						<span class="sp-step-num">1</span>
						<div>
							<h3>Tarkista sähköposti</h3>
							<p>
								Lähetimme sinulle vahvistusmaillin, jossa on PIN-koodi salille pääsemiseksi sekä
								käyttäjätunnukset Säterinportin omille sivuille (ryhmäliikuntavarausten varauksia varten).
							</p>
						</div>
					</li>
					<li class="sp-next-step">
						<span class="sp-step-num">2</span>
						<div>
							<h3>Hae avainkortti aulapalvelusta</h3>
							<p>
								Avainkortti odottaa sinua Säterinportin aulapalvelusta osoitteessa Linnoitustie 6, Espoo.
								Aulapalvelu on avoinna ma–pe klo 7.30–16.30.
							</p>
						</div>
					</li>
					<li class="sp-next-step">
						<span class="sp-step-num">3</span>
						<div>
							<h3>Salille 24/7</h3>
							<p>
								Avainkortilla pääset kuntosalille minä päivänä tahansa, ympäri vuorokauden.
								Ryhmäliikuntatunneille varaat paikan
								<a href="<?php echo esc_url( site_url( '/kalenteri/' ) ); ?>">kalenterista</a>
								omilla tunnuksillasi.
							</p>
						</div>
					</li>
				</ol>
			</section>

			<aside class="sp-contact-card">
				<h3>Tarvitsetko apua?</h3>
				<p>
					Soita Jenni: <a href="tel:+358207199560">020 7199560</a> · Sähköposti:
					<a href="mailto:info@kuntokeskussaterinportti.fi">info@kuntokeskussaterinportti.fi</a>
				</p>
			</aside>

			<div class="sp-actions">
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( $liity_url ); ?>">Takaisin etusivulle</a>
				<a class="sp-btn sp-btn-secondary" href="<?php echo esc_url( site_url( '/omat-tiedot/' ) ); ?>">Avaa omat tiedot →</a>
			</div>

		</div>
	</div>
</main>

<?php get_footer();
