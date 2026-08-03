<?php
return array(
	'_root_'  => 'auth/login',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

	// パッキングリスト画面を表示する
	'item/(:num)' => 'item/index/$1',
);
