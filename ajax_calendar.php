<?php
//  [INIT] 
require __DIR__. DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "db_config.php";
require PATH_LIB . "events.php";

//  [AJAX REQUESTS] 
switch ($_POST['req'])
    {
    default :
    echo "ERR";
    break;

    //  [SHOW CALENDAR] 
    case "list":
    // BASIC CALCULATIONS
    // Start and end of month + number of days in month
    $startMonth = sprintf("01 %s %s", $_POST['month'], $_POST['year']);
    $today = strtotime($startMonth);
    $daysInMonth = date("t", $today);
    $endMonth = sprintf("%s %s %s", $daysInMonth, $_POST['month'], $_POST['year']);

    // First and last day of month
    $firstDayOfMonth = date("N", strtotime($startMonth));
    $lastDayOfMonth = date("N", strtotime($endMonth));

    // YYYY-MM date format for later use
    $ym = date("Y-m-", $today);
    // month date format for later use
    $month = date("m", $today);
    // GET ALL EVENTS FOR SELECTED PERIOD
    $events = $calLib->get_range($ym."01", $ym.$daysInMonth);
    
    // DRAWING CALCULATION
    // This array will hold all the calendar data
    $squares = [];

    // Week start on Monday?
    $startmon = true;

    // Determine the number of blank squares before start of month
    if ($startmon && $firstDayOfMonth != 1)
        {
        for ($i=1; $i<$firstDayOfMonth; $i++)
            {
            $squares[] = "b"; 
            }
        }
    if (!$startmon && $firstDayOfMonth != 7)
        {
        for ($i=0; $i<$firstDayOfMonth; $i++)
            {
            $squares[] = "b"; 
            }
        }

    // Populate the days of the month
    for ($i=1; $i<=$daysInMonth; $i++) 
        {
        $squares[] = $i; 
        }

    // Determine the number of blank squares after end of month
    if ($startmon && $lastDayOfMonth != 7) 
        {
        $blanks = $lastDayOfMonth==6 ? 1 : 7-$lastDayOfMonth;
        for ($i=0; $i<$blanks; $i++) 
            {
            $squares[] = "b"; 
            }
        }
    if (!$startmon && $lastDayOfMonth != 6) 
        {
        $blanks = $lastDayOfMonth==7 ? 6 : 6-$lastDayOfMonth;
        for ($i=0; $i<$blanks; $i++) 
            {
            $squares[] = "b"; 
            }
        }
      // get data from class
      // $month get selected month
      $list_events = $calLib->list_events($month);
      
    ?>

    <!-- Create table  -->
    <table id="calendar">
    <?php
    $days = ["Mon", "Tue", "Wed", "Thur", "Fri", "Sat"];
    if ($startmon)
        {
        $days[] = "Sun";
        } 
    else
        {
        array_unshift($days, "Sun");
        }
    echo '<tr class="day">';
    foreach ($days as $d)
        {
        echo "<td>$d</td>"; 
        }
      echo '</tr>';
      ?>
      <tr>
      <?php
      
        $total = count($squares);
        for ($i=1; $i<=$total; $i++) 
            {
            $thisDay = $squares[$i-1];
            if ($thisDay=="b") 
                {
                echo "<td class='blank'></td>";
                }
            else
                {
                $fullDay = sprintf("%s%02u", $ym, $thisDay);
                $event_day = sprintf("%s%02u", $ym, $thisDay);
                
                printf("<td onclick=\"cal.show('%s')\">%s%s</td>", $fullDay, $thisDay, $events[$fullDay] ? "<div class='day_event'>" . $events[$fullDay] . "</div>" : "");
                
                }
            if ($i!=$total && $i%7==0) 
                { 
                echo "</tr><tr>"; 
                }
            }
      ?>
      </tr>
    </table>

    <?php 
    if ($list_events) 
        {
    ?>
    <table>
    <tr>
        <th>Date</th>
        <th>Event Description</th>
        <th>Map</th>
        <th>link</th>
        <th>Map location</th>
    </tr>
    <?php 
        foreach ($list_events as $k => $v) 
            {
            $replaced = str_replace('-', '', $v['date']);
            ?>

        <tr>
            <td><?php echo $v['date'] ?></td>
            <td><?php echo $v['details'] ?></td>
            <td><?php echo $v['map'] ?></td>
            <td><?php echo $v['link'] ?></td>
            <td>
            <?php
            if ($v['lat']) 
                {
            ?>
                <a href="google_maps.php?&date=<?php echo $replaced ?>"class="button_info small_btn_info mt-15 ml_15">view map</a>
            <?php
                }
            ?>
            </td>
        </tr>
            <?php
            }
            ?>
        </table>
    <?php
        }
    ?>
    <?php 
    break;
    /* SHOW EVENT FOR SELECTED DAY */
    case "show" :
    $evt = $calLib->get($_POST['date']);
    $map = $calLib->map($_POST['date']);
    $gps = $calLib->gps($_POST['date']);
    $lng = $calLib->lng($_POST['date']);
    $link = $calLib->link($_POST['date']);

    // get date and remove dashes for link
    $get_date = str_replace('-', '', $_POST['date']);

    ?>
</head>
<body>
    <form onsubmit="return cal.save()"> 
        <h1><?php echo ( $evt == false ) ? 'Add' : 'Edit' ; ?> Event</h1>

        <div id="date_event"><?php echo $_POST['date']?></div>
        <div class="left ml_10">
            <span>Short title</span>
            <textarea id="details_event"><?php echo ($evt==false) ? '' : $evt ;?></textarea>
        </div>
        <div class="left ml_10">
            <span>Decription</span>
            <textarea id="map_event"><?php echo ($map==false) ? '' : $map ; ?></textarea>
        </div>
        <div class="left ml_10">
            <span>GPS</span>
            <?php
            if ($gps) 
                {
            ?>
                <a href="google_maps.php?&date=<?php echo $get_date ?>" class="button_info small_btn_info mt-5 ml_10">View map</a>
            <?php
                }
            ?>
            <div class="clear"></div>
            <input id="gps_event" class="left mt_10" type="text" value="<?php echo ($gps == false) ? '' : $gps . ', ' . $lng ; ?>">
            <!-- hidden input -->
            <input  id="lng_event" style="display:none" type="text" value="<?php echo ($lng == false) ? '' : $lng ; ?>">
        </div>
        <div class="left ml_10">
            <span>Link</span>
            <div class="clear"></div>
            <input id="link_event" class="mt_10" type="text" value="<?php echo ($link==false) ? '' : $link ; ?>">
        </div>
        
        <div class="clear"></div>
        <div class="mt_10">
            <input class="button_delete" type="button" value="Delete" onclick="cal.del()"/>
            <input class="button_create" type="submit" value="Save"/>
        </div>
    </form>
  <?php

    break;

    //SAVE THE EVENT 
  case "save" :
    echo $calLib->save($_POST['date'], $_POST['details'], $_POST['map'], $_POST['link'], $_POST['gps'], $_POST['lng']) ? "OK" : "ERR" ;
    break;

    //DELETE EVENT
    case "del" :
        echo $calLib->delete($_POST['date']) ? "OK" : "ERR" ;
    break;
    }
    ?>