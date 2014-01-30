<?php
$mysqli = new mysqli("localhost", "root", "", "bookmarkmanager");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
libxml_use_internal_errors(true);
ini_set('max_execution_time', 60);

$q= "SELECT * FROM bookmarks2";
$res = $mysqli->query($q);
if($res)
echo $res->num_rows;
while($arr = $res->fetch_array()){
	$q = "INSERT INTO bookmarks (title, url, add_date) VALUES (\"";
	$q .= $arr[1]."\",\"";
	$q .= $arr[2]."\",\"";
	$q .= $arr[3]."\")";
	echo $q."<br/>";
}
?>