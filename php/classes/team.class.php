<?php

class Team extends Character {
  //a members array in case we need to track who is in the team
  public $members = array();

  //give team the same skills/strengths as player classes so we don't
  //have to change any existing code (winChances, playChallenge etc)
  public $simma;
  public $hoppa;
  public $springa;
  public $kampa;
  public $items = array();

  //not using references as no player property values will be affected
  public function __construct($name, $humanPlayer, $computerPlayer) {
    $this->members[] = $humanPlayer;
    $this->members[] = $computerPlayer;

    // sum skill points of team members
    $this->simma = $humanPlayer->simma + $computerPlayer->simma;
    $this->hoppa = $humanPlayer->hoppa + $computerPlayer->hoppa;
    $this->springa = $humanPlayer->springa + $computerPlayer->springa;
    $this->kampa = $humanPlayer->kampa + $computerPlayer->kampa;

    //how to add tools to a team, assuming any player can have tools
    for ($i=0; $i < count($this->members); $i++) { 
      for ($j=0; $j < count($this->members[$i]->items); $j++) { 
        $this->items[] = $this->members[$i]->items[$j];
      }
    }

    //call the parent class (Character) __construct to set name of team
    parent::__construct($name);
  }
}