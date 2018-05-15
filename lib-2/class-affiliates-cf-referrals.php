<?php
/**
 * class-affiliates-cf-referrals.php
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
 * Affiliates CF Referrals.
 */
class Affiliates_Cf_Referrals {

	/**
	 * Class initialization
	 */
	public static function init() {
		add_action( 'caldera_forms_submit_complete', array( __CLASS__, 'caldera_forms_submit_complete' ), 5, 4 );
	}

	/**
	 * Get form data after submission
	 *
	 * @param array $form
	 * @param string $referrer
	 * @param string $process_id
	 * @param string $entryid
	 */
	public static function caldera_forms_submit_complete( $form, $referrer, $process_id, $entryid ) {
		$affiliate_ids = null;
		$options = get_option( $form['ID'] , array() );
		$enable_form_referrals = isset( $options['affiliates_cf']['enable_form_referrals' ] ) ? $options['affiliates_cf']['enable_form_referrals'] : null;

		if ( $enable_form_referrals ) {
			$data = array();
			foreach ( $form['fields'] as $field_id => $field ) {
				$data[ $field['slug'] ] = Caldera_Forms::get_field_data( $field_id, $form );
			}

			$post_id         = $_POST[ '_cf_cr_pst' ] ? absint( $_POST[ '_cf_cr_pst' ] ) : $form['ID'];
			$description     = sprintf( __( 'Caldera Forms #%d', 'affiliates-caldera-forms' ), $form['name'] );
			$currency        = apply_filters( 'affiliates_cf_currency', 'USD' );
			$referral_amount = isset( $options['affiliates_cf']['referral_amount'] ) ? $options['affiliates_cf']['referral_amount'] : 0;
			$default_status  = get_option( 'aff_default_referral_status', AFFILIATES_REFERRAL_STATUS_ACCEPTED );
			$status          = isset( $options['affiliates_cf']['referral_status' ] ) ? $options['affiliates_cf']['referral_status'] : $default_status;
			$reference       = $entryid;

			$data = array(
				'form_id' => array(
					'title' => 'Caldera Forms Submission #',
					'domain' => 'affiliates-caldera-forms',
					'value' => esc_sql( $form['name'] )
				)
			);

			if ( class_exists( 'Affiliates_Referral_Controller' ) ) {
				$referrer_params = array();
				$rc = new Affiliates_Referral_Controller();

				if ( $affiliate_ids !== null ) {
					foreach ( $affiliate_ids as $affiliate_id ) {
						$referrer_params[] = array( 'affiliate_id' => $affiliate_id );
					}
				} else {
					if ( $rc->evaluate_referrer() ) {
						$referrer_params[] = $rc->evaluate_referrer();
					}
				}
				$n = count( $referrer_params );
				if ( $n > 0 ) {
					foreach ( $referrer_params as $params ) {
						$affiliate_id = $params['affiliate_id'];
						$group_ids = null;
						if ( class_exists( 'Groups_User' ) ) {
							if ( $affiliate_user_id = affiliates_get_affiliate_user( $affiliate_id ) ) {
								$groups_user = new Groups_User( $affiliate_user_id );
								$group_ids = $groups_user->group_ids_deep;
								if ( !is_array( $group_ids ) || ( count( $group_ids ) === 0 ) ) {
									$group_ids = null;
								}
							}
						}
						if ( $form['ID'] ) {
							$referral_items = array();
							$rate_id   = null;
							$object_id = null;
							$term_ids  = null;
							if ( $post_id ) {
								$object_id = $post_id;
								$em_categories = null;
								$term_ids = null;
							}
							if ( $rate = $rc->seek_rate(
								array(
									'affiliate_id' => $affiliate_id,
									'object_id'    => $object_id,
									'term_ids'     => $term_ids,
									'integration'  => 'affiliates-caldera-forms',
									'group_ids'    => $group_ids
								)
							) ) {
								$rate_id = $rate->rate_id;
								$type = 'form';//write_log($rate->value);
								if ( $referral_amount > 0 ) {
									switch ( $rate->type ) {
										case AFFILIATES_PRO_RATES_TYPE_AMOUNT :
											$amount = bcadd( '0', $rate->value, affiliates_get_referral_amount_decimals() );
											break;
										case AFFILIATES_PRO_RATES_TYPE_RATE :
											$amount = bcmul( $referral_amount, $rate->value, affiliates_get_referral_amount_decimals() );
											break;
									}
									// split proportional total if multiple affiliates are involved
									if ( $n > 1 ) {
										$amount = bcdiv( $amount, $n, affiliates_get_referral_amount_decimals() );
									}
									$referral_item = new Affiliates_Referral_Item(
										array(
											'rate_id'     => $rate_id,
											'amount'      => $amount,
											'currency_id' => $currency,
											'type'        => $type,
											'reference'   => $reference,
											'line_amount' => $amount,
											'object_id'   => $object_id
										)
									);
									$referral_items[] = $referral_item;
								}
							}
						}
						$params['post_id']          = $post_id;
						$params['description']      = $description;
						$params['data']             = $data;
						$params['currency_id']      = $currency;
						$params['type']             = 'form';
						$params['referral_items']   = $referral_items;
						$params['reference']        = $reference;
						$params['reference_amount'] = $amount;
						$params['integration']      = 'affiliates-caldera-forms';
						$rc->add_referral( $params );
					}
				}

			} else if ( class_exists( 'Affiliates_Referral_WordPress' ) ) {
				$r = new Affiliates_Referral_WordPress();
				if ( $affiliate_ids !== null ) {
					if ( count( $affiliate_ids ) > 0 ) {
						bcscale( affiliates_get_referral_amount_decimals() );
						$split_amount = bcdiv( $referral_amount, count( $affiliate_ids ) );
						$r->add_referrals( $affiliate_ids, $post_id, $description, $data, $split_amount, null, $currency, $status, 'form', $reference );
					}
				} else {
					$r->evaluate( $post_id, $description, $data, null, $referral_amount, $currency, $status, 'form', $reference );
				}
			} else {
				$aff_id = affiliates_suggest_referral( $post_id, $description, $data, $referral_amount, $currency, $status, 'form', $reference );
			}
		}
	}
} Affiliates_Cf_Referrals::init();
