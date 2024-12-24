<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

set_time_limit(60);

$servername = "localhost";
$username = "hero";
$password = "1234";
$dbname = "networkiocl";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function ping($ip) {
    $pingResult = exec("ping -n 1 $ip", $output, $status);
    return $status === 0 ? 'Up' : 'Down';
}

function generateToken() {
    return bin2hex(random_bytes(16));
}

function sendAlertEmail($downNetworks) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ioclserver608@gmail.com';
        $mail->Password = 'oaengrsymuxdwcza'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('ioclserver608@gmail.com', 'Network Monitor');
        $mail->addAddress('pratiksngh1706@gmail.com');
        // $mail->addCC('bhushanm2@indianoil.in'); // Add CC address

        $mail->isHTML(true);
        $mail->Subject = 'Network Down Alert';

        $bodyContent = '<h1>Network Down Alert</h1>';
        $bodyContent .= '<table border="1" cellspacing="0" cellpadding="5">';
        $bodyContent .= '<tr><th>S/N</th><th>Station Router LAN IP</th><th>Service Provider</th><th>Service Provider WAN IP</th><th>Down Since</th><th>Token ID</th><th>Downtime</th></tr>';

        $index = 1;
        foreach ($downNetworks as $network) {
            foreach (['airtel', 'bsnl', 'jio', 'pgcil'] as $provider) {
                if ($network["{$provider}_status"] === 'Down' && $network["{$provider}_ip"] !== 'No Link') {
                    $downSince = new DateTime($network["{$provider}_status_since"]);
                    $now = new DateTime();
                    $interval = $now->diff($downSince);
                    $downtime = $interval->format('%d days %h hours %i minutes %s seconds');

                    $bodyContent .= '<tr>';
                    $bodyContent .= '<td>' . $index . '</td>';
                    $bodyContent .= '<td>' . $network['location'] . '</td>';
                    $bodyContent .= '<td>' . strtoupper($provider) . '</td>';
                    $bodyContent .= '<td>' . $network["{$provider}_ip"] . '</td>';
                    $bodyContent .= '<td>' . $network["{$provider}_status_since"] . '</td>';
                    $bodyContent .= '<td>' . $network['token'] . '</td>';
                    $bodyContent .= '<td>' . $downtime . '</td>';
                    $bodyContent .= '</tr>';
                    $index++;
                }
            }
        }

        $bodyContent .= '</table>';
        $mail->Body = $bodyContent;
        $mail->send();
    } catch (Exception $e) {
        error_log("Alert email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

function calculateDowntime($downSince) {
    $datetime1 = new DateTime($downSince);
    $datetime2 = new DateTime();
    $interval = $datetime1->diff($datetime2);
    return $interval->format('%d days, %h hours, %i minutes');
}

function updateDowntime($conn, $network, $provider, $downtime) {
    $stmt = $conn->prepare("UPDATE network SET {$provider}_downtime = ? WHERE id = ?");
    $stmt->bind_param("si", $downtime, $network['id']);
    $stmt->execute();
}

$networks = [
    ['location' => 'SERPL-CGD-Karimnagar Project-Construction Office', 'router_ip' => '10.194.39.1', 'airtel_ip' => '172.41.129.210', 'airtel_bandwidth' => '6', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.140.77', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => 'No Link', 'jio_bandwidth' => 'NA', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-PHPL-Visakhapatnam Project-Construction Office', 'router_ip' => '10.198.70.1', 'airtel_ip' => '172.40.218.142', 'airtel_bandwidth' => '6', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.247.61', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.196.6', 'jio_bandwidth' => '6', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-PRRPL-Korba Pump Station', 'router_ip' => '10.130.196.1', 'airtel_ip' => '172.38.11.74', 'airtel_bandwidth' => '6', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.247.213', 'bsnl_bandwidth' => '10', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.195.82', 'jio_bandwidth' => '10', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL PHDPL Balasore Pump Station', 'router_ip' => '10.101.194.1', 'airtel_ip' => '172.43.202.110', 'airtel_bandwidth' => '4', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.174.193', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => 'No Link', 'jio_bandwidth' => 'NA', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'Paradeep-Hyderabad Pipeline Project Office', 'router_ip' => '10.194.35.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.175.69', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.192.234', 'jio_bandwidth' => '6', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL PHBPL Paradip UHQ', 'router_ip' => '10.102.66.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.174.21', 'bsnl_bandwidth' => '24', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.198.74', 'jio_bandwidth' => '12', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-Bhubaneswar Old Office', 'router_ip' => '10.101.37.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.175.97', 'bsnl_bandwidth' => '4', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.199.194', 'jio_bandwidth' => '2', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-Bhubaneswar-Regional Office', 'router_ip' => '10.101.32.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.174.145', 'bsnl_bandwidth' => '48', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.198.54', 'jio_bandwidth' => '24', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL PSHPL Somnathpur Balasore', 'router_ip' => '10.101.195.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.161.214', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.199.38', 'jio_bandwidth' => '6', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-PHBPL-Balasore Pump Station', 'router_ip' => '10.101.197.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.175.61', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.199.42', 'jio_bandwidth' => '6', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-PRRPL-Jharsuguda Pump Station', 'router_ip' => '10.102.129.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.247.141', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.199.46', 'jio_bandwidth' => '6', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-PRRPL-Khunti Pump Station', 'router_ip' => '10.71.131.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.175.109', 'bsnl_bandwidth' => '10', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.194.86', 'jio_bandwidth' => '10', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-PRRPL-Sambalpur Pump Station', 'router_ip' => '10.102.35.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.247.137', 'bsnl_bandwidth' => '12', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.198.78', 'jio_bandwidth' => '12', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'PHPL Vizag Pump Station', 'router_ip' => '10.198.69.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.161.65', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => 'No Link', 'jio_bandwidth' => 'NA', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-CGD Guntur Project Office', 'router_ip' => '10.196.128.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.141.205', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => 'No Link', 'jio_bandwidth' => 'NA', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-CGD Kurnool Project Office', 'router_ip' => '10.196.130.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => '172.31.174.201', 'bsnl_bandwidth' => '6', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => 'No Link', 'jio_bandwidth' => 'NA', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL-PHPL-Berhampur Project-Construction Office', 'router_ip' => '10.102.97.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => 'No Link', 'bsnl_bandwidth' => 'NA', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.199.50', 'jio_bandwidth' => '6', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => ''],
    ['location' => 'SERPL PPL Paradip', 'router_ip' => '10.102.69.1', 'airtel_ip' => 'No Link', 'airtel_bandwidth' => 'NA', 'airtel_status' => '', 'airtel_status_since' => '', 'bsnl_ip' => 'No Link', 'bsnl_bandwidth' => 'NA', 'bsnl_status' => '', 'bsnl_status_since' => '', 'jio_ip' => '172.24.198.82', 'jio_bandwidth' => '12', 'jio_status' => '', 'jio_status_since' => '', 'pgcil_ip' => 'No Link', 'pgcil_bandwidth' => 'NA', 'pgcil_status' => '', 'pgcil_status_since' => '']
];

$downNetworks = [];
foreach ($networks as $index => &$network) {
    foreach (['airtel', 'bsnl', 'jio', 'pgcil'] as $provider) {
        $ip = $network["{$provider}_ip"];
        if ($ip !== 'No Link') {
            $status = ping($ip);
            $currentStatus = $network["{$provider}_status"];

            if ($status === 'Down' && $currentStatus !== 'Down') {
                $network["{$provider}_status"] = 'Down';
                $network["{$provider}_status_since"] = date('Y-m-d H:i:s');
                $network['token'] = generateToken();
            } elseif ($status === 'Down' && $currentStatus === 'Down') {
                // If it was already down, keep the original status since time
                $network["{$provider}_status_since"] = $network["{$provider}_status_since"];
            } elseif ($status === 'Up' && $currentStatus === 'Down') {
                // When it comes back up, update the status but retain the down since time
                $downSince = $network["{$provider}_status_since"];
                $downtime = calculateDowntime($downSince);
                updateDowntime($conn, $network, $provider, $downtime);
                $network["{$provider}_status"] = 'Up';
            } else {
                // When status is Up and remains Up, we do nothing
            }

            if ($network["{$provider}_status"] === 'Down') {
                $downNetworks[$index] = $network;
            }
        }
    }
}

// Insert/Update the network statuses in the database
foreach ($networks as $network) {
    $stmt = $conn->prepare("INSERT INTO network (location, router_ip, airtel_ip, airtel_bandwidth, airtel_status, airtel_status_since, bsnl_ip, bsnl_bandwidth, bsnl_status, bsnl_status_since, jio_ip, jio_bandwidth, jio_status, jio_status_since, pgcil_ip, pgcil_bandwidth, pgcil_status, pgcil_status_since, token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE airtel_status = VALUES(airtel_status), airtel_status_since = VALUES(airtel_status_since), bsnl_status = VALUES(bsnl_status), bsnl_status_since = VALUES(bsnl_status_since), jio_status = VALUES(jio_status), jio_status_since = VALUES(jio_status_since), pgcil_status = VALUES(pgcil_status), pgcil_status_since = VALUES(pgcil_status), token = VALUES(token)");
   
    $stmt->bind_param("sssssssssssssssssss",
        $network['location'],
        $network['router_ip'],
        $network['airtel_ip'],
        $network['airtel_bandwidth'],
        $network['airtel_status'],
        $network['airtel_status_since'],
        $network['bsnl_ip'],
        $network['bsnl_bandwidth'],
        $network['bsnl_status'],
        $network['bsnl_status_since'],
        $network['jio_ip'],
        $network['jio_bandwidth'],
        $network['jio_status'],
        $network['jio_status_since'],
        $network['pgcil_ip'],
        $network['pgcil_bandwidth'],
        $network['pgcil_status'],
        $network['pgcil_status_since'],
        $network['token']
    );
    $stmt->execute();
}

if (count($downNetworks) > 0) {
    sendAlertEmail($downNetworks);
}

// Generate the JSON output for the network status table
$output = [];
foreach ($networks as $network) {
    $output[] = [
        'Location' => $network['location'],
        'Router IP' => $network['router_ip'],
        'Service Providers' => [
            'Airtel' => ['IP' => $network['airtel_ip'], 'Status' => $network['airtel_status']],
            'BSNL' => ['IP' => $network['bsnl_ip'], 'Status' => $network['bsnl_status']],
            'Jio' => ['IP' => $network['jio_ip'], 'Status' => $network['jio_status']],
            'PGCIL' => ['IP' => $network['pgcil_ip'], 'Status' => $network['pgcil_status']],
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($output);

$conn->close();
?>
