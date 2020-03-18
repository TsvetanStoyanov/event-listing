<?php
class events 
  {
  /* [DATABASE HELPER FUNCTIONS] */
  protected $pdo = null;
  protected $stmt = null;
  public $error = "";
  public $lastID = null;

  function __construct() {
  // __construct() : connect to the database
  // PARAM : DB_HOST, DB_CHARSET, DB_NAME, DB_USER, DB_PASSWORD

    // ATTEMPT CONNECT
    try 
      {
      $str = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
      if (defined('DB_NAME')) 
        { 
        $str .= ";dbname=" . DB_NAME; 
        }
      $this->pdo = new PDO(
                          $str, DB_USER, DB_PASSWORD, 
                            [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false
                            ]);
      return true;
    }

    // ERROR - DO SOMETHING HERE
    // THROW ERROR MESSAGE OR SOMETHING
    catch (Exception $ex) 
      {
      print_r($ex);
      die();
      }
  }

  function __destruct() {
  // __destruct() : close connection when done

    if ($this->stmt !== null) { $this->stmt = null; }
    if ($this->pdo !== null) { $this->pdo = null; }
  }

  


  
  function exec($sql, $data=null) {
  // exec() : run insert, replace, update, delete query
  // PARAM $sql : SQL query
  //       $data : array of data
 
    try 
      {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($data);
      $this->lastID = $this->pdo->lastInsertId();
      }
    catch (Exception $ex) 
      {
      $this->error = $ex;
      return false;
      }
    $this->stmt = null;
    return true;
  }

  function fetch($sql, $cond=null, $key=null, $value=null) {
  // fetch() : perform select query
  // PARAM $sql : SQL query
  //       $cond : array of conditions
  //       $key : sort in this $key=>data order, optional
  //       $value : $key must be provided, sort in $key=>$value order

    $result = false;
    try 
      {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      if (isset($key)) 
        {
        $result = array();
        if (isset($value)) 
          {
          while ($row = $this->stmt->fetch(PDO::FETCH_NAMED)) 
            {
            $result[$row[$key]] = $row[$value];
            }
          }
        else 
          {
            while ($row = $this->stmt->fetch(PDO::FETCH_NAMED)) 
            {
            $result[$row[$key]] = $row;
            }
          }
        } 
      else 
        {
        $result = $this->stmt->fetchAll();
        }
      } 
    catch (Exception $ex) 
      {
      $this->error = $ex;
      return false;
      }
    $this->stmt = null;
    return $result;
  }

  /* [EVENT FUNCTIONS] */
  function get($date) 
    {
    // get() : get event for the selected date
    // PARAM $date : date
  
    $sql = "SELECT * FROM `events_list` WHERE `date`=?";
    $evt = $this->fetch($sql, [$date]);  
    // var_dump( count($evt));
    $result_details = $evt[0]['details'];

      $result_details = $evt[0]['details'];
      return count($evt)== 0 ? false : $result_details ;     
    }

  function map($date) 
    {
    // map() : map event for the selected date
    // PARAM $date : date
  
    $sql = "SELECT * FROM `events_list` WHERE `date`=?";
    $evt = $this->fetch($sql, [$date]);  
    // var_dump( count($evt));
    $result_details = $evt[0]['details'];

    $result_map = $evt[0]['map'];
    return count($evt)== 0 ? false : $result_map;
    }

    function list_events() 
    {
    // list_events() : get all events descending
  
    $sql = "SELECT * FROM `events_list` ORDER BY `date` DESC";
    $evt = $this->fetch($sql);  
    
    $result_list = $evt;
    return $result_list;
    }


  function getRange($start, $end) 
    {
    // getRange() : get all events in between selected date range
    // PARAM $start : start date
    //       $end : end date
  
      $sql = "SELECT * FROM `events_list` WHERE `date` BETWEEN ? AND ?";
      $evt = $this->fetch($sql, [$start, $end], "date", "details", "map");
      return count($evt)== 0 ? false : $evt ;
    }

  function save($date, $details, $map) 
    {
    // save() : create/update event on specified date
    // PARAM $date : date
    //       $details : event details
  
    $sql = "REPLACE INTO `events_list` (`date`, `details`, `map`) VALUES (?, ?, ?)";
    $result[0] = $this->exec($sql, [$date, $details, $map]);
    
    
    // insert
    $sql2 = "INSERT INTO `markers` (`name`, `address`, `lat`, `lng`, `type`)
    VALUES (?, ?, '43.709810', '24.394342', ?)";
    $result[1] = $this->exec($sql2, [$date, $details, $map]);
    
    return $result;
    }

  function delete($date) 
    {
    // delete() : delete event on specified date
    // PARAM $date : date
  
    $sql = "DELETE FROM `events_list` WHERE `date`=?";
    return $this->exec($sql, [$date]);
  }
}
$calLib = new events();
?>