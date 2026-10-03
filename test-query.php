<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

echo "<h2>Courses</h2>";
$courses = get_posts(array('post_type' => 'course', 'posts_per_page' => 5, 'post_status' => 'publish'));
foreach($courses as $c) {
    echo $c->post_title . "<br>";
}

echo "<h2>Testimonials</h2>";
$testimonials = get_posts(array('post_type' => 'testimonials', 'posts_per_page' => 5, 'post_status' => 'publish'));
foreach($testimonials as $t) {
    echo $t->post_title . "<br>";
}

echo "<h2>Post Types</h2>";
$types = get_post_types(array('public' => true));
echo "<pre>"; print_r($types); echo "</pre>";
