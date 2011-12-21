<?php
function logToFile($filename, $msg){
  $fp = fopen($filename, "a");
  fwrite($fp, "[".date("Y-m-d G:i:s")."] - ".$msg."\n");
  fclose($fp);
}

function printSwfArgs($critical, $high, $moderate, $low) {
  printf("critical=%s&high=%s&moderate=%s&low=%s", $critical, $high, $moderate, $low);
}
?>
<html>
<head>
<title>Mozilla Security Bug Totals - Weighted</title>
<link rel="stylesheet" type="text/css" href="style/style.css"/>
</head>
<body>
<h1>Mozilla Security Bug Totals - Weighted</h1>
<div id="bugCount">
  <div id="bugCountWeighted">
    <h3>Critical: 10, High: 7.5, Moderate: 4, Low: 1.5</h3>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			      id="weighted" width="100%" height="100%"
			      codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
		  <param name="movie" value="charts/weighted.swf" />
		  <param name="quality" value="high" />
		  <param name="bgcolor" value="#869ca7" />
		  <param name="allowScriptAccess" value="sameDomain" />
		  <embed src="charts/weighted.swf?<?php printSwfArgs(10, 7.5, 4, 1.5); ?>" quality="high" bgcolor="#869ca7"
				     width="100%" height="100%" name="category" align="middle"
				     play="true"
				     loop="false"
				     quality="high"
				     allowScriptAccess="sameDomain"
				     type="application/x-shockwave-flash"
				     pluginspage="http://www.adobe.com/go/getflashplayer">
		  </embed>
	  </object>
    <br/>

    <h3>Critical: 10, High: 8, Moderate: 4, Low: 2</h3>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			      id="weighted" width="100%" height="100%"
			      codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
		  <param name="movie" value="charts/weighted.swf" />
		  <param name="quality" value="high" />
		  <param name="bgcolor" value="#869ca7" />
		  <param name="allowScriptAccess" value="sameDomain" />
		  <embed src="charts/weighted.swf?<?php printSwfArgs(10, 8, 4, 2); ?>" quality="high" bgcolor="#869ca7"
				     width="100%" height="100%" name="category" align="middle"
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
</body>
</html>
