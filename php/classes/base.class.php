<?php

class Base {

  public function __get($name){
    return $this->{"get_".$name}();
  }

  public function __set($name,$val){
    $this->{"set_".$name}($val);
  }

}