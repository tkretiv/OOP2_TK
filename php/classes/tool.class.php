<?php

class Tool extends Base {
  public $description;
  public $skills;

  public function __construct($tool_properties){
    $this->description = $tool_properties["description"];
    $this->skills = $tool_properties["skills"];
  }
}