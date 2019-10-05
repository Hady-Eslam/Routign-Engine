<?php

/**
 * 
 * Here is Your App Schema
 * 
 * @author : Hady Eslam
 * @version : 3.0
 * 
 */

return [

	'' => 'Me',
	
	'Register' => [

		'<string:ID2>' => 'Register/Hell{Begin_Class}',
		
		404 => _APP_ROOT_.'/APP/Templates/DO/MakeNote.html',
		
		'' => 'Register/Hell{Begin}',
		
		'<double(5):ID>' => 'Register/Login{Begin}'
	]
	/*404 => '404.Begin',
	
	'Register' => [

		'Login' => 'Register/Login.Login',

		'SignUP' => 'Register/SignUP.SignUP',
		'SuccessSignUP' => 'Register/SuccessSignUP.Begin',

		'LogOut' => 'Register/LogOut.Begin'
	],

	'DO' => [

		'MakeNote' => 'DO/MakeNote.MakeNote',
		'ShowNotes' => 'DO/ShowNotes.Begin',
		'Note' => [
			'<int>' => 'DO/Note.Begin'
		],

		'EditNote' => [
			'<int>' => ''
		]
	],

	'Profile' => [
		
		'Settings' => [
			'Password' => '',
			'Name' => '',
			'Delete' => '',
		]
	]*/
];
