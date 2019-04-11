<?php

print phpinfo();

print "TestMySqlConnection.php\n";

print extension_loaded('php_pdo_mysql.dll');

$user = "fitswarm";
$pass = "fitswarm";

$dbh = new PDO('mysql:host=fitswarm-mysql;port=3306;dbname=fitswarm', $user, $pass);
