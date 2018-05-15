<?php
/**
 * class-affiliates-cf-admin.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package affiliates-caldera-forms
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiliates integration admin section.
 */
class Affiliates_Cf_Admin {

	/**
	 * Form id
	 *
	 * @var string
	 */
	public static $form_id = null;

	/**
	 * Plugin Initialization
	 */
	public static function init() {
		if ( isset( $_GET['edit'] ) ) {
			self::$form_id = $_GET['edit'];
			add_filter( 'caldera_forms_get_panel_extensions', array( __CLASS__, 'caldera_forms_get_panel_extensions' ), 10, 1 );
		}
	}

	/**
	 * Load translations.
	 */
	public static function wp_init() {
		load_plugin_textdomain( 'affiliates-caldera-forms', false, 'affiliates-caldera-forms/languages' );
	}

	/**
	 * Adds Affiliates tab in form admin page
	 *
	 * @param array $array
	 * @return array
	 */
	public static function caldera_forms_get_panel_extensions( $array ) {
		$array['form_layout']['tabs']['affiliates'] = array(
			'name'     => 'Affiliates',
			'location' => 'lower',
			'label'    => 'Affiliates Settings',
			'canvas'   => AFFILIATES_CALDERA_FORMS_LIB . '/class-affiliates-cf-form-settings.php',
			'tip'      => array(
				// @todo here we should add a link to the doc for Affiliates Caldera Forms integration in docs.itthinx.com
				'link' => 'https://docs.itthinx.com',
				'text' => 'Affiliates Caldera Forms Usage'
			)
		);
		return $array;
	}
} Affiliates_Cf_Admin::init();
