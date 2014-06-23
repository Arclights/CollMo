<?php
require_once('xmlBuilder.php');
if($_GET['ListMovies']) {
	getMovieListing();
}elseif($_GET['MovieDetails']) {
	getMovieDetails($_GET['MovieDetails']);
}elseif($_GET['SearchMovie']) {
	session_start();
	$_SESSION['SearchPhrase']=$_GET['SearchMovie'];
	header('Location: http://192.168.0.7/Search.php');
}elseif($_GET['GetMovie']) {
	
}
?>
