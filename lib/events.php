<?php
class events 
  {
  // DATABASE HELPER FUNCTIONS 
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
      $str = "mysql:host=" . DB_HOST . ";charset = " . DB_CHARSET;
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
  //$data : array of data
 
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

  function fetch($sql, $cond = null, $key = null, $value = null) {
  // fetch() : perform select query
  // PARAM $sql : SQL query
  //$cond : array of conditions
  //$key : sort in this $key=>data order, optional
  //$value : $key must be provided, sort in $key=>$value order

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
  
    $sql = "SELECT * FROM `events_list` WHERE `date` = ?";
    $evt = $this->fetch($sql, [$date]);  

    $result_details = $evt[0]['details'];
    return count($evt)== 0 ? false : $result_details ;
    }

  function map($date) 
    {
    // map() : map event for the selected date
    // PARAM $date : date

    $sql = "SELECT * FROM `events_list` WHERE `date`=?";
    $evt = $this->fetch($sql, [$date]);  

    $result_map = $evt[0]['map'];
    return count($evt) == 0 ? false : $result_map;
    }
    
  function gps($date) 
    {
    // map() : map event for the selected date
    // PARAM $date : date
    $sql = "SELECT * FROM `markers` WHERE `date` = ?";
    $evt = $this->fetch($sql, [$date]);  
    
    $result_gps = $evt[0]['lat'];
    return count($evt) == 0 ? false : $result_gps;
    }


  function lng($date) 
    {
    // map() : map event for the selected date
    // PARAM $date : date
    $sql = "SELECT * FROM `markers` WHERE `date` = ?";
    $evt = $this->fetch($sql, [$date]);  
    
    $lng_result = $evt[0]['lng'];
    return count($evt) == 0 ? false : $lng_result;
    }

  function list_events() 
    {
    // list_events() : get all events descending  
    // join column `lat` from markers to `event list`
    // show only future events
    // SELECT t1.*, t2.`lat` FROM `events_list` AS t1 LEFT JOIN `markers` AS t2 ON t1.`date` = t2.`date` WHERE t1.`date` > NOW()

    
    $sql = "SELECT t1.*, t2.`lat` FROM `events_list` AS t1 LEFT JOIN `markers` AS t2 ON t1.`date` = t2.`date`";
    $evt = $this->fetch($sql);
    
    $result_list = $evt;
    return $result_list;
    }
  
  
    function test($date) 
    {
    // var_dump($date);
    // var_dump($date);
    $sql = "SELECT t1.*, t2.`lat` FROM `events_list` AS t1 LEFT JOIN `markers` AS t2 ON t1.`date` = t2.`date` WHERE MONTH(t1.`date`) = $date ";
    $evt = $this->fetch($sql);
    
    $result_test = $evt;
    // var_dump($result_test);
    return $result_test;
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

  function save($date, $details, $map, $gps, $lng) 
    {
    // save() : create/update event on specified date
    // PARAM $date : date
    //       $details : event details
  
    $sql = "REPLACE INTO `events_list` (`date`, `details`, `map`) VALUES (?, ?, ?)";
    $result[0] = $this->exec($sql, [$date, $details, $map]);

    
    // insert to columns `lat` and `lng` 
    if ($gps) 
      {
      $a = explode(', ', $gps);
      $lat = $a[0];
      $lng = $a[1];
      }
      else 
      {
      $lat = '';
      $lng = '';
      }

    // insert to markers table
    $sql2 = "REPLACE INTO `markers` (`date`, `address`, `lat`, `lng`, `type`) 
                          VALUES (?, ?, ?, ?, ?)";
    $result[1] = $this->exec($sql2, [$date, $details, $lat, $lng, $map]);
    
    return $result;
    }

  function delete($date) 
    {
    // delete() : delete event on specified date
    // PARAM $date : date
  
    $sql = "DELETE FROM `events_list` WHERE `date`= ? ";
    $result[0] = $this->exec($sql, [$date]);

    // delete markers google maps
    $sql = "DELETE FROM `markers` WHERE `date`= ? ";
    $result[1] = $this->exec($sql, [$date]);

    return $result;
    }

}
$calLib = new events();

 
?>