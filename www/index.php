<?php
$dbHost = "localhost";
$dbUser = "secbug";
$dbPass = "BugTracking**";
$dbDB = "secbug";
$link = mysql_connect($dbHost, $dbUser, $dbPass)
  or die("Could not connect to database host.");
mysql_select_db($dbDB) or die("Could not select database");

function getThisWeekDate(){
  $sql = "select distinct date from Details order by date desc limit 1;";
  $result = mysql_query($sql);
  $row = mysql_fetch_assoc($result);
  return substr($row["date"], 0, 10);
}

function getLastWeekDate(){
  $sql = "select distinct date from Details order by date desc limit 1,1;";
  $result = mysql_query($sql);
  $row = mysql_fetch_assoc($result);
  return substr($row["date"], 0, 10);
}

function printCountStats(){
  $week = strftime("%W");
  $year = strftime("%Y");
  
  $categories = array("sg_critical" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=allwordssubstr&status_whiteboard=[sg%3Acritical&keywords_type=allwords&keywords=&bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=&chfieldto=Now&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Acritical&query_based_on=sg%3Acritical&field0-0-0=noop&type0-0-0=noop&value0-0-0=", "#990000", "Critical"),
                      "sg_high" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=allwordssubstr&status_whiteboard=[sg%3Ahigh&keywords_type=allwords&keywords=&bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=&chfieldto=Now&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Ahigh&query_based_on=sg%3Ahigh&field0-0-0=noop&type0-0-0=noop&value0-0-0=", "#ba6427", "High"),
                      "sg_moderate" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=allwordssubstr&status_whiteboard=[sg%3Amoderate&keywords_type=allwords&keywords=&bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=&chfieldto=Now&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Amoderate&query_based_on=sg%3Amoderate&field0-0-0=noop&type0-0-0=noop&value0-0-0=", "#d1940c", "Moderate"),
                      "sg_low" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=allwordssubstr&status_whiteboard=[sg%3Alow&keywords_type=allwords&keywords=&bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=&chfieldto=Now&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Alow&query_based_on=sg%3Alow&field0-0-0=noop&type0-0-0=noop&value0-0-0=", "#267726", "Low"));
  
  printf("<table>\n  <tr class=\"topRow\"><td>Bug Type</td><td>As of<br>%s</td></tr>\n", date("Y-m-d", strtotime("{$year}-W{$week}-7")));
  foreach($categories as $k => $v){
    $sql = sprintf("select * from Stats where category='%s' order by date desc limit 1;", $k);
    $result = mysql_query($sql);
    if($row = mysql_fetch_assoc($result))
      printf("  <tr><td><a href=\"%s\" style=\"color:%s\">%s</a></td><td class=\"center\">%s</td></tr>\n", $v[0], $v[1], $v[2], $row["count"]);
  }
  print "</table>\n";
}

function printResponseStats(){
  $week = strftime("%W");
  $year = strftime("%Y");
  
  $categories = array("sg_opened" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=allwordssubstr&status_whiteboard=&keywords_type=allwords&keywords=&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=-1w&chfieldto=Now&chfield=[Bug+creation]&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Aopened_wk&query_based_on=sg%3Aopened_wk&field0-0-0=bug_group&type0-0-0=equals&value0-0-0=core-security&field0-0-1=status_whiteboard&type0-0-1=substring&value0-0-1=[sg%3A", "#73B301", "Opened/Wk"),
                      "sg_closed" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=allwordssubstr&status_whiteboard=&keywords_type=allwords&keywords=&resolution=FIXED&resolution=INVALID&resolution=WONTFIX&resolution=DUPLICATE&resolution=WORKSFORME&resolution=INCOMPLETE&resolution=EXPIRED&resolution=MOVED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=-1w&chfieldto=Now&chfield=resolution&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Aclosed_wk&query_based_on=sg%3Aclosed_wk&field0-0-0=bug_group&type0-0-0=equals&value0-0-0=core-security&field0-0-1=status_whiteboard&type0-0-1=substring&value0-0-1=[sg%3A", "#E6CC3C", "Closed/Wk"),
                      "sg_needstriage" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=notregexp&status_whiteboard=\[sg%3A&keywords_type=allwords&keywords=&bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=&chfieldto=Now&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Aneedstriage&query_based_on=sg%3Aneedstriage&field0-0-0=bug_group&type0-0-0=equals&value0-0-0=core-security", "#EC4832", "Needs Triage"),
                      "sg_unconfirmed" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=notregexp&status_whiteboard=sg%3Aneedinfo&keywords_type=allwords&keywords=&bug_status=UNCONFIRMED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=&chfieldto=Now&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Aunconfirmed&query_based_on=sg%3Aunconfirmed&field0-0-0=bug_group&type0-0-0=equals&value0-0-0=core-security&field0-0-1=status_whiteboard&type0-0-1=substring&value0-0-1=[sg%3A", "#400058", "Unconfirmed"),
                      "sg_untouched" => array("https://bugzilla.mozilla.org/buglist.cgi?query_format=advanced&short_desc_type=allwordssubstr&short_desc=&long_desc_type=allwordssubstr&long_desc=&bug_file_loc_type=allwordssubstr&bug_file_loc=&status_whiteboard_type=anywordssubstr&status_whiteboard=[sg%3Acritical%2C+[sg%3Ahigh%2C+[sg%3Amoderate%2C+[sg%3Alow&keywords_type=allwords&keywords=&bug_status=UNCONFIRMED&bug_status=NEW&bug_status=ASSIGNED&bug_status=REOPENED&emailassigned_to1=1&emailtype1=substring&email1=&emailassigned_to2=1&emailreporter2=1&emailqa_contact2=1&emailtype2=substring&email2=&bugidtype=include&bug_id=&votes=&chfieldfrom=&chfieldto=Now&chfieldvalue=&cmdtype=doit&order=Reuse+same+sort+as+last+time&known_name=sg%3Auntouched&query_based_on=sg%3Auntouched&field0-0-0=days_elapsed&type0-0-0=greaterthan&value0-0-0=14", "#DFD673", "Stale (14 days)"));
  
  printf("<table>\n  <tr class=\"topRow\"><td>Bug Type</td><td>As of<br>%s</td></tr>\n", date("Y-m-d", strtotime("{$year}-W{$week}-7")));
  foreach($categories as $k => $v){
    $sql = sprintf("select * from Stats where category='%s' order by date desc limit 1;", $k);
    $result = mysql_query($sql);
    if($row = mysql_fetch_assoc($result))
      printf("  <tr><td><a href=\"%s\" style=\"color:%s\">%s</a></td><td class=\"center\">%s</td></tr>\n", $v[0], $v[1], $v[2], $row["count"]);
  }
  print "</table>\n";
}
?>
<html>
<head>
<title>Mozilla Security Bug Stats</title>
<link rel="stylesheet" type="text/css" href="style/style.css"/>
<script>
function processKeyDown(e){
    if (window.event) // Them
        keynum = e.keyCode;
    else if (e.which) // Us
        keynum = e.which;
    if (keynum == 16) // shift key
        getChart("main").doPausePreview();
}
function processKeyUp(e){
    if (window.event) // Them
        keynum = e.keyCode;
    else if (e.which) // Us
        keynum = e.which;
    if (keynum == 16) // shift key
        getChart("main").doUnpausePreview();
}
function getChart(appName){
    if (navigator.appName.indexOf("Microsoft") != -1)
        return window[appName];
    else
        return document[appName];
}
</script>
</head>
<body onkeydown="processKeyDown(event)" onkeyup="processKeyUp(event)">
<h1>Mozilla Security Bug Stats <span style="font-size:40%"><a href="ws-bug-count.php" alt="XML Feed of Graph Data" />XML</a></span></h1>

<div id="bugCount">
  <a name="bugcount" /><h2>Bug Count</h2>
  <div id="bugCountQueries">
    <p>Bugzilla Queries</p> 
    <?php printCountStats(); ?>
    <br/>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            id="preview" width="100%"
            codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
      <param name="movie" value="charts/preview.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#869ca7" />
      <param name="allowScriptAccess" value="sameDomain" />
      <embed src="charts/preview.swf" quality="high" bgcolor="#869ca7"
             width="100%" name="preview" align="middle"
             play="true"
             loop="false"
             quality="high"
             allowScriptAccess="sameDomain"
             type="application/x-shockwave-flash"
             pluginspage="http://www.adobe.com/go/getflashplayer">
      </embed>
    </object>
    <br/>
    <p class="center">Press SHIFT to freeze preview.</p>
  </div>
  <div id="bugCountChart">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            id="main" width="100%" height="100%"
            codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
      <param name="movie" value="charts/main2.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#869ca7" />
      <param name="allowScriptAccess" value="sameDomain" />
      <embed src="charts/main2.swf" quality="high" bgcolor="#869ca7"
             width="100%" height="100%" name="main" align="middle"
             play="true"
             loop="false"
             quality="high"
             allowScriptAccess="sameDomain"
             type="application/x-shockwave-flash"
             pluginspage="http://www.adobe.com/go/getflashplayer">
      </embed>
    </object>
  </div>
  <div id="stackedBugCount">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            id="stacked" width="100%" height="100%"
            codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
      <param name="movie" value="charts/stacked.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#869ca7" />
      <param name="allowScriptAccess" value="sameDomain" />
      <embed src="charts/stacked.swf" quality="high" bgcolor="#869ca7"
             width="100%" height="100%" name="stacked" align="middle"
             play="true"
             loop="false"
             quality="high"
             allowScriptAccess="sameDomain"
             type="application/x-shockwave-flash"
             pluginspage="http://www.adobe.com/go/getflashplayer">
      </embed>
    </object>
  </div>
</div>

<div id="bugDist">
  <a name="distribution" /><h2>Bug Distribution</h2>
  <div id="bugDistCur">
    <p>This week:</p>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            id="distCur" width="100%" height="500px"
            codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
      <param name="movie" value="charts/team.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#869ca7" />
      <param name="allowScriptAccess" value="sameDomain" />
      <embed src="charts/team.swf?date=<?php echo getThisWeekDate();?>" quality="high" bgcolor="#869ca7"
             width="100%" height="500px" name="distCur" align="middle"
             play="true"
             loop="false"
             quality="high"
             allowScriptAccess="sameDomain"
             type="application/x-shockwave-flash"
             pluginspage="http://www.adobe.com/go/getflashplayer">
      </embed>
    </object>
  </div>
  <div id="bugDistLast">
    <p>Last week:</p>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            id="distLast" width="100%" height="500px"
            codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
      <param name="movie" value="charts/team.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#869ca7" />
      <param name="allowScriptAccess" value="sameDomain" />
      <embed src="charts/team.swf?date=<?php echo getLastWeekDate();?>" quality="high" bgcolor="#869ca7"
             width="100%" height="500px" name="distLast" align="middle"
             play="true"
             loop="false"
             quality="high"
             allowScriptAccess="sameDomain"
             type="application/x-shockwave-flash"
             pluginspage="http://www.adobe.com/go/getflashplayer">
      </embed>
    </object>
  </div>
</div>

<div id="secResponse">
  <a name="response" /><h2>Response Metrics</h2>
  <div id="secResponseQueries">
    <p>Bugzilla Queries</p> 
    <?php printResponseStats(); ?>
  </div>
  <div id="secOpenClose">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            id="openclose" width="100%" height="100%"
            codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
      <param name="movie" value="charts/openclose.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#869ca7" />
      <param name="allowScriptAccess" value="sameDomain" />
      <embed src="charts/openclose.swf" quality="high" bgcolor="#869ca7"
             width="100%" height="100%" name="openclose" align="middle"
             play="true"
             loop="false"
             quality="high"
             allowScriptAccess="sameDomain"
             type="application/x-shockwave-flash"
             pluginspage="http://www.adobe.com/go/getflashplayer">
      </embed>
    </object>
  </div>
  <div id="secActivity">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            id="triage" width="100%" height="100%"
            codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
      <param name="movie" value="charts/triage.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" value="#869ca7" />
      <param name="allowScriptAccess" value="sameDomain" />
      <embed src="charts/triage.swf" quality="high" bgcolor="#869ca7"
             width="100%" height="100%" name="triage" align="middle"
             play="true"
             loop="false"
             quality="high"
             allowScriptAccess="sameDomain"
             type="application/x-shockwave-flash"
             pluginspage="http://www.adobe.com/go/getflashplayer">
      </embed>
    </object>
  </div>
</div>
<br/>
</body>
</html>
