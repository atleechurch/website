<?php

/**
 * File Change Detection Execution and Processing
 *
 * Handles all file change detection execution once the feature has been
 * enabled by the user.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_File_Change {

	/**
	 * Setup the module's functionality
	 *
	 * Loads the file change detection module's unpriviledged functionality including
	 * performing the scans themselves
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	function run() {

		global $itsec_globals;

		$settings = ITSEC_Modules::get_settings( 'file-change' );
		$interval = 86400; //Run daily

		// If we're splitting the file check run it every 6 hours.
		if ( isset( $settings['split'] ) && true === $settings['split'] ) {
			$interval = 12342;
		}

		add_action( 'itsec_execute_file_check_cron', array( $this, 'run_scan' ) ); //Action to execute during a cron run.

		add_filter( 'itsec_logger_displays', array( $this, 'itsec_logger_displays' ) ); //adds logs metaboxes
		add_filter( 'itsec_logger_modules', array( $this, 'itsec_logger_modules' ) );
		add_filter( 'itsec_sync_modules', array( $this, 'itsec_sync_modules' ) ); //register sync modules


		if (
			( ! defined( 'DOING_AJAX' ) || DOING_AJAX === false ) &&
			isset( $settings['last_run'] ) &&
			( $itsec_globals['current_time'] - $interval ) > $settings['last_run'] &&
			( ! defined( 'ITSEC_FILE_CHECK_CRON' ) || false === ITSEC_FILE_CHECK_CRON )
		) {

			wp_clear_scheduled_hook( 'itsec_file_check' );
			add_action( 'init', array( $this, 'run_scan' ) );

		} elseif ( defined( 'ITSEC_FILE_CHECK_CRON' ) && true === ITSEC_FILE_CHECK_CRON && ! wp_next_scheduled( 'itsec_execute_file_check_cron' ) ) { //Use cron if needed

			wp_schedule_event( time(), 'daily', 'itsec_execute_file_check_cron' );

		}

	}

	public function run_scan() {
		require_once( dirname( __FILE__ ) . '/scanner.php' );

		return ITSEC_File_Change_Scanner::run_scan();
	}

	/**
	 * Register file change detection for logger
	 *
	 * Registers the file change detection module with the core logger functionality.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array array of logger modules
	 */
	public function itsec_logger_modules( $logger_modules ) {

		$logger_modules['file_change'] = array(
			'type'     => 'file_change',
			'function' => __( 'File Changes Detected', 'better-wp-security' ),
		);

		return $logger_modules;

	}

	/**
	 * Array of displays for the logs screen
	 *
	 * Registers the custom log page with the core plugin to allow for access from the log page's
	 * dropdown menu.
	 *
	 * @since 4.0.0
	 *
	 * @param array $displays metabox array
	 *
	 * @return array metabox array
	 */
	public function itsec_logger_displays( $displays ) {

		$displays[] = array(
			'module'   => 'file_change',
			'title'    => __( 'File Change History', 'better-wp-security' ),
			'callback' => array( $this, 'logs_metabox_content' )
		);

		return $displays;

	}

	/**
	 * Render the file change log metabox
	 *
	 * Displays a metabox on the logs page, when filtered, showing all file change items.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function logs_metabox_content() {

		global $itsec_globals;

		if ( ! class_exists( 'ITSEC_File_Change_Log' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-file-change-log.php' );
		}


		$settings = ITSEC_Modules::get_settings( 'file-change' );


		// If we're splitting the file check run it every 6 hours. Else daily.
		if ( isset( $settings['split'] ) && true === $settings['split'] ) {

			$interval = 12342;

		} else {

			$interval = 86400;

		}

		$next_run_raw = $settings['last_run'] + $interval;

		if ( date( 'j', $next_run_raw ) == date( 'j', $itsec_globals['current_time'] ) ) {
			$next_run_day = __( 'Today', 'better-wp-security' );
		} else {
			$next_run_day = __( 'Tomorrow', 'better-wp-security' );
		}

		$next_run = $next_run_day . ' at ' . date( 'g:i a', $next_run_raw );

		echo '<p>' . __( 'Next automatic scan at: ', 'better-wp-security' ) . '<strong>' . $next_run . '*</strong></p>';
		echo '<p><em>*' . __( 'Automatic file change scanning is triggered by a user visiting your page and may not happen exactly at the time listed.', 'better-wp-security' ) . '</em>';

		$log_display = new ITSEC_File_Change_Log();

		$log_display->prepare_items();
		$log_display->display();

	}

	/**
	 * Register file change detection for Sync
	 *
	 * Reigsters iThemes Sync verbs for the file change detection module.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $sync_modules array of sync modules
	 *
	 * @return array array of sync modules
	 */
	public function itsec_sync_modules( $sync_modules ) {

		$sync_modules['file-change'] = array(
			'verbs' => array(
				'itsec-perform-file-scan' => 'Ithemes_Sync_Verb_ITSEC_Perform_File_Scan',
			),
			'path'  => dirname( __FILE__ ),
		);

		return $sync_modules;

	}

}
