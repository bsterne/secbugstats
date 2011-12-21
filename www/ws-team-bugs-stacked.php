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
  $myTeam = mysql_real_escape_string($_REQUEST["team"]);
  $myTeam = "content";
  $sql = sprintf("select * from Stats where category like '%s_%%' order by date;", $myTeam);
  $result = mysql_query($sql);
  //logToFile("logs/ws-team-bugs-stacked.php.log", "SQL: ".$sql);

  // keep track of when date changes so we know to start a new <stat> element
  $lastDate = $curDate = "";

  // keep track of most recent count for each category to work around a bug in
  // Flex charting where x-axis values have to line up for the stacking to work
  $lastData = array("p1" => 0, "p2" => 0, "p3" => 0, "nopriority" => 0, "p4p5" => 0, "needstriage" => 0);
  $curData = array("p1" => 0, "p2" => 0, "p3" => 0, "nopriority" => 0, "p4p5" => 0, "needstriage" => 0);

  print "<stats>\n";
  
  while($row = mysql_fetch_assoc($result)){
    // when the date changes, emit the data from the previous date
    if ($row["date"] != $curDate) {
      foreach($curData as $k => $v)
        $lastData[$k] = $v;
      $lastDate = $curDate;
      if($lastDate != "")
        printf("<stat><date>%s</date><%s_p1>%s</%s_p1><%s_p2>%s</%s_p2><%s_p3>%s</%s_p3><%s_nopriority>%s</%s_nopriority><%s_p4p5>%s</%s_p4p5><%s_needstriage>%s</%s_needstriage></stat>\n", convertDate($lastDate), $myTeam, $lastData[$myTeam."_p1"], $myTeam, $myTeam, $lastData[$myTeam."_p2"], $myTeam, $myTeam, $lastData[$myTeam."_p3"], $myTeam, $myTeam, $lastData[$myTeam."_nopriority"], $myTeam, $myTeam, $lastData[$myTeam."p4p5"], $myTeam, $myTeam, $lastData[$myTeam."needstriage"], $myTeam);
    }
    // "current" data gets stored in any case
    $curData[$row["category"]] = $row["count"];
    $curDate = $row["date"];
  }
  // emit the current data point
  printf("<stat><date>%s</date><%s_p1>%s</%s_p1><%s_p2>%s</%s_p2><%s_p3>%s</%s_p3><%s_nopriority>%s</%s_nopriority><%s_p4p5>%s</%s_p4p5><%s_needstriage>%s</%s_needstriage></stat>\n", convertDate($curDate), $myTeam, $curData[$myTeam."_p1"], $myTeam, $myTeam, $curData[$myTeam."_p2"], $myTeam, $myTeam, $curData[$myTeam."_p3"], $myTeam, $myTeam, $curData[$myTeam."_nopriority"], $myTeam, $myTeam, $curData[$myTeam."_p4p5"], $myTeam, $myTeam, $curData[$myTeam."_needstriage"], $myTeam);

  print "</stats>\n";
}


header("Content-Type: text/xml");
outputDetails();
?>
