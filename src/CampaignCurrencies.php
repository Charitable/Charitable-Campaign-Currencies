<?php
/**
 * Module responsible for supporting per-campaign currencies.
 *
 * @package   Charitable CampaignCurrencies/Classes
 * @author    Eric Daams
 * @copyright Copyright (c) 2022, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * @version   1.0.0
 */

namespace Charitable\Pro\CurrenciesPerCampaign;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Charitable\Pro\CurrenciesPerCampaign\CampaignCurrencies' ) ) :

	/**
	 * \Charitable\Pro\CurrenciesPerCampaign\CampaignCurrencies
	 *
	 * @since 1.0.0
	 */
	class CampaignCurrencies {

		/**
		 * Whether init hooks have been set up.
		 *
		 * @since 1.0.0
		 *
		 * @var   boolean
		 */
		private static $setup;

		/**
		 * Class instantiation.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( ! isset( self::$setup ) ) {
				$this->init();
			}
		}

		/**
		 * Set up module hooks.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			add_action( 'init', array( $this, 'add_currency_gateway_campaign_fields_section' ) );
			add_filter( 'charitable_default_campaign_fields', array( $this, 'register_field' ) );
			add_filter( 'charitable_donation_meta', array( $this, 'save_donation_meta' ), 10, 3 );
			add_filter( 'charitable_donation_details_table_show_currency', '__return_true' );
			add_filter( 'charitable_currency', array( $this, 'filter_currency_by_campaign' ) );

			self::$setup = true;
		}

		/**
		 * Add Currency section to Campaign Fields API.
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function add_currency_gateway_campaign_fields_section() {
			\charitable()->campaign_fields()->register_section( 'admin', 'currency-gateway-options', __( 'Currency', 'charitable-campaign-currencies' ) );
		}

		/**
		 * Register the gateway field.
		 *
		 * @since  1.0.0
		 *
		 * @param  array $fields Campaign fields.
		 * @return array
		 */
		public function register_field( $fields ) {
			$fields['currency_heading'] = array(
				'label'         => __( 'Currency', 'charitable-campaign-currencies' ),
				'data_type'     => 'core',
				'campaign_form' => false,
				'admin_form'    => array(
					'type'     => 'heading',
					'title'    => __( 'Currency', 'charitable-campaign-currencies' ),
					'priority' => 2,
					'section'  => 'currency-gateway-options',
				),
				'show_in_export' => true,
			);

			$fields['currency'] = array(
				'label'         => __( 'Currency', 'charitable-campaign-currencies' ),
				'data_type'     => 'meta',
				'campaign_form' => false,
				'admin_form'    => array(
					'type'     => 'select',
					'required' => true,
					'default'  => \charitable_get_default_currency(),
					'options'  => \charitable_get_currency_helper()->get_all_currencies(),
					'section'  => 'currency-gateway-options',
					'priority' => 10,
				),
				'show_in_export' => true,
			);

			return $fields;
		}

		/**
		 * Save the currency for the donation to the meta.
		 *
		 * @since  1.0.0
		 *
		 * @param  array                         $meta        Donation meta.
		 * @param  int                           $donation_id Donation ID.
		 * @param  Charitable_Donation_Processor $processor   The donation processor object.
		 * @return array
		 */
		public function save_donation_meta( $meta, $donation_id, $processor ) {
			$campaigns = $processor->get_donation_data_value( 'campaigns' );

			if ( 1 !== count( $campaigns ) ) {
				return $meta;
			}

			$campaign         = charitable_get_campaign( current( $campaigns )['campaign_id'] );
			$meta['currency'] = $campaign->get( 'currency' );

			return $meta;
		}

		/**
		 * Filter the currency based on the current campaign.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $currency The default currency.
		 * @return string
		 */
		public function filter_currency_by_campaign( $currency ) {
			$campaign = charitable_get_current_campaign();

			if ( false !== $campaign ) {
				$currency = $campaign->get( 'currency' );
			}

			return $currency;
		}
	}

endif;
