<?php
$myCategory = htmlentities($_GET["cat"]);
$myDate = htmlentities($_GET["date"]);
$myProduct = htmlentities($_GET["prod"]);

// FIXME - validate category and date since we're displaying them below

function logToFile($filename, $msg){
  $fp = fopen($filename, "a");
  fwrite($fp, "[".date("Y-m-d G:i:s")."] - ".$msg."\n");
  fclose($fp);
}

function convertDate($date){
  $dateParts = split("[- ]", $date);
  return $dateParts[1] . "/" . $dateParts[2] . "/" . $dateParts[0];
}

function getSubHeader($date, $category, $product){
  if (strlen($product))
    return sprintf("%s - <span id=\"catProd\">%s - %s</span>", convertDate($date), ucwords(substr($category, 3)), $product);
  else
    return sprintf("%s - <span id=\"catProd\">%s</span>", convertDate($date), ucwords(substr($category, 3)));
}

function getSwfArgs($date, $category, $product){
  if (strlen($product))
    return sprintf('cat=%s&date=%s&prod=%s', $category, $date, $product);
  else
    return sprintf('cat=%s&date=%s', $category, $date);
}
?>
<html>
<head>
<title>Platform Bug Details</title>
<link rel="stylesheet" type="text/css" href="style/style.css"/>
<script>
function updateHeader(s){
    document.getElementById("catProd").textContent = s;
}
</script>
</head>
<body>
<h1>Platform Team - Bug Details <span style="font-size:40%">(<a href="index.php">Back to Charts</a>)</span></h1>
<h2><?php print getSubHeader($myDate, $myCategory, $myProduct); ?></h2>
<div id="bugCount">
  <div id="bugPieChart">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			      id="category" width="100%" height="100%"
			      codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
		  <param name="movie" value="charts/category.swf" />
		  <param name="quality" value="high" />
		  <param name="bgcolor" value="#869ca7" />
		  <param name="allowScriptAccess" value="sameDomain" />
		  <embed src="charts/category.swf?<?php print getSwfArgs($myDate, $myCategory, $myProduct); ?>" quality="high" bgcolor="#869ca7"
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
