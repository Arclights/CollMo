<?php
include_once ("getDataFreebase.php");
include_once ("getDataOMDB.php");
include_once ('Classes.php');
$movie = new Movie;
getFreebaseData($_GET['id'], $movie);
getOMDBdata($movie);

#var_dump($movie);
#echo $movie;
session_start();
$_SESSION['movie'] = $movie;
header('Location: http://192.168.0.7/SearchResult.php');
?>