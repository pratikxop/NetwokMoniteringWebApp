<?php

$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "networkiocl";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete data from network_status table on the 2nd day of each month
if (date('j') == 2) {
    $sql = "DELETE FROM network_status";
    if ($conn->query($sql) === TRUE) {
        echo "Records deleted successfully";
    } else {
        echo "Error deleting records: " . $conn->error;
    }
}

$conn->close();
?>
