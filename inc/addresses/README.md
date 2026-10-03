# ANA Addresses Manager Plugin

Advanced multiple addresses management for WooCommerce with PF/PJ support.

## Features

✅ **Database Storage (NEW v2.0)** - Dedicated table for fast, scalable address management  
✅ **Hybrid Compatibility** - Auto-migrates from user meta, maintains backward compatibility  
✅ **Multiple Addresses Storage** - Unlimited billing and shipping addresses per user  
✅ **PF/PJ Support** - Separate handling for natural persons (Persoană Fizică) and legal entities (Persoană Juridică)  
✅ **Beautiful UI** - Color-coded address cards with gradients and hover effects  
✅ **AJAX Operations** - Load, add, edit, delete addresses without page reload  
✅ **WooCommerce Integration** - Seamless checkout and order management  
✅ **Guest Checkout** Disabled - Force users to login before checkout  
✅ **Default Address Management** - Set preferred billing/shipping addresses  
✅ **Auto-Migration** - Automatically imports existing WooCommerce addresses  

## Installation

1. Plugin is already installed in: `d:/anaclean-redesign/web/login-acc-checkout/ana-addresses-manager/`
2. Activate the plugin from WordPress Admin → Plugins
3. Requires WooCommerce to be active

## Usage

### For Users (Frontend)

**Checkout:**

- Click "Selectează adresă" button in checkout  
- Browse saved addresses (PF/PJ tabs)
- Select existing address or add new one
- Address auto-populates checkout fields

**My Account:**

- Navigate to My Account → Addresses
- View all saved billing/shipping addresses
- Add/Edit/Delete addresses
- Set default addresses

### Technical Integration

**Hidden Inputs (Add to checkout form):**

```php
<input type="hidden" name="selected_billing_address_id" id="selected_billing_address_id" value="">
<input type="hidden" name="selected_shipping_address_id" id="selected_shipping_address_id" value="">
```

**Load Addresses Button:**

```html
<button data-ana-load-addresses data-type="billing" data-filter="pf">
    Load PF Addresses
</button>
```

**Address Container:**

```html
<div id="ana-addresses-container-billing-pf"></div>
```

## API Functions

```php
// Get all addresses
$addresses = ANA_Addresses_Plugin::get_addresses( $user_id, 'billing', 'pf' );

// Add new address
$address_id = ANA_Addresses_Plugin::add_address( $user_id, $data, 'billing' );

// Update address
ANA_Addresses_Plugin::update_address( $user_id, $address_id, $data, 'billing' );

// Delete address
ANA_Addresses_Plugin::delete_address( $user_id, $address_id, 'billing' );

// Set default
ANA_Addresses_Plugin::set_default_address( $user_id, $address_id, 'billing' );

// Get default
$default = ANA_Addresses_Plugin::get_default_address( $user_id, 'billing' );
```

## Data Structure

**NEW in v2.0**: Addresses are now stored in a dedicated database table for better performance and scalability!

### Database Table: `wp_ana_user_addresses`

**Primary Storage** - All new addresses saved here:

- Auto-incrementing ID for each address
- Indexed by user_id for fast lookups
- Supports unlimited addresses per user
- Tracks created/updated timestamps

**Key Fields:**

- `id` - Unique address ID (numeric)
- `user_id` - WordPress user ID
- `address_type` - 'billing' or 'shipping'
- `entity_type` - 'pf' (Persoană Fizică) or 'pj' (Persoană Juridică)
- Personal info: `first_name`, `last_name`, `company`, `vat_number`
- Address: `address_1`, `address_2`, `city`, `state`, `postcode`, `country`
- Contact: `phone`, `email`
- Meta: `is_default`, `created_at`, `updated_at`

### Hybrid Compatibility

**Automatic Migration** - Plugin automatically:

1. Checks database first for addresses
2. Falls back to user meta if database empty
3. Migrates user meta → database on first read
4. Preserves original user meta as backup

**Legacy Support:**

- Reads old `_ana_billing_addresses` and `_ana_shipping_addresses` user meta
- Imports WooCommerce default addresses if no custom addresses exist
- Seamless upgrade - no data loss!

## Example Usage

```php
// Example: Get user addresses
$billing_addresses = ana_get_user_addresses($user_id, 'billing');
$shipping_addresses = ana_get_user_addresses($user_id, 'shipping');

// Example: Add new address
$address_data = [
    'entity_type' => 'pf',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'address_1' => 'Sample Street 123',
    'city' => 'Bucharest',
    'state' => 'B',
    'postcode' => '010101',
    'country' => 'RO',
    'phone' => '0700000000',
    'email' => 'example@example.com',
];

ANA_Addresses_Plugin::add_address($user_id, $address_data, 'billing');
```

---

**Development by Nazjar Development**

## AJAX Actions

- `ana_load_addresses` - Load addresses for modal
- `ana_save_address` - Save new/update address
- `ana_delete_address` - Delete address
- `ana_set_default_address` - Set default address

## Files Structure

```
ana-addresses-manager/
├── ana-addresses-manager.php (Main plugin file)
├── includes/
│   ├── class-addresses.php (CRUD operations)
│   ├── ajax-handlers.php (AJAX endpoints)
│   └── woocommerce-integration.php (WC hooks)
├── assets/
│   ├── css/addresses.css (Styling)
│   └── js/addresses.js (Frontend logic)
└── README.md (This file)
```

## Next Steps

1. **Activate Plugin** from WordPress dashboard
2. **Update Checkout Template** - Add address loading triggers
3. **Update My Account** - Integrate addresses page
4. **Test** - Add/Edit/Delete addresses
5. **Verify** - Check orders use selected addresses

## Support

Contact: ANA Cleaning Solutions  
Version: 2.0.0 (Database Hybrid Storage)  
Author: ANA Development Team
