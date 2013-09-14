<?php
/**
 * Plugin Name: Jayj Quicktag
 * Plugin URI:	http://jayj.dk/plugins/jayj-quicktag/
 * Description: Allows you to easily add custom quicktags to the editor. Requires at least WordPress 3.3 to work
 * Author:      Jesper J
 * Author URI:  http://jayj.dk
 * Version:     1.2.4
 * License: GPLv2 or later
 */

register_activation_hook( __FILE__, 'jayj_quicktag_install' );
register_uninstall_hook( __FILE__, 'jayj_quicktag_uninstall' );

/* Load the textdomain for translation */
load_plugin_textdomain( 'jayj-quicktag', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Set up default options on install
 *
 * @since 1.0.0
 * @uses apply_filters() Calls 'jayj_quicktag_defaults' filter on the defaults array
 */
function jayj_quicktag_install() {
	$defaults = array( 'buttons' => array( array(
			'text'  => __( 'Example', 'jayj-quicktag' ),
			'title' => __( 'Example Title', 'jayj-quicktag' ),
			'start' => '<example>',
			'end'   => '</example>'
		)
	) );

	add_option( 'jayj_qt_settings', apply_filters( 'jayj_quicktag_defaults', $defaults ) );
}

/**
 * Uninstall function
 *
 * Remove Quicktags from the database
 *
 * @since 1.0.0
 */
function jayj_quicktag_uninstall() {
	delete_option( 'jayj_qt_settings' );
}

/**
 * Add options page
 *
 * @since 1.0.0
 */
function jayj_quicktag_add_options_page() {
	add_options_page( 'Jayj Quicktag', 'Jayj Quicktag', 'manage_options', __FILE__, 'jayj_quicktag_options_page' );
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
 * The Quicktags Options page
 *
 * @since 1.0.0
 */
function jayj_quicktag_options_page() { ?>

<div class="wrap">

	<?php screen_icon( 'options-general' ); ?>

	<h2><?php _e( 'Jayj Quicktag Options', 'jayj-quicktag' ); ?></h2>

	<br />

    <form action="options.php" method="post">

		<?php
			/**
			 * Insert imported Quicktags
			 *
			 * @since 1.1.0
			 */
			if ( isset( $_POST['jayj-import-quicktags-save'] ) ) :

				$options = get_option( 'jayj_qt_settings' );
				$data = maybe_unserialize( stripslashes_deep( $_POST['jayj-import'] ) );

				// Merge the old and the new Quicktags
				if ( ! empty( $data ) )
					$imported['buttons'] = array_merge( (array) $options['buttons'], $data['buttons'] );

				// Succes or error message
				if ( ! empty( $data ) && update_option( 'jayj_qt_settings', $imported ) )
					echo '<div class="updated"><p><strong>' . __( 'Quicktags succesfully imported', 'jayj-quicktag' ) . '</strong></p></div>';
				else
					echo '<div class="error"><p><strong>' . __( 'Error: Quicktags could not be imported', 'jayj-quicktag' ) . '</strong></p></div>';

			endif;
        ?>

        <?php
        	settings_fields( 'jayj_quicktag_options' );
			$options = get_option( 'jayj_qt_settings' );
		?>

		<table class="widefat jayj-quicktags-table">
			<thead>
                <tr>
                    <th scope="col" class="jayj-order" title="<?php esc_attr_e( 'Change order', 'jayj-quicktag' ); ?>"><!-- order --></th>
                    <th scope="col"><?php _e( 'Button Label *', 'jayj-quicktag' ); ?></th>
                    <th scope="col"><?php _e( 'Title Attribute', 'jayj-quicktag' ); ?></th>
                    <th scope="col"><?php _e( 'Start Tag(s) *', 'jayj-quicktag' ); ?></th>
                    <th scope="col"><?php _e( 'End Tag(s)', 'jayj-quicktag' ); ?></th>
                    <th scope="col" title="<?php esc_attr_e( 'Remove button', 'jayj-quicktag' ); ?>"><!-- remove --></th>
                </tr>
            </thead>

			<tbody>
				<?php
                	if ( isset( $options['buttons'] ) ) :

					// Loop through all the buttons
                	for ( $i = 0; $i < count( $options['buttons'] ); $i++ ) :

                		if ( ! isset( $options['buttons'][$i] ) )
                			break;
                ?>
                    <tr valign="top" class="jayj-row">
                    	<td class="jayj-order" title="<?php esc_attr_e( 'Change order', 'jayj-quicktag' ); ?>"><?php echo $i + 1; ?></td>
                        <td><input type="text" name="jayj_qt_settings[buttons][<?php echo $i; ?>][text]" value="<?php echo esc_attr( $options['buttons'][$i]['text'] ); ?>" /></td>
                        <td><input type="text" name="jayj_qt_settings[buttons][<?php echo $i; ?>][title]" value="<?php echo esc_attr( $options['buttons'][$i]['title'] ); ?>" /></td>
                        <td><textarea class="code" name="jayj_qt_settings[buttons][<?php echo $i; ?>][start]" rows="2" cols="25"><?php echo esc_textarea( $options['buttons'][$i]['start'] ); ?></textarea></td>
                        <td><textarea class="code" name="jayj_qt_settings[buttons][<?php echo $i; ?>][end]" rows="2" cols="25"><?php echo esc_textarea( $options['buttons'][$i]['end'] ); ?></textarea></td>
                        <td class="jayj-remove"><a class="jayj-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove button', 'jayj-quicktag' ); ?>">&times;</a></td>
                    </tr>
                <?php endfor; endif; ?>

                    <!-- Empty -->
                    <?php $i = 999; ?>
                    <tr valign="top" class="jayj-clone">
                    	<td class="jayj-order" title="<?php esc_attr_e( 'Change order', 'jayj-quicktag' ); ?>"><?php echo $i; ?></td>
                        <td><input type="text" name="jayj_qt_settings[buttons][<?php echo $i; ?>][text]" title="<?php esc_attr_e( 'Label of the Quicktag', 'jayj-quicktag' ); ?>" value="" /></td>
                        <td><input type="text" name="jayj_qt_settings[buttons][<?php echo $i; ?>][title]" title="<?php esc_attr_e( 'Title attribute of the Quicktag', 'jayj-quicktag' ); ?>" value="" /></td>
                        <td><textarea class="code" name="jayj_qt_settings[buttons][<?php echo $i; ?>][start]" rows="2" cols="25" title="<?php esc_attr_e( 'Start tag(s)', 'jayj-quicktag' ); ?>"></textarea></td>
                        <td><textarea class="code" name="jayj_qt_settings[buttons][<?php echo $i; ?>][end]" rows="2" cols="25" title="<?php esc_attr_e( 'End tag(s)', 'jayj-quicktag' ); ?>"></textarea></td>
						<td class="jayj-remove"><a class="jayj-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove button', 'jayj-quicktag' ); ?>">&times;</a></td>
                    </tr>
    		</tbody>
    	</table>

        <div class="jayj-table-footer">
            <div class="jayj-order-message"></div>
            <?php submit_button( __( 'Save Changes', 'jayj-quicktag' ) ); ?>
            <a href="javascript:;" id="jayj-add-button" class="button-secondary"><?php _e( '+ Add New Button', 'jayj-quicktag' ); ?></a>
        </div>
	</form>

	<!-- Export/Import metaboxes -->
    <form action="" method="post" name="jayj-import-quicktags">
	<div id="poststuff"><div class="metabox-holder">

		<!-- Export function -->
        <div class="postbox closed jayj-quicktags-postbox">
        	<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'jayj-quicktag' ); ?>"><br /></div>
            <h3 class="hndle"><span><?php _e( 'Export Quicktags', 'jayj-quicktag' ); ?></span></h3>

            <div class="inside">
				<?php _e( 'Export your saved Quicktags data by highlighting this text and either', 'jayj-quicktag' ); ?>
                <ul>
                    <li><?php _e( 'Copy/paste it into a blank .txt file. Then save the file for importing into another install of WordPress later.', 'jayj-quicktag' ); ?></li>
                    <li><?php _e( 'Or you could just paste it into <code>Jayj Quicktag > Import Quicktags</code> on another install of WordPress.', 'jayj-quicktag' ); ?></li>
                </ul>
            	<textarea rows="10" cols="60" onclick="this.focus(); this.select();"><?php echo esc_textarea( serialize( $options ) ); ?></textarea>
            </div>
        </div> <!-- .postbox -->

		<!-- Import function -->
        <div class="postbox closed jayj-quicktags-postbox jayj-quicktags-postbox-last">
        	<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'jayj-quicktag' ); ?>"><br /></div>
            <h3 class="hndle"><span><?php _e( 'Import Quicktags', 'jayj-quicktag' ); ?></span></h3>

            <div class="inside">
                <p><?php _e( 'To import your Quicktags, copy and paste the content from "Export Quicktags" into this textarea and press the "Import Quicktags" button below.', 'jayj-quicktag' ); ?></p>
                <textarea rows="10" cols="60" name="jayj-import"></textarea>
                <?php submit_button( __( 'Import Quicktags', 'jayj-quicktag' ), 'secondary', 'jayj-import-quicktags-save' ); ?>
            </div>
        </div> <!-- .postbox -->

    </div></div>
	</form>

</div> <!-- .wrap -->

<?php

} // End function wpaq_options_page

/**
 * Validate the saved options
 *
 * This will make sure that options with an empty "Button Label" value will be removed
 *
 * @since 1.0.0
 * @param array $input The options saved on the options page
 * @returns array The options saved, without the empty ones
 */
function jayj_quicktag_options_validate( $input ) {

	// Don't save empty inputs
	foreach( $input['buttons'] as $i => $btn ) :

		if ( empty( $btn['text'] ) )
			unset( $input['buttons'][$i], $btn );

	endforeach;

	$input['buttons'] = array_values( $input['buttons'] );

	return $input;
}

/**
 * Loads the JavaScript and CSS files required for managing the meta boxes on the plugin settings
 * page, which allows users to toggle the metaboxes
 * and for ordering the Quicktags
 *
 * @since 1.1.0
 */
function jayj_quicktag_settings_page_enqueue_scripts( $hook_suffix ) {
	if ( $hook_suffix == 'settings_page_jayj-quicktag/jayj-quicktag' ) {
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'jayj-quicktag', plugins_url( 'jayj-quicktag.js', __FILE__ ), array( 'jquery', 'postbox' ), '1.2' );
		wp_enqueue_style( 'jayj-quicktag', plugins_url( 'jayj-quicktag.css', __FILE__ ), array(), '1.2' );
	}
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

	// Get the options
	$options = get_option( 'jayj_qt_settings' );

	if ( count( $options['buttons'] ) > 0 ) : ?>

		<!-- Jayj Quicktags -->
		<script type="text/javascript">
		//<![CDATA[
			if (typeof(QTags) != 'undefined') {
				<?php
					$i = 0;

					// Loop through each button
					foreach ( $options['buttons'] as $btn ) :

						$title = ( isset( $btn['title'] ) ) ? $btn['title'] : '';
				?>

					<?php // Self-closing tag ?>
					<?php if ( empty( $btn['end'] ) ) { ?>
						QTags.addButton(
							'jayj_qtag_<?php echo intval( $i ); ?>',
							'<?php echo esc_attr( $btn['text'] ); ?>',
							'<?php echo addslashes( $btn['start'] ); ?>',
							'', '',
							'<?php echo esc_attr( $title ); ?>'
						);
					<?php } else { ?>
						QTags.addButton(
							'jayj_qtag_<?php echo intval( $i ); ?>',
							'<?php echo esc_attr( $btn['text'] ); ?>',
							'<?php echo addslashes( $btn['start'] ); ?>',
							'<?php echo addslashes( $btn['end'] ); ?>',
							'',
							'<?php echo esc_attr( $title ); ?>'
						);
					<?php } // endif
						$i++;
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
