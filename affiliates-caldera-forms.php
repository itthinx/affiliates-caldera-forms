<?php
/**
 * affiliates-caldera-forms.php
 *
 * Copyright (c) 2017 "kento" Karim Rahimpur www.itthinx.com
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
 * @author Karim Rahimpur
 * @package affiliates-caldera-forms
 * @since affiliates-caldera-forms 1.0.0
 *
 * Plugin Name: Affiliates Caldera Forms
 * Plugin URI: http://www.itthinx.com/plugins/affiliates-caldera-forms/
 * Description: Integrates <a href="https://wordpress.org/plugins/affiliates/">Affiliates</a>, <a href="https://www.itthinx.com/shop/affiliates-pro/">Affiliates Pro</a> and <a href="https://www.itthinx.com/shop/affiliates-enterprise/">Affiliates Enterprise</a> with <a href="https://wordpress.org/plugins/caldera-forms/">Caldera Forms</a>.
 * Version: 1.0.0
 * Author: itthinx
 * Author URI: http://www.itthinx.com/
 * License: GPLv3
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AFFILIATES_CALDERA_FORMS_PLUGIN_VERSION', '1.0.0' );
define( 'AFFILIATES_CALDERA_FORMS_PLUGIN_DOMAIN', 'affiliates-caldera-forms' );

/**
 * Plugin boot.
 */
function affiliates_caldera_forms_plugins_loaded() {
	if (
		defined( 'AFFILIATES_EXT_VERSION' ) &&
		version_compare( AFFILIATES_EXT_VERSION, '3.0.0' ) >= 0 &&
		class_exists( 'Affiliates_Referral' ) &&
		(
			!defined( 'Affiliates_Referral::DEFAULT_REFERRAL_CALCULATION_KEY' ) ||
			!get_option( Affiliates_Referral::DEFAULT_REFERRAL_CALCULATION_KEY, null )
		)
	) {
		define ( 'ACF_LIB', 'lib' );
	} else {
		define ( 'ACF_LIB', 'lib-2' );
	}
	define( 'AFFILIATES_CALDERA_FORMS_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'AFFILIATES_CALDERA_FORMS_LIB', AFFILIATES_CALDERA_FORMS_DIR . '/lib' );
	define( 'AFFILIATES_CALDERA_FORMS_PLUGIN_URL', plugins_url( 'affiliates-caldera-forms' ) );
	require_once AFFILIATES_CALDERA_FORMS_LIB . '/class-affiliates-cf-admin.php';
	require_once AFFILIATES_CALDERA_FORMS_LIB . '/class-affiliates-cf-referrals.php';
}
add_action( 'plugins_loaded', 'affiliates_caldera_forms_plugins_loaded' );
