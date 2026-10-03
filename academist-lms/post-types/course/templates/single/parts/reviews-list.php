<div class="eltdf-course-reviews-main-title">
	<h3><?php esc_html_e( 'Reviews', 'academist-lms' ); ?></h3>

</div>

<div class="eltdf-course-reviews-list-top">
	<?php
	if ( academist_lms_core_plugin_installed() ) {
		echo academist_core_list_review_details( 'per-mark' );
	}
	?>
</div>
<div class="eltdf-course-reviews-list">
	<?php comments_template( '/review-comments.php', true ); ?>
</div>
