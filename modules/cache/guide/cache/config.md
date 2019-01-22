# Kohana Cache configuration

Kohana Cache uses configuration groups to create cache instances. A configuration group can
use any supported driver, with successive groups using multiple instances of the same driver type.

The default cache group is loaded based on the `Cache::$default` setting. It is set to the `file` driver as standard, however this can be changed within the `/application/boostrap.php` file.

     // Change the default cache driver to APCu
     Cache::$default = 'apcu';

     // Load the APCu cache driver using default setting
     $cache = Cache::instance();

## Group settings

Below are the default cache configuration groups for each supported driver. Add to- or override these settings
within the `application/config/cache.php` file.

Name           | Required | Description
-------------- | -------- | ---------------------------------------------------------------
driver         | __YES__  | (_string_) The driver type to use
default_expire | __NO__   | (_string_) The driver type to use


	'file'  => array
	(
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache/.kohana_cache',
		'default_expire'     => 3600,
	),

## APCu settings

	'apcu'      => array
	(
		'driver'             => 'apcu',
		'default_expire'     => 3600,
	),
	
## SQLite settings

	'sqlite'   => array
	(
		'driver'             => 'sqlite',
		'default_expire'     => 3600,
		'database'           => APPPATH.'cache/kohana-cache.sql3',
		'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, 
		                                  tags VARCHAR(255), expiration INTEGER, cache TEXT)',
	),

## File settings

	'file'    => array
	(
		'driver'             => 'file',
		'cache_dir'          => 'cache/.kohana_cache',
		'default_expire'     => 3600,
	)

## Wincache settings

	'wincache' => array
	(
		'driver'             => 'wincache',
		'default_expire'     => 3600,
	),


## Override existing configuration group

The following example demonstrates how to override an existing configuration setting, using the config file in `/application/config/cache.php`.

	<?php
	return array
	(
		// Override the default configuration
		'apcu'   => array
		(
			'driver'         => 'apcu',  // Use APCu as the default driver
			'default_expire' => 8000,        // Overide default expiry
	);

## Add new configuration group

The following example demonstrates how to add a new configuration setting, using the config file in `/application/config/cache.php`.

	<?php
	return array
	(
		// Override the default configuration
		'fastkv'   => array
		(
			'driver'         => 'apcu',  // Use APCu as the default driver
			'default_expire' => 1000,   // Overide default expiry
		)
	);
