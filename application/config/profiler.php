<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profiler & Debug Bar Configuration
 */

// Custom Profiler Sections
$config['codeigniter_info']     = TRUE;
$config['exceptions']           = TRUE;
$config['messages']             = TRUE;
$config['php_info']             = TRUE;
$config['included_files']       = TRUE;

// Profiler Display Options
$config['benchmarks']           = TRUE;
$config['config']               = TRUE;
$config['controller_info']      = TRUE;
$config['get']                  = TRUE;
$config['http_headers']         = TRUE;
$config['memory_usage']         = TRUE;
$config['post']                 = TRUE;
$config['queries']              = TRUE;
$config['uri_string']           = TRUE;
$config['session_data']         = TRUE;
$config['query_toggle_count']   = 25;

// Debug Bar Renderer Config
$config['base_url']                 = NULL;
$config['include_vendors']          = FALSE;
$config['enable_jquery_noconflict'] = FALSE;
$config['open_handler_url']         = NULL;

// Cache Directory Path
$config['cache_path']               = '';

// Display Assets & Scripts
$config['display_assets']           = TRUE;
$config['display_javascript']       = TRUE;
