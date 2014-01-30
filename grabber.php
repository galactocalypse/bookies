<?php
$mysqli = new mysqli("localhost", "root", "", "bookmarkmanager");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
libxml_use_internal_errors(true);
ini_set('max_execution_time', 60);
$directory = "./files/";
$files = glob($directory . "*.html");
$count = 0;
foreach($files as $file){
	$dom = new DOMDocument();
	$load = $dom->loadHTMLFile($file);
	//$dom->saveHTML();
	$list = $dom->getElementsByTagName("a");
	$i=0;
	$rep=0;
	$done=0;
	foreach($list as $x){
		$q= "INSERT INTO "."bookmarks";
		$q .= "(title,url,add_date) VALUES ( \"";
		$q .= $mysqli->real_escape_string($x->nodeValue)."\",\"";
		$q .= $mysqli->real_escape_string($x->getAttribute("href"))."\",FROM_UNIXTIME(\"";
		$q .= $mysqli->real_escape_string($x->getAttribute("add_date"))."\"))";
		if($mysqli->query($q))$done++;
		else{
			$q="SELECT * FROM bookmarks WHERE URL = \"".$mysqli->real_escape_string($x->getAttribute("href"))."\"";
			$res=$mysqli->query($q);
			if(!$res)echo "Could not query other table.<br/>";
			else {
				if($res->num_rows>0){/*echo "repeated : ".$res->num_rows."<br/>";*/}
				$rep++;
			}
		}
		$i++;
	}
	if($done>0)
	echo "Executed ".$done." queries successfully for ".$file."!<br/>";
	else if($rep>0) echo "All repeated queries for ".$file."<br/>";
	else echo "Could not process ".$file."<br/>";
	$count++;
}
echo "Processed ".$count." files successfully!<br/>";

?>