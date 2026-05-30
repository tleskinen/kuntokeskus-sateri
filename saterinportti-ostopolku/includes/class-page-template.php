<?php
/**
 * Sivupohjien lataus ja auto-luonti.
 *
 * Mu-plugin tarjoaa neljä sivupohjaa Säterinportin ostopolulle:
 *  - /liity      → page-liity.php       (pakettivalinta)
 *  - /jatka      → page-rekisterointi.php (vaihe 1)
 *  - /maksu      → page-maksu.php       (vaihe 2)
 *  - /vahvistus  → page-vahvistus.php   (kiitos-sivu)
 *
 * Kun mu-plugin aktivoidaan ja "Asetukset → Säterinportti Ostopolku → Luo
 * sivut" klikataan (tai automaattinen luonti on päällä), nämä sivut luodaan
 * WordPressiin oikeilla slugilla ja sivupohjilla.
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Page_Template {

	/**
	 * Slug → tiedosto + WP-näyttöotsikko -mappaus.
	 *
	 * @return array<string, array{template: string, file: string, title: string}>
	 */
	public static function pages(): array {
		$pages = [
			// DEMO / PROTOTYPE — etusivu kuuluu Säterinportin Sage-teemaan tuotannossa.
			// Tämä sivu on prototypoinnin ja Playground-demon tueksi. Tuotannossa
			// suositellaan poistettavaksi joko WP-administa TAI teema-puolen filtterillä:
			//   add_filter('saterinportti_ostopolku_pages', function($p){ unset($p['etusivu']); return $p; });
			'etusivu' => [
				'template' => 'saterinportti-ostopolku/page-etusivu.php',
				'file'     => 'page-etusivu.php',
				'title'    => 'Etusivu',
				'demo'     => true,
			],
			'liity' => [
				'template' => 'saterinportti-ostopolku/page-liity.php',
				'file'     => 'page-liity.php',
				'title'    => 'Liity jäseneksi',
			],
			'jatka' => [
				'template' => 'saterinportti-ostopolku/page-rekisterointi.php',
				'file'     => 'page-rekisterointi.php',
				'title'    => 'Yhteystiedot',
			],
			'maksu' => [
				'template' => 'saterinportti-ostopolku/page-maksu.php',
				'file'     => 'page-maksu.php',
				'title'    => 'Maksu',
			],
			'vahvistus' => [
				'template' => 'saterinportti-ostopolku/page-vahvistus.php',
				'file'     => 'page-vahvistus.php',
				'title'    => 'Tilaus vahvistettu',
			],
		];

		/**
		 * Sivulistan filtteri — anna teemalle (tai muulle pluginille) mahdollisuus
		 * poistaa demo-sivut tuotannosta.
		 */
		return apply_filters( 'saterinportti_ostopolku_pages', $pages );
	}

	public function register(): void {
		add_filter( 'theme_page_templates', [ $this, 'add_templates_to_selector' ] );
		add_filter( 'template_include', [ $this, 'maybe_load_template' ], 99 );
	}

	public function add_templates_to_selector( array $templates ): array {
		foreach ( self::pages() as $slug => $info ) {
			$templates[ $info['template'] ] = 'Säterinportti — ' . $info['title'];
		}
		return $templates;
	}

	/**
	 * Lataa oma sivupohja jos:
	 *  1) Sivu-editorista valittu meidän template, TAI
	 *  2) Sivun slug matchaa oletukseen (/liity, /jatka, /maksu, /vahvistus)
	 */
	public function maybe_load_template( string $template ): string {
		if ( ! is_singular( 'page' ) ) {
			return $template;
		}

		$post = get_queried_object();
		if ( ! $post instanceof \WP_Post ) {
			return $template;
		}

		$selected = get_page_template_slug( $post->ID );
		$pages    = self::pages();

		// 1) Eksplisiittisesti valittu sivupohja
		foreach ( $pages as $slug => $info ) {
			if ( $info['template'] === $selected ) {
				return $this->resolve( $info['file'] );
			}
		}

		// 2) Slug-pohjainen mappaus
		if ( isset( $pages[ $post->post_name ] ) ) {
			return $this->resolve( $pages[ $post->post_name ]['file'] );
		}

		return $template;
	}

	private function resolve( string $file ): string {
		return SATERINPORTTI_OSTOPOLKU_DIR . 'templates/' . $file;
	}

	/**
	 * Luo WP-sivut jos niitä ei vielä ole. Kutsutaan plugin-aktivoinnissa
	 * tai admin-asetuksissa "Luo sivut" -napilla.
	 *
	 * @return array<string, int> slug → post ID (luotu tai olemassa oleva)
	 */
	public static function create_pages(): array {
		$created = [];
		foreach ( self::pages() as $slug => $info ) {
			$existing = get_page_by_path( $slug );
			if ( $existing ) {
				$created[ $slug ] = (int) $existing->ID;
				continue;
			}
			$post_id = wp_insert_post( [
				'post_title'   => $info['title'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
				'meta_input'   => [
					'_wp_page_template' => $info['template'],
				],
			] );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				$created[ $slug ] = (int) $post_id;
			}
		}
		return $created;
	}
}
