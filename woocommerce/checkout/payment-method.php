<?php
/**
 * Output a single payment method – AnaCleaning custom override
 * Professional card-style design, screenreader-friendly, XStore-safe.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Determine icon
$icon_map = [
	'cod'  => '💵',
	'bacs' => '🏦',
	'bt'   => '🏦',
	'ipay' => '🏦',
];
$icon = '💳';
foreach ( $icon_map as $key => $emoji ) {
	if ( strpos( $gateway->id, $key ) !== false ) {
		$icon = $emoji;
		break;
	}
}

$is_chosen = $gateway->chosen;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> rima-pm<?php echo $is_chosen ? ' rima-pm--active' : ''; ?>">

	<?php /* The native radio – visually hidden but fully functional */ ?>
	<input
		id="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
		type="radio"
		class="input-radio rima-pm__radio"
		name="payment_method"
		value="<?php echo esc_attr( $gateway->id ); ?>"
		<?php checked( $is_chosen, true ); ?>
		data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>"
	/>

	<?php /* Clickable card label – contains all visible UI */ ?>
	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="rima-pm__label">

		<span class="rima-pm__radio-dot" aria-hidden="true"></span>

		<span class="rima-pm__icon" aria-hidden="true"><?php echo $icon; ?></span>

		<span class="rima-pm__info">
			<span class="rima-pm__title"><?php echo esc_html( $gateway->get_title() ); ?></span>
			<?php if ( $gateway->get_description() && ! $gateway->has_fields() ) : ?>
				<span class="rima-pm__desc"><?php echo wp_kses_post( wp_strip_all_tags( $gateway->get_description() ) ); ?></span>
			<?php endif; ?>
		</span>

		<?php if ( $gateway->get_icon() ) : ?>
			<span class="rima-pm__gateway-icon"><?php echo $gateway->get_icon(); // phpcs:ignore ?></span>
		<?php endif; ?>

	</label>

	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>"<?php if ( ! $is_chosen ) echo ' style="display:none;"'; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>

</li>
