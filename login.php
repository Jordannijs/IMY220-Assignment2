<?php
	session_start();
	
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbuser";
	$mysqli = mysqli_connect($server, $username, $password, $database);
	

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	$file = isset($_POST["picToUpload"]) ? $POST["picToUpload"] : false;

	$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
	$res = $mysqli->query($query);
	$row = mysqli_fetch_array($res);
	$_SESSION["user_id"] = $row["user_id"];
	$userID = $_SESSION["user_id"];
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

	
	
	// Check if image is an actual image (Note that this method is unsafe)
	
	$directoryName = 'gallery';
	//Check if the directory already exists.
	if(!is_dir($directoryName)){
    //Directory does not exist, so lets create it.
    	mkdir($directoryName, 0755);
	}
	

	if(isset($_POST["submit"])){
		$target_dir = "gallery/";
		$uploadFile = $_FILES["picToUpload"];
		$target_file = $target_dir . basename($uploadFile["name"]);
		$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
		$check = getimagesize($uploadFile["tmp_name"]);

		if(($uploadFile["type"] == "image/jpg" || $uploadFile["type"] == "image/jpeg")&& $uploadFile["size"] < 1048576){
			move_uploaded_file($uploadFile["tmp_name"], "gallery/" . $uploadFile["name"]);
			//echo "Stored in: " . "gallery/" . $uploadFile["name"];
			$file = $uploadFile["name"];
			$query = "INSERT INTO tbgallery(user_id, filename) VALUES ('$userID','$file')";
			mysqli_query($mysqli, $query);
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Jordan Nijs">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$_SESSION["user_id"] = $row["user_id"];
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
									<input type='hidden' id='loginEmail' name='loginEmail' value='".$email."'/>
									<input type='hidden' id='loginPass' name='loginPass' value='".$pass."'/>
								</div>
							  </form>";

							  $query2 = "SELECT * FROM tbgallery WHERE user_id = '$userID' ";
							  $result = $mysqli->query($query2);
							  $row2 = mysqli_num_rows($result);

							echo '<h1> Image Gallery </h1>
							<div class="row imageGallery">';

							while($row2 = mysqli_fetch_array($result)){
								echo '<div class="col-3" style="background-image: url(gallery/' .$row2["filename"]. ')"></div>';
							}
							echo '</div>';
							  
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>