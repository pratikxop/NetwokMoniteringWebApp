<?php
require 'vendor/autoload.php';

use Google\Client;

session_start();

$client = new Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google\Service\Gmail::MAIL_GOOGLE_COM);
$client->setRedirectUri('http://localhost/Website/oauth2callback.php');
if (!isset($_GET['code'])) {
	$authUrl = $client->createAuthUrl();
	header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
	exit();
} else {
	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
	header('Location: http://localhost/Website/public/index.html'); 
	exit();
}
?>


