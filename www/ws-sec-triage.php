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
  $sql = "select s.sid, d.did, s.category, s.count, s.date, d.bug_list from Stats s, Details d where s.sid=d.sid and s.category in ('sg_needstriage', 'sg_unconfirmed', 'sg_untouched') and s.date > '2008-08-01 00:00:00' order by date, category;";
  $result = mysql_query($sql);

  // keep track of when date changes so we know to start a new <stat> element
  $curDate = "";
  // keep track of current values of the categories so we can emit them
  // when the date changes
  $curNT = $curUC = $curUT = 0;

  // keep track of combined bug lists
  $ntBugs = $ucBugs = $utBugs = array();

  print "<stats>\n";
  //printf("<stats sql=\"%s\">\n", $sql);
  
  while($row = mysql_fetch_assoc($result)){

    // new date -> new stat
    if ($row["date"] != $curDate) {

      if ($curDate != "") {
        printf("<stat><date>%s</date><sg_needstriage bug_list=\"%s\">%s</sg_needstriage><sg_unconfirmed bug_list=\"%s\">%s</sg_unconfirmed><sg_untouched bug_list=\"%s\">%s</sg_untouched></stat>\n", convertDate($curDate), implode(",", $ntBugs), $curNT, implode(",", $ucBugs), $curUC, implode(",", $utBugs), $curUT);
        $curNT = $curUC = $curUT = 0;
        $ntBugs = $ucBugs = $utBugs = array();
      }
    }
          
    $curDate = $row["date"];
    //$curCat = $row["category"];
    //$curCount = $row["count"];
    switch ($row["category"]) {
    case "sg_needstriage":
      $curNT = $row["count"];
      array_push($ntBugs, $row["bug_list"]);
      break;
    case "sg_unconfirmed":
      $curUC = $row["count"];
      array_push($ucBugs, $row["bug_list"]);
      break;
    case "sg_untouched":
      $curUT = $row["count"];
      array_push($utBugs, $row["bug_list"]);
      break;
    }
  }
  // emit last category still queued
  printf("<stat><date>%s</date><sg_needstriage bug_list=\"%s\">%s</sg_needstriage><sg_unconfirmed>%s</sg_unconfirmed><sg_untouched>%s</sg_untouched></stat>\n", convertDate($curDate), implode(",", $ntBugs), $curNT, $curUC, $curUT);

  print "</stats>\n";
}

//logToFile("ws-sec-triage.php.log", print_r($_REQUEST, True));

header("Content-Type: text/xml");
outputDetails();
?>
