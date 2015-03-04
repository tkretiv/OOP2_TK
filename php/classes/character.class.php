<?php


class Character extends Base {
  // properties
  // make sure you declare ALL properties of the class
  protected $name;
  protected $success =50;

  protected $items = array();

  //constructor
  public function __construct($name) {
    $this->name = $name;
  }

  //greet
  public function greet() {
    return "Hi! My name is ".$this->name;
  }
  

  public function pickupRandomTool(&$items) {
    //take in $items by reference so that we work with the original
    //$ds->available_tools data and not a silly clone

    if (count($this->items) < 3) {
      //select a random tool
      $random_tool_index = rand(0, count($items)-1);
      $random_tool = $items[$random_tool_index];
      //pick it up
      $this->items[] = $random_tool;
      //and remove it from $ds->available_tools
      array_splice($items, $random_tool_index, 1);
    }
  }

  public function loseTool(&$items) {
    //take in $items by reference so that we work with the original
    //$ds->available_tools data and not a silly clone

    //if the Character has any tools.
    if (count($this->items) > 0) {
      //remove one and return the lost tool to available_tools
      //so it's available to all players.
      $items[] = array_shift($this->items);
    }
  }
  
  public function doChallenge($challenge, &$players) {
    //find the winners and return them

    return $challenge->playChallenge($players);
  }

  public function doChallengeWithFriend($challenge, &$players) {

    return $this->doChallenge($challenge, $players);
  }
}
