<?php
/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/


$application = 'application';
$modules = 'modules';
$system = 'system';
define('EXT', '.php');

error_reporting(E_ALL | E_STRICT);

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Make the application relative to the docroot
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

define('TKSHORTNM', 'sm-ivory'); // Toolkit short name, e.g. 'sm-ivory'
define('TKLONGNM', 'Sourcemap Ivory'); // Toolkit long name, e.g. 'Sourcemap Ivory'
define('APPSHORTNM', getenv('APPSHORTNM') === false ? 'sm-base' : getenv('APPSHORTNM')); // Application short name, e.g. 'sm-base'
define('APPLONGNM', 'Sourcemap Base'); // Application long name, e.g. 'Sourcemap Base'
define('SOURCEMAP_SITE', getenv('SOURCEMAP_SITE') ? getenv('SOURCEMAP_SITE') : 'default');

// Define the absolute paths for configured directories
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);
// Log path
define('LOGPATH', getenv('SOURCEMAP_LOG_PATH') ? getenv('SOURCEMAP_LOG_PATH') : '/var/log/'.TKSHORTNM.'/'.APPSHORTNM.'/');
define('SOURCEMAP_LOG_PATH', LOGPATH);
// Cache path
define('CACHEPATH', getenv('SOURCEMAP_CACHE_PATH') ? getenv('SOURCEMAP_CACHE_PATH') : '/var/cache/'.TKSHORTNM.'/'.APPSHORTNM.'/');
define('SOURCEMAP_CACHE_PATH', CACHEPATH);
// Sites directory
define('SOURCEMAP_SITES_PATH', realpath(dirname(APPPATH)).'/sites/');

unset($application, $modules, $system);

if (file_exists('install'.EXT)) { return include 'install'.EXT; }
require SYSPATH.'base'.EXT;
require SYSPATH.'classes/kohana/core'.EXT;
if (is_file(APPPATH.'classes/kohana'.EXT)) { require APPPATH.'classes/kohana'.EXT; }
else { require SYSPATH.'classes/kohana'.EXT;}
require APPPATH.'bootstrap'.EXT;
