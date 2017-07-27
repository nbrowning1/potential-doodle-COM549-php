<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bookorama';

$searchTerm = $_POST['searchterm'];

$db = new mysqli($host, $username, $password, $database);

if (mysqli_connect_errno()) 
{
	echo "<p>Could not connect to database</p>";
}

$query = "SELECT * FROM Books WHERE Author = '$searchTerm'";
echo $query;
$stmt = $db->prepare($query);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($isbn, $author, $title,$price);

echo "<p>Number of books found:" . $stmt->num_rows . "</p>";

while($stmt->fetch())
{
	echo "<p>Title: "  . $title ."<br />";
	echo "Author: "  . $author ."<br />";
	echo "ISBN: "  . $isbn ."<br />";
	echo "Price: "  . $price ."<br />";
}
 
?>
