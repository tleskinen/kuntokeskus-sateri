<?php
/**
 * Pakettien data: scope (Kuntosali / Kuntosali + jumpat) × commitment-tyyppi.
 *
 * Säterinportin todellinen rakenne:
 *  - Kaksi scope-tasoa
 *  - Kolme commitment-tyyppiä per scope:
 *    1. Etuasiakkuus (12 kk sopimus, kk-laskutus)        — 39/54 €/kk
 *    2. Vuosisäästö  (12 kk vuosikortti, kertamaksu)     — 370/550 € (30,83/45,83 €/kk)
 *    3. Joustojäsenyys (toistaiseksi voimassa, kk)       — 49/64 €/kk
 *
 *  - Tarjous (tutustumistarjous) voi olla aktivoitu joillekin commitment-tyypeille.
 *  - Ryhmäliikuntakatalog on yhteinen kaikille (renderöidään modaalissa).
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Packages {

	const OPTION_TARJOUS = 'saterinportti_ostopolku_tarjous';
	const TRANSIENT_KEY  = 'saterinportti_ostopolku_packages_cache';
	const TRANSIENT_TTL  = 15 * MINUTE_IN_SECONDS;

	/**
	 * Scope-vaihtoehdot — mitä palveluita asiakas valitsee.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_scopes(): array {
		return apply_filters( 'saterinportti_ostopolku_scopes', [
			[
				'key'      => 'gym',
				'name'     => 'Kuntosali',
				'subtitle' => 'Salipääsy 24/7, saunatilat ke+su.',
				'classes_link' => false,
			],
			[
				'key'      => 'gym-classes',
				'name'     => 'Kuntosali + jumpat',
				'subtitle' => 'Sali ja kaikki ryhmätunnit ml. REBELS.',
				'classes_link' => true, // näytä info-ikoni "jumpat"-sanan vieressä
			],
		] );
	}

	/**
	 * Hinnasto: scope × commitment → kk-hinta, vuosihinta, kk-ekvivalentti.
	 * Numerot Säterinportin verkkokaupasta (2026-04).
	 *
	 * @return array<string, array<string, array<string, mixed>>>
	 */
	public function get_pricing(): array {
		return apply_filters( 'saterinportti_ostopolku_pricing', [
			'gym' => [
				'etuasiakkuus' => [
					'monthly'             => '39',
					'total_year'          => '468',
					'product_id'          => 82,   // "3. Kuntosali 12 kk"
					'tarjous_product_id'  => 123,  // "Treenit KESÄKUUN loppuun 1€ / 12 kk asiakkuus kuntosalille"
				],
				'vuosisaasto' => [
					'monthly_equiv'       => '30,83',
					'total_year'          => '370',
					// HUOM: 370 € vakio-SKU ei ole tällä hetkellä FB:ssä. Käytetään
					// tilapäisesti 350 €:n tarjous-tuotetta (ID 97). Jenni luo vakion
					// myöhemmin tai vaihtaa total_year = 350.
					'product_id'          => null,
					'tarjous_product_id'  => 97,
					'note'                => 'Vain tarjousversio (350 €) saatavilla — pyydä vakio-SKU Jenniltä',
				],
				'joustojasenyys' => [
					'monthly'             => '49',
					'product_id'          => 57,   // "1. Kuntosali, toistaiseksi voimassaoleva asiakkuus"
					'tarjous_product_id'  => 124,  // "Treenit toukokuun loppuun 1€ / Kuntosali toistaiseksi"
				],
			],
			'gym-classes' => [
				'etuasiakkuus' => [
					'monthly'             => '54',
					'total_year'          => '648',
					'product_id'          => 103,  // "3. Ryhmäliikunta, REBELS-tunnit & kuntosali 12kk"
					'tarjous_product_id'  => 105,  // "Treenit KESÄKUUN loppuun 1€ / 12 kk asiakkuus jumpat & sali"
				],
				'vuosisaasto' => [
					'monthly_equiv'       => '45,83',
					'total_year'          => '550',
					'product_id'          => 43,   // "Vuosikortti ryhmäliikunta ja kuntosali 550€"
					'tarjous_product_id'  => null,  // ei tarjousversiota — vuosikortti aina vakiohintana
				],
				'joustojasenyys' => [
					'monthly'             => '64',
					'product_id'          => 102,  // "2. Ryhmäliikunta, REBELS-tunnit & kuntosali"
					'tarjous_product_id'  => 86,   // "Treenit toukokuun loppuun 1€ / jumpat & sali toistaiseksi"
				],
			],
		] );
	}

	/**
	 * Commitment-tyypit: 3 korttia /liity-sivulla.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_commitments(): array {
		return apply_filters( 'saterinportti_ostopolku_commitments', [
			[
				'key'          => 'etuasiakkuus',
				'name'         => 'Etuasiakkuus',
				'subtitle'     => 'Vuosisopimus kk-laskutuksella',
				'pill_accent'  => 'Suosituin',
				'pill_neutral' => '',
				'commit_note'  => '12 kk sitoumus, jatkuu joustavasti',
				'features'     => [
					'Laskutetaan kuukausittain',
					'Tauotettavissa 1–2 kertaa sopimuskaudella',
				],
				'highlight'    => true,
			],
			[
				'key'          => 'vuosisaasto',
				'name'         => 'Vuosisäästö',
				'subtitle'     => 'Vuosi kerralla maksettuna',
				'pill_accent'  => 'Edullisin',
				'pill_neutral' => '',
				'commit_note'  => '12 kk, maksu ennakkoon',
				'features'     => [
					'Voimassa 12 kk ostopäivästä',
					'Tauotettavissa 1–2 kk sopimuskaudella',
				],
				'highlight'    => false,
			],
			[
				'key'          => 'joustojasenyys',
				'name'         => 'Joustojäsenyys',
				'subtitle'     => 'Kuukausilaskutus, ei sitoutumista',
				'pill_accent'  => '',
				'pill_neutral' => 'Ei sitoutumista',
				'commit_note'  => 'Peruutus milloin vain',
				'features'     => [
					'Laskutetaan kuukausittain',
					'Kuukauden irtisanomisaika',
				],
				'highlight'    => false,
			],
		] );
	}

	/**
	 * Tarjous-data: WP-optiosta haettu, defaultit kovakoodattuna.
	 *
	 * Schema:
	 *   [
	 *     'active'             => true,
	 *     'label'              => 'Treenit huhtikuun loppuun',
	 *     'first_month_price'  => 1,                       // €
	 *     'applies_to'         => ['etuasiakkuus', 'joustojasenyys'],
	 *     'avainkortti_free'   => true,
	 *     'ends_at_label'      => 'huhtikuun loppuun',
	 *   ]
	 *
	 * @return array<string, mixed>
	 */
	public function get_tarjous(): array {
		$default = [
			'active'             => true,
			'label'              => 'Treenit toukokuun ja kesäkuun loppuun',
			'first_month_price'  => 1,
			'applies_to'         => [ 'etuasiakkuus', 'joustojasenyys' ],
			'avainkortti_free'   => true,
			// Tarjouksen kesto vaihtelee sopimustyypin mukaan:
			// - Etuasiakkuus (12 kk): kesäkuun loppuun (pidempi sitouttamiseen)
			// - Joustojäsenyys (toistaiseksi): toukokuun loppuun
			'ends_at_label'                => 'kesäkuun loppuun',  // fallback
			'ends_at_label_per_commitment' => [
				'etuasiakkuus'    => 'kesäkuun loppuun',
				'joustojasenyys'  => 'toukokuun loppuun',
			],
		];
		$opt = get_option( self::OPTION_TARJOUS, [] );
		return is_array( $opt ) ? array_merge( $default, $opt ) : $default;
	}

	/**
	 * Yhteinen ryhmäliikunta-tuntilista — Säterinportin oman valikoiman pohjalta.
	 *
	 * @return array<int, array{key: string, label: string, hero: bool}>
	 */
	public function get_classes_catalog(): array {
		return apply_filters( 'saterinportti_ostopolku_classes', [
			[ 'key' => 'rebels',     'label' => '⚡ REBELS',                   'hero' => true ],
			[ 'key' => 'bodypump',   'label' => 'Les Mills BODYPUMP',          'hero' => false ],
			[ 'key' => 'bodycombat', 'label' => 'Les Mills BODYCOMBAT',        'hero' => false ],
			[ 'key' => 'bodybalance','label' => 'Les Mills Bodybalance',       'hero' => false ],
			[ 'key' => 'spinning',   'label' => 'Spinning',                    'hero' => false ],
			[ 'key' => 'crosstraining', 'label' => 'Cross Training',           'hero' => false ],
			[ 'key' => 'kahvakuula', 'label' => 'Kahvakuula',                  'hero' => false ],
			[ 'key' => 'pilates',    'label' => 'Pilates',                     'hero' => false ],
			[ 'key' => 'yinjooga',   'label' => 'Yin Jooga',                   'hero' => false ],
			[ 'key' => 'aromaflow',  'label' => 'Aroma Flow',                  'hero' => false ],
			[ 'key' => 'zumba',      'label' => 'Zumba',                       'hero' => false ],
			[ 'key' => 'liikkuvuus', 'label' => 'Kehonpaino & liikkuvuus',     'hero' => false ],
		] );
	}

	/**
	 * Upsell-tuotteet, näytetään /maksu-sivulla.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_upsells(): array {
		return apply_filters( 'saterinportti_ostopolku_upsells', [
			[
				'key'         => 'inbody',
				'thumb'       => '📊',
				'name'        => 'InBody-kehonkoostumusmittaus',
				'desc'        => 'Mittaa lähtötaso ja seuraa edistystä. Sis. lyhyen yhteenvetokeskustelun ohjaajan kanssa.',
				'price'       => 25,
				'price_was'   => 35,
			],
			[
				'key'         => 'pt-aloitus',
				'thumb'       => '🏋',
				'name'        => 'Personal Training -aloituskerta',
				'desc'        => '55 minuutin ohjattu kerta + henkilökohtainen treeniohjelma. Hinta vain uusille jäsenille.',
				'price'       => 49,
				'price_was'   => 60,
			],
		] );
	}

	/**
	 * Lisäjäsen / kaveri-alennus -konfiguraatio. Tämä on erillään tavallisista
	 * upselleistä koska:
	 *  - Hinta on dynaaminen (riippuu valitusta scopesta + commitmentista)
	 *  - Vaatii kaverin tiedot (lomakekentät)
	 *  - Saa tarjouksen samat ehdot kuin pääjäsen
	 *  - On toistuva, ei kertaluonteinen kuten InBody tai t-paita
	 *
	 * @return array<string, mixed>
	 */
	public function get_lisajasen_config(): array {
		return apply_filters( 'saterinportti_ostopolku_lisajasen', [
			'enabled'                     => true,
			'discount_percent'            => 15,
			'thumb'                       => '👥',
			'name'                        => 'Lisäjäsen — Tuo kaveri',
			'desc'                        => 'Tuo puoliso, kaveri tai treenikamu samalla sopimuksella. Kaveri saa 15 % alennusta ja samat tarjousehdot kuin sinä.',
			'tarjous_applies'             => true,  // saa myös 1€ ensimmäinen kk jos tarjous koskee sopimusta
			'avainkortti_free_in_tarjous' => true,  // ja avainkortti veloituksetta
		] );
	}

	/**
	 * Saatavilla olevat maksutavat /maksu-sivulla, ryhmiteltyinä
	 * (Visma Pay = verkkopankit + kortit + MobilePay + Siirto, Liikuntaetu = ePassi/Smartum/Edenred).
	 *
	 * Sama valikoima kuin Säterinportin nykyisessä /verkkokauppa/-kassassa.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_payment_methods(): array {
		return apply_filters( 'saterinportti_ostopolku_payment_methods', [
			[
				'group'       => 'visma',
				'group_label' => 'Visma Pay',
				'methods'     => [
					[ 'key' => 'nordea',       'label' => 'Nordea' ],
					[ 'key' => 'op',           'label' => 'OP' ],
					[ 'key' => 'danske',       'label' => 'Danske Bank' ],
					[ 'key' => 'pop',          'label' => 'POP Pankki' ],
					[ 'key' => 'saastopankki', 'label' => 'Säästöpankki' ],
					[ 'key' => 'omasp',        'label' => 'Oma Säästöpankki' ],
					[ 'key' => 'aktia',        'label' => 'Aktia' ],
					[ 'key' => 'alandsbanken', 'label' => 'Ålandsbanken' ],
					[ 'key' => 'spankki',      'label' => 'S-Pankki' ],
					[ 'key' => 'mastercard',   'label' => 'Mastercard' ],
					[ 'key' => 'visa',         'label' => 'VISA' ],
					[ 'key' => 'mobilepay',    'label' => 'MobilePay' ],
					[ 'key' => 'siirto',       'label' => 'Siirto' ],
				],
			],
			[
				'group'       => 'liikuntaetu',
				'group_label' => 'Maksaminen liikuntaetuudella',
				'methods'     => [
					[ 'key' => 'epassi',  'label' => 'ePassi' ],
					[ 'key' => 'smartum', 'label' => 'Smartum' ],
					[ 'key' => 'edenred', 'label' => 'Edenred' ],
				],
			],
		] );
	}
}
