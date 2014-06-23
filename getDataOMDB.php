<?php

include_once('Classes.php');
function populateMovieOMDB($response, $movie) {
	$movie->plot = $response['Plot'];
	
	array_push($movie->posters, $response['Poster']);
	
	array_push($movie->links, $response['Website']);
	
	$vr = new ViewerRatings;
	$vr->imdb = $response['imdbRating'];
	$vr->tomatoMeter = $response['tomatoMeter'];
	$vr->tomatoUser = $response['tomatoUserRating'];
	$vr->metascore = $response['Metascore'];
	$movie->viewerRatings = $vr;
}

function getOMDBdata($movie) {
	$service_url = 'http://www.omdbapi.com/';
	$params = array(
	       'i' => $movie->imdbId,
	       'tomatoes' => 'true'
	);
	$url = $service_url . '?' . http_build_query($params);
	#echo urldecode($url);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($ch);
	#header('Content-Type: application/json');
	#echo $res;
	$response = json_decode($res, true);
	curl_close($ch);
	  
	populateMovieOMDB($response, $movie);
}
?>