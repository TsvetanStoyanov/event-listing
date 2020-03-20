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
    <!-- <script src="public/jquery-3.4.1.min.js"></script> -->
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


  </body>
</html>

