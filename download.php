<?php
function fix_imdb_id($imdb_id) {
	for($i = 0; $i <= 7-strlen($imdb_id); $i++) {
		$imdb_id = '0'.$imdb_id;
	}
	return 'tt'.$imdb_id;
}

function create_omdb_url($imdb_id) {
	return "http://www.omdbapi.com/?i=".fix_imdb_id($imdb_id)."&tomatoes=true&r=xml";
}

function download_ratings($imdb_id) {
	$xml = file_get_contents(create_omdb_url($imdb_id));
	$doc = new DOMDocument();
	$doc->loadXML($xml);
	foreach($doc->getElementsByTagName('movie') as $movie){
		return array($movie->getAttribute('imdbRating'),$movie->getAttribute('metascore'),$movie->getAttribute('tomatoMeter'),$movie->getAttribute('tomatoUserMeter'));
	}
	return array('N/A','N/A','N/A','N/A');
}
?>