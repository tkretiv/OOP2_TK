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

//destroy old game data
unset($ds->players);
unset($ds->have_won);
unset($ds->have_lost);
unset($ds->current_challenge);
unset($ds->challenges);
unset($ds->available_tools);

//these should be delivered with AJAX
if (isset($_REQUEST["playerChoice"])) {
  $playerChoice = $_REQUEST["playerChoice"];
} else {
  //dev code
  $playerChoice = array(
    "name" => "aName",
    "class" => "arnold",
  );

  //real code
  //echo(json_encode(falsee));
  //exit();
}



/**
 * Create all three players
 *
 */

//push human player first to players property
$ds->players[] = New $playerChoice["class"]($playerChoice["name"]);

//then make two bots
$available_classes = array("gaolin", "arnold", "ivan");

for ($i=0; $i < count($available_classes); $i++) { 
  if ($available_classes[$i] != $player_class) {
    $ds->players[] = New $available_classes[$i]("Bot".$i);
  }
}




/**
 * Create all nine tools
 *
 */

//there are five kinds of tools
//all accept an associative array of settings
$tool_properties = array(
  array(
    "description" => "Breda",
    "skills" => array(
      "simma" => 20,
    ),
  ),
  array(
    "description" => "sten",
    "skills" => array(
      "kampa" => 30,
      "simma" => -10,
    ),
  ),
  array(
    "description" => "käpp",
    "skills" => array(
      "hoppa" => 10,
    ),
  ),
  array(
    "description" => "stövlar",
    "skills" => array(
      "springa" => 20,
      "kampa" => -10,
    ),
  ),
  array(
    "description" => "rustning",
    "skills" => array(
      "kampa" => 20,
      "hoppa" => -20,
    ),
  ),
  array(
    "description" => "hjälm",
    "skills" => array(
      "kampa" => 30,
      "springa" => -20,
    ),
  ),
  array(
    "description" => "sabel",
    "skills" => array(
      "kampa" => 20,
    ),
  ),
  array(
    "description" => "svärd",
    "skills" => array(
      "kampa" => 30,
      "hoppa" => -10,
    ),
  ),
  array(
    "description" => "värja",
    "skills" => array(
      "kampa" => 10,
    ),
  ),
);


//now create tools!
for ($i=0; $i < count($tool_properties); $i++) { 
  $ds->available_tools[] = New Tool($tool_properties[$i]);
}




/**
 * Create some Challenges
 *
 */


//all accept a description and an associative array of settings
$ds->challenges[] = new Challenge(
  "Klassisk triathlon! Först ska du Simma! ".
  "Ni ska löpa endast 1 mil så snabt som möjligt.".
  "Den sista är Cykling.",
  array(
    "kampa" => 0,
    "hoppa" => 10,
    "springa" => 80,
    "simma" => 70
  )
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 1. Först Kulstötning".
  "Nästa Diskus".
  "Och äntligen, din favoritsk: Spjutkastning.",
  array(
    "kampa" => 60,
    "hoppa" => 60,
    "simma" => 0,
    "springa" => 30
  )
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 2. 500 meter häck.".
  "Längdhopp. ".
  "Höjdhopp.",
  array(
    "hoppa" => 80,
    "simma" => 0,
    "springa" => 70,
    "kampa" => 10
  )
);

$ds->challenges[] = new Challenge(
  "Dags att kämpa. Brottning ",
  array(
    "kampa" => 90,
    "simma" => 0,
    "hoppa" => 20,
    "springa" => 10
  )
);

$ds->challenges[] = new Challenge(
  "Dags att kämpa. Kung Fu".
  "Brasiliansk jiu-jitsu.",
  array(
    "kampa" => 50,
    "hoppa" => 90,
    "springa" => 10,
    "simma" => 0
  )
);

$ds->challenges[] = new Challenge(
  "Dags att kämpa. Brasiliansk jiu-jitsu.",
  array(
    "kampa" => 80,
    "hoppa" => 60,
    "springa" => 20,
    "simma" => 0
  )
);


//and echo out the human player
echo(json_encode($ds->players[0]));





//echo out everything created
$echo_arr = array(
  "players" => $ds->players,
  "tools" => $ds->available_tools,
  "challenges" => $ds->challenges,
);

echo(json_encode($echo_arr));