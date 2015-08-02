<?php
/**
 * Removes the Quicktags from the database
 *
 * @since 1.4.0
 */

// If uninstall hasn't been called, then bail
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'jayj_qt_settings' );
