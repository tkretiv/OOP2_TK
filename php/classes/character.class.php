<?php

class Character extends Base {
  // properties
  // make sure you declare ALL properties of the class
  protected $name;
  protected $health = 100;
  protected $level = 1;
  protected $strength = 10;
  protected $vikt = 50;
  protected $hastighet =50;
  protected $skill=1;

  protected $items = array();

  //constructor
  public function __construct($name) {
    $this->name = $name;
  }

  //greet
  public function greet() {
    return "Hi! My name is ".$this->name;
  }

   public function isAlive(){
    // return a boolean
    echo ($this->name." isAlive . LEVEL : ".$this->level);
    return $this->health > 0;
  }

  public function attack($otherCharacter, $contest, $randomness =1){

    

    echo("<br>. attack to ".$otherCharacter->name."<br>");
     if(!$otherCharacter->isAlive()){
      return $this->name. " tries to attack ".$otherCharacter->name.
      " but ".$otherCharacter->name." is already dead!";
    }
    elseif (!$this->isAlive()) {
      return $this->name. " tries to attack ".$otherCharacter->name.
      " but ".$this->name." only succeeds in flopping around like a fish!";
    }

    // //ternary expressions are always thinking in terms of true or false
    // $ternaryVariable = "5" === 5 ? "Banan" : $this->greet();
    // //the ternary above is the exact same thing as the if statement below
    // if ("5" === 5) {
    //   $ternaryVariable = "Banan";
    // } else {
    //   $ternaryVariable = $this->greet();
    // }

    //solution for handling if level starts at 0
    //not very good as it treats level 0 as level 1
    // $level_diff = 1;
    // if ($otherCharacter->level !== 0 && $this->level !== 0) {
    //   $level_diff = $this->level / $otherCharacter->level;
    // }
    // elseif ($otherCharacter->level !== 0 && $this->level === 0) {
    //   $level_diff = 1 / $otherCharacter->level;
    // }
    // elseif ($otherCharacter->level === 0 && $this->level !== 0) {
    //   $level_diff = $this->level / 1;
    // }

    //solution: set initial level to 1 :D
    //making our level_diff equation super simple:
    echo ("LEVEL : ".$otherCharacter->level);

    // $level_diff = $this->level / $otherCharacter->level;

    $this -> health += $this->strength * $level_diff*$randomness;
    $otherCharacter -> health -= $this->strength * $level_diff*$randomness;
    echo ("Other health : ".$otherCharacter->health."<br>");
    if(!$otherCharacter->isAlive()){
      //increase my level by one when i kill someone
      $this->level++;
      return $this->name. " kills ".$otherCharacter->name."!";
    }
    return "<br>".$this->name. " attacks ".$otherCharacter->name."!";
  }
}