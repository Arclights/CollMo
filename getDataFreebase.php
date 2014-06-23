<?php
include_once('Classes.php');

function getPerson($input) {
	$out = new Person;
	$out->name = $input['name'];
	$out->imdbId = $input['/imdb/topic/name_id'][0];
	foreach($input['/common/topic/image'] as $image){
      array_push($out->images, 'https://usercontent.googleapis.com/freebase/v1/image' . $image['id']);
		#Use '?maxwidth=225&maxheight=225&mode=fillcropmid' when getting full size
	}
	return $out;
}

function getRole($input) {
	$r = new Role;
	$r->character = $input['character'];
	$r->person = getPerson($input['actor']);
	return $r;
}


function populateMovie($result, $movie) {
	$movie->name = $result['name'];
	
	foreach($result['/common/topic/image'] as $poster){
      array_push($movie->posters, 'https://usercontent.googleapis.com/freebase/v1/image' . $poster['id']);
		#Use '?maxwidth=225&maxheight=225&mode=fillcropmid' when getting full size
	}
	
	$movie->imdbId = $result['/imdb/topic/title_id'];
  
	$movie->runtime = $result['runtime'][0]['runtime'];
	
	$movie->language = str_ireplace('language', '', $result['language'][0]);
	
	$movie->country = $result['country'][0];

	foreach($result['genre'] as $genre){
		array_push($movie->genres, str_ireplace('film', '', $genre['name']));
	}
	
	foreach($result['rating'] as $rating){
		array_push($movie->ratings, preg_replace('/ (.*)/', '', $rating));
	}
	
	foreach($result['cinematography'] as $cine){
		array_push($movie->cinematographers, getPerson($cine));
	}
	
	foreach($result['directed_by'] as $dir){
		array_push($movie->directors, getPerson($dir));
	}
	
	foreach($result['produced_by'] as $prod){
		array_push($movie->producers, getPerson($prod));
	}
	
	foreach($result['executive_produced_by'] as $exprod){
		array_push($movie->executiveProducers, getPerson($exprod));
	}
	
	foreach($result['written_by'] as $writ){
		array_push($movie->writers, getPerson($writ));
	}
	
	foreach($result['music'] as $mus){
		array_push($movie->musicians, getPerson($mus));
	}
	
	foreach($result['production_companies'] as $pc){
		array_push($movie->productionCompanies, $pc);
	}
	
	foreach($result['starring'] as $star){
		array_push($movie->starring, getRole($star));
	}
	
	foreach($result['trailers'] as $trailer){
		array_push($movie->trailers, $trailer);
	}
}

function getFreebaseData($id, $movie) {
	$person = ['name' => NULL, '/common/topic/image' => [['id' => NULL, 'optional' => true]], '/imdb/topic/name_id' => []];#"{\"name\":null,\"/common/topic/image\":[{}],\"/imdb/topic/name_id\":null}";
	$actorsAndRoles = ['starring' => [['actor' => $person, 'character' => NULL]]];
	$genres = ['genre' => [['name' => NULL]]];
	$directors = ['directed_by' => [$person]];
	$executiveProducers = ['executive_produced_by' => [$person]];
	$producers = ['produced_by' => [$person]];
	$writers = ['written_by' => [$person]];
	$cinematographers = ['cinematography' => [$person]];
	$musicians = ['music'=>[$person]];
	$runtime = ['runtime' => [['runtime' => NULL]]];
	$languages = ['language' => []];
	$ratings = ['rating' => []];
	$country = ['country' => []];
	$productionCompany = ['production_companies' => []];
	$imdbId = ['/imdb/topic/title_id' => NULL];
	$poster = ['/common/topic/image' => [['id' => NULL, 'optional' => true]]];
	$trailers = ['trailers' => []];
	$query = [['id' => $id, 'type' => '/film/film', 'name' => NULL] + $languages + $runtime + $ratings + $country + $genres + $imdbId + $actorsAndRoles + $directors + $producers + $writers + $cinematographers + $productionCompany + $executiveProducers + $musicians + $poster + $trailers];
	  $service_url = 'https://www.googleapis.com/freebase/v1/mqlread';
	  $params = array(
	       'query' => json_encode($query),
	       'key' => 'AIzaSyDdmKYHIJWy28c_j_xNV1ZP8p8SkDM_hFM',
	       'indent' => 1
	  );
	  $url = $service_url . '?' . http_build_query($params);
	  #echo urldecode($url);
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  $res = curl_exec($ch);
	 # header('Content-Type: application/json');
	  #echo $res;
	  $response = json_decode($res, true);
	  curl_close($ch);
	  $result = $response['result'][0];
	  
	 populateMovie($result, $movie);
}
?>