<?php
require_once( '../../../../wp-load.php' ); // 4 levels up
$terms = get_terms( array('taxonomy' => 'course-category', 'hide_empty' => false) );
foreach ( $terms as $term ) {
    echo "Term: " . $term->name . " (ID: " . $term->term_id . ")<br>";
    echo "<pre>";
    print_r(get_term_meta($term->term_id));
    echo "</pre>";
}
?>
