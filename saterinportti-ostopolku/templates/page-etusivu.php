<?php
/**
 * Template Name: Säterinportti — Etusivu (DEMO)
 *
 * DEMO / PROTOTYPE: Brand-aware homepage based on Bööna design from Figma
 * (https://www.figma.com/design/k0Qpu3gl6DjAnda3vPNE5C/FIT-App?node-id=1353-30692).
 *
 * Tuotannossa etusivu kuuluu Säterinportin Sage-teemaan, EI tähän pluginiin.
 * Tämä templaatti on prototyyppi-vaiheen testaamista ja Playground-demoa varten,
 * jotta voidaan näyttää brand-tyylin vaikutus koko sivun mittakaavassa.
 *
 * @package Saterinportti\Ostopolku
 */

defined( 'ABSPATH' ) || exit;

get_header();

$liity_url = ( $p = get_page_by_path( 'liity' ) ) ? get_permalink( $p ) : home_url( '/liity' );
?>

<div class="sp-home">

	<!-- Brand-navbar (tumma yläbanderolli) -->
	<header class="sp-home-navbar" role="banner">
		<div class="sp-home-navbar-inner">
			<a class="sp-home-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="bööna">
				<img src="<?php echo esc_url( SATERINPORTTI_OSTOPOLKU_URL . 'assets/brand/Boona/' ); ?>bööna-logo.svg" alt="bööna" loading="eager">
			</a>
			<nav class="sp-home-nav" aria-label="Päävalikko">
				<a href="#kuntosali">Kuntosali</a>
				<a href="#palvelut">Palvelut</a>
				<a href="#meista">Meistä</a>
				<a href="#asiakaspalvelu">Asiakaspalvelu</a>
			</nav>
			<div class="sp-home-nav-cta">
				<a class="sp-home-btn sp-home-btn--cream" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
				<a class="sp-home-nav-login" href="#login">Kirjaudu</a>
			</div>
			<button type="button" class="sp-home-nav-toggle" aria-expanded="false" aria-controls="sp-home-mobile-menu" aria-label="Avaa valikko">
				<span class="sp-home-nav-toggle-bars" aria-hidden="true">
					<span></span><span></span><span></span>
				</span>
			</button>
		</div>
		<div class="sp-home-mobile-menu" id="sp-home-mobile-menu" hidden>
			<nav aria-label="Mobiili-päävalikko">
				<a href="#kuntosali">Kuntosali</a>
				<a href="#palvelut">Palvelut</a>
				<a href="#meista">Meistä</a>
				<a href="#asiakaspalvelu">Asiakaspalvelu</a>
				<a href="#login" class="sp-home-mobile-menu-login">Kirjaudu</a>
			</nav>
			<a class="sp-home-btn sp-home-btn--cream sp-home-mobile-menu-cta" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
		</div>
	</header>

	<!-- Marquee-rivi -->
	<div class="sp-home-marquee">
		<p>Vain naisille &nbsp;•&nbsp; 600 m² treenitilaa &nbsp;•&nbsp; Monipuoliset laitteet &nbsp;•&nbsp; Vapaat painot &nbsp;•&nbsp; Tankopaikka &nbsp;•&nbsp; Upea jugend-talo Kallion sydämessä</p>
	</div>

	<!-- Hero -->
	<section class="sp-home-hero">
		<div class="sp-home-hero-inner">
			<h1>Treenit heinäkuun loppuun 10 €</h1>
			<p>
				Liity jäseneksi ja aloita treenit vaikka saman tien. Ei liittymismaksua.
				<br>Kesäkampanja on voimassa 31.7.2026 asti.
			</p>
			<a class="sp-home-btn sp-home-btn--cream sp-home-btn--lg" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
		</div>
	</section>

	<!-- Intro -->
	<section class="sp-home-intro">
		<p>Bööna on kunnon gymi mimmeille. Monipuoliset laitteet, isot tilat ja rento tunnelma tekevät Böönasta paikan, jonne tekee mieli tulla yhä uudelleen.</p>
	</section>

	<!-- Naisilta naisille -->
	<section class="sp-home-row" id="meista">
		<div class="sp-home-row-image sp-home-row-image--portrait" aria-hidden="true"></div>
		<div class="sp-home-row-content">
			<h2>Naisilta naisille</h2>
			<p>Missiomme on luoda aidosti hyvä ja monipuolinen treenipaikka naisilta naisille — paikka, jossa voi treenata, viihtyä ja voida hyvin.</p>
			<p>Bööna on kunnolla varusteltu kuntosali, josta löytyy laitteita ja välineitä niin aloittelijoille kuin kokeneillekin raudannostajille. Historiallisen rakennuksen kauniit, paikoin yli kahdeksan metriä korkeat tilat tarjoavat poikkeuksellisen treeniympäristön Helsingin Kalliossa.</p>
			<p>Meille voi tulla tavoittelemaan ennätyksiä, hakemaan energiaa päivään tai vain hetkeksi karkuun arjen kiireitä.</p>
			<div class="sp-home-row-ctas">
				<a class="sp-home-btn sp-home-btn--dark" href="#tutustu">Tutustu lisää</a>
				<a class="sp-home-btn sp-home-btn--cream" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
			</div>
		</div>
	</section>

	<!-- Leveä gym-kuva (Figmassa: koko leveys, ~645px korkea) -->
	<div class="sp-home-wide-image" aria-hidden="true"></div>

	<!-- 3 kolumnia: aloitus / osoite / sali -->
	<section class="sp-home-features" id="palvelut">
		<div class="sp-home-features-grid">
			<div class="sp-home-feature">
				<h3>Aloita treenaaminen</h3>
				<ol>
					<li>Osta jäsenyys verkosta</li>
					<li>Saat PIN-koodin heti</li>
					<li>Tule treenaamaan milloin tahansa</li>
					<li>Nähdään Böönassa!</li>
				</ol>
				<a class="sp-home-btn sp-home-btn--cream" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
			</div>
			<div class="sp-home-feature">
				<h3>Osoite &amp; saapuminen</h3>
				<p>Sijaitsemme upeassa Jugend-kiinteistössä osoitteessa Kolmas linja 2, Helsinki. Täällä ihan Hakaniemen torin kupeessa.</p>
				<a class="sp-home-btn sp-home-btn--cream" href="#yhteystiedot">Yhteystiedot</a>
			</div>
			<div class="sp-home-feature" id="kuntosali">
				<h3>Naisten kuntosali</h3>
				<p>Böönasta löytyy 600 m² kuntosali, Hot-jooga &amp; wellness studio, kaunis pukuhuone, suihkut ja infrapunasauna.</p>
				<a class="sp-home-btn sp-home-btn--cream" href="#tilat">Tutustu tiloihin</a>
			</div>
		</div>
	</section>

	<!-- Kuvagalleria — 3 kuvaa -->
	<div class="sp-home-gallery">
		<div class="sp-home-gallery-tile sp-home-gallery-tile--a" aria-hidden="true"></div>
		<div class="sp-home-gallery-tile sp-home-gallery-tile--b" aria-hidden="true"></div>
		<div class="sp-home-gallery-tile sp-home-gallery-tile--c" aria-hidden="true"></div>
	</div>

	<!-- Tumma marquee-rivi -->
	<div class="sp-home-marquee sp-home-marquee--dark">
		<p>Historiallinen miljöö &nbsp;•&nbsp; Maksuttomat viikkotunnit jäsenille &nbsp;•&nbsp; Infrapunasauna &nbsp;•&nbsp; Ei sitoutumispakkoa &nbsp;•&nbsp; Avoinna joka päivä klo 5.30–22</p>
	</div>

	<!-- Jooga & Wellness -->
	<section class="sp-home-row">
		<div class="sp-home-row-image sp-home-row-image--yoga" aria-hidden="true"></div>
		<div class="sp-home-row-content">
			<h2>Jooga &amp; Wellness Studio</h2>
			<p>Kun kaipaat vastapainoa raudan nostelulle, jäsenille maksuttomat pilates- ja joogatunnit tarjoavat hetken palautumiselle. Historiallisessa studiossa voit yhä aistia vuonna 1913 tilassa toimineen elokuvateatteri Kalevan tunnelman.</p>
			<p>Tila on vapaasti käytettävissä myös omaan harjoitteluun.</p>
			<div class="sp-home-row-ctas">
				<a class="sp-home-btn sp-home-btn--dark" href="#tunnit">Varaa paikka tunnille</a>
			</div>
		</div>
	</section>

	<!-- Personal training (teksti vasen, kuva oikea) -->
	<section class="sp-home-row sp-home-row--reverse">
		<div class="sp-home-row-image sp-home-row-image--pt" aria-hidden="true"></div>
		<div class="sp-home-row-content">
			<h2>Personal training</h2>
			<p>Olitpa aloittamassa treenaamista, palaamassa liikunnan pariin tai tavoittelemassa seuraavaa tasoa, personal trainer auttaa sinua etenemään tehokkaasti ja turvallisesti.</p>
			<p>Yksilöllinen valmennus ja juuri sinulle suunniteltu ohjelma tuovat varmuutta harjoitteluun, auttavat saavuttamaan tavoitteesi ja rakentamaan pysyviä tuloksia. Böönan valmentajat ovat kokeneita ammattilaisia.</p>
			<div class="sp-home-row-ctas">
				<a class="sp-home-btn sp-home-btn--dark" href="#pt">Lue lisää</a>
			</div>
		</div>
	</section>

	<!-- Kerma-marquee -->
	<div class="sp-home-marquee sp-home-marquee--cream">
		<p>Ennen tai jälkeen treenin voit rentoutua ihanassa olkkarissamme palkkaria tai kahvia nautiskellen.</p>
	</div>

	<!-- Iso lopputilakuva -->
	<div class="sp-home-banner" aria-hidden="true"></div>

	<!-- Footer -->
	<footer class="sp-home-footer" id="asiakaspalvelu">
		<div class="sp-home-footer-inner">
			<div class="sp-home-footer-cols">
				<div>
					<h4>Bööna</h4>
					<p>Naisten kuntosali Helsingin Kalliossa</p>
					<p>Kolmas linja 2, Helsinki</p>
					<p><strong>Aukioloajat</strong><br>Ma–Su 5:30–22:00</p>
					<p><strong>Yhteystiedot</strong><br>
					boonagym@gmail.com<br>
					Nina: 010 200 0740<br>
					Jenni: 020 7199560<br>
					(arkisin klo 9–17)</p>
				</div>
				<div>
					<h4>Palvelut</h4>
					<ul>
						<li>Naisten kuntosali</li>
						<li>Tutustu tiloihin</li>
						<li>Jooga &amp; Wellness Studio</li>
						<li>Ryhmäliikuntatunnit</li>
						<li>Personal training</li>
						<li>Pienryhmäharjoittelu</li>
						<li>Polttarit</li>
						<li>Yrityspalvelut</li>
					</ul>
				</div>
				<div>
					<h4>Inspiroidu</h4>
					<ul>
						<li>Kuvagalleria</li>
						<li>Laitelistaus</li>
						<li>Jooga &amp; Wellness Studio</li>
						<li>Intensed-treenivaatteet</li>
					</ul>
				</div>
				<div>
					<h4>Asiakaspalvelu</h4>
					<ul>
						<li><a href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a></li>
						<li>Yhteystiedot</li>
						<li>Usein kysytyt kysymykset</li>
						<li>Asiakassopimusehdot</li>
						<li>Tietosuojaseloste</li>
					</ul>
				</div>
			</div>
			<div class="sp-home-footer-wordmark" aria-hidden="true">
				<img src="<?php echo esc_url( SATERINPORTTI_OSTOPOLKU_URL . 'assets/brand/Boona/' ); ?>bööna-logo.svg" alt="">
			</div>
		</div>
	</footer>

</div>

<script>
/* Mobiilimenu-toggle — hamburger avaa/sulkee pudotusvalikon */
(function () {
	'use strict';
	var btn = document.querySelector('.sp-home-nav-toggle');
	var menu = document.getElementById('sp-home-mobile-menu');
	if (!btn || !menu) return;

	function setOpen(open) {
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		btn.setAttribute('aria-label', open ? 'Sulje valikko' : 'Avaa valikko');
		menu.hidden = !open;
	}

	btn.addEventListener('click', function () {
		setOpen(btn.getAttribute('aria-expanded') !== 'true');
	});

	// Sulje kun käyttäjä klikkaa linkkiä menusta
	menu.querySelectorAll('a').forEach(function (a) {
		a.addEventListener('click', function () { setOpen(false); });
	});

	// Escape-näppäin sulkee
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && btn.getAttribute('aria-expanded') === 'true') {
			setOpen(false);
			btn.focus();
		}
	});
})();
</script>

<?php get_footer();
