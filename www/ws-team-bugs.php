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

  // keep track of when date changes so we know to start a new <stat> element
  $curDate = "";

  print "<stats>\n";
  
  while($row = mysql_fetch_assoc($result)){
    // new date -> new <stat>
    if($row["date"] != $curDate){
      if($curDate != "")
        print "\n</stat>\n";
      print "<stat>\n";
      printf("<date>%s</date>", convertDate($row["date"]));
      $curDate = $row["date"];
    }
    printf("<%s>%s</%s>", $row["category"], $row["count"], $row["category"]);
  }
  print "\n</stat>\n";
  print "</stats>\n";
}

header("Content-Type: text/xml");
outputDetails();
?>
