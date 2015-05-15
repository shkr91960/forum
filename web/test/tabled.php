<?php
/*$servername = "localhost";
$username = "root";
$password = "preeti";
$dbname = "forum";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// prepare and bind
$stmt = $conn->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $first, $emailid, $passwrd);

// set parameters and execute
$first = "John";
$emailid = "john@example.com";
$stmt->execute();

$firstname = "Mary";
$emailid = "mary@example.com";
$stmt->execute();

$firstname = "Julie";
$emailid = "julie@example.com";
$stmt->execute();

echo "New records created successfully";

$stmt->close();
$conn->close();*/
?>