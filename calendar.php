
<?php
  // MONTHS
  $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

  // DEFAULT MONTH/YEAR = TODAY
  $today = strtotime("today");
  $monthNow = date("M", $today);
  $yearNow = date("Y", $today); 
?>


<!DOCTYPE html>
<html>
  <head>
    <title>Simple event listing</title>
    <script src="public/calendar_script.js"></script>
    
    <link href="public/calendar_style.css" rel="stylesheet">
  </head>
  <body>
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

    <!-- [CALENDAR] -->
    <div id="container"></div>

    <!-- [EVENT] -->
    <div id="event"></div>

  </body>


<!-- table list all events descending -->
<div style="width: 50%">
<table>
  <tr>
    <th>Date</th>
    <th>Details</th>
    <th>Map</th>
    <th>Url event</th>
    <th>Integration</th>
  </tr>

<?php 
require __DIR__. DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "db_config.php";
require PATH_LIB . "events.php";

$list = $calLib->list_events(); 

    foreach ($list as $k => $v) 
        {
        ?>
        <tr>
        <td><?php echo $v['date'] ?></td>
        <td><?php echo $v['details'] ?></td>
        <td><?php echo $v['map'] ?></td>
        <td><?php echo $v['url_event'] ?></td>
        <td><button>add to calendar</button></td>
        </tr>
    
        

    <?php
        }
    ?>




</table>


</div>
</html>

