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





//this should be delivered with AJAX
$challenge_instructions = isset($_REQUEST["challenge_instructions"]) ? $_REQUEST["challenge_instructions"] : false;

//if no data, echo challenge as JSON
if (!$challenge_instructions) {
  echo(json_encode($ds->current_challenge[0]));
  exit();
}


/**
 * Play the current challenge
 *
 */

//due to how the code is written, the human player is 
//always first in the array
// $human_player = $ds->players[0];

$human_player = $ds->players[0];

//DEV
// $challenge_instructions["teamUp"] = false;
// $challenge_instructions["teamUpWith"] = 1;

//if $challenge_instructions["teamUp"] is not false (STRICT) user 
//asked to have a companion for this challenge
if ($challenge_instructions["teamUp"] and $challenge_instructions["teamUpWith"]>0) {
  //find the companion class
  $companion = &$ds->players[$challenge_instructions["teamUpWith"]];
  //and make the opponent whichever of the three is left
  $opponent = count($ds->players) - $challenge_instructions["teamUpWith"];
  $opponent = &$ds->players[$opponent];

  //teaming up has a cost..
  $human_player->success -= 5;
  $companion->success -= 5;

  //create a new team
  $players = array();
  $players[] = New Team("Team1", $human_player, $companion);
  //then add the opponent
  $players[] = $opponent;

  //and do the challenge 
  $result = $human_player->doChallengeWithFriend($ds->current_challenge[0], $players);

  //who first etc.
  $winner = $result[0];
  $last = $result[count($result)-1];

  //if the team won or not
  if (get_class($winner) == "Team") {
    //Team winners get 9 points
    $human_player->success += 9;
    $companion->success += 9;

    //loser loses 5 points and a random tool
    $opponent->success -= 5;
    // $opponent->loseTool($ds->available_tools);
  } else {
    //Solo winners get 15 points
    $winner->success += 15;

    //losers lose 5 points and a random tool each
    $human_player->success -= 5;
    $human_player->loseTool($ds->available_tools);
    $companion->success -= 5;
    $companion->loseTool($ds->available_tools);
  }
} else {
  //PLAY CHALLENGE
  $result = $human_player->doChallenge($ds->current_challenge[0], $ds->players);

  //who first etc.
  $winner = $result[0];
  $last = $result[count($result)-1];

  //winner gets 15 points
  $winner->success += 15;

  //third lose 5 points and a random tool
  $last->success -= 5;
  $last->loseTool($ds->available_tools);
}


//data to echo back to frontend
$echo_data = array(
  "result" => $result,
  "playing" => $ds->players,
);

echo(json_encode($echo_data));