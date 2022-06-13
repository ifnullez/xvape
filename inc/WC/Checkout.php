<?php
namespace App\WC;

use WC_Checkout;

class Checkout extends WC_Checkout {
    public function get_checkout_fields( $fieldset = '' ) {
		if ( ! is_null( $this->fields ) ) {
			return $fieldset ? $this->fields[ $fieldset ] : $this->fields;
		}

		// Fields are based on billing/shipping country. Grab those values but ensure they are valid for the store before using.
		$billing_country   = $this->get_value( 'billing_country' );
		$billing_country   = empty( $billing_country ) ? WC()->countries->get_base_country() : $billing_country;
		$allowed_countries = WC()->countries->get_allowed_countries();

		if ( ! array_key_exists( $billing_country, $allowed_countries ) ) {
			$billing_country = current( array_keys( $allowed_countries ) );
		}

		$shipping_country  = $this->get_value( 'shipping_country' );
		$shipping_country  = empty( $shipping_country ) ? WC()->countries->get_base_country() : $shipping_country;
		$allowed_countries = WC()->countries->get_shipping_countries();

		if ( ! array_key_exists( $shipping_country, $allowed_countries ) ) {
			$shipping_country = current( array_keys( $allowed_countries ) );
		}

		$this->fields = array(
			'billing'  => WC()->countries->get_address_fields(
				$billing_country,
				'billing_'
			),
			'shipping' => WC()->countries->get_address_fields(
				$shipping_country,
				'shipping_'
			),
			'account'  => array(),
			'order'    => array(
				'order_comments' => array(
					'type'        => 'textarea',
					'class'       => array( 'notes' ),
					'label'       => __( 'Order notes', 'woocommerce' ),
					'placeholder' => esc_attr__(
						'Notes about your order, e.g. special notes for delivery.',
						'woocommerce'
					),
				),
			),
		);

		if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
			$this->fields['account']['account_username'] = array(
				'type'         => 'text',
				'label'        => __( 'Account username', 'woocommerce' ),
				'required'     => true,
				'placeholder'  => esc_attr__( 'Username', 'woocommerce' ),
				'autocomplete' => 'username',
			);
		}

		if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
			$this->fields['account']['account_password'] = array(
				'type'         => 'password',
				'label'        => __( 'Create account password', 'woocommerce' ),
				'required'     => true,
				'placeholder'  => esc_attr__( 'Password', 'woocommerce' ),
				'autocomplete' => 'new-password',
			);
		}
		$this->fields = apply_filters( 'woocommerce_checkout_fields', $this->fields );

		foreach ( $this->fields as $field_type => $fields ) {
			// Sort each of the checkout field sections based on priority.
			uasort( $this->fields[ $field_type ], 'wc_checkout_fields_uasort_comparison' );

			// Add accessibility labels to fields that have placeholders.
			foreach ( $fields as $single_field_type => $field ) {
				if ( empty( $field['label'] ) && ! empty( $field['placeholder'] ) ) {
					$this->fields[ $field_type ][ $single_field_type ]['label']       = $field['placeholder'];
					$this->fields[ $field_type ][ $single_field_type ]['label_class'] = array( 'screen-reader-text' );
				}
			}
		}

		return $fieldset ? $this->fields[ $fieldset ] : $this->fields;
	}
}