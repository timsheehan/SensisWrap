<?php
include('sensiswrap.php');
$sensis = new Sensis();

/**
 * Define params, more info in class or on Sensis
 * http://developers.sensis.com.au/docs/read/using_endpoints/Searching
 */
$params = array(
	'query'    => 'test',
	'location' => 'brisbane'
);

/**
 * Basic usage example, return PHP array with response from Sensis.
 * Note: should return 403 not authorised when testing.
 */
if($output = $sensis->search($params))
{
	var_dump($output);
}
else
{
	var_dump($sensis->errors);
}