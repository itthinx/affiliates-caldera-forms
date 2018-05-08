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
	 * @param string $id form id
	 */
	public static function render_form( $id ) {
		$output = '';
		$default_status = get_option( 'aff_default_referral_status', AFFILIATES_REFERRAL_STATUS_ACCEPTED );
		$options = get_option( $id , array() );
		$enable_form_referrals = isset( $options['affiliates_cf']['enable_form_referrals'] ) ? $options['affiliates_cf']['enable_form_referrals'] : false;

		if ( defined( 'ACF_LIB' ) ) {
			if ( ACF_LIB == 'lib' ) {
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
				$output .= '<div class="caldera-config-field">';
				$output .= '<p class="description">';
				$output .= esc_html__( 'Referral Commissions for this form can be set through Affiliates > Rates.', 'affiliates-caldera-forms' );
				$output .= '</p>';
				$output .= '</div>';
				$output .= '</div>';
			} else {
				$referral_status       = isset( $options['affiliates_cf']['referral_status' ] ) ? $options['affiliates_cf']['referral_status'] : $default_status;
				$referral_amount       = isset( $options['affiliates_cf']['referral_amount' ] ) ? $options['affiliates_cf']['referral_amount'] : '';
				$referral_rate         = isset( $options['affiliates_cf']['referral_rate'] ) ? $options['affiliates_cf']['referral_rate'] : '';
				$referral_rate_field   = isset( $options['affiliates_cf']['referral_rate_field'] ) ? $options['affiliates_cf']['referral_rate_field'] : '';

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

				// referral rate amount field
				// We use a select here, because there is no explicit total field in Caldera Forms
				// that can be used automatically for referral rate calculations
				$form_fields  = null;
				$caldera_form = Caldera_Forms_Forms::get_form( $id );
				foreach ( $caldera_form['fields'] as $field_id ) {
					if ( $field_id['type'] == 'text' || $field_id['type'] == 'number' ) {
						$form_fields = array( $field_id['ID'] => $field_id['label'] );
					}
				}
				if ( is_array( $form_fields ) ) {
					$rate_field_select = '<select name="config[affiliates_cf][referral_rate_field]">';
					foreach ( $form_fields as $field_key => $field_label ) {
						if ( $field_key == $referral_rate_field ) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						$rate_field_select .= "<option value='$field_key' $selected>$field_label</option>";
					}
					$rate_field_select .= '</select>';
				}

				// datainput-mask, field sanitization based on CF API
				$amount_mask = "'mask': '[9{*}]' "; // integers
				$rate_mask   = "'mask': '[.9{*}]' "; // decimals

				// Form output
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

				$output .= '<div class="caldera-config-group">';
				$output .= '<label>';
				$output .= esc_html__( 'Referral Rate Field', 'affiliates-caldera-forms' );
				$output .= '</label>';
				$output .= '<div class="caldera-config-field">';
	// This is the first option
	// @todo I'm not sure how we can set this field and use it in Rates, for referral rate calculations
				if ( isset( $rate_field_select ) ) {
					$output .= '<label>';
					$output .= $rate_field_select;
					$output .= '&nbsp&nbsp';
					$output .= esc_html__( 'Field Label', 'affiliates-caldera-forms' );
					$output .= '</label>';
					$output .= '<p class="description">';
					$output .= esc_html__( 'Select the form amount field to be used for Referral Rate calculations.', 'affiliates-caldera-forms' );
					$output .= '</p>';
				} else {
					$output .= '<label>';
					$output .= esc_html__( 'Field Label', 'affiliates-caldera-forms' );
					$output .= '</label>';
					$output .= '<p class="description">';
					$output .= esc_html__( 'Add a new text or number type field in your form to select it here for Referral Rate calculations.', 'affiliates-caldera-forms' );
					$output .= '</p>';
				}
				$output .= '</div>';
				$output .= '</div>';

	// This is the second option
				$output .= '<div class="caldera-config-group">';
				$output .= '<label>';
				$output .= esc_html__( 'Referral Rate Field', 'affiliates-caldera-forms' );
				$output .= '</label>';
				$output .= '<div class="caldera-config-field">';
				if ( isset( $affiliates_amount_field ) ) {
					$output .= '<label>';
					$output .= '\'';
					$output .= $affiliates_amount_field;
					$output .= '\'';
					$output .= '&nbsp&nbsp';
					$output .= esc_html__( ' field', 'affiliates-caldera-forms' );
					$output .= '</label>';
					$output .= '<p class="description">';
					$output .= esc_html__( 'The field with label \'Affiliates\' will be used for Referral Rate calculations.', 'affiliates-caldera-forms' );
					$output .= '</p>';
				} else {
					$output .= '<label>';
					$output .= esc_html__( 'No \'Affiliates\' field has been found on this form.', 'affiliates-caldera-forms' );
					$output .= '</label>';
					$output .= '<p class="description">';
					$output .= esc_html__( 'Add a new text field in your form and use \'affiliates\' as the slug, or \'Affiliates\' as the label to be used for Referral Rate calculations.', 'affiliates-caldera-forms' );
					$output .= '</p>';
				}

				$output .= '</div>';
				$output .= '</div>';
			}
		}

		// @codingStandardsIgnoreStart
		echo $output;
		// @codingStandardsIgnoreEnd
	}
} Affiliates_Cf_Form_Settings::init();
