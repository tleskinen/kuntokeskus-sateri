<?php
/**
 * Template Name: Säterinportti — Etusivu (DEMO)
 *
 * DEMO / PROTOTYPE: Brand-aware homepage with content for both
 * Säterinportti (default brand) and Bööna. Layout is shared; copy
 * differs per brand via [data-brand-show] attributes.
 *
 * Tuotannossa etusivu kuuluu Säterinportin Sage-teemaan, EI tähän pluginiin.
 * Tämä templaatti on prototyyppi-vaiheen testaamista ja Playground-demoa varten.
 *
 * Sisältö Säterinportille kerätty kuntokeskussaterinportti.fi-sivulta;
 * Bööna-sisältö Figma-prototyypistä.
 *
 * @package Saterinportti\Ostopolku
 */

defined( 'ABSPATH' ) || exit;

get_header();

$liity_url = ( $p = get_page_by_path( 'liity' ) ) ? get_permalink( $p ) : home_url( '/liity' );
$img_url   = SATERINPORTTI_OSTOPOLKU_URL . 'assets/brand/Images/Boona/';
?>

<div class="sp-home">

	<!-- Brand-navbar (sticky) -->
	<header class="sp-home-navbar" role="banner">
		<div class="sp-home-navbar-inner">
			<a class="sp-home-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Etusivu">
				<img data-brand-show="boona" src="<?php echo esc_url( SATERINPORTTI_OSTOPOLKU_URL . 'assets/brand/Boona/' ); ?>bööna-logo.svg" alt="bööna" loading="eager">
				<span data-brand-show="saterinportti" class="sp-home-logo-text">Säterinportti</span>
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
				<span class="sp-home-nav-toggle-bars" aria-hidden="true"><span></span><span></span><span></span></span>
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

	<!-- Marquee top -->
	<div class="sp-home-marquee">
		<p data-brand-show="boona">Vain naisille &nbsp;•&nbsp; 600 m² treenitilaa &nbsp;•&nbsp; Monipuoliset laitteet &nbsp;•&nbsp; Vapaat painot &nbsp;•&nbsp; Tankopaikka &nbsp;•&nbsp; Upea jugend-talo Kallion sydämessä</p>
		<p data-brand-show="saterinportti">24/7 kuntosalin käyttö &nbsp;•&nbsp; Avarat tilat &nbsp;•&nbsp; Well-sertifikaatti &nbsp;•&nbsp; Saunat ke–su &nbsp;•&nbsp; Ei sitova asiakkuus &nbsp;•&nbsp; 2,5 h maksuton pysäköinti</p>
	</div>

	<!-- Hero -->
	<section class="sp-home-hero">
		<div class="sp-home-hero-inner">
			<div data-brand-show="boona">
				<h1>Treenit heinäkuun loppuun 10 €</h1>
				<p>
					Liity jäseneksi ja aloita treenit vaikka saman tien. Ei liittymismaksua.
					<br>Kesäkampanja on voimassa 31.7.2026 asti.
				</p>
			</div>
			<div data-brand-show="saterinportti">
				<h1>Liiku heinäkuun loppuun 5 €:lla</h1>
				<p>
					Koko kesän treenit vain 5 €. Ei liittymismaksua, ei sitovaa sopimusta.
					<br>Tarjous voimassa 31.7.2026 asti.
				</p>
			</div>
			<a class="sp-home-btn sp-home-btn--cream sp-home-btn--lg" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
		</div>
	</section>

	<!-- Intro -->
	<section class="sp-home-intro">
		<p data-brand-show="boona">Bööna on kunnon gymi mimmeille. Monipuoliset laitteet, isot tilat ja rento tunnelma tekevät Böönasta paikan, jonne tekee mieli tulla yhä uudelleen.</p>
		<p data-brand-show="saterinportti">Säterinportti on kuntokeskus, jossa avarat tilat, monipuoliset laitteet ja huolellinen palvelu tekevät treenaamisesta mukavaa joka kerta. Sinä päätät — me huolehdimme puitteista.</p>
	</section>

	<!-- "Meistä" image+text row -->
	<section class="sp-home-row" id="meista">
		<div class="sp-home-row-image sp-home-row-image--portrait" aria-hidden="true"></div>
		<div class="sp-home-row-content">
			<div data-brand-show="boona">
				<h2>Naisilta naisille</h2>
				<p>Missiomme on luoda aidosti hyvä ja monipuolinen treenipaikka naisilta naisille — paikka, jossa voi treenata, viihtyä ja voida hyvin.</p>
				<p>Bööna on kunnolla varusteltu kuntosali, josta löytyy laitteita ja välineitä niin aloittelijoille kuin kokeneillekin raudannostajille. Historiallisen rakennuksen kauniit, paikoin yli kahdeksan metriä korkeat tilat tarjoavat poikkeuksellisen treeniympäristön Helsingin Kalliossa.</p>
				<p>Meille voi tulla tavoittelemaan ennätyksiä, hakemaan energiaa päivään tai vain hetkeksi karkuun arjen kiireitä.</p>
			</div>
			<div data-brand-show="saterinportti">
				<h2>Tervetuloa Säterinporttiin</h2>
				<p>Avarat tilat, monipuoliset laitteet ja huolellisesti hoidettu sali tekevät treenaamisesta mukavaa joka kerta. Saimme ensimmäisenä Suomessa Well-sertifikaatin, joka kertoo tinkimättömästä laadusta ilmanvaihdossa, valaistuksessa ja siisteydessä.</p>
				<p>Olit aloittelija tai konkari, sinun on helppo tulla luoksemme. Saliin pääset 24/7 omalla avainkortilla, eikä asiakkuus sido — voit tauottaa tai irtisanoa milloin haluat.</p>
				<p>Treenaaminen on meillä mukavaa. Mutta yhdessä se on vielä mukavampaa.</p>
			</div>
			<div class="sp-home-row-ctas">
				<a class="sp-home-btn sp-home-btn--dark" href="#tutustu">Tutustu lisää</a>
				<a class="sp-home-btn sp-home-btn--cream" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
			</div>
		</div>
	</section>

	<!-- Wide gym image -->
	<div class="sp-home-wide-image" aria-hidden="true"></div>

	<!-- 3-column features with images -->
	<section class="sp-home-features" id="palvelut">
		<div class="sp-home-features-grid">

			<div class="sp-home-feature">
				<div class="sp-home-feature-image sp-home-feature-image--1" aria-hidden="true"></div>
				<h3>Aloita treenaaminen</h3>
				<ol data-brand-show="boona">
					<li>Osta jäsenyys verkosta</li>
					<li>Saat PIN-koodin heti</li>
					<li>Tule treenaamaan milloin tahansa</li>
					<li>Nähdään Böönassa!</li>
				</ol>
				<ol data-brand-show="saterinportti">
					<li>Osta jäsenyys verkosta</li>
					<li>Saat PIN-koodin sähköpostiisi</li>
					<li>Hae avainkortti aulapalvelusta</li>
					<li>Treenaa 24/7 omaan tahtiin</li>
				</ol>
				<a class="sp-home-btn sp-home-btn--cream" href="<?php echo esc_url( $liity_url ); ?>">Liity jäseneksi</a>
			</div>

			<div class="sp-home-feature">
				<div class="sp-home-feature-image sp-home-feature-image--2" aria-hidden="true"></div>
				<h3>Osoite &amp; saapuminen</h3>
				<p data-brand-show="boona">Sijaitsemme upeassa Jugend-kiinteistössä osoitteessa Kolmas linja 2, Helsinki. Täällä ihan Hakaniemen torin kupeessa.</p>
				<p data-brand-show="saterinportti">Linnoitustie 6, 02600 Espoo. Pysäköinti omassa pihassamme — 2,5 h ilmaista. Bussipysäkki vieressä, ja Sello on viiden minuutin matkan päässä.</p>
				<a class="sp-home-btn sp-home-btn--cream" href="#yhteystiedot">Yhteystiedot</a>
			</div>

			<div class="sp-home-feature" id="kuntosali">
				<div class="sp-home-feature-image sp-home-feature-image--3" aria-hidden="true"></div>
				<h3 data-brand-show="boona">Naisten kuntosali</h3>
				<h3 data-brand-show="saterinportti">Kaikki samalla kortilla</h3>
				<p data-brand-show="boona">Böönasta löytyy 600 m² kuntosali, Hot-jooga &amp; wellness studio, kaunis pukuhuone, suihkut ja infrapunasauna.</p>
				<p data-brand-show="saterinportti">Kuntosali, ryhmäliikunta, saunat (ke–su 16–21) ja Personal Training — kaikki yhdellä jäsenyydellä. Liikuntaedut käyvät: Smartum, Epassi ja Edenred.</p>
				<a class="sp-home-btn sp-home-btn--cream" href="#tilat">Tutustu tiloihin</a>
			</div>

		</div>
	</section>

	<!-- Dark marquee -->
	<div class="sp-home-marquee sp-home-marquee--dark">
		<p data-brand-show="boona">Historiallinen miljöö &nbsp;•&nbsp; Maksuttomat viikkotunnit jäsenille &nbsp;•&nbsp; Infrapunasauna &nbsp;•&nbsp; Ei sitoutumispakkoa &nbsp;•&nbsp; Avoinna joka päivä klo 5.30–22</p>
		<p data-brand-show="saterinportti">Well-sertifikaatti &nbsp;•&nbsp; Saunat ke–su 16–21 &nbsp;•&nbsp; Liikuntaedut (Smartum / Epassi / Edenred) &nbsp;•&nbsp; Maksuton tauotus 1–2 krt &nbsp;•&nbsp; Aulapalvelu ma–pe 7.30–16.30</p>
	</div>

	<!-- Ryhmäliikunta / Jooga -->
	<section class="sp-home-row">
		<div class="sp-home-row-image sp-home-row-image--yoga" aria-hidden="true"></div>
		<div class="sp-home-row-content">
			<div data-brand-show="boona">
				<h2>Jooga &amp; Wellness Studio</h2>
				<p>Kun kaipaat vastapainoa raudan nostelulle, jäsenille maksuttomat pilates- ja joogatunnit tarjoavat hetken palautumiselle. Historiallisessa studiossa voit yhä aistia vuonna 1913 tilassa toimineen elokuvateatteri Kalevan tunnelman.</p>
				<p>Tila on vapaasti käytettävissä myös omaan harjoitteluun.</p>
			</div>
			<div data-brand-show="saterinportti">
				<h2>Ryhmäliikunta</h2>
				<p>Ryhmäliikuntatunteja jokaiseen makuun ja tasoon — spin, jooga, aerobic, voimaharjoittelu, pilates ja paljon muuta. Tunnit kuuluvat jäsenyyteen, ja paikan varaat helposti kalenterista.</p>
				<p>Tervetuloa kokeilemaan, vaikka olisit ihan ensikertaa salilla.</p>
			</div>
			<div class="sp-home-row-ctas">
				<a class="sp-home-btn sp-home-btn--dark" href="#tunnit">Katso aikataulu</a>
			</div>
		</div>
	</section>

	<!-- Personal training (reverse) -->
	<section class="sp-home-row sp-home-row--reverse">
		<div class="sp-home-row-image sp-home-row-image--pt" aria-hidden="true"></div>
		<div class="sp-home-row-content">
			<div data-brand-show="boona">
				<h2>Personal training</h2>
				<p>Olitpa aloittamassa treenaamista, palaamassa liikunnan pariin tai tavoittelemassa seuraavaa tasoa, personal trainer auttaa sinua etenemään tehokkaasti ja turvallisesti.</p>
				<p>Yksilöllinen valmennus ja juuri sinulle suunniteltu ohjelma tuovat varmuutta harjoitteluun, auttavat saavuttamaan tavoitteesi ja rakentamaan pysyviä tuloksia. Böönan valmentajat ovat kokeneita ammattilaisia.</p>
			</div>
			<div data-brand-show="saterinportti">
				<h2>Personal training &amp; fysioterapia</h2>
				<p>Henkilökohtainen valmentaja auttaa eteenpäin riippumatta lähtötasosta. Räätälöity ohjelma, säännöllinen seuranta ja konkari-valmentaja varmistavat, että tulokset kestävät.</p>
				<p>Saatavilla myös fysioterapia ja pienryhmäharjoittelu — yhdellä jäsenyydellä saat käyttöösi kaikki Säterinportin palvelut.</p>
			</div>
			<div class="sp-home-row-ctas">
				<a class="sp-home-btn sp-home-btn--dark" href="#pt">Tutustu valmentajiin</a>
			</div>
		</div>
	</section>

	<!-- Cream marquee -->
	<div class="sp-home-marquee sp-home-marquee--cream">
		<p data-brand-show="boona">Ennen tai jälkeen treenin voit rentoutua ihanassa olkkarissamme palkkaria tai kahvia nautiskellen.</p>
		<p data-brand-show="saterinportti">Treenaaminen on meillä mukavaa. Mutta yhdessä se on vielä mukavampaa.</p>
	</div>

	<!-- Big banner image -->
	<div class="sp-home-banner" aria-hidden="true"></div>

	<!-- Footer -->
	<footer class="sp-home-footer" id="asiakaspalvelu">
		<div class="sp-home-footer-inner">
			<div class="sp-home-footer-cols">

				<!-- Col 1: brand + contact -->
				<div data-brand-show="boona">
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
				<div data-brand-show="saterinportti">
					<h4>Säterinportti</h4>
					<p>Kuntokeskus Espoossa</p>
					<p>Linnoitustie 6, 02600 Espoo</p>
					<p><strong>Aukioloajat</strong><br>
					Kuntosali 24/7 avainkortilla<br>
					Aulapalvelu ma–pe 7.30–16.30<br>
					Saunat ke–su 16–21</p>
					<p><strong>Yhteystiedot</strong><br>
					saterinporttigym@gmail.com<br>
					020 7199560<br>
					(arkisin klo 9–17)</p>
				</div>

				<!-- Col 2: Palvelut -->
				<div>
					<h4>Palvelut</h4>
					<ul data-brand-show="boona">
						<li>Naisten kuntosali</li>
						<li>Tutustu tiloihin</li>
						<li>Jooga &amp; Wellness Studio</li>
						<li>Ryhmäliikuntatunnit</li>
						<li>Personal training</li>
						<li>Pienryhmäharjoittelu</li>
						<li>Polttarit</li>
						<li>Yrityspalvelut</li>
					</ul>
					<ul data-brand-show="saterinportti">
						<li>Kuntosali 24/7</li>
						<li>Ryhmäliikunta</li>
						<li>Personal training</li>
						<li>Fysioterapia</li>
						<li>Pienryhmäharjoittelu</li>
						<li>Saunat</li>
						<li>Työhyvinvointi</li>
						<li>Polttarit</li>
					</ul>
				</div>

				<!-- Col 3: Inspiroidu -->
				<div>
					<h4>Inspiroidu</h4>
					<ul data-brand-show="boona">
						<li>Kuvagalleria</li>
						<li>Laitelistaus</li>
						<li>Jooga &amp; Wellness Studio</li>
						<li>Intensed-treenivaatteet</li>
					</ul>
					<ul data-brand-show="saterinportti">
						<li>Kuvagalleria</li>
						<li>Laitelistaus</li>
						<li>Well-sertifikaatti</li>
						<li>Blogi</li>
					</ul>
				</div>

				<!-- Col 4: Asiakaspalvelu -->
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
				<img data-brand-show="boona" src="<?php echo esc_url( SATERINPORTTI_OSTOPOLKU_URL . 'assets/brand/Boona/' ); ?>bööna-logo.svg" alt="">
				<span data-brand-show="saterinportti" class="sp-home-footer-wordmark-text">Säterinportti</span>
			</div>
		</div>
	</footer>

</div>

<script>
/* Mobiilimenu-toggle */
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
	menu.querySelectorAll('a').forEach(function (a) {
		a.addEventListener('click', function () { setOpen(false); });
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && btn.getAttribute('aria-expanded') === 'true') {
			setOpen(false);
			btn.focus();
		}
	});
})();
</script>

<?php get_footer();
