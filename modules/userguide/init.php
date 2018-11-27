<?php

// Static file serving (CSS, JS, images)
Route::set('docs/media', 'guide-media(/<file>)', array('file' => '.+'))
	->defaults(array(
		'controller' => 'Userguide',
		'action'     => 'media',
		'file'       => NULL,
	));

// API Browser, if enabled
if (Kohana::$config->load('userguide.api_browser') === TRUE)
{
	Route::set('docs/api', 'guide-api(/<class>)', array('class' => '[a-zA-Z0-9_]+'))
		->defaults(array(
			'controller' => 'Userguide',
			'action'     => 'api',
			'class'      => NULL,
		));
}

// User guide pages, in modules
Route::set('docs/guide', 'guide(/<module>(/<page>))', array(
		'page' => '.+',
	))
	->defaults(array(
		'controller' => 'Userguide',
		'action'     => 'docs',
		'module'     => '',
	));