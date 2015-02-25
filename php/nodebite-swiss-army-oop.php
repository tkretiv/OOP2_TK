<?php
/*
  The Nodebite Swiss Army Knife for OOP and database connections
  Includes an autoloader, a base class for ES6/C#-like getters
  and setters, a PDO helper and a OOP to DB-saver.

  (c)Nodebite 2014. Enjoy! Open source and for tutorial purposes.

  Thomas and Hugo
*/


/**
* 
* Nodebite: Autoloader v 1.1
* 
* A simple autoloader.
*
* Giving you a choice to store your classes in a classes folder
* or in the same folder as your main script and
* using only lowercase or the same casing as in your class name
* + giving you a choice to use underscores or hyphens in place of
* camel casing
*
*/
spl_autoload_register(function ($class) {

    $variants = array(
      $class,
      strtolower($class),
      decamelize($class),
      str_replace("_","-",decamelize($class)),
    );

    foreach($variants as $variant){
      $variants[] = "classes/".$variant;
    }

    foreach($variants as &$variant){
      $variant .= '.class.php';
      if(file_exists($variant)){
        include $variant;
        return;
      }
    }

    throw new Exception(
      "Failed to find a suitable file to include ".
      "for the class ".$class."\nGave up after trying: \n".
      implode(", ",$variants)
    );
});

// http://stackoverflow.com/questions/1993721/how-to-convert-camelcase-to-camel-case
// seelts solution
function decamelize($word) {
  return strtolower(preg_replace('/(?|([a-z\d])([A-Z])|([^\^])([A-Z][a-z]))/', '$1_$2', $word));
}


/**
 * Base class for getters and setters version 1.1
 * Nodebite 2014, Thomas Frank
 *
 *
 * This lets us create getters and setters
 * in all extended classes like
 *
 * MyClass extends Base (or another class that extends Base)
 *
 * protected $propName;
 * protected get_propName(){return $propName;}
 * protected set_propName($val){$propName = $val;}
 *
 * (but of course you should add constraints in 
 *  your get/set methods)
 */
class Base {

  public function __get($name){
    return $this->{"get_".$name}();
  }

  public function __set($name,$val){
    $this->{"set_".$name}($val);
  }

  public function __isset($name){
    return method_exists("get_".$name);
  }

}

// --------------------------------------------------------------

/**
 * PDO Helper version 1.21
 * PDO made simpler, Nodebite 2014, Thomas Frank
 * Comments by Hugo Leandersson
 *
 */
class PDOHelper {

  /**
   * function to create a new connection 
   * to a database using the PDO class
   * read more about PDO here: 
   * http://codular.com/php-pdo-how-to
   *
   */
  protected function connectToDatabase($host,$dbname,$user,$pass){
    return new PDO(
      "mysql:host=$host;dbname=$dbname",
      $user,
      $pass,

      // this FORCES MySQL to use UTF-8
      // to prevent problems with åäö charactes
      // when, for example, conterting data to JSON
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
  }

  /**
   * function to convert any numerical strings 
   * back into real numbers
   *
   */
  protected function fixNumerics($result){
    //PDO has a bad habit of returning numbers as strings
    foreach ($result as &$row) {
      foreach ($row as $key => &$val) {
        //so we fix that by converting numeric strings
        //(strings that only contain numbers) back to
        //real numbers
        if (is_numeric($val)) {
          $row[$key] = (float) $val;
        }
      }
    }
    return $result;
  }

  /**
   * function to prepare and execute a query
   * read more about prepared statements here: 
   * http://codular.com/php-pdo-how-to
   *
   */
  public function query($sql,$parameters = array()){
    // here we use prepared statements to protect 
    // ourselves from SQL-injections
    $query = $this->PDO->prepare($sql);
    $query->execute($parameters);
    // only get a result if we are making a SELECT OR SHOW
    if(stripos($sql,'SELECT') === 0 || stripos($sql,'SHOW') === 0){
      $result = $this->fixNumerics($query->fetchAll(PDO::FETCH_ASSOC));
      return $result;
    }

    // else simply return true
    return true;
  }

  /**
   * a simple method that returns query results as 
   * json (which is great for AJAX!)
   *
   */
  public function jsonQuery($sql,$parameters = array()){
    return json_encode($this->query($sql,$parameters));
  }

  /**
   * constructor
   * please note that $user and $pass are optional
   * parameters as we have given them a default value
   *
   */
  public function __construct($host,$dbname,$user="root",$pass=""){
    // create a new connection to database and stores
    // it as an object property we can use to make
    // new DB queries in other methods 
    $this->PDO = $this->connectToDatabase($host,$dbname,$user,$pass);
  }
}

// --------------------------------------------------------------

/**
 * DBObjectSaver version 1.0.1
 * Save and load class-based objects to db, 
 * Nodebite 2014, Thomas Frank
 *
 * Dependencies: PDOHelper
 *
 * NOTE: If you use this class for all your db-storage needs
 * you will never have have to call PDOHelper directly.
 *
 * NOTE: This is a helper class suitable for apps using
 * relatively small amounts of data
 * - the database WON'T be normalized and all data
 * in the automatically created tables will be loaded 
 * to PHP and rewritten to the database
 * for EACH execution of the a PHP script using this
 * class. However it simplifies your life, since
 * complex object structures can be revived with ease.
 *
 * For apps with large amounts of data - use a proper ORM
 * instead. Like http://propelorm.org/ or http://redbeanphp.com/
 * Disadvantages of these: You can not use classes and composition
 * as freely. Advantages: Much faster on big data sets,
 * generating normalized data in your database...
 *
 * /HOW TO USE/
 *
 * Initialize:
 * $ds = new DBObjectSaver(array(
 *   "host" => "127.0.0.1",
 *   "dbname" => "testdb",
 *   "username" => "root",
 *   "password" => "mysql",
 *   "prefix" => "myApp" // a prefix unique for your app/project
 * ));
 *
 * Data will automatically be loaded and stored
 * in a serialized format in the database
 *
 * Usage:
 * $ds->persons["Carl"] = new Person("Carl");
 * $ds->persons["Eve"] = new Person("Eve");
 *
 * Will create a db-table named myApp_persons
 * and save 2 serialized posts to it.
 * Note: You don't have to create the persons array explicitly.
 *
 * $ds->someNames = array("Carl","Eve","Rebecca");
 * Will create a db-table named myApp_someNames with 3 posts.
 *
 * $ds->greeting = "Yo";
 * will add a a post a new post to the db-table myApp_default
 *
 * unset($ds->someProp) 
 * will delete the db-table myApp_someProp
 * 
 * $ds->someNames = array();
 * will delete the db-table myApp_someNames (since the array is empty)
 *
*/

class DbObjectSaver {

  protected $mem = array(), $dbh, $prefix;

  protected function is_assoc($array) {
    return is_array($array) 
      && $array !== array_values($array);
  }

  protected function delete_tables(){
    $tables = $this->getTables();
    foreach($tables as $table){
      $this->dbh->query("DROP TABLE ".$table);
    }
  }

  protected function create_table($tableName){
    $this->dbh->query(
      "CREATE TABLE ".$tableName.
      " (_key VARCHAR(255) NOT NULL, _value LONGBLOB,".
      " PRIMARY KEY (_key));"
    );
  }

  protected function insert($tableName,$key,$val){
    $this->dbh->query(
      "INSERT INTO ".$tableName." VALUES (:k,:v)",
      array("k" => $key, "v" => $val)
    );
  }

  protected function getTables(){
    $result = $this->dbh->query(
      "SHOW TABLES LIKE :pre",array("pre" => $this->prefix."%")
    );
    $tables = array();
    foreach($result as $row){
      $tables[] = implode("",array_values($row));
    };
    return $tables;
  }

  public function autoSave(){
    $this->delete_tables();
    $a = $this->mem;
    foreach($a as $key => $item){
      if(is_array($item)){
        // use specific table, create multiple rows
        if(count($item)>0){
          $this->create_table($this->prefix."_".$key);
          foreach($item as $keyb => $itemb){
            $this->insert($this->prefix."_".$key,$keyb,serialize($itemb));
          }
        }
      }
      else {
        // use standard default table, single row
        $this->create_table($this->prefix."_default");
        $this->insert($this->prefix."_default",$key,serialize($item));
      }
    }
  }

  public function autoLoad(){
    $tables = $this->getTables();
    $mem = &$this->mem;
    foreach($tables as $table){
      $tableContent = $this->dbh->query(
        "SELECT * FROM ".$table." ORDER BY _key/1"
      );
      foreach($tableContent as $row){
        if($table == "default"){
          $mem[$row["_key"]] = unserialize($row["_value"]);
        }
        else {
          $tshort = substr($table,strlen($this->prefix)+1);
          if(!isset($mem[$tshort])){
            $mem[$tshort] = array();
          }
          $mem[$tshort][$row["_key"]] = unserialize($row["_value"]);
        }
      }
    }
  }

  public function __construct($arr = array()){
    // Check input
    $ok = 
      $this->is_assoc($arr) &&
      isset($arr["host"]) &&
      isset($arr["dbname"]) && 
      isset($arr["username"]) && 
      isset($arr["password"]) &&
      isset($arr["prefix"]);
    if(!$ok){
      throw new Exception(
        "To construct a new instance of DbObjectSaver ".
        "please provide an associative array ".
        "with host, dbname, username, password & prefix as keys."
      );
    }
    // Connect to db
    $this->dbh = new PDOHelper(
      $arr["host"],$arr["dbname"],$arr["username"],$arr["password"]
    );
    $this->prefix = $arr["prefix"];
    // Register function to run on PHP exit
    register_shutdown_function(array($this, 'autoSave'));
    // Autoload data from db
    $this->autoLoad();
  }

  public function &__get($name){
    // Autocreate missing props as empty arrays
    if(!isset($this->mem[$name])){
      $this->mem[$name] = array();
    }
    return $this->mem[$name];
  }

  public function __set($name,$val){
    $this->mem[$name] = $val;
  }

  public function __isset($name){
    // Since we autocreate missing props this will
    // always be true...
    return true;
  }

  public function __unset($name){
    unset($this->mem[$name]);
  }

}