<?php
/**
 * elFinder connector helpers (admin + frontend blocks).
 *
 * @package File_Manager_Advanced
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!function_exists('fma_create_elfinder_connector')) {
	/**
	 * Create a stream-safe elFinder connector instance.
	 *
	 * @param elFinder $elfinder Configured elFinder instance.
	 * @return elFinderConnector|class_fma_elfinder_connector
	 */
	function fma_create_elfinder_connector($elfinder)
	{
		if (!class_exists('elFinderConnector')) {
			$autoload = defined('FMAFILEPATH')
				? FMAFILEPATH . 'application/library/php/autoload.php'
				: WP_PLUGIN_DIR . '/file-manager-advanced/application/library/php/autoload.php';
			if (file_exists($autoload)) {
				require_once $autoload;
			}
		}

		if (!class_exists('class_fma_elfinder_connector')) {
			$connector_file = defined('FMAFILEPATH')
				? FMAFILEPATH . 'application/class_fma_elfinder_connector.php'
				: WP_PLUGIN_DIR . '/file-manager-advanced/application/class_fma_elfinder_connector.php';
			if (file_exists($connector_file) && class_exists('elFinderConnector')) {
				require_once $connector_file;
			}
		}

		if (class_exists('class_fma_elfinder_connector')) {
			return new class_fma_elfinder_connector($elfinder);
		}

		return new elFinderConnector($elfinder);
	}
}
