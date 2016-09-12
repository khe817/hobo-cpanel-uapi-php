<?php
// --- general settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . 'class.cpanel_uapi.php';

$domain_name = 'example.com';
$ssl_cert = '-----BEGIN CERTIFICATE-----
MIIFTzCCBDegAwIBAgIRAO4kMx99Ec3QGG6PEyAkwKEwDQYJKoZIhvcNAQELBQAw
----------------------------------------------------------------
CaqWgkpQYgbkYk8xBAF3QTf0YQ==
-----END CERTIFICATE-----';
$ssl_key = '-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEAzF+BpBWiOxBPCKhnsRstsamSdFF4VTlSQQX77YX1+oHM9tGj
----------------------------------------------------------------
dK3Hu4xxpWYsSSBNqsHLSoqsMsuRy6J0N6HPqieNWcCMqKb+/9pwRCI=
-----END RSA PRIVATE KEY-----';

$host_IP_address = '1.0.0.127';
$cpanel_user = 'user';
$cpanel_pass = 'pass';
// initialize
$cpanel_uapi = new CPanel_UAPI($host_IP_address, $cpanel_user, $cpanel_pass);

try {
	$module = 'Mysql';
	$function = 'get_privileges_on_database';
	$params = array(
		'user'     => 'dbuser',
		'database' => 'mydb',
		);
	// get list of privileges on the database
	$test = $cpanel_uapi->execute($module, $function, $params); // default request type is GET
	// DEBUG: output array to screen
	echo '<pre>';
	echo HtmlSpecialChars(var_export($test, 1));
	echo '</pre>';

	// Set up the payload to send to the server.
	$payload = array(
		'domain' => $domain_name,
		'cert'   => $ssl_cert,
		'key'    => $ssl_key,
		);
	// install SSL cert using POST request
	$cpanel_uapi->execute('SSL', 'install_ssl', $payload, 'post');

	echo PHP_EOL . 'Add SSL certificate for ' . $domain_name . ' success.' . PHP_EOL;
} catch (Exception $e) {
	echo PHP_EOL . 'Add SSL certificate for ' . $domain_name . ' failed:' . PHP_EOL . $e->getMessage();
	exit();
}

exit();
// eof