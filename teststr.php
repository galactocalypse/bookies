<?php
	libxml_use_internal_errors(true);
	
function get_match_string($phrase){
	$words = preg_split("/[\s,\.@\$]+/",$phrase);
	return $words;
}

function get_criteria($phrase, $field){
	$q = "(";
	$words = get_match_string($phrase);
	for($i=0;$i<count($words);$i++){
		$word=trim($words[$i]);
		if(strlen($words[$i])==0)continue;
		$q .= "((LENGTH($field) - LENGTH(REPLACE(lower($field) , lower('$word') , '')))/".strlen($word).")";
		$q .= "+";
	}
	if(substr($q,-1)=="+"){
		$q = substr($q,0, -1);
	}
	$q .= ")";
	return $q;
}

function get_search_query($phrase){
	$q1 = get_criteria($phrase, "b.url");
	$q2 = get_criteria($phrase, "b.title");
	$innerq = "SELECT URL, TITLE ,$q1 AS USUM, $q2 AS TSUM FROM bookmarks AS b";
	$q = "SELECT (USUM + TSUM) AS TOTSUM, URL, TITLE FROM(";
	$q .= $innerq;
	$q .= ") AS T HAVING TOTSUM > 0 ORDER BY TOTSUM DESC";
	return $q;
}
function search($phrase,$mysqli){
	global $mysqli;
	$q = get_search_query($phrase);
	$i = 1;
	return $mysqli->query($q);
}

$mysqli = new mysqli("localhost", "root", "", "bookmarkmanager");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
search("machine");
?>	