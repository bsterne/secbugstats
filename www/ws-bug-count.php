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
      // see if we have details to display on this stat
      $sql2 = sprintf("select did from Details where sid=%s;", $row["sid"]);
      $result2 = mysql_query($sql2);
      if(mysql_num_rows($result2))
        print "<details>1</details>";
    }
    printf("<%s>%s</%s>", $row["category"], $row["count"], $row["category"]);
  }
  print "\n</stat>\n";
  print "</stats>\n";
}

//logToFile("ws-details-snapshot.php.log", print_r($_REQUEST, True));

header("Content-Type: text/xml");
outputDetails();
?>
