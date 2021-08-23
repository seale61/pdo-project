<?php
/**
 * file:    index.php
 * Author:  Charles Seale
 * Created: Aug. 22, 2021
 * This is a test script that demonstrates how to instantiate and use simplePDO. 
 */

require_once('./vendor/autoload.php');
require_once('./classes/pdo.class.php');

/** 
 * Using Dotenv is highly recommended if you wish to load sensitive info 
 * into environment variables.
 *
*/

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Demonstration of simplePDO usage

$db = new simplePDO();  // Create a new instance of the simplePDO
$output = [];           // Create an output array to hold results of this demo

// SELECT EXAMPLE ONE --------------------------------------------------------------------------------
$sql = "SELECT * FROM people";
$binds = [];

$fetchMode = '';

/** 
 * If you are not using a specified fetchMode, the variable "$fetchMode" can be ignored and ommited 
 * from the paramaters passed to the query method. The default fetch mode is "object"
 * 
 * Valid FETCH modes:
 * 
 *      num_array   - A standard, numerically indexed, array
 *      assoc_array - An array of associative arrays (key/value pairs)
 *      object      - An array of objects 
 *    
 * For data that is returned to a website using json_encode, the results from 
 * "assoc_array" and "object" produce the same result after json encoding.
 * 
*/

$data = $db->query($sql, $binds, $fetchMode);
array_push($output, 'Full people listing');
array_push($output, $data);

// SELECT conditional statement ex. 1 ----------------------------------------------------------------
$sql = "SELECT * FROM people WHERE last_name = ?";
$binds = ['Smith'];

$fetchMode = '';
$data = $db->query($sql, $binds, $fetchMode);
array_push($output, 'People whose last name is '.$binds[0]);
array_push($output, $data);

// SELECT conditional statement ex. 2 ----------------------------------------------------------------
$sql = "SELECT * FROM people WHERE last_name <> ?";
$binds = ['Smith'];

$fetchMode = '';
$data = $db->query($sql, $binds, $fetchMode);
array_push($output, 'Proeple whose last name is not '.$binds[0]);
array_push($output, $data);

// INSERT example ------------------------------------------------------------------------------------
$sql = "INSERT INTO people (first_name, last_name, email) VALUES (?, ?, ?)";
$binds = ['Bobby', 'McGee', 'bobby@somewhere.org'];
$data = $db->query($sql, $binds);

$msg = $binds[0].' '.$binds[1].' has been added to the database.';
array_push($output, $msg);
array_push($output, $data);

$sql = "SELECT * FROM people";
$binds = [];

$data = $db->query($sql, $binds);
array_push($output, 'People listing that shows the new person');
array_push($output, $data);

// UPDATE example ------------------------------------------------------------------------------------
$sql = "UPDATE people SET last_name = ? WHERE last_name = ?";
$binds = ['McGuire', 'McGee'];

$data = $db->query($sql, $binds);
array_push($output, 'Updating McGee\'s last name to '.$binds[0]);
array_push($output, $data);

$sql = "SELECT * FROM people WHERE last_name = ?";
$binds = [$binds[0]];
$data = $db->query($sql, $binds);
array_push($output, 'Updated record');
array_push($output, $data);

// DELETE example ------------------------------------------------------------------------------------
$sql = "DELETE FROM people WHERE last_name = ?";
$binds = ['McGuire'];
$data = $db->query($sql, $binds);
array_push($output, 'You know, I never much liked '.$binds[0].'. Let delete him!');
array_push($output, $data);

$sql = "SELECT * FROM people";
$binds = [];

$data = $db->query($sql, $binds);
array_push($output, 'And now we\'re back to our original list of people');
array_push($output, $data);


echo json_encode($output);