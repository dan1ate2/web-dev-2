<?php 
	$movData = getMovieSummaryData($_POST["movie-list"]);
?>
<form name="show-movie-details" id ="show-movie-details" action="admin.php" method="post">
	<label for="movie-id">Movie ID</label>
	<input type="text" name="movie-id" id="movie-id" title="Movie ID" value="<?php echo $movData[0]['movie_id'] ?>" class="grey-text" readonly><br>
	<label for="movie-title">Movie Title</label>
	<input type="text" name="movie-title" id="movie-title" title="Movie title" value="<?php echo $movData[0]['title'] ?>" class="grey-text" readonly><br>
	<label for="movie-tagline" class="textarea-margin-label">Movie Tagline</label>
	<textarea name="movie-tagline" class="textarea-margin" id="movie-tagline" title="Movie tagline" rows="6" cols="39" readonly><?php echo $movData[0]['tagline'] ?></textarea>
	<label for="movie-plot" class="textarea-margin-label">Movie Plot</label>
	<textarea name="movie-plot" class="textarea-margin" id="movie-plot" title="Movie plot" rows="6" cols="39" readonly><?php echo $movData[0]['plot'] ?></textarea>
	
	<div class="form-buttons">
		<input type="submit" name="level-3-request" value="Delete Movie">
	</div>
</form>

<?php
function getMovieSummaryData($movie) {
	include_once ("includes/connectDB.php");
	$db = getDBConnection();
	$movData;
	try {
		$sql = $db->prepare("SELECT movie_id, title, tagline, plot FROM movie WHERE movie_id = :movie");
		$sql->bindValue(':movie', intval($movie), PDO::PARAM_INT); // sanitizes data
		$sql->execute();
		$movData = $sql->fetchAll(PDO::FETCH_ASSOC);
		// print_r($movData); // debug query
	} catch (PDOException $ex) {
    	echo "Error: " . $ex->getMessage() . "<br>";
	}
	$db = null; // close db connection
	return $movData;
}