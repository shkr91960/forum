<?php
$first=$_POST['first'];
$last=$_POST['last'];
$emailid=$_POST['emailid'];
$passwrd=$_POST['passwrd'];
$cpasswrd=$_POST['cpasswrd'];

$servername = "localhost";
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

$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM user");
$stmt->execute();
$res = $stmt->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);

echo "<table cellpadding=5><tr><th>Name</th><th>Email</th><th>User ID</th><th>Points</th></tr>";

echo "<tbody>"; 

foreach ($rows as $value) {
    echo "<tr><td>" . $value['Name'] . "</td><td>" . $value['Email'] . "</td><td>" . $value['Userid'] . "</td><td>" . $value['Points'] . "</td></tr>";
}

echo "</tbody>";
echo "</table>"; 

$stmt->execute();
$stmt->close();
$conn->close();
?>

</body>
</html>