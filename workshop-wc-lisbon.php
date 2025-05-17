<?php
/*
 * Plugin Name:       Workshop WordCamp Lisbon 2025
 * Plugin URI:        #
 * Description:       Example of a Dashboard widget
 * Version:           0.0.1
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Uros Tasic
 * Author URI:        #
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       workshop-wc-lisbon
 * Domain Path:       /languages
 */

 
/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function gt_add_dashboard_widgets() {
	wp_add_dashboard_widget(
		'gt_dashboard_widget',                          // Widget slug.
		esc_html__( 'Welcome to Playground!', 'workshop-wc-lisbon' ), // Title.
		'gt_dashboard_widget_render'                    // Display function.
	); 
    // Globalize the metaboxes array, this holds all the widgets for wp-admin.
	global $wp_meta_boxes;
	
	// Get the regular dashboard widgets array 
	// (which already has our new widget but appended at the end).
	$default_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	
	// Backup and delete our new dashboard widget from the end of the array.
	$gt_dashboard_backup = array( 'gt_dashboard_widget' => $default_dashboard['gt_dashboard_widget'] );
	unset( $default_dashboard['gt_dashboard_widget'] );
 
	// Merge the two arrays together so our widget is at the beginning.
	$sorted_dashboard = array_merge( $gt_dashboard_backup, $default_dashboard );
 
	// Save the sorted array back into the original metaboxes. 
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}
add_action( 'wp_dashboard_setup', 'gt_add_dashboard_widgets' );

/**
 * Create the function to output the content of our Dashboard Widget.
 */
function gt_dashboard_widget_render() {
    $user_id = get_current_user_id();
    $meta_key = 'gt_dashboard_note';
    $message = '';

    // Handle form submission
    if ( isset($_POST['gt_note_nonce']) && wp_verify_nonce($_POST['gt_note_nonce'], 'gt_save_note') ) {
        if ( isset($_POST['gt_note']) ) {
            $note = sanitize_textarea_field($_POST['gt_note']);
            update_user_meta($user_id, $meta_key, $note);
            $message = '<div style="color:green;">' . esc_html__('Note saved!', 'workshop-wc-lisbon') . '</div>';
        }
    }

    $note = get_user_meta($user_id, $meta_key, true);
    ?>
    <?php echo $message; ?>
    <form method="post">
        <label for="gt_note"><?php esc_html_e('Quick Note (only you can see this):', 'workshop-wc-lisbon'); ?></label><br>
        <textarea name="gt_note" id="gt_note" rows="3" cols="40"><?php echo esc_textarea($note); ?></textarea><br>
        <?php wp_nonce_field('gt_save_note', 'gt_note_nonce'); ?>
        <input type="submit" value="<?php esc_attr_e('Save Note', 'workshop-wc-lisbon'); ?>" class="button button-primary">
    </form>
    <?php
}
