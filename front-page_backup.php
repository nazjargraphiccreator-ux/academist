<?php
/**
 * The front page template file
 *
 * This forces WordPress to load our custom modern homepage 
 * without needing to manually set the page template in the admin.
 */

// Include our custom modern homepage template
require_once get_stylesheet_directory() . '/page-home-modern.php';
