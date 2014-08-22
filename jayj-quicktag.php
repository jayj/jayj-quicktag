<?php
/**
 * Plugin Name: Jayj Quicktag
 * Plugin URI:  http://jayj.dk/plugins/jayj-quicktag/
 * Description: Allows you to easily add custom quicktags to the editor.
 * Author:      Jesper Johansen
 * Author URI:  http://jayj.dk
 * Version:     1.3.1
 * License:     GPLv2 or later
 * Text Domain: jayj-quicktag
 * Domain Path: /languages
 */

/* Register uninstall function. */
register_uninstall_hook( __FILE__, 'jayj_quicktag_uninstall' );

/* Load the textdomain for translation. */
load_plugin_textdomain( 'jayj-quicktag', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Removes the Quicktags from the database
 *
 * @since 1.0.0
 */
function jayj_quicktag_uninstall() {
	delete_option( 'jayj_qt_settings' );
}

/**
 * Adds the options page
 *
 * @since 1.0.0
 */
function jayj_quicktag_add_options_page() {
	add_options_page( __( 'Jayj Quicktag', 'jayj-quicktag' ), __( 'Jayj Quicktag', 'jayj-quicktag' ), 'manage_options', __FILE__, 'jayj_quicktag_options_page' );
}

add_action( 'admin_menu', 'jayj_quicktag_add_options_page' );

/**
 * Register the options page
 *
 * @since 1.0.0
 */
function jayj_quicktag_register_setting() {
	register_setting( 'jayj_quicktag_options', 'jayj_qt_settings', 'jayj_quicktag_options_validate' );
}

add_action( 'admin_init', 'jayj_quicktag_register_setting' );

/**
 * The Quicktags options page
 *
 * @since 1.0.0
 */
function jayj_quicktag_options_page() { ?>

<div class="wrap">

	<h2><?php _e( 'Jayj Quicktag Options', 'jayj-quicktag' ); ?></h2>

	<br />

	<form action="options.php" method="post">

		<?php
			/*
			 * Insert imported Quicktags
			 */
			if ( isset( $_POST['jayj-quicktag-import-save'] ) ) :

				$options = get_option( 'jayj_qt_settings' );
				$data = maybe_unserialize( stripslashes_deep( $_POST['jayj-quicktag-import'] ) );

				// Merge the old and the new Quicktags.
				if ( ! empty( $data ) ) {
					$imported['buttons'] = array_merge( (array) $options['buttons'], $data['buttons'] );
				}

				// Succes or error message.
				if ( ! empty( $data ) && update_option( 'jayj_qt_settings', $imported ) ) {
					echo '<div class="updated"><p><strong>' . __( 'Quicktags succesfully imported', 'jayj-quicktag' ) . '</strong></p></div>';
				} else {
					echo '<div class="error"><p><strong>' . __( 'Error: Quicktags could not be imported', 'jayj-quicktag' ) . '</strong></p></div>';
				}

			endif;
		?>

		<?php
			settings_fields( 'jayj_quicktag_options' );

			// Get the saved options.
			$options   = get_option( 'jayj_qt_settings' );
			$quicktags = $options['buttons'];
		?>

		<table class="widefat jayj-quicktag-table">

			<thead>
				<tr>
					<th class="jayj-quicktag-order" title="<?php esc_attr_e( 'Change order', 'jayj-quicktag' ); ?>"><!-- order --></th>
					<th><?php _e( 'Button Label *', 'jayj-quicktag' ); ?></th>
					<th><?php _e( 'Title Attribute', 'jayj-quicktag' ); ?></th>
					<th><?php _e( 'Start Tag(s) *', 'jayj-quicktag' ); ?></th>
					<th><?php _e( 'End Tag(s)', 'jayj-quicktag' ); ?></th>
					<th title="<?php esc_attr_e( 'Remove quicktag', 'jayj-quicktag' ); ?>"><!-- remove --></th>
				</tr>
			</thead>

			<tbody>
				<?php
					if ( isset( $quicktags ) ) :

					// Loop through all the buttons.
					for ( $number = 0; $number < count( $quicktags ); $number++ ) :

						if ( ! isset( $options['buttons'][$number] ) ) {
							break;
						}
				?>

					<tr valign="top" class="jayj-quicktag-row">
						<td class="jayj-quicktag-order" title="<?php esc_attr_e( 'Change order', 'jayj-quicktag' ); ?>">
							<?php echo $number + 1; ?>
						</td>

						<td>
							<input type="text" name="jayj_qt_settings[buttons][<?php echo intval( $number ); ?>][text]" value="<?php echo esc_attr( $quicktags[$number]['text'] ); ?>" />
						</td>

						<td>
							<input type="text" name="jayj_qt_settings[buttons][<?php echo intval( $number ); ?>][title]" value="<?php echo esc_attr( $quicktags[$number]['title'] ); ?>" />
						</td>

						<td>
							<textarea class="code" name="jayj_qt_settings[buttons][<?php echo intval( $number ); ?>][start]" rows="2" cols="25"><?php echo esc_textarea( $quicktags[$number]['start'] ); ?></textarea>
						</td>

						<td>
							<textarea class="code" name="jayj_qt_settings[buttons][<?php echo intval( $number ); ?>][end]" rows="2" cols="25"><?php echo esc_textarea( $quicktags[$number]['end'] ); ?></textarea>
						</td>

						<td class="jayj-quicktag-remove">
							<button class="button jayj-quicktag-remove-button" title="<?php esc_attr_e( 'Remove quicktag', 'jayj-quicktag' ); ?>">&times;</button>
						</td>
					</tr>

				<?php endfor; endif; ?>

					<!-- Empty row -->
					<?php $i = 9999; ?>

					<tr valign="top" class="jayj-quicktag-clone <?php if ( empty ( $options['buttons'] ) ) echo 'jayj-quicktag-clone-show'; ?>">
						<td class="jayj-quicktag-order" title="<?php esc_attr_e( 'Change order', 'jayj-quicktag' ); ?>"><?php echo $i; ?></td>

						<?php
							// Set the name attribute.
							$clone_name = 'jayj_qt_settings[buttons][' . $i . ']';
						?>

						<td>
							<input type="text" name="<?php echo esc_attr( $clone_name ); ?>[text]" title="<?php esc_attr_e( 'Label of the Quicktag', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( 'Example', 'jayj-quicktag' ); ?>" value="" />
						</td>

						<td>
							<input type="text" name="<?php echo esc_attr( $clone_name ); ?>[title]" title="<?php esc_attr_e( 'Title attribute of the Quicktag', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( 'Example title', 'jayj-quicktag' ); ?>" value="" />
						</td>

						<td>
							<textarea class="code" name="<?php echo esc_attr( $clone_name ); ?>[start]" rows="2" cols="25" title="<?php esc_attr_e( 'Start tag(s)', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( '<example>', 'cakifo'); ?>"></textarea>
						</td>

						<td>
							<textarea class="code" name="<?php echo esc_attr( $clone_name ); ?>[end]" rows="2" cols="25" title="<?php esc_attr_e( 'Optional: End tag(s)', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( '</example>', 'jayj-quicktag' ); ?>"></textarea>
						</td>

						<td class="jayj-quicktag-remove">
							<button class="button jayj-quicktag-remove-button" title="<?php esc_attr_e( 'Remove button', 'jayj-quicktag' ); ?>">&times;</button>
						</td>
					</tr> <!-- .jayj-quicktag-clone -->
			</tbody>
		</table>

		<div class="jayj-quicktag-table-footer">
			<?php submit_button( __( 'Save Changes', 'jayj-quicktag' ), 'primary', 'submit', false ); ?>

			<button class="jayj-quicktag-add-quicktag button-secondary"><?php _e( '+ Add New Button', 'jayj-quicktag' ); ?></button>
		</div>
	</form>

	<!-- Export/Import metaboxes -->
	<form action="" method="post" name="jayj-quicktag-import" class="jayj-quicktag-postboxes">

		<div id="poststuff"><div class="metabox-holder">

		<!-- Export function -->
		<div class="postbox closed">
			<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'jayj-quicktag' ); ?>"><br /></div>

			<h3 class="hndle"><span><?php _e( 'Export Quicktags', 'jayj-quicktag' ); ?></span></h3>

			<div class="inside">
				<?php _e( 'Export your saved Quicktags data by highlighting the following text, and either:', 'jayj-quicktag' ); ?>
				<ul>
					<li><?php _e( 'Copy/paste it into a blank .txt file. Then save the file for importing into another install of WordPress later.', 'jayj-quicktag' ); ?></li>
					<li><?php _e( 'Or you could paste it into <code>Jayj Quicktag > Import Quicktags</code> on another WordPress install.', 'jayj-quicktag' ); ?></li>
				</ul>
				<textarea rows="10" cols="60" onclick="this.focus(); this.select();"><?php echo esc_textarea( serialize( $options ) ); ?></textarea>
			</div>
		</div> <!-- .postbox -->

		<!-- Import function -->
		<div class="postbox closed">
			<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'jayj-quicktag' ); ?>"><br /></div>

			<h3 class="hndle"><span><?php _e( 'Import Quicktags', 'jayj-quicktag' ); ?></span></h3>

			<div class="inside">
				<p><?php _e( 'To import your Quicktags, copy and paste the content from "Export Quicktags" into this textarea and press the "Import Quicktags" button below.', 'jayj-quicktag' ); ?></p>
				<textarea rows="10" cols="60" name="jayj-quicktag-import"></textarea>
				<?php submit_button( __( 'Import Quicktags', 'jayj-quicktag' ), 'secondary', 'jayj-quicktag-import-save' ); ?>
			</div>
		</div> <!-- .postbox -->

		</div></div>
	</form>

</div> <!-- .wrap -->

<?php

} // End jayj_quicktag_options_page()


/**
 * Validate the saved quicktags.
 *
 * This will make sure removed quicktags or quicktags with an
 * empty "Button label" will be removed.
 *
 * @param  array $input The quicktags saved on the options page
 * @return array        The quicktags with the empty ones removed
 */
function jayj_quicktag_options_validate( $input ) {

	// Don't save empty inputs.
	foreach( $input['buttons'] as $i => $btn ) :

		if ( empty( $btn['text'] ) ) {
			unset( $input['buttons'][$i], $btn );
		}

	endforeach;

	$input['buttons'] = array_values( $input['buttons'] );

	return $input;
}


/**
 * Loads the JavaScript and CSS files required for the options page.
 *
 * This manages the meta boxes on the plugin settings page, which
 * allows users to toggle the metaboxes, and for ordering the Quicktags.
 *
 * @since 1.1.0
 */
function jayj_quicktag_settings_page_enqueue_scripts( $hook_suffix ) {
	if ( 'settings_page_jayj-quicktag/jayj-quicktag' != $hook_suffix )
		return;

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'postbox' );
	wp_enqueue_script( 'jquery-ui-sortable' );

	wp_enqueue_script( 'jayj-quicktag', plugins_url( 'jayj-quicktag' . $suffix . '.js', __FILE__ ), array( 'jquery', 'postbox' ), '1.3' );

	wp_enqueue_style( 'jayj-quicktag', plugins_url( 'jayj-quicktag.css', __FILE__ ), array(), '1.3' );
}

add_action( 'admin_enqueue_scripts', 'jayj_quicktag_settings_page_enqueue_scripts' );

/**
 * Add the quicktags to editor
 *
 * @since 1.0.0
 */
function jayj_quicktag_editor() {

	// Check if the wp_editor() function has been called. If it hasn't, don't include the Quicktags javascript
	if ( ! did_action( 'before_wp_tiny_mce' ) )
		return;

	// Get the options.
	$options = get_option( 'jayj_qt_settings' );

	if ( count( $options['buttons'] ) > 0 ) : ?>

		<!-- Jayj Quicktags -->
		<script type="text/javascript">
		//<![CDATA[
			if ( typeof(QTags) != 'undefined' ) {
				<?php
					$number = 0;

					// Loop through each button
					foreach ( $options['buttons'] as $btn ) :

						$title = ( isset( $btn['title'] ) ) ? $btn['title'] : '';
				?>

					<?php // Self-closing tag ?>

					<?php if ( empty( $btn['end'] ) ) { ?>
						QTags.addButton(
							'jayj_qtag_<?php echo intval( $number ); ?>',
							'<?php echo esc_js( $btn['text'] ); ?>',
							'<?php echo addslashes( $btn['start'] ); ?>',
							'', '',
							'<?php echo esc_js( $title ); ?>'
						);
					<?php } else { ?>
						QTags.addButton(
							'jayj_qtag_<?php echo intval( $number ); ?>',
							'<?php echo esc_js( $btn['text'] ); ?>',
							'<?php echo addslashes( $btn['start'] ); ?>',
							'<?php echo addslashes( $btn['end'] ); ?>',
							'',
							'<?php echo esc_js( $title ); ?>'
						);
					<?php } // endif

						$number++;

					endforeach;
				?>
			}
		//]]>
		</script>
		<!-- // Jayj Quicktags --><?php

	endif;
}

add_action( 'admin_print_footer_scripts', 'jayj_quicktag_editor', 100 );

?>
