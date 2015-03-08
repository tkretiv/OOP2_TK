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
  "dbname" => "wu14oop2",
  "username" => "root",
  "password" => "mysql",
  "prefix" => "wu14oop2",
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
 
  echo(json_encode(false));
  exit();
}

/**
 * Create all three players
 *
 */
$player_class=$playerChoice["class"];
//push human player first to players property
$ds->players[] = New $playerChoice["class"]($playerChoice["name"]);

//then make two bots
$available_classes = array("gaolin", "arnold", "ivan");

for ($i=0; $i < count($available_classes); $i++) { 
  if ($available_classes[$i] != $player_class) {
    $ds->players[] = New $available_classes[$i]($available_classes[$i]);
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
      "simma" => 30,
    ),
  ),
  array(
    "description" => "Sten",
    "skills" => array(
      "kampa" => 30,
      "simma" => -10,
    ),
  ),
  array(
    "description" => "Käpp",
    "skills" => array(
      "hoppa" => 10,
      "kampa" => 5,
    ),
  ),
  array(
    "description" => "Stövlar",
    "skills" => array(
      "springa" => 20,
      "simma" => -10,
    ),
  ),
  array(
    "description" => "Rustning",
    "skills" => array(
      "kampa" => 20,
      "hoppa" => -20,
    ),
  ),
  array(
    "description" => "Hjälm",
    "skills" => array(
      "kampa" => 30,
      "springa" => -10,
    ),
  ),
  array(
    "description" => "Sabel",
    "skills" => array(
      "kampa" => 20,
    ),
  ),
  array(
    "description" => "Svärd",
    "skills" => array(
      "kampa" => 30,
      "hoppa" => -10,
    ),
  ),
  array(
    "description" => "Värja",
    "skills" => array(
      "kampa" => 10,
    ),
  ),
);


//now create tools!
for ($i=0; $i < count($tool_properties); $i++) { 
  $ds->available_tools[] = New Tool($tool_properties[$i]);
}

// $temp_tool = array();
for ($i = 0; $i < count($ds->players); $i++) {
  $person = $ds->players[$i];
    while (count($person->items) < 3) {
      $person -> pickupRandomTool($ds->available_tools);
    }
 
          }
        
/**
 * Create some Challenges
 *
 */


//all accept a description and an associative array of settings
$ds->challenges[] = new Challenge(
  "Klassisk triathlon! <br>Först ska du simma! <br>".
  "Efter ska ni löpa endast 1 mil så snabbt som möjligt. <br>".
  "Den sista är cykling. ",
  array(
    "kampa" => 10,
    "hoppa" => 10,
    "springa" => 80,
    "simma" => 90
  ),
  "img/triathlon.jpg"
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 1. Kulstötning. ",
  array(
    "kampa" => 60,
    "hoppa" => 10,
    "simma" => 0,
    "springa" => 30
  ),
  "img/kul.jpg"
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 2. Diskus. ",
  array(
    "kampa" => 60,
    "hoppa" => 30,
    "simma" => 0,
    "springa" => 20
  ),
  "img/discobol.jpg"
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 3.  Spjutkastning.",
  array(
    "kampa" => 60,
    "hoppa" => 20,
    "simma" => 0,
    "springa" => 50
  ),
  "img/spjut.jpg"
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 4. 500 meter häck. ",
  array(
    "hoppa" => 20,
    "simma" => 0,
    "springa" => 90,
    "kampa" => 10
  ),
  "img/barrier.jpg"
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 5. Längdhopp. ",
  array(
    "hoppa" => 80,
    "simma" => 0,
    "springa" => 50,
    "kampa" => 10
  ),
  "img/1.jpg"
);

$ds->challenges[] = new Challenge(
  "Mångkamp. Dag 6. Höjdhopp.",
  array(
    "hoppa" => 90,
    "simma" => 0,
    "springa" => 40,
    "kampa" => 10
  ),
  "img/hojdhopp.png"
);

$ds->challenges[] = new Challenge(
  "Styrkelyft.",
  array(
    "hoppa" => 10,
    "simma" => 0,
    "springa" => 0,
    "kampa" => 90
  ),
  "img/power_lifting.png"
);

$ds->challenges[] = new Challenge(
  "Dags att kämpa. Brottning. ",
  array(
    "kampa" => 90,
    "simma" => 0,
    "hoppa" => 10,
    "springa" => 10
  ),
  "img/kamp.jpg"
);

$ds->challenges[] = new Challenge(
  "Dags att kämpa. Kung Fu",
  array(
    "kampa" => 70,
    "hoppa" => 60,
    "springa" => 10,
    "simma" => 0
  ),
  "img/kungfu.jpg"
);

$ds->challenges[] = new Challenge(
  "Dags att kämpa. Brasiliansk jiu-jitsu.",
  array(
    "kampa" => 80,
    "hoppa" => 60,
    "springa" => 10,
    "simma" => 0
  ),
  "img/brasil.jpg"
);



//echo out everything created
$echo_arr = array(
  "players" => $ds->players,
  "tools" => $ds->available_tools,
  "challenges" => $ds->challenges,
);

echo(json_encode($echo_arr));