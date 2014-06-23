<?php

function getRelativePath($path){
	$path = explode('CollectorzDatabase\\', $path)[1];
	$parts = explode('\\', $path);
	$parts[count($parts)-1] = strtolower($parts[count($parts)-1]);
	return "CollectorzDatabase".join("", array_map(function($s) {return '/'.$s;}, $parts));
}

function image2base64($path) {
	$imagedata = file_get_contents($path);
	return base64_encode($imagedata);
}

function get_person($person, $tag, $xml) {
	$w = $xml->createElement($tag);
	$name = $xml->createElement('name');
	$name->appendChild($xml->createTextNode($person['full_name']));
	$w->appendChild($name);
	$imdb_url = $xml->createElement('imdburl');
	$imdb_url->appendChild($xml->createTextNode($person['imdb_url']));
	$w->appendChild($imdb_url);
	$image = $xml->createElement('image');
	if($person['image_url'] != ''){
		$image->appendChild($xml->createTextNode(image2base64($person['image_url'])));
	}
	$w->appendChild($image);
	return $w;
}

function getFileName($path) {
	$parts = explode('\\', $path);
	return strtolower($parts[count($parts)-1]);
}

function getMovieListing() {
	$db = new PDO('sqlite:MovieDatabase.db');

	$results = $db->query('select id, title, sort_title, release_year, thumbnail from movies');
	$results->setFetchMode(PDO::FETCH_ASSOC);

	$xml = new DOMDocument('1.0', 'UTF-8');
	$root = $xml->createElement('movielist');
	$xml_root = $xml->appendChild($root);

	while ($row = $results->fetch()) {
		$list_item = $xml->createElement('movielistitem');
		$root->appendChild($list_item);
		
		$id = $xml->createElement('id');
		$id_text = $xml->createTextNode($row['id']);
		$id->appendChild($id_text);
		$list_item->appendChild($id);
		
		$title = $xml->createElement('title');
		if($row['sort_title'] != ''){
			$title_text = $xml->createTextNode($row['sort_title']);
		}else {
			$title_text = $xml->createTextNode($row['title']);
		}
		$title->appendChild($title_text);
		$list_item->appendChild($title);
		
		$year = $xml->createElement('year');
		$year_text = $xml->createTextNode($row['release_year']);
		$year->appendChild($year_text);
		$list_item->appendChild($year);
		
		$thumbnail = $xml->createElement('thumbnail');
		$thumbnail_text = $xml->createTextNode(image2base64(getRelativePath($row['thumbnail'])));
		$thumbnail->appendChild($thumbnail_text);
		$list_item->appendChild($thumbnail);
	}
	
	header('Content-Type: application/xml');
	
	$xml->formatOutput = true;
	echo $xml->saveXML();
}

function getMovieDetails($id) {
	require_once('download.php');
	$db = new PDO('sqlite:MovieDatabase.db');
	
	$sth = $db->prepare('select id, title, edition_key, front_image, plot, imdb_id, runtime, barcode, release_year, country_key, language_key, medium_key, edition_release_year, owner_key, rating_key, series_key, condition_key, box_set_key, extra_features from movies where id = ?');
	$sth->execute(array($id));
	
	$movie = $sth->fetch();
	
	header('Content-Type: application/xml');

	$xml = new DOMDocument('1.0', 'UTF-8');
	$root = $xml->createElement('movie');
	$xml->appendChild($root);
	
	# Barcode
	$barcode = $xml->createElement('barcode');
	$barcode->appendChild($xml->createTextNode($movie['barcode']));
	$root->appendChild($barcode);
	
	# Title
	$title = $xml->createElement('title');
	$title->appendChild($xml->createTextNode($movie['title']));
	$root->appendChild($title);
	
	# Release Year
	$year = $xml->createElement('year');
	$year->appendChild($xml->createTextNode($movie['release_year']));
	$root->appendChild($year);
	
	# Release Year of Edition
	$edition_release_year = $xml->createElement('editionreleaseyear');
	$edition_release_year->appendChild($xml->createTextNode($movie['edition_release_year']));
	$root->appendChild($edition_release_year);
	
	# Storage Medium
	$medium = $xml->createElement('medium');
	$sth = $db->prepare('select large_image from mediums where id = ?');
	$sth->execute(array($movie['medium_key']));
	$m = $sth->fetch();
	$medium->appendChild($xml->createTextNode(getFileName($m['large_image'])));
	$root->appendChild($medium);
	
	# Owner
	$owner = $xml->createElement('owner');
	if($movie['owner_key'] != ''){
		$sth = $db->prepare('select full_name from owners where id = ?');
		$sth->execute(array($movie['owner_key']));
		$m = $sth->fetch();
		$owner->appendChild($xml->createTextNode($m['full_name']));
	}
	$root->appendChild($owner);
	
	# Condition of Case
	$condition = $xml->createElement('condition');
	if($movie['condition_key'] != ''){
		$sth = $db->prepare('select condition from conditions where id = ?');
		$sth->execute(array($movie['condition_key']));
		$m = $sth->fetch();
		$condition->appendChild($xml->createTextNode($m['condition']));
	}
	$root->appendChild($condition);
	
	# Box Set
	$boxset = $xml->createElement('boxset');
	$boxset_name = $xml->createElement('name');
	$boxset_image = $xml->createElement('image');
	$boxset_barcode = $xml->createElement('barcode');
	$boxset_year = $xml->createElement('year');
	if($movie['box_set_key'] != ''){
		$sth = $db->prepare('select box_set, front_image, barcode, release_year from box_sets where id = ?');
		$sth->execute(array($movie['box_set_key']));
		$m = $sth->fetch();
		$boxset_name->appendChild($xml->createTextNode($m['box_set']));
		$boxset_image->appendChild($xml->createTextNode(image2base64(getRelativePath($m['front_image']))));
		$boxset_barcode->appendChild($xml->createTextNode($m['barcode']));
		$boxset_year->appendChild($xml->createTextNode($m['release_year']));
	}
	$boxset->appendChild($boxset_name);
	$boxset->appendChild($boxset_image);
	$boxset->appendChild($boxset_barcode);
	$boxset->appendChild($boxset_year);
	$root->appendChild($boxset);
	
	# Viewer Rating
	$viewer_rating = $xml->createElement('viewerrating');
	$viewer_rating_rating = $xml->createElement('rating');
	$viewer_rating_image = $xml->createElement('image');
	$sth = $db->prepare('select rating, large_image from ratings where id = ?');
	$sth->execute(array($movie['rating_key']));
	$m = $sth->fetch();
	$viewer_rating_rating->appendChild($xml->createTextNode($m['rating']));
	$viewer_rating_image->appendChild($xml->createTextNode(getFileName($m['large_image'])));
	$viewer_rating->appendChild($viewer_rating_rating);
	$viewer_rating->appendChild($viewer_rating_image);
	$root->appendChild($viewer_rating);
	
	# Critics Rating
	$rating = download_ratings($movie['imdb_id']);
	$ratings = $xml->createElement('ratings');
	$imdb_rating = $xml->createElement('imdbrating');
	$imdb_rating->appendChild($xml->createTextNode($rating[0]));
	$ratings->appendChild($imdb_rating);
	$metascore = $xml->createElement('metascore');
	$metascore->appendChild($xml->createTextNode($rating[1]));
	$ratings->appendChild($metascore);
	$tomatometer = $xml->createElement('tomatometer');
	$tomatometer->appendChild($xml->createTextNode($rating[2]));
	$ratings->appendChild($tomatometer);
	$tomatousermeter = $xml->createElement('tomatousermeter');
	$tomatousermeter->appendChild($xml->createTextNode($rating[3]));
	$ratings->appendChild($tomatousermeter);
	$root->appendChild($ratings);
	
	# Poster
	$poster = $xml->createElement('poster');
	if($movie['front_image'] != ''){
		$poster->appendChild($xml->createTextNode(image2base64(getRelativePath($movie['front_image']))));
	}
	$root->appendChild($poster);
	
	# Studios
	$studios = $xml->createElement('studios');
	$sth = $db->prepare('select studio_id from has_studios where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select studio_name from studios where id = ?');
		$sth2->execute(array($s['studio_id']));
		$studio = $xml->createElement('studio');
		$studio->appendChild($xml->createTextNode($sth2->fetch()['studio_name']));
		$studios->appendChild($studio);
	}	
	$root->appendChild($studios);
	
	# Genres
	$genres = $xml->createElement('genres');
	$sth = $db->prepare('select genre_id from has_genres where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select genre from genres where id = ?');
		$sth2->execute(array($s['genre_id']));
		$genre = $xml->createElement('genre');
		$genre->appendChild($xml->createTextNode($sth2->fetch()['genre']));
		$genres->appendChild($genre);
	}	
	$root->appendChild($genres);	
	
	# Extra Features
	$extra_features = $xml->createElement('extrafeatures');
	$extra_features->appendChild($xml->createTextNode($movie['extra_features']));
	$root->appendChild($extra_features);
	
	# Country
	$country = $xml->createElement('country');
	$sth = $db->prepare('select country, large_image from countries where id = ?');
	$sth->execute(array($movie['country_key']));
	$count = $sth->fetch();
	$country_name = $xml->createElement('name');
	$country_name->appendChild($xml->createTextNode($count['country']));
	$country->appendChild($country_name);
	$country_image = $xml->createElement('image');
	$country_image->appendChild($xml->createTextNode(getFileName($count['large_image'])));
	$country->appendChild($country_image);
	$root->appendChild($country);
	
	# Languages
	$language = $xml->createElement('language');
	$sth = $db->prepare('select language, large_image from languages where id = ?');
	$sth->execute(array($movie['language_key']));
	$lang = $sth->fetch();
	$language_name = $xml->createElement('name');
	$language_name->appendChild($xml->createTextNode($lang['language']));
	$language->appendChild($language_name);
	$language_image = $xml->createElement('image');
	$language_image->appendChild($xml->createTextNode(getFileName($lang['large_image'])));
	$language->appendChild($language_image);
	$root->appendChild($language);
	
	# Runtime
	$runtime = $xml->createElement('runtime');
	$runtime->appendChild($xml->createTextNode($movie['runtime']));
	$root->appendChild($runtime);		
	
	# Crew
	$crew = $xml->createElement('crew');
	$directs = $xml->createElement('job');
	$job_name = $xml->createElement('jobname');
	$job_name->appendChild($xml->createTextNode('Director'));
	$directs->appendChild($job_name);
	$prods = $xml->createElement('job');
	$job_name = $xml->createElement('jobname');
	$job_name->appendChild($xml->createTextNode('Producer'));
	$prods->appendChild($job_name);
	$writs = $xml->createElement('job');
	$job_name = $xml->createElement('jobname');
	$job_name->appendChild($xml->createTextNode('Writer'));
	$writs->appendChild($job_name);
	$cines = $xml->createElement('job');
	$job_name = $xml->createElement('jobname');
	$job_name->appendChild($xml->createTextNode('Cinematographer'));
	$cines->appendChild($job_name);
	$musis = $xml->createElement('job');
	$job_name = $xml->createElement('jobname');
	$job_name->appendChild($xml->createTextNode('Musician'));
	$musis->appendChild($job_name);
	$sth = $db->prepare('select crew_id, position from has_crew where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select full_name, imdb_url, image_url from crew where id = ?');
		$sth2->execute(array($s['crew_id']));
		$c = $sth2->fetch();
		if($s['position'] == '49'){
			$writs->appendChild(get_person($c, 'worker', $xml));
		}elseif($s['position'] == '7') {
			$prods->appendChild(get_person($c, 'worker', $xml));
		}elseif($s['position'] == '51') {
			$musis->appendChild(get_person($c, 'worker', $xml));
		}elseif($s['position'] == '50') {
			$cines->appendChild(get_person($c, 'worker', $xml));
		}elseif($s['position'] == '8') {
			$directs->appendChild(get_person($c, 'worker', $xml));
		}
	}
	$crew->appendChild($directs);	
	$crew->appendChild($prods);
	$crew->appendChild($writs);
	$crew->appendChild($cines);
	$crew->appendChild($musis);
	$root->appendChild($crew);		
	
	# Cast
	$cast = $xml->createElement('cast');
	$sth = $db->prepare('select actor_id, role_name from roles where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select full_name, imdb_url, image_url from actors where id = ?');
		$sth2->execute(array($s['actor_id']));
		$role = $xml->createElement('role');
		$role->appendChild(get_person($sth2->fetch(), 'actor', $xml));
		$role_name = $xml->createElement('rolename');
		$role_name->appendChild($xml->createTextNode($s['role_name']));
		$role->appendChild($role_name);
		$cast->appendChild($role);
	}	
	$root->appendChild($cast);		
	
	# Plot
	$plot = $xml->createElement('plot');
	$plot->appendChild($xml->createTextNode($movie['plot']));
	$root->appendChild($plot);
	
	# Picture Formats
	$picture_formats = $xml->createElement('picture_formats');
	$sth = $db->prepare('select picture_format_id from has_picture_formats where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select picture_format from picture_formats where id = ?');
		$sth2->execute(array($s['picture_format_id']));
		$pf = $xml->createElement('picture_format');
		$pf->appendChild($xml->createTextNode($sth2->fetch()['picture_format']));
		$picture_formats->appendChild($pf);
	}	
	$root->appendChild($picture_formats);			
	
	# Subtitles
	$subtitles = $xml->createElement('subtitles');
	$sth = $db->prepare('select subtitle_id from has_subtitles where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select language from subtitles where id = ?');
		$sth2->execute(array($s['subtitle_id']));
		$pf = $xml->createElement('subtitle');
		$pf->appendChild($xml->createTextNode($sth2->fetch()['language']));
		$subtitles->appendChild($pf);
	}	
	$root->appendChild($subtitles);
	
	# Regions
	$regions = $xml->createElement('regions');
	$sth = $db->prepare('select region_id from has_regions where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select region, large_image from regions where id = ?');
		$sth2->execute(array($s['region_id']));
		$r = $sth2->fetch();
		$region = $xml->createElement('region');
		$region_name = $xml->createElement('name');
		$region_name->appendChild($xml->createTextNode($r['region']));
		$region->appendChild($region_name);
		$region_image = $xml->createElement('image');
		if($r['large_image'] != ''){
			$region_image->appendChild($xml->createTextNode(getFileName($r['large_image'])));
		}
		$region->appendChild($region_image);
		$regions->appendChild($region);
	}	
	$root->appendChild($regions);
	
	# Sound Formats
	$sound_formats = $xml->createElement('sound_formats');
	$sth = $db->prepare('select sound_format_id from has_sound_formats where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$sth2 = $db->prepare('select sound_format, large_image from sound_formats where id = ?');
		$sth2->execute(array($s['sound_format_id']));
		$r = $sth2->fetch();
		$sound_format = $xml->createElement('sound_format');
		$sound_format_name = $xml->createElement('name');
		$sound_format_name->appendChild($xml->createTextNode($r['sound_format']));
		$sound_format->appendChild($sound_format_name);
		$sound_format_image = $xml->createElement('image');
		if($r['large_image'] != ''){
			$sound_format_image->appendChild($xml->createTextNode(getFileName($r['large_image'])));
		}
		$sound_format->appendChild($sound_format_image);
		$sound_formats->appendChild($sound_format);
	}	
	$root->appendChild($sound_formats);
	
	# Links
	$links = $xml->createElement('links');
	$sth = $db->prepare('select title, url, type from links where movie_id = ?');
	$sth->execute(array($movie['id']));
	while($s = $sth->fetch()){
		$link = $xml->createElement('link');
		$link_title = $xml->createElement('title');
		$link_title->appendChild($xml->createTextNode($s['title']));
		$link->appendChild($link_title);
		$link_url = $xml->createElement('url');
		$link_url->appendChild($xml->createTextNode($s['url']));
		$link->appendChild($link_url);
		$link_type = $xml->createElement('type');
		$link_type->appendChild($xml->createTextNode($s['type']));
		$link->appendChild($link_type);
		$links->appendChild($link);
	}	
	$root->appendChild($links);
	
	$xml->formatOutput = true;
	echo $xml->saveXML();
}
?>