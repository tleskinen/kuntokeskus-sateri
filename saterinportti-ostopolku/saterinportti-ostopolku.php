<?php
/**
 * Plugin Name: Säterinportti Ostopolku
 * Description: Ostopolun uudistus — /liity-sivu, FitnessBooker-tuoteparametrin säilytys rekisteröinnin/kirjautumisen yli sekä pakettien hallinta. Tarkoitettu ajettavaksi mu-pluginina.
 * Version: 0.9.0
 * Author: Säterinportti
 * Text Domain: saterinportti-ostopolku
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

define( 'SATERINPORTTI_OSTOPOLKU_VERSION', '0.9.0' );
define( 'SATERINPORTTI_OSTOPOLKU_FILE', __FILE__ );
define( 'SATERINPORTTI_OSTOPOLKU_DIR', plugin_dir_path( __FILE__ ) );
define( 'SATERINPORTTI_OSTOPOLKU_URL', plugin_dir_url( __FILE__ ) );

/**
 * Mu-pluginit ovat flat-tiedostoja oletuksena. Ladataan alihakemiston pääluokat käsin.
 */
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-plugin.php';
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-fiboproduct.php';
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-packages.php';
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-page-template.php';
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-assets.php';
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-admin-settings.php';
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-payment-icons.php';
require_once SATERINPORTTI_OSTOPOLKU_DIR . 'includes/class-brand-toggle.php';

add_action( 'plugins_loaded', static function () {
	Plugin::instance()->boot();
} );
