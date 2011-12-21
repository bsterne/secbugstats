<?php
include_once("./config.php");

function logToFile($filename, $msg){
  $fp = fopen($filename, "a");
  fwrite($fp, "[".date("Y-m-d G:i:s")."] - ".$msg."\n");
  fclose($fp);
}

function convertDate($date){
  $dateParts = split("[- ]", $date);
  return $dateParts[1] . "/" . $dateParts[2] . "/" . $dateParts[0];
}

function outputDetails(){
  $myCategory = mysql_real_escape_string($_REQUEST["category"]);
  $sql = sprintf("select * from Stats where category in ('sg_critical', 'sg_high', 'sg_moderate', 'sg_low') order by date;");
  $result = mysql_query($sql);
  //logToFile("ws-bug-count-stacked.php.log", "SQL: ".$sql);

  // keep track of when date changes so we know to start a new <stat> element
  $lastDate = $curDate = "";

  // keep track of most recent count for each category to work around a bug in
  // Flex charting where x-axis values have to line up for the stacking to work
  $lastData = array("sg_critical" => 0, "sg_high" => 0, "sg_moderate" => 0, "sg_low" => 0);
  $curData = array("sg_critical" => 0, "sg_high" => 0, "sg_moderate" => 0, "sg_low" => 0);

  print "<stats>\n";
  
  while($row = mysql_fetch_assoc($result)){
    // when the date changes, emit the data from the previous date
    if ($row["date"] != $curDate) {
      foreach($curData as $k => $v)
        $lastData[$k] = $v;
      $lastDate = $curDate;
      if($lastDate != "")
        printf("<stat><date>%s</date><sg_critical>%s</sg_critical><sg_high>%s</sg_high><sg_moderate>%s</sg_moderate><sg_low>%s</sg_low></stat>\n", convertDate($lastDate), $lastData["sg_critical"], $lastData["sg_high"], $lastData["sg_moderate"], $lastData["sg_low"]);
    
    }
    // "current" data gets stored in any case
    $curData[$row["category"]] = $row["count"];
    $curDate = $row["date"];
  }
  // emit the last data point
  printf("<stat><date>%s</date><sg_critical>%s</sg_critical><sg_high>%s</sg_high><sg_moderate>%s</sg_moderate><sg_low>%s</sg_low></stat>\n", convertDate($curDate), $curData["sg_critical"], $curData["sg_high"], $curData["sg_moderate"], $curData["sg_low"]);
  print "</stats>\n";
}


header("Content-Type: text/xml");
outputDetails();
?>
