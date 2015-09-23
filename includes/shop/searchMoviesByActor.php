<?php
// search for movies by chosen actor
function searchMoviesByActor($actorName) {
	include_once ("includes/connectDB.php");
	$db = getDBConnection(); // db connection
	// get movies from db
	$sql = "SELECT actor.actor_name, movie_actor.movie_id, director.director_name, 
	studio.studio_name, genre.genre_name, movie.*
	FROM actor 
	INNER JOIN movie_actor ON actor.actor_id = movie_actor.actor_id 
	INNER JOIN movie ON movie_actor.movie_id = movie.movie_id 
	INNER JOIN director ON movie.director_id = director.director_id 
	INNER JOIN studio ON movie.studio_id = studio.studio_id  
	INNER JOIN genre ON movie.genre_id = genre.genre_id 
	WHERE actor_name = '$actorName' 
	ORDER BY movie.title";
	// query db for actors that match
	$stmt = $db->prepare($sql);
	$stmt->execute();
	// result
	$result = $stmt->fetchAll();
	// var_dump($result); // debug query
	// count matches, don't trigger print function if none found
	$matches = count($result);
	if (empty($matches)) {
		echo '<br><p class="error-text">No results for '.$actorName.' were found.</p><br>';
	}
	else {
		// print results
		printActorMovieResults($result);
	}
	$db = null; // close db connection
}

// print html blocks for each movie
function printActorMovieResults($queryArray) {
	$moviesToPrint = true;
	// check if a member or admin (for rent button)
	$loggedIn = false;
	if (isset($_SESSION["Username"]) || isset($_SESSION["StaffName"])) {
		$loggedIn = true;
	}
	$divColumn = 1;
	while ($moviesToPrint) {
		foreach ($queryArray as $movie) {
			switch ($divColumn) {
				case 1:
					echo '<div class="fw-box">
						<div class="one-third-box">';
						printHtml($movie, $loggedIn);
					echo '</div>';
					break;
				case 2:
					echo '<div class="one-third-box">';
					printHtml($movie, $loggedIn);
					echo '</div>';
					break;
				case 3:
					echo '<div class="one-third-box">';
					printHtml($movie, $loggedIn);
					echo '</div>
						</div>
						<div class="clear"></div>';
					break;
				default:
					break;
			}
			if ($divColumn >= 3) {
				$divColumn = 1;
			}
			else {
				$divColumn++;
			}
		} // end foreach
		$moviesToPrint = false;
	} // end while
	// check to see if any more html/closing tags needed
	if ($divColumn <= 3 && $divColumn != 1) {
		echo '</div>
			<div class="clear"></div>';
	}
} // end printMovieResultHTML()

// prints movie details in HTML
function printHtml($m, $rentButton) {
	$dvdAvail = intval($m[18]) - intval($m[19]); // dvd stock level - rented
	$blurayAvail = intval($m[22]) - intval($m[23]); // bluray stock level - rented
	
	// print details
	echo '<h3 class="orange-text">'.$m[6].'</h3>
		<img src="images/movies/'.$m[9].'" alt="'.$m[6].'" width="102" height="150" 
		class="center-align">';
	// add rent button if logged in user. attach movie id for queries
	if ($rentButton) {
		echo '<form name="movie-request" id ="shop-search" action="moviezone.php" 
			method="get">
			<div class="form-buttons">
			<input type="hidden" name="movie-id" value="'.$m[1].'">
			<input type="submit" name="movie-request" value="Rent Me">
			</div>
			</form>';
	}
	echo '<p class="l-align-txt"><span class="orange-text">Tagline: </span>'.$m[7].'</p>
		<p class="l-align-txt"><span class="orange-text">Plot: </span>'.$m[8].'</p>
		<p class="l-align-txt"><span class="orange-text">Year: </span>'.$m[15].'</p>
		<p class="l-align-txt"><span class="orange-text">Director: </span>'.$m[2].'</p>
		<p class="l-align-txt"><span class="orange-text">Studio: </span>'.$m[3].'</p>
		<p class="l-align-txt"><span class="orange-text">Genre: </span>'.$m[4].'</p>
		<p class="l-align-txt"><span class="orange-text">Classification: </span>'.$m[13].'</p>
		<p class="l-align-txt"><span class="orange-text">Rental Period: </span>'.$m[14].'</p>
		<p class="l-align-txt"><span class="orange-text">DVD Rental Price: </span>$'.$m[16].'</p>
		<p class="l-align-txt"><span class="orange-text">DVD Purchase Price: </span>$'.$m[17].'</p>
		<p class="l-align-txt"><span class="orange-text">DVD\'s Available: </span>'.$dvdAvail.'</p>
		<p class="l-align-txt"><span class="orange-text">BluRay\'s Rental Price: </span>$'.$m[20].'</p>
		<p class="l-align-txt"><span class="orange-text">BluRay\'s Purchase Price: </span>$'.$m[21].'</p>
		<p class="l-align-txt"><span class="orange-text">BluRay\'s Available: </span>'.$blurayAvail.'</p>';
} // end printHtml()