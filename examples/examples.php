<?php
/**
 * Examples
 *
 * Usage examples for PHPDB.
 *
 * @author      KingFase
 * @date        02 December 2014
 * @version     0.1.0
 */

//
// Setup
//

// Include path to class.db.php
include('class.db.php');

// Set up new db handler with your database info and credentials
$db = new DB('DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS');



//
// Execute query with no results
//

$query = "SELECT 1;";

$db->execute($query);



//
// Execute query with results
//

$query = "SELECT :a as a, :b as b, :c as c;";

// Param types can be one of following
// PARAM_BOOL | PARAM_NULL | PARAM_INT | PARAM_STR | PARAM_LOB
// See: http://php.net/manual/en/pdo.constants.php

$binds = array(
	array('bind' => 'a', 'value' => 'potato', 'type' => PDO::PARAM_STR),
	array('bind' => 'b', 'value' => 'carrot', 'type' => PDO::PARAM_STR),
	array('bind' => 'c', 'value' => 'corn',   'type' => PDO::PARAM_STR)
);

if ($results = $db->execute($query, $binds)) {

	// Select results from the first row (index 0)
	$a = $results[0]['a'];
	$b = $results[0]['b'];
	$c = $results[0]['c'];
	
	echo "{$a}, {$b}, {$c}";
}