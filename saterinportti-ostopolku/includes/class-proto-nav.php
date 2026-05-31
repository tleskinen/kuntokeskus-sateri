<?php
/**
 * Prototyyppi-navigaattori (DEMO / PROTOTYPE).
 *
 * Liukuva widget alavasemmalla, joka näyttää kaikki ostopolun sivut
 * pillipalkkina ja korostaa kulloinkin auki olevan. Asiakas (joka katsoo
 * prototyyppiä) pystyy hyppäämään mille tahansa sivulle yhdellä klikillä
 * ilman täytettyjä lomakkeita.
 *
 * Tuotannossa piilotetaan filtterillä:
 *   add_filter( 'saterinportti_ostopolku_show_proto_nav', '__return_false' );
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Proto_Nav {

	public function register(): void {
		add_action( 'wp_footer', [ $this, 'render' ], 100 );
	}

	public function render(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$pages = [
			'etusivu'   => [ 'label' => 'Etusivu',   'icon' => '🏠' ],
			'liity'     => [ 'label' => 'Liity',     'icon' => '1' ],
			'jatka'     => [ 'label' => 'Jatka',     'icon' => '2' ],
			'maksu'     => [ 'label' => 'Maksu',     'icon' => '3' ],
			'vahvistus' => [ 'label' => 'Vahvistus', 'icon' => '✓' ],
		];

		$current = $this->current_page_slug();
		?>
		<nav class="sp-proto-nav" aria-label="Prototyyppi-sivut">
			<span class="sp-proto-nav-label" aria-hidden="true">Sivut</span>
			<?php foreach ( $pages as $slug => $info ) :
				$page = get_page_by_path( $slug );
				if ( ! $page ) {
					continue;
				}
				$url      = 'etusivu' === $slug ? home_url( '/' ) : get_permalink( $page );
				$is_active = $current === $slug;
				?>
				<a href="<?php echo esc_url( $url ); ?>"
				   class="sp-proto-nav-item<?php echo $is_active ? ' is-active' : ''; ?>"
				   <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
					<span class="sp-proto-nav-icon" aria-hidden="true"><?php echo esc_html( $info['icon'] ); ?></span>
					<span class="sp-proto-nav-text"><?php echo esc_html( $info['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
		<?php
	}

	private function current_page_slug(): string {
		if ( is_front_page() ) {
			return 'etusivu';
		}
		$post = get_queried_object();
		if ( $post instanceof \WP_Post ) {
			return (string) $post->post_name;
		}
		return '';
	}

	private function is_enabled(): bool {
		return (bool) apply_filters( 'saterinportti_ostopolku_show_proto_nav', true );
	}
}
