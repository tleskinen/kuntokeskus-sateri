<?php
/**
 * Template Name: Säterinportti — /liity-sivu
 *
 * Renderöi Säterinportin ostopolun aloitussivun: scope-valinta + kolme
 * commitment-korttia + tutustumistarjous + ryhmäliikunta-modaali +
 * Näin liityt + FAQ.
 *
 * @package Saterinportti\Ostopolku
 */

defined( 'ABSPATH' ) || exit;

use Saterinportti\Ostopolku\Plugin;

get_header();

$plugin    = Plugin::instance();
$scopes    = $plugin->packages->get_scopes();
$tarjous   = $plugin->packages->get_tarjous();
$catalog   = $plugin->packages->get_classes_catalog();
?>

<main id="liity" class="sp-liity">
	<div class="sp-container">

		<header class="sp-page-head">
			<h1 class="sp-page-title">Valitse jäsenyys</h1>
			<p class="sp-page-lede">Kaksi askelta: valitse palvelut ja sopimustyyppi.</p>
		</header>

		<!-- Vaihe 1: Scope -->
		<div class="sp-scope-row" role="radiogroup" aria-label="Mitä palveluita">
			<?php foreach ( $scopes as $i => $s ) :
				$is_first = 0 === $i;
				?>
				<div class="sp-scope-option<?php echo $is_first ? ' is-selected' : ''; ?>"
				     role="radio"
				     aria-checked="<?php echo $is_first ? 'true' : 'false'; ?>"
				     tabindex="0"
				     data-scope="<?php echo esc_attr( $s['key'] ); ?>">
					<div class="sp-scope-text">
						<h3>
							<?php if ( ! empty( $s['classes_link'] ) ) : ?>
								<?php
								$name = $s['name'];
								$pos  = mb_strrpos( $name, ' ' );
								if ( false !== $pos ) {
									$prefix   = mb_substr( $name, 0, $pos + 1 );
									$lastword = mb_substr( $name, $pos + 1 );
								} else {
									$prefix   = '';
									$lastword = $name;
								}
								?>
								<?php echo esc_html( $prefix ); ?>
								<span class="sp-jumpat-link">
									<?php echo esc_html( $lastword ); ?>
									<button type="button" class="sp-info-icon" data-classes-open aria-label="Näytä tuntivalikoima">i</button>
								</span>
							<?php else : ?>
								<?php echo esc_html( $s['name'] ); ?>
							<?php endif; ?>
						</h3>
						<p><?php echo esc_html( $s['subtitle'] ); ?></p>
					</div>
					<span class="sp-scope-radio" aria-hidden="true"></span>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="sp-divider"></div>

		<!-- Vaihe 2: Commitment-kortit -->
		<div class="sp-cards" id="sp-cards" role="radiogroup" aria-label="Sopimustyyppi">
			<?php /* Kortit renderöidään JS:llä jotta hinnat päivittyvät scope-valinnalla. */ ?>
		</div>

		<div class="sp-cta-wrap">
			<a class="sp-cta" href="<?php echo esc_url( get_permalink( get_page_by_path( 'jatka' ) ) ?: '/jatka' ); ?>">
				Jatka ostamaan <span aria-hidden="true">→</span>
			</a>
			<div class="sp-cta-trust">
				<p class="sp-cta-trust-row sp-cta-trust-row--payment">
					Maksu onnistuu myös Smartumilla, Epassilla ja Edenredillä.
				</p>
				<p class="sp-cta-trust-row sp-cta-trust-row--discount">
					<strong>−20&nbsp;%</strong> opiskelijoille, eläkeläisille ja nuorille — sovelletaan todennusta vastaan.
				</p>
				<?php if ( ! empty( $tarjous['active'] ) ) : ?>
					<p class="sp-cta-trust-footnote"><span class="sp-tarjous-star" aria-hidden="true">*</span>Tarjoushinta voimassa ensimmäisen kuukauden. Sen jälkeen normaali kk-hinta. Tarjous päättyy <?php echo esc_html( $tarjous['ends_at_label'] ); ?>.</p>
				<?php endif; ?>
			</div>
		</div>

		<!-- Ryhmäliikunta teaser -->
		<section class="sp-classes-teaser">
			<div class="sp-classes-teaser-inner">
				<div>
					<span class="sp-classes-eyebrow">Ryhmäliikunta &amp; REBELS</span>
					<h2 class="sp-classes-teaser-title">20+ tuntia viikossa — joogasta REBELS-bootcampiin</h2>
				</div>
				<button type="button" class="sp-classes-open" data-classes-open>
					Katso tuntivalikoima <span aria-hidden="true">→</span>
				</button>
			</div>
		</section>

		<!-- Ryhmäliikunta modaali -->
		<dialog class="sp-classes-modal" id="sp-classes-modal" aria-labelledby="sp-classes-modal-title">
			<button type="button" class="sp-classes-modal-close" id="sp-classes-modal-close" aria-label="Sulje">✕</button>
			<span class="sp-classes-eyebrow">Ryhmäliikunta &amp; REBELS</span>
			<h2 id="sp-classes-modal-title" class="sp-classes-title">20+ tuntia viikossa — valitse oma juttusi</h2>
			<p class="sp-classes-lede">
				REBELS-bootcampistä Les Millsin tarkkaan koreografiaan ja Yin Joogan rauhaan.
				Löydät sopivan menon, oli tavoite palauttava hengitys tai täysi tykitys.
			</p>
			<div class="sp-classes-grid">
				<?php foreach ( $catalog as $c ) : ?>
					<span class="sp-class-tag<?php echo ! empty( $c['hero'] ) ? ' sp-class-tag--hero' : ''; ?>">
						<?php echo esc_html( $c['label'] ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		</dialog>

		<!-- Näin liityt -->
		<section class="sp-steps-section" aria-labelledby="sp-steps-title">
			<h2 id="sp-steps-title" class="sp-section-title">Näin liityt</h2>
			<div class="sp-steps-grid">
				<div class="sp-step">
					<span class="sp-step-num">1</span>
					<h3>Valitse jäsenyys</h3>
					<p>Osta verkkokaupassa. Saat PIN-koodin sähköpostiin heti ostoksen jälkeen.</p>
				</div>
				<div class="sp-step">
					<span class="sp-step-num">2</span>
					<h3>Hae avainkortti</h3>
					<p>Säterinportin aulapalvelusta 15 €:lla ma–pe klo 7.30–16.30. Tarjouksen aikana veloituksetta.</p>
				</div>
				<div class="sp-step">
					<span class="sp-step-num">3</span>
					<h3>Salille 24/7</h3>
					<p>Kuntosali auki joka päivä ympäri vuorokauden. Ryhmäliikuntaan varaat paikan kalenterista.</p>
				</div>
			</div>
		</section>

		<!-- FAQ -->
		<section class="sp-faq-section" aria-labelledby="sp-faq-title">
			<h2 id="sp-faq-title" class="sp-section-title">Usein kysyttyä</h2>
			<div class="sp-faq-list">
				<details class="sp-faq-item">
					<summary>Mitä tarjouksen jälkeen tapahtuu?</summary>
					<div class="sp-faq-answer">
						Sopimus jatkuu normaalihintaisena valitsemasi sopimustyypin mukaan (esim. 49 €/kk tai 64 €/kk toistaiseksi voimassa). Voit päättää asiakkuuden ensimmäisen normaalihintaisen kuukauden aikana — sopimus päättyy irtisanomista seuraavan laskutuskauden loppuun.
					</div>
				</details>
				<details class="sp-faq-item">
					<summary>Voinko maksaa liikuntaeduilla?</summary>
					<div class="sp-faq-answer">
						Kyllä — Smartum, Epassi ja Edenred käyvät niin jäsenmaksussa kuin Personal Training -ostoksissa.
					</div>
				</details>
				<details class="sp-faq-item">
					<summary>Miten saan avainkortin?</summary>
					<div class="sp-faq-answer">
						Osta verkkokaupassa → saat PIN-koodin sähköpostiin. Avainkortin (15 € kertamaksu) noudat Säterinportin aulapalvelusta ma–pe klo 7.30–16.30.
					</div>
				</details>
				<details class="sp-faq-item">
					<summary>Voiko jäsenyyttä tauottaa?</summary>
					<div class="sp-faq-answer">
						Etuasiakkuuden ja Vuosisäästön voi tauottaa 1–2 kertaa asiakkuuden aikana yhteensä enintään 2 kk ajaksi veloituksetta. Joustojäsenyyden tauotus sovitaan erikseen, esim. sairauden vuoksi.
					</div>
				</details>
				<details class="sp-faq-item">
					<summary>Onko opiskelijoille alennusta?</summary>
					<div class="sp-faq-answer">
						Kyllä — −20 % alennus opiskelijoille, nuorille ja senioreille. Alennus sovelletaan todennusta vastaan.
					</div>
				</details>
				<details class="sp-faq-item">
					<summary>Mihin aikaan salille pääsee?</summary>
					<div class="sp-faq-answer">
						Kuntosali on auki 24 h/vrk joka päivä avainkortilla. Ryhmäliikuntatunneilla on omat aikataulut — paikan varaat Säterinportin kalenterista.
					</div>
				</details>
			</div>
			<p class="sp-faq-contact">
				Jäikö jotain kysymättä? <a href="mailto:info@kuntokeskussaterinportti.fi">info@kuntokeskussaterinportti.fi</a> tai 020 7199560 ma–pe klo 9–17.
			</p>
		</section>

	</div>
</main>

<?php get_footer();
