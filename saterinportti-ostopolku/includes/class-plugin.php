<?php
/**
 * Päälaukaisin. Kokoaa palikat yhteen ja käynnistää ne.
 *
 * @package Saterinportti\Ostopolku
 */

namespace Saterinportti\Ostopolku;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	/**
	 * @var Plugin|null
	 */
	private static $instance = null;

	/** @var Fiboproduct */
	public $fiboproduct;

	/** @var Packages */
	public $packages;

	/** @var Page_Template */
	public $page_template;

	/** @var Assets */
	public $assets;

	/** @var Admin_Settings */
	public $admin_settings;

	/** @var Brand_Toggle */
	public $brand_toggle;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function boot(): void {
		$this->packages       = new Packages();
		$this->fiboproduct    = new Fiboproduct();
		$this->page_template  = new Page_Template();
		$this->assets         = new Assets();
		$this->admin_settings = new Admin_Settings();
		$this->brand_toggle   = new Brand_Toggle();

		$this->fiboproduct->register();
		$this->page_template->register();
		$this->assets->register();
		$this->admin_settings->register();
		$this->brand_toggle->register();
	}
}
