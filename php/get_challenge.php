<?php



/**
 * "PHP används för klasser och spellogik. Spelets status sparas 
 * i en databas för varje url-anrop till ditt PHP-skript. PHP 
 * returnerar endast JSON."
 *
 */



//Nodebite black box
include_once("nodebite-swiss-army-oop.php");

//create a new instance of the DBObjectSaver class 
//and store it in the $ds variable
$ds = new DBObjectSaver(array(
  "host" => "127.0.0.1",
  "dbname" => "OOP2DB",
  "username" => "root",
  "password" => "mysql",
  "prefix" => "OOP2DB",
));

// echo(json_encode(array("challenge" => $ds->challenges[0], "index" => 0)));

/**
 * AJAX data (if user wants to change challenge)
 *
 */

//this should be delivered with AJAX, else exit()

if (!isset($_REQUEST["lastChallenge"])) {
	echo(json_encode(array("challenge" => $ds->challenges[0], "index" => 0)));
}
else {
	$last_challenge_index = isset($_REQUEST["lastChallenge"]) ? $_REQUEST["lastChallenge"] / 1 : exit();
}

/**
 * Pick a new challenge
 *
 */
if (isset($_REQUEST["refuse"])) {
	$ds->players[0]->success-= 5;
	// $last_challenge_index = isset($_REQUEST["lastChallenge"]) ? $_REQUEST["lastChallenge"] / 1 : exit();
}

//check if $last_challenge_index was last of all challenges (set to 0)
if ($last_challenge_index >= count($ds->challenges) - 1) {
  $last_challenge_index = 0; 
} else {
  $new_challenge_index = $last_challenge_index + 1;
}

//remove old challenge
unset($ds->current_challenge);

//add the new one
$ds->current_challenge[] = $ds->challenges[$new_challenge_index];

//and echo it out
echo(json_encode(array("challenge" => $ds->current_challenge[0], "index" => $new_challenge_index)));
