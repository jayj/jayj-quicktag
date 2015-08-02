<?php
/**
 * Plugin Name: Jayj Quicktag
 * Plugin URI:  http://jayj.dk/plugins/jayj-quicktag/
 * Description: Allows you to easily add custom quicktags to the editor.
 * Author:      Jesper Johansen
 * Author URI:  http://jayj.dk
 * Version:     1.4
 * License:     GPLv2 or later
 * Text Domain: jayj-quicktag
 * Domain Path: /languages
 */

/**
 * Sets up and initializes the plugin.
 *
 * @since  1.4.0
 * @access public
 * @return void
 */
class Jayj_Quicktag_Plugin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.4.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	private $plugin_version = '1.4.0';

	public $quicktags = array();
	private $menu_name;
	private $settings_name = 'jayj_qt_settings';
	private $settings_field = 'jayj_quicktag_options';

	private $import_status;

	/**
	 * Sets up needed actions/filters for the plugin to initialize.
	 *
	 * @since  1.4.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		load_plugin_textdomain( 'jayj-quicktag', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'add_quicktags_to_editor' ), 100 );
	}


	/**
	 * [add_options_page description]
	 * @since [version]
	 */
	function add_options_page() {
		$this->menu_name = __( 'Editor Quicktags', 'jayj-quicktag' );
		add_options_page( $this->menu_name, $this->menu_name, 'manage_options', __FILE__, array( $this, 'options_page' ) );
	}


	/**
	 * [register_setting description]
	 * @since  [version]
	 * @return [type]    [description]
	 */
	function register_setting() {
		register_setting( $this->settings_field, $this->settings_name, array( $this, 'validate_options' ) );
	}


	/**
	 * Validate the saved quicktags.
	 *
	 * This will make sure removed quicktags or quicktags with an
	 * empty name will be removed.
	 *
	 * @param  array $input The quicktags saved on the options page
	 * @return array        The quicktags with the empty ones removed
	 */
	function validate_options( $input ) {

		foreach( $input['buttons'] as $key => $button ) :

			// Remove quicktags with empty names
			if ( empty( $button['text'] ) ) {
				unset( $input['buttons'][$key], $button );
			}

		endforeach;

		// Reindex the array
		$input['buttons'] = array_values( $input['buttons'] );

		return $input;
	}


	/**
	 * [import_quicktags description]
	 * @since  [version]
	 * @return [type]    [description]
	 */
	function import_quicktags() {
		$options = get_option( $this->settings_name );

		$existing_quicktags = (array) $options['buttons'];
		$imported_quicktags = json_decode( stripslashes_deep( $_POST['jayj-quicktag-import'] ), true );

		if ( empty( $imported_quicktags ) ) {
			$imported_quicktags = array();
		}

		$options['buttons'] = array_merge( $existing_quicktags, $imported_quicktags );

		if ( ! empty( $imported_quicktags ) && update_option( $this->settings_name, $options ) ) {
			$this->import_status = 'success';
			echo '<div class="notice updated is-dismissible"><p><strong>' . __( 'Quicktags succesfully imported.', 'jayj-quicktag' ) . '</strong></p></div>';
		} else {
			$this->import_status = 'error';
			echo '<div class="notice error is-dismissible"><p><strong>' . __( 'Error: Quicktags could not be imported. Please try again.', 'jayj-quicktag' ) . '</strong></p></div>';
		}
	}

	/**
	 * [options_page description]
	 * @since  [version]
	 * @return [type]    [description]
	 */
	function options_page() { ?>

	<div class="wrap">

	<h1><?php _e( 'Quicktag Settings', 'jayj-quicktag' ); ?></h1>

	<form class="jayj-quicktag-form" action="options.php" method="post">

		<?php
			/* Insert imported quicktags */
			if ( isset( $_POST['jayj-quicktag-import-save'] ) ) {
				$this->import_quicktags();
			}
		?>

		<?php
			settings_fields( $this->settings_field );

			// Get the saved options.
			$options   = get_option( $this->settings_name );
			$quicktags = $options['buttons'];
		?>

		<table class="widefat jayj-quicktag-table striped">

			<thead>
				<tr>
					<th scope="col" class="jayj-quicktag-order">
						<span aria-hidden="true"><?php _e( '#', 'jayj-quicktag' ); ?></span>
						<span class="screen-reader-text"><?php _e( 'Order', 'jayj-quicktag' ); ?></span>
					</th>
					<th scope="col"><?php _e( 'Button name *', 'jayj-quicktag' ); ?></th>
					<th scope="col"><?php _e( 'Start tag *', 'jayj-quicktag' ); ?></th>
					<th scope="col"><?php _e( 'End tag', 'jayj-quicktag' ); ?></th>
					<th scope="col"><?php _e( 'Help text', 'jayj-quicktag' ); ?></th>
					<th scope="col"><span class="screen-reader-text"><?php _e( 'Remove', 'jayj-quicktag' ); ?></span></th>
				</tr>
			</thead>

			<tbody>

			<?php if ( isset( $quicktags ) ) : ?>

				<?php foreach ( $quicktags as $number => $quicktag ) : ?>

					<tr class="jayj-quicktag-row" data-id="jayj_qtag_<?php echo esc_attr( $number ); ?>">
						<td class="jayj-quicktag-order">
							<?php echo $number + 1; ?>
						</td>

						<td>
							<input type="text" name="jayj_qt_settings[buttons][<?php echo esc_attr( $number ); ?>][text]" class="jayj-quicktag-name-input" value="<?php echo esc_attr( $quicktag['text'] ); ?>" />
						</td>

						<td>
							<textarea name="jayj_qt_settings[buttons][<?php echo esc_attr( $number ); ?>][start]" class="code" rows="2" cols="25"><?php echo esc_textarea( $quicktag['start'] ); ?></textarea>
						</td>

						<td>
							<textarea name="jayj_qt_settings[buttons][<?php echo esc_attr( $number ); ?>][end]" class="code" rows="2" cols="25"><?php echo esc_textarea( $quicktag['end'] ); ?></textarea>
						</td>

						<td>
							<input type="text" name="jayj_qt_settings[buttons][<?php echo esc_attr( $number ); ?>][title]" value="<?php echo esc_attr( $quicktag['title'] ); ?>" />
						</td>

						<td class="jayj-quicktag-remove">
							<button class="button jayj-quicktag-remove-button">
								<span class="dashicons dashicons-no"></span>
								<span class="screen-reader-text"><?php esc_attr_e( 'Remove quicktag', 'jayj-quicktag' ); ?></span>
							</button>
						</td>
					</tr>

				<?php endforeach; ?>
			<?php endif; ?>

					<!-- Empty row -->
					<?php $i = 9999; ?>

				<tr valign="top" class="jayj-quicktag-clone <?php if ( empty ( $options['buttons'] ) ) echo 'jayj-quicktag-clone-show'; ?>"data-id="jayj_qtag_<?php echo intval( $i ); ?>">
					<td class="jayj-quicktag-order"><?php echo $i; ?></td>

					<?php
						// Set the name attribute.
						$clone_name = 'jayj_qt_settings[buttons][' . $i . ']';
					?>

					<td>
						<input type="text" class="jayj-quicktag-name-input" name="<?php echo esc_attr( $clone_name ); ?>[text]" title="<?php esc_attr_e( 'Label of the Quicktag', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( 'Example', 'jayj-quicktag' ); ?>" value="" />
					</td>

					<td>
						<textarea class="code" name="<?php echo esc_attr( $clone_name ); ?>[start]" rows="2" cols="25" title="<?php esc_attr_e( 'Start tag(s)', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( '<example>', 'cakifo'); ?>"></textarea>
					</td>

					<td>
						<textarea class="code" name="<?php echo esc_attr( $clone_name ); ?>[end]" rows="2" cols="25" title="<?php esc_attr_e( 'Optional: End tag(s)', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( '</example>', 'jayj-quicktag' ); ?>"></textarea>
					</td>

					<td>
						<input type="text" name="<?php echo esc_attr( $clone_name ); ?>[title]" title="<?php esc_attr_e( 'Title attribute of the Quicktag', 'jayj-quicktag' ); ?>" placeholder="<?php esc_attr_e( 'Example title', 'jayj-quicktag' ); ?>" value="" />
					</td>

					<td class="jayj-quicktag-remove">
						<button class="button jayj-quicktag-remove-button" title="<?php esc_attr_e( 'Remove button', 'jayj-quicktag' ); ?>">&times;</button>
					</td>
				</tr> <!-- .jayj-quicktag-clone -->
			</tbody>
		</table>

		<div class="jayj-quicktag-table-footer">
			<?php submit_button( __( 'Save Changes', 'jayj-quicktag' ), 'primary', 'submit', false ); ?>

			<button class="jayj-quicktag-add-quicktag button-secondary"><?php _e( '+ Add New Quicktag', 'jayj-quicktag' ); ?></button>
		</div>
	</form>

	<!-- Export/Import metaboxes -->
	<form action="" method="post" name="jayj-quicktag-import" class="jayj-quicktag-postboxes">

		<?php $class = ( 'error' === $this->import_status ) ? 'open' : 'closed'; ?>

		<div id="poststuff"><div class="metabox-holder">

		<!-- Export function -->
		<div class="postbox <?php echo esc_attr( $class ); ?>">
			<div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'jayj-quicktag' ); ?>"><br /></div>

			<h3 class="hndle"><span><?php _e( 'Export Quicktags', 'jayj-quicktag' ); ?></span></h3>

			<div class="inside">
				<?php _e( 'Export your saved Quicktags data by highlighting the following text, and either:', 'jayj-quicktag' ); ?>
				<ul>
					<li><?php _e( 'Copy/paste it into a blank .txt file. Then save the file for importing into another install of WordPress later.', 'jayj-quicktag' ); ?></li>
					<li><?php _e( 'Or you could paste it into <code>Jayj Quicktag > Import Quicktags</code> on another WordPress install.', 'jayj-quicktag' ); ?></li>
				</ul>
				<textarea rows="10" cols="60" onclick="this.focus(); this.select();"><?php echo esc_textarea( json_encode( $options['buttons'] ) ); ?></textarea>
			</div>
		</div> <!-- .postbox -->

		<!-- Import function -->
		<div class="postbox <?php echo esc_attr( $class ); ?>">
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

	<h2>Preview</h2>

	<?php wp_editor( 'Preview', 'jayj_quicktag_preview_editor', array( 'media_buttons' => false, 'textarea_rows' => 2, 'tinymce' => false ) ); ?>

</div> <!-- .wrap -->

<?php
	}

	/**
	 * Loads the JavaScript and CSS files required for the options page.
	 */
	function enqueue_scripts( $hook_suffix ) {
		if ( 'settings_page_jayj-quicktag/jayj-quicktag' != $hook_suffix ) {
			return;
		}

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script( 'jayj-quicktag', plugins_url( 'jayj-quicktag' . $suffix . '.js', __FILE__ ), array( 'jquery', 'postbox' ), $this->plugin_version );

		wp_enqueue_style( 'jayj-quicktag', plugins_url( 'jayj-quicktag.css', __FILE__ ), array(), $this->plugin_version );

	}

	function add_quicktags_to_editor() {
		// Check if the wp_editor() function has been called. If it hasn't, don't include the Quicktags javascript
		if ( ! did_action( 'before_wp_tiny_mce' ) ) {
			return;
		}

		$options = get_option( $this->settings_name );

		if ( count( $options['buttons'] ) > 0 ) : ?>

			<!-- Jayj Quicktag -->
			<script type="text/javascript">
				if ( typeof(QTags) !== 'undefined' ) {
					var quicktags = <?php echo json_encode( $options['buttons'] ); ?>;

					for (i = 0; i < quicktags.length; i++) {
						var tag = quicktags[i];
						QTags.addButton( 'jayj_qtag_' + i, tag.text, tag.start, tag.end, '', tag.title, '', '', { ariaLabel: tag.title } );
					}
				}
			</script>
			<!-- // Jayj Quicktag --><?php

		endif;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.4.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Jayj_Quicktag_Plugin::get_instance();


?>
