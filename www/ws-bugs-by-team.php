<?php
include_once("./config.php");

function logToFile($filename, $msg){
  $fp = fopen($filename, "a");
  fwrite($fp, "[".date("Y-m-d G:i:s")."] - ".$msg."\n");
  fclose($fp);
}

function outputStats(){
  // make sure we have a properly formatted date for the query
  if(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_REQUEST["date"]))
    $myDate = htmlentities(mysql_real_escape_string($_REQUEST["date"]));
  else {
    print "<stats/>";
    exit;
  }

  printf("<?xml version=\"1.0\" encoding=\"utf-8\" ?><stats date=\"%s\">\n", $myDate);

  // different groups to sort by (layout, content, JS, Firefox, GFX, networking)
  // "where" clauses to use in the queries below
  $myGroups = array("Layout" => "d.product='Core' AND (d.component LIKE 'layout%' OR d.component LIKE 'printing%' OR d.component IN ('Style System (CSS)'))", 
                    "JavaScript" => "d.product='Tamarin' OR (d.product='Core' AND d.component LIKE 'javascript%')",
                    "Content" => "d.product='Core' AND (d.component LIKE 'DOM%' OR d.component in ('Document Navigation','Editor','Embedding: Docshell','Event Handling','HTML: Form Submission','HTML: Parser','Java: OJI','Plug-ins','RDF','Security','Security: CAPS','Selection','Serializers','Spelling checker','String','SVG','Video/Audio','Web Services','XBL','XML','XPCOM','XPConnect','XSLT','XUL'))",
                    "GFX" => "d.product='Core' AND (d.component LIKE 'GFX%' OR d.component LIKE 'widget%' OR d.component in ('ImageLib','MathML','Graphics'))",
                    "Frontend" => "d.product='Firefox' OR d.product='Toolkit' OR (d.product='Core' AND (d.component in ('Security: UI')))",
                    "Networking" => "d.product='Core' AND d.component like 'Networking%'",
                    "Mail" => "d.product='MailNews Core' OR d.product='Thunderbird' OR (d.product='Core' AND (d.component like 'Mail%' OR d.component in ('Security: S/MIME')))",
                    );

  // take the inverse of the previous sets of bugs to generate the "Other" category
  $other = "";
  foreach ($myGroups as $group => $condition) {
    if ($condition == end($myGroups))
      $other .= "NOT (" . $condition . ")";
    else
      $other .= "NOT (" . $condition . ") AND ";
  }
  $myGroups["Other"] = $other;

  
  // different severities to display
  $mySeverities = array("sg_critical", "sg_high", "sg_moderate", "sg_low");

  foreach($myGroups as $gName => $gClause){
    print "<stat>";
    // store total number of bugs for this group
    $groupBugs = 0;
    foreach($mySeverities as $s){
      // store the count of total bugs for this (group, severity) as well as
      // combined list of bugs and total age of bugs
      $bugCount = 0;
      $bugLists = array();
      $bugAgeDays = 0;

      $sql = sprintf("select d.did, s.category, d.product, d.component, d.count, d.bug_list, d.avg_age_days from Details d, Stats s where d.sid=s.sid and d.date like '%s%%' and (%s) and s.category='%s';", $myDate, $gClause, $s);
      if($_GET["sql"] == 1)
        printf("<sql>%s</sql>", $sql);
      $result = mysql_query($sql);
      while($row = mysql_fetch_assoc($result)){
        $bugCount += $row["count"];
        $groupBugs += $row["count"];
        array_push($bugLists, $row["bug_list"]);
        $bugAgeDays += $row["count"]*$row["avg_age_days"];
      }
      // output the stats for this (group, severity)
      if (count($bugLists) == 0)
        $avgAge = 0;
      else
        $avgAge = round($bugAgeDays/$bugCount);
      
      printf("<%s avg_age_days=\"%s\" bug_list=\"%s\">%s</%s>", $s, $avgAge, implode(",", $bugLists), $bugCount, $s);
    }
    printf("<group total=\"%s\">%s</group>", $groupBugs, $gName);
    print "</stat>";
  }
  
  print "</stats>\n";
}

//logToFile("ws-bugs-by-team.php.log", print_r($_REQUEST, True));

header("Content-Type: text/xml");
outputStats();
?>
