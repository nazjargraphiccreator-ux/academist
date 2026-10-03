<?php
echo '<!-- RIMA_DEBUG: review-comments.php loaded -->';
if ( post_password_required() ) {
	return;
}
?>
<div class="eltdf-comment-holder clearfix" id="comments">
		<?php if ( have_comments() ) { ?>
			<div class="eltdf-comment-holder-inner">
				<h4 class="eltdf-comments-title"><?php esc_html_e( 'See what learners said', 'academist' ); ?></h4>
				<div class="eltdf-comments">
					<ul class="eltdf-comment-list">
						<?php wp_list_comments( array_unique( array_merge( array( 'callback' => 'academist_elated_comment' ), apply_filters( 'academist_elated_filter_comments_callback', array() ) ) ) ); ?>
					</ul>
				</div>
			</div>
		<?php } ?>
		<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
			<p><?php esc_html_e( 'Sorry, the comment form is closed at this time.', 'academist' ); ?></p>
		<?php } ?>
	</div>
	<?php
		$eltdf_commenter = wp_get_current_commenter();
		$eltdf_req       = get_option( 'require_name_email' );
		$eltdf_aria_req  = ( $eltdf_req ? " aria-required='true'" : '' );
		$eltdf_consent   = empty( $eltdf_commenter['comment_author_email'] ) ? '' : ' checked="checked"';

		$eltdf_args = array(
			'id_form'              => 'commentform',
			'id_submit'            => 'submit_comment',
			'title_reply'          => esc_html__( 'Post a Comment', 'academist' ),
			'title_reply_before'   => '<h5 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h5>',
			'title_reply_to'       => esc_html__( 'Post a Reply to %s', 'academist' ),
			'cancel_reply_link'    => esc_html__( 'cancel reply', 'academist' ),
			'label_submit'         => esc_html__( 'Submit', 'academist' ),
			'comment_field'        => apply_filters( 'academist_elated_filter_comment_form_textarea_field', '<textarea id="comment" placeholder="' . esc_attr__( 'Comment', 'academist' ) . '" name="comment" cols="45" rows="6" aria-required="true"></textarea>' ),
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'fields'               => apply_filters(
				'academist_elated_filter_comment_form_default_fields',
				array(
					'author'  => '<input id="author" name="author" placeholder="' . esc_attr__( 'Your Name', 'academist' ) . '" type="text" value="' . esc_attr( $eltdf_commenter['comment_author'] ) . '"' . $eltdf_aria_req . ' />',
					'email'   => '<input id="email" name="email" placeholder="' . esc_attr__( 'Your Email', 'academist' ) . '" type="text" value="' . esc_attr( $eltdf_commenter['comment_author_email'] ) . '"' . $eltdf_aria_req . ' />',
					'url'     => '<input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'academist' ) . '" type="text" value="' . esc_attr( $eltdf_commenter['comment_author_url'] ) . '" size="30" maxlength="200" />',
					'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $eltdf_consent . ' />' .
						'<label for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.', 'academist' ) . '</label></p>',
				)
			),
		);

		if ( get_comment_pages_count() > 1 ) {
			?>
		<div class="eltdf-comment-pager">
			<p><?php paginate_comments_links(); ?></p>
		</div>
			<?php } ?>
	<?php
	$eltdf_show_comment_form = apply_filters( 'academist_elated_filter_show_comment_form_filter', true );
	$can_review = true;
		if ( is_singular( 'course' ) && function_exists( 'academist_lms_user_has_course' ) ) {
			if ( ! is_user_logged_in() || ! academist_lms_user_has_course( get_the_ID() ) ) {
				$can_review = false;
			}
		}
		?>
		<div class="eltdf-comment-form">
			<div class="eltdf-comment-form-inner">
				<?php 
				if ( $can_review ) {
					comment_form( $eltdf_args ); 
				} else {
					echo '<p class="rima-course-review-notice" style="padding: 20px; background: var(--rmc-bg); border-radius: 12px; text-align: center; font-weight: 600; color: var(--rmc-muted);">';
					esc_html_e( 'You must be enrolled in this course to leave a review.', 'academist' );
					echo '</p>';
				}
				?>
			</div>
		</div>
