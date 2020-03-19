<?php
// db connection
$username ="admin";
$password ="admin";
$database ="events";

function parseToXML($htmlStr)
{
$xmlStr =str_replace('<','&lt;',$htmlStr);
$xmlStr =str_replace('>','&gt;',$xmlStr);
$xmlStr =str_replace('"','&quot;',$xmlStr);
$xmlStr =str_replace("'",'&#39;',$xmlStr);
$xmlStr =str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}
// Opens a connection to a MySQL server
$connection = mysqli_connect ('localhost', $username, $password);

if (!$connection) 
  {
  die('Not connected : ' . mysqli_error());
  }

// Set the active MySQL database
$db_selected = mysqli_select_db($connection, $database);
if (!$db_selected) 
  {
  die ('Can\'t use db : ' . mysqli_error());
  }

  // replace date and remove dashes
  $sort = str_replace('-', '', $_GET['date']);

// Select all the rows in the markers table
$query = 'SELECT * FROM `markers` WHERE `date` = '. $sort .'' ;
$result = mysqli_query($connection, $query);
if (!$result) 
  {
  die('Invalid query: ' . mysqli_error());
  }

header("Content-type: text/xml");

// Start XML file, echo parent node
echo "<?xml version='1.0' ?>";
echo '<markers>';
$ind = 0;
// Iterate through the rows, printing XML nodes for each
while ($row = @mysqli_fetch_assoc($result)){
  // Add to XML document node
  echo '<marker ';
  echo 'date="' . parseToXML($_GET['date']) . '" ';
  echo 'address="' . parseToXML($row['type']) . '" ';
  echo 'lat="' . $row['lat'] . '" ';
  echo 'lng="' . $row['lng'] . '" ';
  echo 'link="' . $row['address'] . '" ';
  echo '/>';
  $ind = $ind + 1;
}

// End XML file
echo '</markers>';
?>