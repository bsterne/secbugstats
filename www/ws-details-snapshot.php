<?php
include_once("./config.php");

function logToFile($filename, $msg){
  $fp = fopen($filename, "a");
  fwrite($fp, "[".date("Y-m-d G:i:s")."] - ".$msg."\n");
  fclose($fp);
}

function outputDetails(){
  $myCategory = htmlentities(mysql_real_escape_string($_REQUEST["category"]));
  $myDate = htmlentities(mysql_real_escape_string($_REQUEST["date"]));
  $myProduct = htmlentities(mysql_real_escape_string($_REQUEST["product"]));
  if($myProduct == "")
    $sql = sprintf("select d.sid, d.date, d.product, sum(d.count) as count, sum(d.count*d.avg_age_days) as total_age_days from Details d, Stats s where d.sid=s.sid and s.category='%s' and d.date LIKE '%s%%' group by d.product order by d.date, count desc, d.product;", $myCategory, $myDate);
  else
    $sql = sprintf("select d.did, d.sid, d.date, d.product, d.component, d.count, d.bug_list, d.avg_age_days, d.med_age_days from Details d, Stats s where d.sid=s.sid and s.category='%s' and d.product='%s' and d.date LIKE '%s%%' order by d.date, d.count desc, d.product, d.component;", $myCategory, $myProduct, $myDate);
  //print $sql;
  //logToFile("ws-details-snapshot.php.log", $sql);
  $result = mysql_query($sql);
  if(!mysql_num_rows($result)){
    printf("<details category=\"%s\"></details>\n", $myCategory);
    return;
  }

  printf("<details category=\"%s\">\n", $myCategory);

  // we need to keep a temporary representation of the data so we can group
  // components together based on what's before the first colon
  if($myProduct != ""){
    $myData = array();
    while($row = mysql_fetch_assoc($result)){
      // check for product in array, create if not present
      if(!array_key_exists($myProduct, $myData))
        $myData[$myProduct] = array();
      // check for component (everything before colon) in product array
      $compParts = split(":", $row["component"]);
      $myComponent = $compParts[0];
      // create new category array
      if(!array_key_exists($myComponent, $myData[$myProduct])){
        $myData[$myProduct][$myComponent] = array("did" => $row["did"], "sid" => $row["sid"], "date" => $row["date"], "count" => $row["count"], "bug_list" => $row["bug_list"], "avg_age_days" => $row["avg_age_days"], "med_age_days" => $row["med_age_days"]);
      }
      // modify the existing category data
      else {
        // update avg_age_days
        $totalAgeDays = (int)(($myData[$myProduct][$myComponent]["count"]*$myData[$myProduct][$myComponent]["avg_age_days"])+($row["count"]*$row["avg_age_days"]));
        $totalCount = (int)($myData[$myProduct][$myComponent]["count"]+$row["count"]);
        $myData[$myProduct][$myComponent]["avg_age_days"] = round($totalAgeDays/$totalCount);
        // update did list
        $myData[$myProduct][$myComponent]["did"] .= "," . $row["did"];
        // update count
        $myData[$myProduct][$myComponent]["count"] += $row["count"];
        // update bug_list
        $myData[$myProduct][$myComponent]["bug_list"] .= "," . $row["bug_list"];
        // clear median age since we can't accurately count that
        $myData[$myProduct][$myComponent]["med_age_days"] = "";
      } 
    }
    
    //print_r($myData);

    foreach($myData as $product => $components){
      foreach($components as $component => $fields){
        printf("<detail><did>%s</did><sid>%s</sid><date>%s</date><product>%s</product><component>%s</component><count>%s</count><bug_list>%s</bug_list><avg_age_days>%s</avg_age_days><med_age_days>%s</med_age_days><total_age_days>%s</total_age_days></detail>\n", $fields["did"], $fields["sid"], $fields["date"], htmlentities($product), htmlentities($component), $fields["count"], $fields["bug_list"], $fields["avg_age_days"], $fields["med_age_days"], $fields["total_age_days"]);
      }
    }
  }

  else {
    while($row = mysql_fetch_assoc($result)){
      print "<detail>\n";
      // no group_concat in mysql --> need to emulate it
      if($myProduct == "") {
        $bugListArr = array();
        $sql2 = sprintf("select d.bug_list from Details d, Stats s where d.sid=s.sid and s.category='%s' and product='%s' and d.date LIKE '%s%%';", $myCategory, $row["product"], $myDate);
        //print $sql2;
        $result2 = mysql_query($sql2);
        while($row2 = mysql_fetch_assoc($result2))
          array_push($bugListArr, $row2["bug_list"]);
        $bugList = implode(",", $bugListArr);
      }
      else
        $bugList = $row["bug_list"];
      
      printf("<did>%s</did><sid>%s</sid><date>%s</date><product>%s</product><component>%s</component><count>%s</count><bug_list>%s</bug_list><avg_age_days>%s</avg_age_days><med_age_days>%s</med_age_days><total_age_days>%s</total_age_days>\n", $row["did"], $row["sid"], $row["date"], htmlentities($row["product"]), htmlentities($row["component"]), $row["count"], $bugList, $row["avg_age_days"], $row["med_age_days"], $row["total_age_days"]);
      print "</detail>\n";
    }
  }

  print "</details>\n";
}

//logToFile("ws-details-snapshot.php.log", print_r($_REQUEST, True));

header("Content-Type: text/xml");
outputDetails();
?>
