<?php
/**
 * Course item template overridden to add a WooCommerce Add to Cart button
 * and a modern premium design.
 */
?>
<div class="rima-modern-course-card">
	<?php echo academist_lms_get_cpt_shortcode_module_template_part( 'course', 'course-list', 'parts/image', '', $params ); ?>

	<?php if ( $enable_price == 'yes' ) { ?>
		<div class="rima-course-price-badge">
			<?php echo academist_lms_get_cpt_shortcode_module_template_part( 'course', 'course-list', 'parts/price', '', $params ); ?>
		</div>
	<?php } ?>

	<div class="eltdf-cli-text-holder">
		<?php echo academist_lms_get_cpt_shortcode_module_template_part( 'course', 'course-list', 'parts/title', '', $params ); ?>
		
		<?php if ( $enable_instructor == 'yes' ) {
			echo academist_lms_get_cpt_shortcode_module_template_part( 'course', 'course-list', 'parts/instructor', '', $params );
		} ?>
		
		<?php echo academist_lms_get_cpt_shortcode_module_template_part( 'course', 'course-list', 'parts/excerpt', '', $params ); ?>
		
		<div class="rima-course-add-to-cart-wrapper">
			<a href="<?php echo esc_url(get_permalink()); ?>" class="eltdf-btn eltdf-btn-solid rima-btn-blue" style="width: 100%; text-align: center; display: block;">
				<span class="rima-en">View Course</span><span class="rima-ro">Vezi Cursul</span>
			</a>
		</div>
	</div>
</div>
