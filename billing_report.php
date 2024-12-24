<?php
$servername = "localhost";
$username = "hero";
$password = "1234";
$dbname = "networkiocl";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch total downtime for each provider
$sql = "SELECT 
        service_provider AS provider, 
        SUM(
            CAST(
                SUBSTRING_INDEX(downtime_duration, ' ', 1) AS SIGNED) * 86400 +
            CAST(
                SUBSTRING_INDEX(SUBSTRING_INDEX(downtime_duration, ' ', -2), ' ', 1) AS SIGNED) * 3600 +
            CAST(
                SUBSTRING_INDEX(downtime_duration, ' ', -1) AS SIGNED) * 60
        ) / 3600 AS total_downtime
    FROM 
        downtime 
    GROUP BY 
        service_provider";


$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'provider' => $row['provider'],
            'total_downtime' => floatval($row['total_downtime']) // Ensure total_downtime is converted to float (hours)
        ];
    }
} else {
    die("No data found in table");
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
