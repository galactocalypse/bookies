<?php
$mysqli = new mysqli("localhost", "root", "", "bookmarkmanager");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
libxml_use_internal_errors(true);


function index($words){
	foreach($words as $word){
		if($word=='')continue;
		$pword = $mysqli->real_escape_string($word);
		$q = "CREATE TABLE `$pword` (";
		$q .= "`ID` int(11) not null auto_increment,";
		$q .= "`BM_ID` int(11) not null,";
		$q .= "`TIME` datetime,";
		$q .= "PRIMARY KEY (`ID`),";
		$q .= "UNIQUE KEY (`BM_ID`)) AUTO_INCREMENT=1";
		
		
		if($mysqli->query($q)||$word == "text"){
		
			$q = "SELECT ID FROM bookmarks WHERE MATCH(TITLE, URL) AGAINST ('".$pword."')";
			echo $q;
			
			$res = $mysqli->query($q);
			
			if($res){
				echo $res->num_rows;
			
				if($res->num_rows == 0){
					echo "No matched results for $word<br/>";
				}
				else{
					
					$q = "INSERT INTO $pword (BM_ID) VALUES (\"";
					$i=0;
					while($row = $res->fetch_array()){
						
						$qq = $q.$row[0]."\")";
						
						$x = $mysqli->query($qq);
						if($x)$i++;
						else echo "Could not query database for BM_ID $row[0]<br/>";
					}
					echo "Processed $i records for $pword.<br/>";
				}
				
			}
			
		}
		else echo "Could not create table $pword<br/>";
		
		
	}
}//end of function index


$filename = "tables.txt";

$words = explode("\r\n", file_get_contents($filename));
index($words);
?>