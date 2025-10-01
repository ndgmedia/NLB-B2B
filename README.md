# Dynamic Fields for WooCommerce Product Add-on Plugin

This code extends WooCommerce Product Add-Ons Ultimate to provide dynamic min/max validation for width and height fields based on color selection.

## Features

- **Dynamic Validation**: Width and height fields automatically update their min/max limits when a color is selected
- **Multi-field Support**: Handles multiple field sets efficiently with array-based configuration
- **Admin Interface**: Adds Min/Max Width and Height columns to the product add-on options
- **Performance Optimized**: Single configuration system handles all field sets

## How It Works

1. **Color Selection**: User selects a color/material from a dropdown field
2. **Dynamic Updates**: Width and height number fields automatically update their validation limits
3. **Admin Control**: Set different min/max values per color option in the WordPress admin

## Configuration

Update the field IDs in `minmax_get_field_config()`:

```php
function minmax_get_field_config() {
    return [
        [
            'color_field' => 727,   // Color select field ID
            'width_field' => 818,   // Width number field ID  
            'height_field' => 821   // Height number field ID
        ],
        [
            'color_field' => 755,   // Second set
            'width_field' => 756,
            'height_field' => 757
        ]
        // Add more field sets as needed
    ];
}
```

## Installation

1. Add the code to your theme's `functions.php` file
2. Or include as a separate file: `require_once 'dynamic-fields-addon.php';`
3. Configure your field IDs in the `minmax_get_field_config()` function
4. Set min/max values for each color option in WooCommerce Product Add-ons admin

## Requirements

- WooCommerce Product Add-Ons Ultimate plugin
- WordPress with WooCommerce
- jQuery (included with WordPress)

## Example Use Case

**Blinds/Window Coverings Store:**
- Material options: "Aluminum", "Wood", "Bamboo"  
- Width constraints: Aluminum (30-200cm), Wood (40-150cm), Bamboo (25-180cm)
- Height constraints: Different limits per material

When customer selects "Wood", width field automatically limits to 40-150cm and height field gets wood-specific limits.