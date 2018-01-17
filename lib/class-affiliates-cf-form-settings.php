<?php
/**
 * class-affiliates-cf-form-settings.php
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
 * Affiliates CF Settings
 */
class Affiliates_Cf_Form_Settings {

	/**
	 * Class initialisation
	 */
	public static function init() {
		if ( isset( Affiliates_Cf_Admin::$form_id ) ) {
			self::render_form( Affiliates_Cf_Admin::$form_id );
		}
	}

	/**
	 * Renders Affiliates settings form
	 *
	 * @param string $id
	 */
	public static function render_form( $id ) {
		$default_status = get_option( 'aff_default_referral_status', AFFILIATES_REFERRAL_STATUS_ACCEPTED );
		$options = get_option( $id , array() );

		$enable_form_referrals = isset( $options['affiliates_cf']['enable_form_referrals'] ) ? $options['affiliates_cf']['enable_form_referrals'] : false;
		$referral_status = isset( $options['affiliates_cf']['referral_status' ] ) ? $options['affiliates_cf']['referral_status'] : $default_status;
		$referral_amount = isset( $options['affiliates_cf']['referral_amount' ] ) ? $options['affiliates_cf']['referral_amount'] : '';
		$referral_rate = isset( $options['affiliates_cf']['referral_rate'] ) ? $options['affiliates_cf']['referral_rate'] : '';

		// referral status
		$status_descriptions = array(
			AFFILIATES_REFERRAL_STATUS_ACCEPTED => __( 'Accepted', 'affiliates-caldera-forms' ),
			AFFILIATES_REFERRAL_STATUS_CLOSED   => __( 'Closed', 'affiliates-caldera-forms' ),
			AFFILIATES_REFERRAL_STATUS_PENDING  => __( 'Pending', 'affiliates-caldera-forms' ),
			AFFILIATES_REFERRAL_STATUS_REJECTED => __( 'Rejected', 'affiliates-caldera-forms' ),
		);
		$status_select = '<select name="config[affiliates_cf][referral_status]">';
		foreach ( $status_descriptions as $status_key => $status_value ) {
			if ( $status_key == $referral_status ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$status_select .= "<option value='$status_key' $selected>$status_value</option>";
		}
		$status_select .= '</select>';

		// datainput-mask, based on CF API
		$amount_mask = "'mask': '[9{*}]' ";
		$rate_mask = "'mask': '[.9{*}]' ";

		$output = '';
		$output .= '<div class="caldera-config-group">';
		$output .= '<label>';
		$output .= esc_html__( 'Referrals', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<div class="caldera-config-field">';

		$output .= '<label>';
		$output .= '<input name="config[affiliates_cf][enable_form_referrals]" type="checkbox" ' . ( $enable_form_referrals ? 'checked="checked" ' : '' ) . '/>';
		$output .= esc_html__( 'Enable referrals', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<p class="description">';
		$output .= esc_html__( 'Allow affiliates to earn commissions on submissions of this form.', 'affiliates-caldera-forms' );
		$output .= '</p>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="caldera-config-group">';
		$output .= '<label>';
		$output .= esc_html__( 'Referral Status', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<div class="caldera-config-field">';
		$output .= '<label>';
		$output .= $status_select;
		$output .= '&nbsp&nbsp';
		$output .= esc_html__( 'Referral Status', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<p class="description">';
		$output .= esc_html__( 'The default status of referrals recorded for this form.', 'affiliates-caldera-forms' );
		$output .= '</p>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="caldera-config-group">';
		$output .= '<label>';
		$output .= esc_html__( 'Referral Amount', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<div class="caldera-config-field">';
		$output .= '<label>';
		$output .= '<input type="text" name="config[affiliates_cf][referral_amount]" value="' . $referral_amount . '" data-inputmask="' . $amount_mask . ' "  >';
		$output .= '&nbsp&nbsp';
		$output .= esc_html__( 'Referral Amount', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<p class="description">';
		$output .= esc_html__( 'If a fixed amount is desired, input the referral amount to be credited for form submissions.', 'affiliates-caldera-forms' );
		$output .= '<br />';
		$output .= esc_html__( 'Leave this empty if a commission based on the form total should be granted.', 'affiliates-caldera-forms' );
		$output .= '</p>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="caldera-config-group">';
		$output .= '<label>';
		$output .= esc_html__( 'Referral Rate', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<div class="caldera-config-field">';
		$output .= '<label>';
		$output .= '<input type="text" name="config[affiliates_cf][referral_rate]" value="' . $referral_rate . '" data-inputmask="' . $rate_mask . ' "  >';
		$output .= '&nbsp&nbsp';
		$output .= esc_html__( 'Referral Rate', 'affiliates-caldera-forms' );
		$output .= '</label>';
		$output .= '<p class="description">';
		$output .= esc_html__( 'If the referral amount should be calculated based on the form total, input the rate to be used.', 'affiliates-caldera-forms' );
		$output .= '<br />';
		$output .= esc_html__( 'For example, use 0.1 to grant a commission of 10%. Leave this empty if a fixed commission should be granted.', 'affiliates-caldera-forms' );
		$output .= '</p>';
		$output .= '</div>';
		$output .= '</div>';
		// @codingStandardsIgnoreStart
		echo $output;
		// @codingStandardsIgnoreEnd
	}

} Affiliates_Cf_Form_Settings::init();
