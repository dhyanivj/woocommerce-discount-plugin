<?php
if ( isset( $_REQUEST['edit'] ) && ! empty( $_REQUEST['edit'] ) ) {
	echo '<input type="hidden" name="update" value="' . esc_html( sanitize_text_field( $_REQUEST['edit'] ) ) . '" >';
}
?>   
<script>
	jQuery( document ).ready( function () {
		jQuery( '#check_on' ).on( 'change', function () {
			let thisval = jQuery( '#check_on' ).val().replace( 'TotalQuantity', 'Total Units' ).replace( 'Quantity', 'No of Items' );
			jQuery( "[for=min]" ).html( 'Minimum ' + thisval + '<span style="color:red;padding-left:5px">*<span>' );
			jQuery( "[for=max]" ).html( 'Maximum ' + thisval );
		} );
		jQuery( '#update' ).on( 'click', function () {
			if( jQuery( '#category_id' ).find( ":selected" ).length<=0 )
			{
				alert( 'Please select a category before saving' );
				return false;
			}   
			return true;
		} );
		jQuery( '#discount_type' ).on( 'change', function () {
			let thisval = jQuery( '#discount_type' ).val();
			if ( thisval == 'Percent Discount' )
			{
				jQuery( '#max_discount' ).parent().show();
				jQuery( "[for=value]" ).html( 'Discount percentage' + '<span style="color:red;padding-left:5px">*<span>' );
			} else if ( thisval == 'Flat Discount' )
			{
				jQuery( "[for=value]" ).html( 'Flat discount amount' + '<span style="color:red;padding-left:5px">*<span>' );
				jQuery( '#max_discount' ).val( '' );
				jQuery( '#max_discount' ).parent().hide();
			} else if ( thisval == 'Fixed Price' )
			{
				jQuery( '#max_discount' ).parent().show();
				jQuery( "[for=value]" ).html( 'Fixed price' + '<span style="color:red;padding-left:5px">*<span>' );
			}
		} );
		jQuery( '#rule_tab label' ).append( '<span style="color:red;padding-left:5px">*<span>' );
		jQuery( '#discount_type' ).trigger( 'change' );
		jQuery( '#check_on' ).trigger( 'change' );
		jQuery( "[for=max]" ).html( jQuery( "[for=max]" ).text() );



	} );

</script>
<div >
	<div id="normal-sortables" class="meta-box-sortables ui-sortable">

		</br>

		<div class="clear"></div>
		<div id="woocommerce-product-data" class="postbox ">
			<div class="inside">
				<div class="panel-wrap product_data" style="min-height: 390px;">
					<ul class="product_data_tabs wc-tabs">
						<li class="rule_options   active">
							<a class="xa_link" onclick="select( this, '#rule_tab' )">
								<span>Rule</span>
							</a>
						</li>
						<li class="adjustment_options">
							<a class="xa_link" onclick="select( this, '#adjustment_tab' )">
								<span>Adjustments<span class="super" style="color:black;">Premium</span></span>
							</a>
						</li>
						<li class="roles_options " style="display: block;">
							<a class="xa_link" onclick="select( this, '#allowed_roles_and_date_tab' )">
								<span>Allowed Roles & Date<span class="super" style="color:black;">Premium</span></span>
							</a>
						</li>
						<li class="restricion_options " style="display: block;">
							<a class="xa_link" onclick="select( this, '#restriction_tab' )">
								<span>Restrictions<span class="super" style="color:black;">Premium</span></span>
							</a>
						</li>

					</ul>
					<div  class="panel woocommerce_options_panel" style="display: block;">
						<div class="options_group" id="rule_tab" style="display: block;">
							<?php
							woocommerce_wp_text_input(
								array(
									'id' => 'offer_name',
									'label' => __( 'Offer name', 'woocommerce' ),
									'placeholder' => 'Enter a descriptive offer name',
									'description' => __( 'Name/Text of the offer to be displayed in the Offer Table. We suggest a detailed description of the discount.', 'woocommerce' ),
									'type' => 'text',
									'desc_tip' => true,
									'value' => ! empty( $_REQUEST['offer_name'] ) ? sanitize_text_field( $_REQUEST['offer_name'] ) : '',
									'custom_attributes' => array(
										'required' => 'required',
									),
								)
							);

							// start Categories  search
							$allowed_html = wp_kses_allowed_html( 'post' );
							?>
							<p class="form-field"><label for="category_id"><?php esc_html_e( 'Product categories', 'woocommerce' ); ?></label>
								<select id="category_id" name="category_id[]" style="width: 50%;height:30px" multiple  class="wc-enhanced-select"  data-placeholder="<?php esc_attr_e( 'Any category', 'woocommerce' ); ?>">
									<?php
									$category_ids = ! empty( $_REQUEST['category_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['category_id'] ) ) : array();  //selected product categorie
									if ( ! is_array( $category_ids ) ) {
										$category_ids = array( $category_ids );
									}
									$prod_categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
									if ( $prod_categories ) {
										foreach ( $prod_categories as $prod_cat ) {
											echo '<option value="' . esc_attr( $prod_cat->term_id ) . '"' . selected( in_array( $prod_cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $prod_cat->name ) . '</option>';
										}
									}
									?>
								</select> <?php echo wp_kses( wc_help_tip( __( 'Product categories that the rule will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ), $allowed_html ); ?></p>
							<?php
							//// end category search
							?>
							<p class="form-field check_on_field">
								<label for="check_on"><?php echo esc_html( 'Check for', 'woocommerce' ); ?></label>
								<i class="desc-tip woocommerce-help-tip" data-tip="<?php echo esc_html( 'The rules can be applied based on “No_of_Items/Price/Weight/Total_Units”', 'woocommerce' ); ?>"></i>
								<select id="check_on" name="check_on" class="select short">
									<option value="Quantity" <?php echo ( !empty($_REQUEST['check_on']) && ( 'Quantity' == $_REQUEST['check_on'] ) ) ? 'selected' : ''; ?>>Quantity</option>
									<option value="Weight" <?php echo ( !empty($_REQUEST['check_on']) && ( 'Weight' == $_REQUEST['check_on'] ) ) ? 'selected' : ''; ?>>Weight</option>
									<option value="Price" <?php echo ( !empty($_REQUEST['check_on']) && ( 'Price' == $_REQUEST['check_on'] ) ) ? 'selected' : ''; ?>>Price</option>
									<option value="TotalQuantity" <?php echo ( !empty($_REQUEST['check_on']) && ( 'TotalQuantity' == $_REQUEST['check_on'] ) ) ? 'selected' : ''; ?>>Total Units</option>
								</select>
							</p>
							<?php
							woocommerce_wp_text_input(
								array(
									'id' => 'min',
									'label' => __( 'Minimum', 'woocommerce' ),
									'description' => __( 'Minimum value to check', 'woocommerce' ),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty( $_REQUEST['min'] ) ? sanitize_text_field( $_REQUEST['min'] ) : '1',
									'custom_attributes' => array(
										'required' => 'required',
										'step' => 'any',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'max',
									'label' => __( 'Maximum', 'woocommerce' ),
									'description' => __( 'Maximum value to check, set it empty for no limit', 'woocommerce' ),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty( $_REQUEST['max'] ) ? sanitize_text_field( $_REQUEST['max'] ) : '',
									'custom_attributes' => array(
										'step' => 'any',
									),
								)
							);
							?>
							<p class="form-field discount_type_field">
								<label for="discount_type"><?php echo esc_html( 'Discount type', 'woocommerce' ); ?></label>
								<i class="desc-tip woocommerce-help-tip" data-tip="<?php echo esc_html( 'Three types of discounts can be applied – “Percentage Discount/Flat Discount/Fixed Price”', 'woocommerce' ); ?>"></i>
								<select id="discount_type" name="discount_type" class="select short">
									<option value="Percent Discount" <?php echo ( !empty($_REQUEST['discount_type']) && ( 'Percent Discount' == $_REQUEST['discount_type'] ) ) ? 'selected' : ''; ?>>Percent Discount</option>
									<option value="Flat Discount" <?php echo ( !empty($_REQUEST['discount_type']) && ( 'Flat Discount' == $_REQUEST['discount_type'] ) ) ? 'selected' : ''; ?>>Flat Discount</option>
									<option value="Fixed Price" <?php echo ( !empty($_REQUEST['discount_type']) && ( 'Fixed Price' == $_REQUEST['discount_type'] ) ) ? 'selected' : ''; ?>>Fixed Price</option>
								</select>
							</p>
							<?php
							
							woocommerce_wp_text_input(
								array(
									'id' => 'value',
									'label' => __( 'Discount', 'woocommerce' ),
									'description' => __( 'If you select “Percentage Discount”, the given percentage ( value ) would be discounted on each unit of the product in the cart. If you select “Flat Discount”, the given amount ( value ) would be discounted at subtotal level in the cart If you select “Fixed Price”, the original price of the product is replaced by the given fixed price ( value ).', 'woocommerce' ),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty( $_REQUEST['value'] ) ? sanitize_text_field( $_REQUEST['value'] ) : '',
									'custom_attributes' => array(
										'required' => 'required',
										'step' => 'any',
									),
								)
							);
							?>
						</div>
						<div class="options_group" id="adjustment_tab" style="display: none;">
							<?php
							woocommerce_wp_text_input(
								array(
									'id' => 'max_discount',
									'label' => __( 'Maximum discount amount', 'woocommerce' ),
									'description' => __( 'After Calculation Discount Value Must Not Exceeed This Amount For This Rule', 'woocommerce' ),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty( $_REQUEST['max_discount'] ) ? sanitize_text_field( $_REQUEST['max_discount'] ) : '',
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'adjustment',
									'label' => __( 'Adjustment amount', 'woocommerce' ),
									'description' => __( 'Adjust final discount amount by this amount', 'woocommerce' ),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty( $_REQUEST['adjustment'] ) ? sanitize_text_field( $_REQUEST['adjustment'] ) : '',
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							?>
						</div>
						<div class="options_group" id="allowed_roles_and_date_tab" style="display: none;">
							<?php
							global $wp_roles;
							$roles        = $wp_roles->get_names();
							$roles['all'] = esc_html__( 'All', 'eh-dynamic-pricing-discounts' );
							woocommerce_wp_select(
								array(
									'id' => 'allow_roles',
									'label' => __( 'Allowed Roles', 'woocommerce' ),
									'options' => $roles,
									'value' => ! empty( $_REQUEST['allow_roles'] ) ? sanitize_text_field( $_REQUEST['allow_roles'] ) : 'all',
									'description' => 'Select the roles for which you want to apply this discount rule.',
									'desc_tip' => true,
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'from_date',
									'value' => esc_attr( ! empty( $_REQUEST['from_date'] ) ? sanitize_text_field( $_REQUEST['from_date'] ) : '' ),
									'label' => __( 'Valid from date', 'woocommerce' ),
									'placeholder' => 'YYYY-MM-DD',
									'description' => 'The date from which the rule would be applied. This can be left blank if do not wish to set up any date range.',
									'desc_tip' => true,
									'class' => 'date-picker',
									'custom_attributes' => array(
										/**
										 * Apply filter hook for date input html pattern
										 * 
										 * @since 1.0.0
										 */
										'pattern' => apply_filters( 'woocommerce_date_input_html_pattern', '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}' ),
										'disabled' => 'disabled',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'to_date',
									'value' => esc_attr( ! empty( $_REQUEST['to_date'] ) ? sanitize_text_field( $_REQUEST['to_date'] ) : '' ),
									'label' => __( 'Expiry date', 'woocommerce' ),
									'placeholder' => 'YYYY-MM-DD',
									'description' => ' The date till which the rule would be valid. You can leave it blank if you wish the rule to be applied forever or would like to end it manually.',
									'desc_tip' => true,
									'class' => 'date-picker',
									'custom_attributes' => array(
										/**
										 * Apply filter hook for date input html pattern
										 * 
										 * @since 1.0.0
										 */
										'pattern' => apply_filters( 'woocommerce_date_input_html_pattern', '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}' ),
										'disabled' => 'disabled',
									),
								)
							);
							?>
						</div>
						<div class="options_group" id="restriction_tab" style="display: none;">
							<?php //premium ?>
							<?php
							woocommerce_wp_text_input(
								array(
									'id' => 'email_ids',
									'label' => __( 'Allowed Email Ids', 'woocommerce' ),
									'placeholder' => 'Enter Email ids seperated by commas',
									'description' => __( 'Enter Email ids seperated by commas, for which you want to allow this rule. and leave blank to allow for all', 'woocommerce' ),
									'type' => 'text',
									'desc_tip' => true,
									'value' => ! empty( $_REQUEST['email_ids'] ) ? sanitize_text_field( $_REQUEST['email_ids'] ) : '',
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'prev_order_count',
									'label' => __( 'Minimum number of orders ( previous orders )', 'woocommerce' ),
									'description' => __( 'Minimum count of preivious orders required for this rule to be executed', 'woocommerce' ),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'custom_attributes' => array(
										'step' => 1,
										'min' => 0,
										'disabled' => 'disabled',
									),
									'value' => ! empty( $_REQUEST['prev_order_count'] ) ? sanitize_text_field( $_REQUEST['prev_order_count'] ) : '',
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'prev_order_total_amt',
									'label' => __( 'Minimum total spending ( previous orders )', 'woocommerce' ),
									'description' => __( 'Minimum amount the user has spent till now for the rule to execute. total calculated from all previous orders', 'woocommerce' ),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'custom_attributes' => array(
										'step' => 1,
										'min' => 0,
										'disabled' => 'disabled',
									),
									'value' => ! empty( $_REQUEST['prev_order_total_amt'] ) ? sanitize_text_field( $_REQUEST['prev_order_total_amt'] ) : '',
								)
							);
							?>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
</div>
