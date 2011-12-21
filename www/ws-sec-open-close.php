<?php
include_once("./config.php");

function logToFile($filename, $msg){
  $fp = fopen($filename, "a");
  fwrite($fp, "[".date("Y-m-d G:i:s")."] - ".$msg."\n");
  fclose($fp);
}

function convertDate($date){
  $dateParts = split("[- ]", $date);
  if ($_GET["time"] == "week")
    return $dateParts[1] . "/" . $dateParts[2] . "/" . $dateParts[0];
  else
    return $dateParts[1] . "/" . $dateParts[0];
}

function getDelta($open, $close){
  /*
  $delta = $open - $close;
  if ($delta > 0)
    return "+" . $delta;
  else return $delta;
  */
  return $open - $close;
}

function outputDetails(){
  $myCategory = mysql_real_escape_string($_REQUEST["category"]);
  
  if ($_GET["time"] == "week")
    $sql = "select * from Stats where category in ('sg_opened', 'sg_closed') and date > '2008-08-01 00:00:00' order by date;";
  else
    $sql = "select sid, category, sum(count) as count, date, extract(month from date) as mymonth, extract(year from date) as myyear from Stats where category in ('sg_opened', 'sg_closed') and date > '2008-08-01 00:00:00' group by myyear, mymonth, category order by myyear, mymonth;";
  $result = mysql_query($sql);

  // keep track of when date changes so we know to start a new <stat> element
  $curDate = "";

  //printf("<stats sql=\"%s\">\n", $sql);
  print "<stats>\n";

  // save open/close counts to display delta
  $open = $close = 0;
  
  while($row = mysql_fetch_assoc($result)){

    // debug
    //print_r($row);

    // new date -> new <stat>
    if($row["date"] != $curDate){

      if($curDate != ""){
        printf("    <delta>%s</delta>", getDelta($open, $close));
        print "\n  </stat>\n";
      }

      print "  <stat>\n";
      printf("    <date>%s</date>\n", convertDate($row["date"]));
      $curDate = $row["date"];
      
      /*
      // see if we have details to display on this stat
      $sql2 = sprintf("select did from Details where sid=%s;", $row["sid"]);
      $result2 = mysql_query($sql2);
      if(mysql_num_rows($result2))
        print "<details>1</details>";
      */
    }
    printf("    <%s>%s</%s>\n", $row["category"], $row["count"], $row["category"]);

    // store the opened and closed numbers for this data point
    if ($row["category"] == "sg_opened")
      $open = (int)$row["count"];
    if ($row["category"] == "sg_closed")
      $close = (int)$row["count"];

    //printf("<sid>%s</sid><category>%s</category><count>%s</count><date>%s</date>\n", $row["sid"], $row["category"], $row["count"], convertDate($row["date"]));
  }
  printf("    <delta>%s</delta>", getDelta($open, $close));
  print "\n  </stat>\n";
  print "</stats>\n";
}

//logToFile("ws-sec-response.php.log", print_r($_REQUEST, True));

header("Content-Type: text/xml");
outputDetails();
?>
