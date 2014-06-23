<?php
session_start();
function searchFreebase($phrase) {
	$content = file_get_contents("https://www.googleapis.com/freebase/v1/search?query=".urlencode($phrase)."&filter=(all%20type:/film/film)&limit=10&indent=true&key=AIzaSyDdmKYHIJWy28c_j_xNV1ZP8p8SkDM_hFM");
	$out = array();
	
	$results = json_decode($content, true)["result"];
	foreach($results as $result){
		$movie = array();
		$movie['id'] = $result['id'];
		$movie['name'] = $result['name'];
		$out[] = $movie;
	}
	
	return $out;
}
echo '<center><p>';
foreach(searchFreebase($_SESSION['SearchPhrase']) as $movie){
	echo "<a href=\"fetchMovieInfo.php?id=".$movie['id']."\">".$movie['name']."</a></br>";
}
?>
</p></center>