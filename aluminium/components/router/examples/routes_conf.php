<?php

return array(

	// Sample route configuration for static paths
	array(
		'path'   => '/section/about-me',
		'method' => 'GET',
		// You can customize the values freely, there are infinite ways to do this
		'values' => array(
			'folder'	=> '/section/',
			'filename'	=> 'about-me'
		)
	),

	// Sample route configuration for MVC
	array(
		'path'   =>	'/{:controller}/{:action}/{:id}',
		'method' => 'GET',
		'params' => array(
			'controller'=> '(\w+)',
			'action'	=> '(\w+)',
			'id'		=> '(\d+)'
		),
		'values' => array(
			'extra1'	=> 'some_value',
			'extra2'	=> 23
		)
	)

);

?>
