<?php
	class Person{
		public $name = "";
		public $imdbId = "";
		public $images = array();
		public function __toString()
  		{
  			echo "name: ".$this->name . '</br>';
  			echo "IMDB id: ".$this->imdbId . '</br>';
  			echo 'images: ';
			foreach($this->images as $image){
      		echo '<img src=displayImage.php?image='.$image.' alt="sample image" />';
      		}
  			echo '</br>';
  			return '';
  		}
	}
	
	class Role{
		public $character = "";
		public $person = NULL;
		public function __toString()
  		{
  			echo "character: ".$this->character . '</br>';
  			echo $this->person;
  			return '';
  		}
	}
	
	class ViewerRatings{
		public $imdb = 'N/A';
		public $tomatoMeter = 'N/A';
		public $tomatoUser	= 'N/A';
		public $metascore = 'N/A';
		
		public function __toString()
  		{	
  			echo '<p>';
  			echo 'IMDB: ' . $this->imdb . '</br>';
			echo 'Toamto Meter: ' . $this->tomatoMeter . '</br>';
			echo 'Tomato user: ' . $this->tomatoUser . '</br>';
			echo 'Metascore: ' . $this->metascore . '</br>';
			echo '</p>';
  			return '';
  		}
	}
	
	class Movie{
		public $name = "";
		public $runtime = 0;
		public $language = "";
		public $country = "";
		public $imdbId = "";
		public $plot = "";
		public $viewrRatings = NULL;
		public $posters = [];
		public $genres = [];
		public $cinematographers = [];
		public $directors = [];
		public $ratings = [];
		public $producers = [];
		public $executiveProducers = [];
		public $writers = [];
		public $musicians = [];
		public $productionCompanies = [];
		public $starring = [];
		public $links = [];
		public $trailers = [];
		
		public function __toString()
  		{
      	echo "<h3>Name</h3>";
      	echo "<p>".$this->name."</p>";
      	echo '<h3>Posters</h3>';
      	foreach($this->posters as $poster){
      		echo '<img src=displayImage.php?image='.$poster.' alt="sample image" />';
      	}
      	echo "<h3>Plot</h3>";
      	echo "<p>".$this->plot."</p>";
      	echo "<h3>IMDB ID</h3>";
      	echo "<p>".$this->imdbId."</p>";
      	echo "<h3>Runtime</h3>";
      	echo "<p>".$this->runtime."</p>";
			echo "<h3>Language</h3>";
      	echo "<p>".$this->language."</p>";
      	echo "<h3>Country</h3>";
      	echo "<p>".$this->country."</p>";
      	
			echo "<h3>Genres</h3>";
			echo "<p>";
			foreach($this->genres as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
			echo "<h3>Viewer Ratings</h3>";
      	echo $this->viewerRatings;
      	
			echo "<h3>ratings</h3>";
			echo "<p>";
			foreach($this->ratings as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
			echo "<h3>Cinematographers</h3>";
			echo "<p>";
			foreach($this->cinematographers as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
			echo "<h3>directors</h3>";
			echo "<p>";
			foreach($this->directors as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
			echo "<h3>producers</h3>";
			echo "<p>";
			foreach($this->producers as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
      	echo "<h3>Executive Producers</h3>";
			echo "<p>";
			foreach($this->executiveProducers as $ep){
      		echo $ep."</br>";
      	}
      	echo "</p>";
      	
      	echo "<h3>writers</h3>";
			echo "<p>";
			foreach($this->writers as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
			echo "<h3>Musicians</h3>";
			echo "<p>";
			foreach($this->musicians as $mus){
      		echo $mus."</br>";
      	}
      	echo "</p>";
      	
      	echo "<h3>productionCompanies</h3>";
			echo "<p>";
			foreach($this->productionCompanies as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
      	echo "<h3>starring</h3>";
			echo "<p>";
			foreach($this->starring as $genre){
      		echo $genre."</br>";
      	}
      	echo "</p>";
      	
      	echo "<h3>Links</h3>";
			echo "<p>";
			foreach($this->links as $link){
      		echo $link."</br>";
      	}
      	echo "</p>";
      	
      	echo "<h3>Trailers</h3>";
			echo "<p>";
			foreach($this->trailers as $trailer){
      		echo $trailer."</br>";
      	}
      	echo "</p>";
      	return '';
  		}
	}
?>