<?php
if (isset($_REQUEST['edit']) && ! empty($_REQUEST['edit'])) {
	echo '<input type="hidden" name="update" value="' . esc_html(sanitize_text_field($_REQUEST['edit'])) . '" >';
}
?>   
<script>
	jQuery( document ).ready( function () {
		jQuery( '#check_on' ).on( 'change', function () {
			let thisval = jQuery( '#check_on' ).val();
			jQuery( "[for=min]" ).html( 'Minimum ' + thisval + '<span style="color:red;padding-left:5px">*<span>' );
			jQuery( "[for=max]" ).html( 'Maximum ' + thisval );
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
		jQuery( '#rule_on' ).on( 'change', function () {
			let selected = jQuery( '#rule_on' ).val();

			jQuery( '#product_id' ).removeAttr( 'required' );
			if ( selected == 'products' )
			{

				jQuery( '#category_id' ).parent().hide();
				jQuery( '#product_id' ).parent().show();
				jQuery( '#product_id' ).attr( 'required', 'required' );
			} else if ( selected == 'categories' )
			{
				jQuery( "#product_id" ).empty();
				jQuery( '#product_id' ).parent().hide();
				jQuery( '#category_id' ).parent().show();
			} else if ( selected == 'cart' )
			{
				jQuery( '#product_id' ).parent().hide();
				jQuery( '#category_id' ).parent().hide();
			}
		} );
		jQuery( '#rule_on' ).trigger( 'change' );
		jQuery( '#rule_tab label' ).append( '<span style="color:red;padding-left:5px">*<span>' );
		jQuery( '#check_on' ).trigger( 'change' );
		jQuery( "[for=max]" ).html( jQuery( "[for=max]" ).text() );
		jQuery( '#discount_type' ).trigger( 'change' );

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
								<span>Allowed Roles & Date</span>
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
									'label' => __('Offer name', 'woocommerce'),
									'placeholder' => 'Enter a descriptive offer name',
									'description' => __('Name/Text of the offer to be displayed in the Offer Table. We suggest a detailed description of the discount.', 'woocommerce'),
									'type' => 'text',
									'desc_tip' => true,
									'value' => ! empty($_REQUEST['offer_name']) ? sanitize_text_field($_REQUEST['offer_name']) : '',
									'custom_attributes' => array(
										'required' => 'required',
									),
								)
							);
							?>
							<p class="form-field rule_on_field">
								<label for="rule_on"><?php echo esc_html('Rule applicable on', 'woocommerce'); ?></label>
								<i class="desc-tip woocommerce-help-tip" data-tip='<u>Selected products:</u>The rule would be applied to the selected products individually.</br><u>Selected category:</u>This is different from the "Category Rule". In this case, the rule would be individually applied to all the products in the category.</br><u>Products in Cart:</u>Rule will be applied individually on each product in cart'></i>
								<select id="rule_on" name="rule_on" class="select short">
									<option value="products" <?php echo ( !empty($_REQUEST['rule_on']) && ( 'products' == $_REQUEST['rule_on'] ) ) ? 'selected' : ''; ?>>Selected products</option>
									<option value="categories" <?php echo ( !empty($_REQUEST['rule_on']) && ( 'categories' == $_REQUEST['rule_on'] ) ) ? 'selected' : ''; ?>>Selected category</option>
									<option value="cart" <?php echo ( !empty($_REQUEST['rule_on']) && ( 'cart' == $_REQUEST['rule_on'] ) ) ? 'selected' : ''; ?>>Products in Cart</option>
								</select>
							</p>
							<?php
							///// start product search
							if (elex_dp_is_wc_version_gt_eql('2.7')) {
								?>
								<p class="form-field"><label><?php esc_html_e('Products', 'woocommerce'); ?></label>
									<select class="wc-product-search" multiple="multiple" style="width: 50%;height:30px" id="product_id" name="product_id[]" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products_and_variations">
										<?php
										$product_ids = ! empty($_REQUEST['product_id']) ? array_map('sanitize_text_field', wp_unslash($_REQUEST['product_id'])) : array();  // selected product ids
										foreach ($product_ids as $product_id) {
											$product = wc_get_product($product_id);
											if (is_object($product)) {
												echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
											}
										}
										?>
									</select>
									<?php
									$allowed_html = wp_kses_allowed_html('post');
									echo wp_kses(wc_help_tip(__('Rule to be applied on which products', 'woocommerce')), $allowed_html);
									?>
								</p>
								<?php
							} else {
								?>
								<p class="form-field"><label><?php esc_html_e('Products', 'woocommerce'); ?></label>
									<input id="product_id" name="product_id" type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;"  data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="
									<?php
									$product_ids = ( ! empty($_REQUEST['product_id']) && is_array($_REQUEST['product_id']) ) ? array_map('sanitize_text_field', wp_unslash($_REQUEST['product_id'])) : array();  // selected product ids
									$json_ids    = array();
									foreach ($product_ids as $product_id) {
										$product = wc_get_product($product_id);
										if (is_object($product)) {
											$json_ids[ $product_id ] = wp_kses_post($product->get_formatted_name());
										}
									}

									echo esc_attr(json_encode($json_ids));
									?>
									" value="<?php echo esc_html(implode(',', array_keys($json_ids))); ?>" />
									<?php
									$allowed_html = wp_kses_allowed_html('post');
									echo wp_kses(wc_help_tip(__('Rule to be applied on which products', 'woocommerce')), $allowed_html);
									?>
								</p>
								<?php
							}
								// start Categories  search
							?>
							<p class="form-field"><label for="category_id"><?php esc_html_e('Product categories', 'woocommerce'); ?></label>
								<select id="category_id" name="category_id" style="width: 50%;height:30px"  class="wc-enhanced-select"  data-placeholder="<?php esc_attr_e('Any category', 'woocommerce'); ?>">
									<?php
									$category_ids    = ! empty($_REQUEST['category_id']) ? sanitize_text_field($_REQUEST['category_id']) : '';  //selected product categorie
									$prod_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
									if ($prod_categories) {
										foreach ($prod_categories as $prod_cat) {
											echo '<option value="' . esc_attr($prod_cat->term_id) . '"' . selected($prod_cat->term_id == $category_ids, true, false) . '>' . esc_html($prod_cat->name) . '</option>';
										}
									}
									?>
								</select> <?php echo wp_kses(wc_help_tip(__('Product categories that the rule will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce')), $allowed_html); ?></p>
							<?php
							//// end category search
							?>
							<p class="form-field check_on_field">
								<label for="check_on"><?php echo esc_html('Check for', 'woocommerce'); ?></label>
								<i class="desc-tip woocommerce-help-tip" data-tip="<?php echo esc_html('The rules can be applied based on Quantity/Price/Weight', 'woocommerce'); ?>"></i>
								<select id="check_on" name="check_on" class="select short">
									<option value="Quantity" <?php echo ( !empty($_REQUEST['check_on']) && ( 'Quantity' == $_REQUEST['check_on'] ) ) ? 'selected' : ''; ?>>Quantity</option>
									<option value="Weight" <?php echo ( !empty($_REQUEST['check_on']) && ( 'Weight' == $_REQUEST['check_on'] ) ) ? 'selected' : ''; ?>>Weight</option>
									<option value="Price" <?php echo ( !empty($_REQUEST['check_on']) && ( 'Price' == $_REQUEST['check_on'] ) ) ? 'selected' : ''; ?>>Price</option>
								</select>
							</p>
							<?php
							woocommerce_wp_text_input(
								array(
									'id' => 'min',
									'label' => __('Minimum', 'woocommerce'),
									'description' => __('Minimum value to check', 'woocommerce'),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty($_REQUEST['min']) ? sanitize_text_field($_REQUEST['min']) : '1',
									'custom_attributes' => array(
										'required' => 'required',
										'step' => 'any',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'max',
									'label' => __('Maximum', 'woocommerce'),
									'description' => __('Maximum value to check, set it empty for no limit', 'woocommerce'),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty($_REQUEST['max']) ? sanitize_text_field($_REQUEST['max']) : '',
									'custom_attributes' => array(
										'step' => 'any',
									),
								)
							);
							?>
							<p class="form-field discount_type_field">
								<label for="discount_type"><?php echo esc_html('Discount type', 'woocommerce'); ?></label>
								<i class="desc-tip woocommerce-help-tip" data-tip="<?php echo esc_html('Three types of discounts can be applied – “Percentage Discount/Flat Discount/Fixed Price”', 'woocommerce'); ?>"></i>
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
									'label' => __('Discount', 'woocommerce'),
									'description' => __('If you select “Percentage Discount”, the given percentage ( value ) would be discounted on each unit of the product in the cart. If you select “Flat Discount”, the given amount ( value ) would be discounted at subtotal level in the cart. If you select “Fixed Price”, the original price of the product is replaced by the given fixed price ( value ).', 'woocommerce'),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty($_REQUEST['value']) ? sanitize_text_field($_REQUEST['value']) : '',
									'custom_attributes' => array(
										'required' => 'required',
										'step' => 'any',
									),
								)
							);
							?>
						</div>
						<div class="options_group" id="adjustment_tab" style="display: none;">
							<?php //premium ?>
							<?php
							woocommerce_wp_text_input(
								array(
									'id' => 'max_discount',
									'label' => __('Maximum discount amount', 'woocommerce'),
									'description' => __('After Calculation Discount Value Must Not Exceeed This Amount For This Rule', 'woocommerce'),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty($_REQUEST['max_discount']) ? sanitize_text_field($_REQUEST['max_discount']) : '',
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'adjustment',
									'label' => __('Adjustment amount', 'woocommerce'),
									'description' => __('Adjust final discount amount by this amount', 'woocommerce'),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'value' => ! empty($_REQUEST['adjustment']) ? sanitize_text_field($_REQUEST['adjustment']) : '',
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							woocommerce_wp_checkbox(
								array(
									'id' => 'repeat_rule',
									'label' => __('Allow repeat execution', 'woocommerce'),
									'description' => sprintf('<span class="description">' . __('Rule will be executed multiple times if quantity of product is in multiple of max quantity &( min = max )', 'woocommerce') . '</span>'),
									'value' => ! empty($_REQUEST['repeat_rule']) ? sanitize_text_field($_REQUEST['repeat_rule']) : 'on',
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							?>
						</div>
						<div class="options_group" id="allowed_roles_and_date_tab" style="display: none;" disabled>
							<?php
							global $wp_roles;
							$roles    = $wp_roles->get_names();
							$role_all = esc_html__('All', 'eh-dynamic-pricing-discounts');
							$roles    = array_merge(array( 'all' => $role_all ), $roles);
							?>
							<p class="form-field allow_roles[]_field ">
							<label for="allow_roles[]">Allowed Roles</label>
							<span class="woocommerce-help-tip" data-tip="<?php echo esc_html__('Select the roles for which you want to apply this discount rule.', 'eh-dynamic-pricing-discounts'); ?>"></span>
							<select id="allow_roles[]" name="allow_roles[]" class="roles_select select2-hidden-accessible" style="width:50%;" multiple="" tabindex="-1" aria-hidden="true">
							<?php
							$selected = ! empty($_REQUEST['allow_roles']) ? array_map('sanitize_text_field', wp_unslash($_REQUEST['allow_roles'])) : array();
							if (! array( $selected )) {
								$selected = array( $selected );
							}
							foreach ($roles as $key => $val) {
								$is_selected = in_array($key, $selected) ? ' selected ' : ' ';
								echo "<option value='" . esc_html($key) . " ' " . esc_html($is_selected) . ' >' . esc_html($val) . '</option>';
							}
							?>
							</select>    
							</p>    
							<?php
							woocommerce_wp_text_input(
								array(
									'id' => 'from_date',
									'value' => esc_attr(! empty($_REQUEST['from_date']) ? sanitize_text_field($_REQUEST['from_date']) : ''),
									'label' => __('Valid from date', 'woocommerce'),
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
										'pattern' => apply_filters('woocommerce_date_input_html_pattern', '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4} (0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])'),
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'to_date',
									'value' => esc_attr(! empty($_REQUEST['to_date']) ? sanitize_text_field($_REQUEST['to_date']) : ''),
									'label' => __('Expiry date', 'woocommerce'),
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
										'pattern' => apply_filters('woocommerce_date_input_html_pattern', '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4} (0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])'),
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
									'label' => __('Allowed Email Ids', 'woocommerce'),
									'placeholder' => 'Enter Email ids seperated by commas',
									'description' => __('Enter Email ids seperated by commas, for which you want to allow this rule. and leave blank to allow for all', 'woocommerce'),
									'type' => 'text',
									'desc_tip' => true,
									'value' => ! empty($_REQUEST['email_ids']) ? sanitize_text_field($_REQUEST['email_ids']) : '',
									'custom_attributes' => array(
										'disabled' => 'disabled',
									),
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'prev_order_count',
									'label' => __('Minimum number of orders ( previous orders )', 'woocommerce'),
									'description' => __('Minimum count of preivious orders required for this rule to be executed', 'woocommerce'),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'custom_attributes' => array(
										'step' => 1,
										'min' => 0,
										'disabled' => 'disabled',
									),
									'value' => ! empty($_REQUEST['prev_order_count']) ? sanitize_text_field($_REQUEST['prev_order_count']) : '',
								)
							);
							woocommerce_wp_text_input(
								array(
									'id' => 'prev_order_total_amt',
									'label' => __('Minimum total spending ( previous orders )', 'woocommerce'),
									'description' => __('Minimum amount the user has spent till now for the rule to execute. total calculated from all previous orders', 'woocommerce'),
									'type' => 'number',
									'desc_tip' => true,
									'class' => 'short',
									'custom_attributes' => array(
										'step' => 1,
										'min' => 0,
										'disabled' => 'disabled',
									),
									'value' => ! empty($_REQUEST['prev_order_total_amt']) ? sanitize_text_field($_REQUEST['prev_order_total_amt']) : '',
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
