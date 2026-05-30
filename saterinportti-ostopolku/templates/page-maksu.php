<?php
/**
 * Template Name: Säterinportti — /maksu (Vaihe 2: Upsellit + maksu)
 *
 * @package Saterinportti\Ostopolku
 */

defined( 'ABSPATH' ) || exit;

use Saterinportti\Ostopolku\Plugin;

get_header();

$plugin     = Plugin::instance();
$upsells    = $plugin->packages->get_upsells();
$methods    = $plugin->packages->get_payment_methods();
$lisajasen  = $plugin->packages->get_lisajasen_config();
$jatka_url     = ( $p = get_page_by_path( 'jatka' ) ) ? get_permalink( $p ) : home_url( '/jatka' );
$vahvistus_url = ( $p = get_page_by_path( 'vahvistus' ) ) ? get_permalink( $p ) : home_url( '/vahvistus' );
?>

<main class="sp-liity">
	<div class="sp-container">

		<header class="sp-page-head" style="text-align:left">
			<a class="sp-back-link" href="<?php echo esc_url( $jatka_url ); ?>">← Takaisin tietoihin</a>
			<div class="sp-steps" aria-label="Tilauksen vaiheet">
				<span class="sp-step-pill is-done"><span class="num">✓</span> Yhteystiedot</span>
				<span class="sp-step-divider" aria-hidden="true"></span>
				<span class="sp-step-pill is-active"><span class="num">2</span> Maksu</span>
			</div>
			<h1 class="sp-page-title">Maksu</h1>
		</header>

	</div>

	<div class="sp-layout">

		<!-- LEFT: upsellit + maksutavat -->
		<div class="sp-layout-main">

			<section class="sp-section-card" aria-labelledby="sp-upsells-title">
				<div class="sp-section-card-head">
					<h2 id="sp-upsells-title">Lisää tilaukseen</h2>
					<p class="sp-section-card-lede">Tarjouksia uusille jäsenille — alennetut hinnat vain ostohetkellä.</p>
				</div>
				<div class="sp-upsells">
					<?php foreach ( $upsells as $u ) : ?>
						<article class="sp-upsell" data-id="<?php echo esc_attr( $u['key'] ); ?>" data-price="<?php echo esc_attr( $u['price'] ); ?>">
							<div class="sp-upsell-info">
								<span class="sp-upsell-name"><?php echo esc_html( $u['name'] ); ?></span>
								<span class="sp-upsell-desc"><?php echo esc_html( $u['desc'] ); ?></span>
								<div class="sp-upsell-price-row">
									<?php if ( ! empty( $u['price_was'] ) ) : ?>
										<span class="sp-upsell-was"><?php echo esc_html( $u['price_was'] ); ?> €</span>
									<?php endif; ?>
									<span class="sp-upsell-now"><?php echo esc_html( $u['price'] ); ?> €</span>
								</div>
							</div>
							<button type="button" class="sp-upsell-add">Lisää</button>
						</article>
					<?php endforeach; ?>

					<?php if ( ! empty( $lisajasen['enabled'] ) ) : ?>
						<article class="sp-upsell sp-upsell-friend"
						         data-id="lisajasen"
						         data-discount="<?php echo esc_attr( $lisajasen['discount_percent'] ); ?>"
						         data-tarjous-applies="<?php echo $lisajasen['tarjous_applies'] ? '1' : '0'; ?>"
						         data-avainkortti-free="<?php echo $lisajasen['avainkortti_free_in_tarjous'] ? '1' : '0'; ?>">
							<div class="sp-upsell-info">
								<span class="sp-upsell-name"><?php echo esc_html( $lisajasen['name'] ); ?></span>
								<span class="sp-upsell-desc"><?php echo esc_html( $lisajasen['desc'] ); ?></span>
								<div class="sp-upsell-price-row" id="sp-friend-price">
									<!-- JS täyttää dynaamisesti scope+commitment+tarjous-tilan mukaan -->
								</div>
							</div>
							<button type="button" class="sp-upsell-add">Lisää</button>
							<div class="sp-friend-form" hidden>
								<h4 class="sp-friend-form-title">Kaverin tiedot</h4>
								<div class="sp-form-grid">
									<div class="sp-form-field">
										<label for="sp-friend-firstname">Etunimi</label>
										<input type="text" id="sp-friend-firstname" data-friend-field="firstname" required>
									</div>
									<div class="sp-form-field">
										<label for="sp-friend-lastname">Sukunimi</label>
										<input type="text" id="sp-friend-lastname" data-friend-field="lastname" required>
									</div>
									<div class="sp-form-field sp-form-field--full">
										<label for="sp-friend-email">Sähköposti</label>
										<input type="email" id="sp-friend-email" data-friend-field="email" required>
									</div>
								</div>
								<p class="sp-form-help">
									Kaveri saa oman PIN-koodin ja avainkortin maksun jälkeen sähköpostiinsa.
								</p>
							</div>
						</article>
					<?php endif; ?>
				</div>
			</section>

			<section class="sp-section-card" aria-labelledby="sp-payment-title">
				<div class="sp-section-card-head">
					<h2 id="sp-payment-title">Maksutapa</h2>
					<p class="sp-section-card-lede">Verkkopankit, kortit ja liikuntaedut.</p>
				</div>

				<?php
				// Sisennettyä logiikkaa: ensimmäinen ensimmäisen ryhmän ensimmäinen on aktiivinen
				$active_set = false;
				foreach ( $methods as $group ) :
					?>
					<div class="sp-payment-group">
						<h3 class="sp-payment-group-label"><?php echo esc_html( $group['group_label'] ); ?></h3>
						<div class="sp-payment-grid" role="radiogroup" aria-label="<?php echo esc_attr( $group['group_label'] ); ?>">
							<?php foreach ( $group['methods'] as $m ) :
								$is_active = ! $active_set;
								$active_set = $active_set || $is_active;
								?>
								<?php $icon_svg = \Saterinportti\Ostopolku\Payment_Icons::svg( $m['key'] ); ?>
								<button type="button"
								        class="sp-payment-method<?php echo $is_active ? ' is-active' : ''; ?>"
								        role="radio"
								        aria-checked="<?php echo $is_active ? 'true' : 'false'; ?>"
								        aria-label="<?php echo esc_attr( $m['label'] ); ?>"
								        data-method="<?php echo esc_attr( $m['key'] ); ?>">
									<?php if ( $icon_svg ) : ?>
										<span class="sp-payment-method-icon" aria-hidden="true"><?php echo $icon_svg; // phpcs:ignore — luotettu inline-SVG ?></span>
									<?php else : ?>
										<span class="sp-payment-method-label"><?php echo esc_html( $m['label'] ); ?></span>
									<?php endif; ?>
								</button>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</section>

		</div>

		<!-- RIGHT: tilauskooste + maksunappi -->
		<aside class="sp-summary" aria-labelledby="sp-summary-title">
			<h2 id="sp-summary-title" class="sp-summary-title">Tilauksesi</h2>

			<div class="sp-summary-product">
				<span class="sp-summary-name" id="sp-sum-name">Joustojäsenyys</span>
				<span class="sp-summary-meta" id="sp-sum-scope">Kuntosali + jumpat · toistaiseksi voimassa</span>
			</div>

			<div class="sp-summary-rows" id="sp-sum-rows">
				<!-- JS täyttää: jäsenyys + avainkortti + lisätyt upsellit -->
			</div>

			<div class="sp-summary-total">
				<span class="sp-summary-total-label">Yhteensä</span>
				<span class="sp-summary-total-value" id="sp-sum-total">1 €</span>
			</div>

			<button type="button" class="sp-summary-payment" id="sp-summary-payment" data-scroll-to="sp-payment-title">
				<span class="sp-summary-payment-label">Maksutapa</span>
				<span class="sp-summary-payment-value">
					<span class="sp-summary-payment-icon" id="sp-sum-pay-icon" aria-hidden="true"></span>
					<span class="sp-summary-payment-name" id="sp-sum-pay-name">Valitse maksutapa</span>
				</span>
				<span class="sp-summary-payment-change" aria-hidden="true">Vaihda →</span>
			</button>

			<a class="sp-pay-btn" id="sp-pay-btn" href="<?php echo esc_url( $vahvistus_url ); ?>">Maksa 1 €</a>

			<div class="sp-trust-line">
				<span class="sp-trust-line-item" id="sp-trust-commit">12 kk sitoumus, jatkuu joustavasti</span>
				<span class="sp-trust-line-item">Avainkortti aulapalvelusta arkisin</span>
				<span class="sp-trust-line-item">PIN-koodi sähköpostiisi heti maksun jälkeen</span>
			</div>

			<p class="sp-summary-note" id="sp-sum-note">
				<span class="sp-tarjous-star">*</span>
				Sen jälkeen normaali kk-hinta. 12 kk sitoumus, jatkuu joustavasti.
			</p>
		</aside>

	</div>
</main>

<?php get_footer();
