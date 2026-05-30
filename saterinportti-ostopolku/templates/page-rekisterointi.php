<?php
/**
 * Template Name: Säterinportti — /jatka (Vaihe 1: Yhteystiedot)
 *
 * Vaihe 1/2 ostopolussa: kirjautumisvalinta + yhteystiedot.
 *
 * @package Saterinportti\Ostopolku
 */

defined( 'ABSPATH' ) || exit;

use Saterinportti\Ostopolku\Plugin;

get_header();

$plugin    = Plugin::instance();
$liity_url = ( $p = get_page_by_path( 'liity' ) ) ? get_permalink( $p ) : home_url( '/liity' );
$maksu_url = ( $p = get_page_by_path( 'maksu' ) ) ? get_permalink( $p ) : home_url( '/maksu' );
$terms_url = apply_filters(
	'saterinportti_ostopolku_terms_url',
	'https://kuntokeskussaterinportti.fi/wp-content/uploads/2022/06/Asiakassopimusehdot-Saterinportti.pdf'
);
?>

<main class="sp-liity">
	<div class="sp-container">

		<header class="sp-page-head" style="text-align:left">
			<a class="sp-back-link" href="<?php echo esc_url( $liity_url ); ?>">← Takaisin valintaan</a>
			<div class="sp-steps" aria-label="Tilauksen vaiheet">
				<span class="sp-step-pill is-active"><span class="num">1</span> Yhteystiedot</span>
				<span class="sp-step-divider" aria-hidden="true"></span>
				<span class="sp-step-pill is-pending"><span class="num">2</span> Maksu</span>
			</div>
			<h1 class="sp-page-title">Yhteystiedot</h1>
		</header>

	</div>

	<div class="sp-layout">

		<!-- LEFT: kirjautuminen + lomake -->
		<div class="sp-layout-main">

			<section class="sp-section-card" aria-labelledby="sp-login-choice-title">
				<h2 id="sp-login-choice-title">Oletko jo asiakkaamme?</h2>
				<div class="sp-login-choice-row" role="radiogroup" aria-label="Asiakkuus" id="sp-login-choice">
					<button type="button" class="sp-login-option" role="radio" aria-checked="false" data-mode="login">
						<span class="sp-login-option-title">Kirjaudu sisään</span>
						<span class="sp-login-option-meta">Olen jo Säterinportin jäsen</span>
					</button>
					<button type="button" class="sp-login-option is-active" role="radio" aria-checked="true" data-mode="guest">
						<span class="sp-login-option-title">Jatka uutena asiakkaana</span>
						<span class="sp-login-option-meta">Luo tunnukset rekisteröitymällä</span>
					</button>
				</div>

				<div class="sp-login-form" id="sp-login-form">
					<div class="sp-form-grid">
						<div class="sp-form-field sp-form-field--full">
							<label for="sp-login-email">Sähköposti</label>
							<input type="email" id="sp-login-email" autocomplete="email">
						</div>
						<div class="sp-form-field sp-form-field--full">
							<label for="sp-login-password">Salasana</label>
							<div class="sp-input-wrap sp-input-wrap--password">
								<input type="password" id="sp-login-password" autocomplete="current-password">
								<button type="button" class="sp-password-toggle" data-password-toggle="sp-login-password" aria-pressed="false" aria-label="Näytä salasana">
									<svg class="sp-pw-icon sp-pw-icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
										<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
										<circle cx="12" cy="12" r="3"/>
									</svg>
									<svg class="sp-pw-icon sp-pw-icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
										<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
										<path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
										<path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
										<line x1="3" y1="3" x2="21" y2="21"/>
									</svg>
								</button>
							</div>
						</div>
					</div>
					<div class="sp-login-actions">
						<label class="sp-login-remember">
							<input type="checkbox" name="rememberme" value="forever">
							<span>Pidä minut kirjautuneena</span>
						</label>
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Unohdin salasanan</a>
					</div>
					<p class="sp-login-help">Mikäli olet jo asiakkaamme mutta et tiedä tunnuksiasi, voit pyytää ne <a href="mailto:info@kuntokeskussaterinportti.fi">asiakaspalvelustamme</a>.</p>
				</div>
			</section>

			<form id="sp-jatka-form" novalidate>
			<section class="sp-section-card" id="sp-guest-form" aria-labelledby="sp-guest-form-title">
				<h2 id="sp-guest-form-title">Tietosi</h2>
				<p class="sp-form-intro">
					Luo uudet tunnukset täyttämällä alla olevat kentät. Kaikki kentät ovat pakollisia. Säilytämme tiedot turvallisesti ja jaamme vain maksun käsittelyn ja FitnessBookerin kanssa.
				</p>

				<div class="sp-form-section">
					<h3 class="sp-form-subhead">
						<span class="sp-form-section-num" aria-hidden="true">1</span>
						<span>Henkilötiedot</span>
					</h3>
					<div class="sp-form-grid">
						<div class="sp-form-field sp-form-field--full">
							<label for="sp-email">Sähköposti</label>
							<input type="email" id="sp-email" name="email" autocomplete="email" required>
						</div>
						<div class="sp-form-field">
							<label for="sp-firstname">Etunimi</label>
							<input type="text" id="sp-firstname" name="firstname" autocomplete="given-name" required>
						</div>
						<div class="sp-form-field">
							<label for="sp-lastname">Sukunimi</label>
							<input type="text" id="sp-lastname" name="lastname" autocomplete="family-name" required>
						</div>
						<div class="sp-form-field">
							<label for="sp-phone">Puhelin</label>
							<input type="tel" id="sp-phone" name="phone" autocomplete="tel" required>
						</div>
						<div class="sp-form-field">
							<label for="sp-birthdate">Syntymäaika</label>
							<input type="date" id="sp-birthdate" name="birthdate" autocomplete="bday" required>
						</div>
						<div class="sp-form-field sp-form-field--full">
							<span class="sp-form-label">
								Sukupuoli
								<button type="button" class="sp-info-icon" aria-expanded="false" aria-controls="sp-gender-why" data-info-toggle aria-label="Miksi sukupuolta kysytään?">i</button>
							</span>
							<div class="sp-radio-row" role="radiogroup" aria-label="Sukupuoli">
								<label class="sp-radio-pill">
									<input type="radio" name="gender" value="female" required>
									<span>Nainen</span>
								</label>
								<label class="sp-radio-pill">
									<input type="radio" name="gender" value="male">
									<span>Mies</span>
								</label>
							</div>
							<div class="sp-info-hint" id="sp-gender-why" role="note" hidden>
								<button type="button" class="sp-info-hint-close" data-info-close="sp-gender-why" aria-label="Sulje selitys">×</button>
								<strong>Miksi kysymme tätä?</strong>
								Säterinportilla on erilliset pukuhuoneet naisille ja miehille, ja jäsenrekisterimme (FitnessBooker) edellyttää sukupuolitiedon. Tieto tallennetaan vain jäsentietoihisi — emme käytä sitä markkinointiin emmekä luovuta sitä kolmansille osapuolille FitnessBookerin ulkopuolelle.
							</div>
						</div>
					</div>
				</div>

				<div class="sp-form-section">
					<h3 class="sp-form-subhead">
						<span class="sp-form-section-num" aria-hidden="true">2</span>
						<span>Osoite</span>
					</h3>
					<div class="sp-form-grid">
						<div class="sp-form-field sp-form-field--full">
							<label for="sp-address">Katuosoite</label>
							<input type="text" id="sp-address" name="address" autocomplete="street-address" required>
						</div>
						<div class="sp-form-field sp-form-field--postal">
							<label for="sp-postalcode">Postinumero</label>
							<input type="text" id="sp-postalcode" name="postalcode" autocomplete="postal-code" inputmode="numeric" pattern="[0-9]{5}" maxlength="5" required>
						</div>
						<div class="sp-form-field sp-form-field--city">
							<label for="sp-city">Postitoimipaikka</label>
							<input type="text" id="sp-city" name="city" autocomplete="address-level2" required>
						</div>
					</div>
				</div>

				<div class="sp-form-section">
					<h3 class="sp-form-subhead">
						<span class="sp-form-section-num" aria-hidden="true">3</span>
						<span>Tunnukset</span>
						<span class="sp-form-section-hint">Vähintään 8 merkkiä</span>
					</h3>
					<div class="sp-form-grid">
						<div class="sp-form-field">
							<label for="sp-password">Salasana</label>
							<div class="sp-input-wrap sp-input-wrap--password">
								<input type="password" id="sp-password" name="password" autocomplete="new-password" minlength="8" required>
								<button type="button" class="sp-password-toggle" data-password-toggle="sp-password" aria-pressed="false" aria-label="Näytä salasana">
									<svg class="sp-pw-icon sp-pw-icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
										<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
										<circle cx="12" cy="12" r="3"/>
									</svg>
									<svg class="sp-pw-icon sp-pw-icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
										<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
										<path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
										<path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
										<line x1="3" y1="3" x2="21" y2="21"/>
									</svg>
								</button>
							</div>
						</div>
						<div class="sp-form-field">
							<label for="sp-password-confirm">Salasana uudelleen</label>
							<div class="sp-input-wrap sp-input-wrap--password">
								<input type="password" id="sp-password-confirm" name="password_confirm" autocomplete="new-password" minlength="8" required>
								<button type="button" class="sp-password-toggle" data-password-toggle="sp-password-confirm" aria-pressed="false" aria-label="Näytä salasana">
									<svg class="sp-pw-icon sp-pw-icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
										<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
										<circle cx="12" cy="12" r="3"/>
									</svg>
									<svg class="sp-pw-icon sp-pw-icon--hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
										<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
										<path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
										<path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
										<line x1="3" y1="3" x2="21" y2="21"/>
									</svg>
								</button>
							</div>
						</div>
					</div>
				</div>

				<div class="sp-form-section sp-form-section--guardian" id="sp-guardian-section" hidden>
					<h3 class="sp-form-subhead">
						<span class="sp-form-section-num sp-form-section-num--alert" aria-hidden="true">!</span>
						<span>Huoltajan laskutustiedot</span>
					</h3>
					<p class="sp-form-intro">
						Ole hyvä ja täytä huoltajan laskutustiedot. Säterinportti laskuttaa alaikäisen jäsenyyden huoltajalta.
					</p>
					<div class="sp-form-grid">
						<div class="sp-form-field sp-form-field--full">
							<label for="sp-guardian-name">Laskun saaja</label>
							<input type="text" id="sp-guardian-name" name="guardian_name" autocomplete="section-guardian name">
						</div>
						<div class="sp-form-field sp-form-field--full">
							<label for="sp-guardian-email">Laskutus sähköposti</label>
							<input type="email" id="sp-guardian-email" name="guardian_email" autocomplete="section-guardian email">
						</div>
						<div class="sp-form-field sp-form-field--full">
							<label for="sp-guardian-address">Laskutus katuosoite</label>
							<input type="text" id="sp-guardian-address" name="guardian_address" autocomplete="section-guardian street-address">
						</div>
						<div class="sp-form-field sp-form-field--postal">
							<label for="sp-guardian-postalcode">Laskutus postinumero</label>
							<input type="text" id="sp-guardian-postalcode" name="guardian_postalcode" autocomplete="section-guardian postal-code" inputmode="numeric" pattern="[0-9]{5}" maxlength="5">
						</div>
						<div class="sp-form-field sp-form-field--city">
							<label for="sp-guardian-city">Laskutus postitoimipaikka</label>
							<input type="text" id="sp-guardian-city" name="guardian_city" autocomplete="section-guardian address-level2">
						</div>
					</div>
				</div>

				<div class="sp-form-footer">
					<label class="sp-checkbox-row sp-checkbox-row--terms" for="sp-accept-terms">
						<input type="checkbox" id="sp-accept-terms" name="accept_terms" required>
						<span>
							<strong>Olen lukenut ja hyväksynyt käyttöehdot.</strong>
							<span class="sp-checkbox-meta">
								<a href="<?php echo esc_url( $terms_url ); ?>" target="_blank" rel="noopener">Asiakassopimusehdot (PDF) →</a>
							</span>
						</span>
					</label>
					<p class="sp-form-disclaimer">
						Rekisteröitymällä hyväksyt markkinointiviestien vastaanottamisen. Voit perua markkinointiviestit milloin tahansa tilisi asetuksista.
					</p>
				</div>
			</section>

			<button type="submit" class="sp-continue-btn" data-maksu-url="<?php echo esc_url( $maksu_url ); ?>">
				<span>Jatka maksuun</span>
				<span aria-hidden="true">→</span>
			</button>
			</form>

		</div>

		<!-- RIGHT: tilauskooste -->
		<aside class="sp-summary" aria-labelledby="sp-summary-title">
			<h2 id="sp-summary-title" class="sp-summary-title">Tilauksesi</h2>

			<div class="sp-summary-product">
				<span class="sp-summary-name" id="sp-sum-name">Joustojäsenyys</span>
				<span class="sp-summary-meta" id="sp-sum-scope">Kuntosali + jumpat · toistaiseksi voimassa</span>
			</div>

			<div class="sp-summary-rows" id="sp-sum-rows">
				<!-- JS täyttää -->
			</div>

			<div class="sp-summary-total">
				<span class="sp-summary-total-label">Yhteensä tänään</span>
				<span class="sp-summary-total-value" id="sp-sum-total">1 €</span>
			</div>

			<p class="sp-summary-note" id="sp-sum-note">
				<span class="sp-tarjous-star">*</span>
				Sen jälkeen normaali kk-hinta. 12 kk sitoumus, jatkuu joustavasti.
			</p>
		</aside>

	</div>
</main>

<?php get_footer();
