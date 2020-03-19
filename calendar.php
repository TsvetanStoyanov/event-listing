
<?php
  // MONTHS
  $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

  // DEFAULT MONTH/YEAR = TODAY
  $today = strtotime("today");
  $monthNow = date("M", $today);
  $yearNow = date("Y", $today); 
 

// var_dump($_GET['date']);

?>


<!DOCTYPE html>
<html>
  <head>
    <title>Simple event listing</title>
    <script src="public/calendar_script.js"></script>
    <script src="public/jquery-3.4.1.min.js"></script>
    
    <link href="public/calendar_style.css" rel="stylesheet">
  </head>
  <body>

</script>
    <!-- [DATE SELECTOR] -->
    <div id="selector">
      <select id="month">
      <?php
        foreach ($months as $m) 
            {
            printf("<option %svalue='%s'>%s</option>", $m==$monthNow ? "selected='selected' " : "", $m, $m );
            }
      ?>
      </select>
      <select id="year">
      <?php
        // 10 years picker
        for ($y=$yearNow-10; $y<=$yearNow+10; $y++) 
            {
            printf("<option %svalue='%s'>%s</option>", $y==$yearNow ? "selected='selected' " : "", $y, $y);
            }
      ?>
      </select>
      <input type="button" value="SET" onclick="cal.list()"/>
    </div>
    
        <!-- [EVENT] -->
        <div id="event"></div>
    <!-- [CALENDAR] -->
    <div id="container"></div>
    

    <!-- <div>
      <table>
        <tr>
          <th>Date</th>
          <th>Event Description</th>
          <th>Map</th>
          <th>Url event</th>
          <th>Map locations</th>
        </tr>

      <?php 
      require __DIR__. DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "db_config.php";
      require PATH_LIB . "events.php";

          

      $list = $calLib->list_events(); 

          foreach ($list as $k => $v) 
              {
                // var_dump($v);
                // remove dashes between dates
                $replaced = str_replace('-', '', $v['date']);
              ?>
              <tr>
              <td><?php echo $v['date'] ?></td>
              <td><?php echo $v['details'] ?></td>
              <td><?php echo $v['map'] ?></td>
              <td><?php echo $v['url_event'] ?></td>
              <td>
              <?php
              if ($v['lat']) {
                ?>
                <button>
                  <a href="google_maps.php?&date=<?php echo $replaced ?>">view on map</a>
                </button>
                <?php
              }
              ?>
              </td>
              </tr>
          
              

          <?php
              }
          ?>
      </table>
    </div> -->

    
  </body>
</html>

