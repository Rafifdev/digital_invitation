<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Enable/Disable Migrations
$config['migration_enabled'] = TRUE;

// Migration Type
$config['migration_type'] = 'sequential';

// Migrations table
$config['migration_table'] = 'migrations';

// Auto Migrate To Latest
$config['migration_auto_latest'] = FALSE;

// Migrations version
$config['migration_version'] = 001;

// Migrations Path
$config['migration_path'] = APPPATH.'migrations/';
