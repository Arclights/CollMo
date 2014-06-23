<div id="content">
	<?php
	include_once ('formatting.php');
	include_once ('Classes.php');
	session_start();
	$movie = $_SESSION['movie'];

	function getTextField($field_name, $field_content) {
		return '<input type="text" name="' . $field_name . '" value="' . $field_content . '">';
	}

	function getTextFields($field_name, $field_content) {
		$field_name = $field_name . '[]';
		$out = '';
		foreach ($field_content as $content) {
			$out = $out . getTextField($field_name, $content) . '</br>';
		}
		return $out;
	}

	function getPictureSelector($field_name, $field_content) {
		$out = '<div class="pictureSelector">';
		foreach ($field_content as $content) {
			$out = $out . '<ul><li><img src=displayImage.php?image=' . $content . '/></li><li><input type="radio" name="' . $field_name . '" value="' . $content . '"></li></ul>';
		}
		return $out.'</div>';
	}

	function getViewerRatings($ratings) {
		return '<table>' .

		# IMDB
		'<tr>' . '<td>IMDB</td><td>' . getTextField('ratingIMDB', $ratings -> imdb) . '</td>' . '</tr>' .

		# Tomato Meter
		'<tr>' . '<td>Tomato Meter</td><td>' . getTextField('ratingTomatoMeter', $ratings -> tomatoMeter) . '</td>' . '</tr>' .

		# Tomato User
		'<tr>' . '<td>Tomato User</td><td>' . getTextField('ratingTomatoUser', $ratings -> tomatoUser) . '</td>' . '</tr>' .

		# Metascore
		'<tr>' . '<td>Metascore</td><td>' . getTextField('ratingMetascore', $ratings -> metascore) . '</td>' . '</tr>' . '</table>';
	}

	function getPersonFields($field_name, $persons) {
		$out = '<table class="persontable">';
		foreach ($persons as $pers) {
			$out = $out . '<tr><td>Name' . getTextField($field_name . '_name[]', $pers -> name) . '</td><td>IMDB ID' . getTextField($field_name . '_imdbid[]', $pers -> imdbId) . '</td><td>Images' . getPictureSelector($field_name . '_image[]', $pers -> images) . '</tr>';
		}
		return $out . '</table>';
	}

	function getRoleFields($field_name, $starring) {
		$out = '<table>';
		foreach ($starring as $star) {
			$out = $out . '<tr><td>Character Name' . getTextField($field_name . '_charactername[]', $star -> character) . '</td><td>' . getTextField($field_name . '_name[]', $star -> person -> name) . '</td><td>IMDB ID' . getTextField($field_name . '_imdbid[]', $star -> person -> imdbId) . '</td><td>Images' . getPictureSelector($field_name . '_image[]', $star -> person -> images) . '</tr>';
		}
		return $out . '</table>';
	}

	echo '<form>' .

	# Title
	'<h3>Title</h3>' . getTextField('title', $movie -> name) .

	# Posters
	'<h3>Posters</h3>' . getPictureSelector('poster', $movie -> posters) .

	# Plot
	'<h3>Plot</h3>' . '<textarea rows="8" cols="50" name="plot">' . $movie -> plot . '</textarea>' .
	# IMDB ID
	'<h3>IMDB ID</h3>' . getTextField('imdbid', $movie -> imdbId) .

	# Runtime
	'<h3>Runtime</h3>' . getTextField('runtime', $movie -> runtime) . 'min' .

	# Runtime
	'<h3>Language</h3>' . getTextField('language', $movie -> language) .

	# Country
	'<h3>Country</h3>' . getTextField('country', $movie -> country) .

	# Production Companies
	'<h3>Production Companies</h3>' . getTextFields('productioncompanies', $movie -> productionCompanies) .

	# Genres
	'<h3>Genres</h3>' . getTextFields('genre', $movie -> genres) .

	# Viewer Ratings
	'<h3>Viewer Ratings</h3>' . getViewerRatings($movie -> viewerRatings) .

	# Ratings
	'<h3>Ratings</h3>' . getTextFields('rating', $movie -> ratings) .

	# Cinematorgraphers
	'<h3>Cinematographers</h3>' . getPersonFields('cinematographers', $movie -> cinematographers) .

	# Directors
	'<h3>Directors</h3>' . getPersonFields('directors', $movie -> directors) .

	# Producers
	'<h3>Producers</h3>' . getPersonFields('producers', $movie -> producers) .

	# Executive Producers
	'<h3>Executive Producers</h3>' . getPersonFields('executiveproducers', $movie -> executiveProducers) .

	# Writers
	'<h3>Writers</h3>' . getPersonFields('writers', $movie -> writers) .

	# Musicians
	'<h3>Musicians</h3>' . getPersonFields('musicians', $movie -> musicians) .

	# Starring
	'<h3>Starring</h3>' . getRoleFields('starring', $movie -> starring) .

	# Links
	'<h3>Links</h3>' . getTextFields('link', $movie -> links) .

	# Trailers
	'<h3>Trailers</h3>' . getTextFields('trailer', $movie -> trailers) .

	# End
	'</form>';
	echo $movie;
?>
</div>