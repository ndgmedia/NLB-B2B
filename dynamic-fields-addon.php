<?php
/**
 * Dynamic Fields for WooCommerce Product Add-on Plugin
 * 
 * Adds dynamic min/max validation for width and height fields based on color selection
 * 
 * @package NLB-B2B
 * @version 1.0.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Configuration for all field sets
 */
function minmax_get_field_config() {
	return [
		[
			'color_field' => 727,
			'width_field' => 818,
			'height_field' => 821
		],
		[
			'color_field' => 755,
			'width_field' => 756,
			'height_field' => 757
		]
		// Add more field sets as needed
	];
}

/**
 * Get all color field IDs
 */
function minmax_get_color_field_ids() {
	return array_column(minmax_get_field_config(), 'color_field');
}

/**
 * Add new columns for min / max values
 */
function minmax_add_extra_params( $option_count, $group_id, $item_key, $item, $key ) {
	
	if( isset( $item['field_id'] ) && in_array( $item['field_id'], minmax_get_color_field_ids() ) ) {
		$name = '_product_extra_groups_' . esc_attr( $group_id ) . '_' . esc_attr( $item_key ) . '[field_options][' . esc_attr( $option_count ) . ']';
		$min_width = isset( $item['field_options'][esc_attr( $key )]['min_width'] ) ? $item['field_options'][esc_attr( $key )]['min_width'] : '';
		$max_width = isset( $item['field_options'][esc_attr( $key )]['max_width'] ) ? $item['field_options'][esc_attr( $key )]['max_width'] : '';
		$min_height = isset( $item['field_options'][esc_attr( $key )]['min_height'] ) ? $item['field_options'][esc_attr( $key )]['min_height'] : '';
		$max_height = isset( $item['field_options'][esc_attr( $key )]['max_height'] ) ? $item['field_options'][esc_attr( $key )]['max_height'] : ''; ?>
		<td class="pewc-option-min_width pewc-option-extra">
			<input type="number" class="pewc-field-option-extra pewc-field-option-min_width" name="<?php echo $name; ?>[min_width]" value="<?php echo esc_attr( $min_width ); ?>">
		</td>
		<td class="pewc-option-max_width pewc-option-extra">
			<input type="number" class="pewc-field-option-extra pewc-field-option-max_width" name="<?php echo $name; ?>[max_width]" value="<?php echo esc_attr( $max_width ); ?>">
		</td>
		<td class="pewc-option-min_height pewc-option-extra">
			<input type="number" class="pewc-field-option-extra pewc-field-option-min_height" name="<?php echo $name; ?>[min_height]" value="<?php echo esc_attr( $min_height ); ?>">
		</td>
		<td class="pewc-option-max_height pewc-option-extra">
			<input type="number" class="pewc-field-option-extra pewc-field-option-max_height" name="<?php echo $name; ?>[max_height]" value="<?php echo esc_attr( $max_height ); ?>">
		</td>
	<?php }
	
}
add_action( 'pewc_after_option_params', 'minmax_add_extra_params', 10, 5 );

/**
 * Add titles to the new columns
 */ 
function minmax_add_extra_params_titles( $group_id, $item_key, $item ) {
	
	if( isset( $item['field_id'] ) && in_array( $item['field_id'], minmax_get_color_field_ids() ) ) {
		printf(
			'<th class="pewc-option-min_width pewc-option-extra-title"><div class="pewc-label">%s</div></th>',
			__( 'Min Width', 'pewc' )
		);
		printf(
			'<th class="pewc-option-max_width pewc-option-extra-title"><div class="pewc-label">%s</div></th>',
			__( 'Max Width', 'pewc' )
		);
		printf(
			'<th class="pewc-option-min_height pewc-option-extra-title"><div class="pewc-label">%s</div></th>',
			__( 'Min Height', 'pewc' )
		);
		printf(
			'<th class="pewc-option-max_height pewc-option-extra-title"><div class="pewc-label">%s</div></th>',
			__( 'Max Height', 'pewc' )
		);
	}

}
add_action( 'pewc_after_option_params_titles', 'minmax_add_extra_params_titles', 10, 3 );

/**
 * Update the attributes on the front end
 */ 
function minmax_option_attribute_string( $option_attribute_string, $item, $option_value, $option_index ) {
	
	if( isset( $item['field_id'] ) && in_array( $item['field_id'], minmax_get_color_field_ids() ) ) {

		$min_width = ! empty( $item['field_options'][$option_index]['min_width'] ) ? $item['field_options'][$option_index]['min_width'] : '';
		$max_width = ! empty( $item['field_options'][$option_index]['max_width'] ) ? $item['field_options'][$option_index]['max_width'] : '';
		$min_height = ! empty( $item['field_options'][$option_index]['min_height'] ) ? $item['field_options'][$option_index]['min_height'] : '';
		$max_height = ! empty( $item['field_options'][$option_index]['max_height'] ) ? $item['field_options'][$option_index]['max_height'] : '';

		$option_attribute_string .= " data-min-width='" . esc_attr( trim( $min_width ) ) . "'";
		$option_attribute_string .= " data-max-width='" . esc_attr( trim( $max_width ) ) . "'";
		$option_attribute_string .= " data-min-height='" . esc_attr( trim( $min_height ) ) . "'";
		$option_attribute_string .= " data-max-height='" . esc_attr( trim( $max_height ) ) . "'";
		
	}
	
	return $option_attribute_string;
}
add_filter( 'pewc_option_attribute_string', 'minmax_option_attribute_string', 10, 4 );

/**
 * Add custom JS on the product page
 */ 
function minmax_js() {
	if( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	} ?>
	<script>
		( function( $ ) {
			var field_config = <?php echo json_encode(minmax_get_field_config()); ?>;
			
			var minmax_update = {
				init: function() {
					// Bind events for all color fields
					field_config.forEach(function(config) {
						$( 'body' ).on( 'change', '.pewc-field-' + config.color_field + ' select', minmax_update.reset_minmax );
						$( '.pewc-field-' + config.color_field + ' select' ).trigger( 'change' );
					});
				},
				reset_minmax: function() {
					var $selected = $( this ).find( ':selected' );
					var color_field_id = parseInt( $( this ).closest( '[class*="pewc-field-"]' ).attr( 'class' ).match(/pewc-field-(\d+)/)[1] );
					
					// Find matching config
					var config = field_config.find(function(c) { return c.color_field === color_field_id; });
					if( !config ) return;
					
					// Update width and height fields
					['width', 'height'].forEach(function(type) {
						var min_val = $selected.attr( 'data-min-' + type );
						var max_val = $selected.attr( 'data-max-' + type );
						var field_id = config[type + '_field'];
						
						$( '.pewc-field-' + field_id ).attr( 'data-field-minval', min_val );
						$( '.pewc-field-' + field_id + ' input' ).attr( 'min', min_val );
						$( '.pewc-field-' + field_id ).attr( 'data-field-maxval', max_val );
						$( '.pewc-field-' + field_id + ' input' ).attr( 'max', max_val );
					});
				}
			}
			minmax_update.init();
		})( jQuery );
	</script>
	<?php
}
add_action( 'wp_footer', 'minmax_js' );