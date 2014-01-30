<?php
	$mysqli = new mysqli("localhost", "root" , "", "bookmarkmanager");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	libxml_use_internal_errors(true);
?>
<html>
	<head>
	</head>
	<body>
		<h2>Search my bookmarks : </h2>
		<form id="search" method="GET" action="index.php">
			Enter search term <input name="q" type="text" />
			<br/><input type="submit" />
		</form>
		<br/>
		<?php
function search_indexes($word){
			global $mysqli;
			$q="SELECT BM_ID FROM ".$mysqli->real_escape_string($word);
						
			$res = $mysqli->query($q);
			
			if($res){
			
				if($res->num_rows==0){
					echo "No reuslts for ".$_GET['q']."<br/>";
					
				}
				else{
					$i=1;
					echo "Found ".$res->num_rows." results.<br/>";
					while($row = $res->fetch_array()){
						$q="SELECT * FROM bookmarks WHERE ID=\"".$row[0]."\" LIMIT 1";
						$r=$mysqli->query($q);
						if($r){							
							$bm=$r->fetch_array();
							echo $i++." <a href=\"".$bm['URL']."\">".$bm['TITLE']."</a><br/>";
						}
						else echo "Could not retrieve result for ID $row[0].<br/>";					
					}
					$i--;
					echo "<br/>Retrieved $i entries.<br/>";
				}
				
			}
			else{
				echo "No results for ".$_GET['q']."<br/>";
			}
		}
		
		
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

		if($_GET['q']){
		
		?>
			<h2>Search Results :</h2>
			<br/>
		<?php
			$res = search($_GET['q']);
			if($res){
				echo "$res->num_rows results found:<br/><br/>";
				$i = 1;
				while($row = $res->fetch_array()){
					echo $i++.". <a href='".$row['URL']."'>".$row['TITLE']."</a><br/>";
				}
				
			}
			else echo "Your search did not return any results!<br/>";
	}			
		?>
	</body>
</html>
<?php
?>