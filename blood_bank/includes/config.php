<?php 
// DB credentials.
define('DB_HOST','localhost');  
define('DB_PORT','3306');       
define('DB_USER','root');
define('DB_PASS','');           
define('DB_NAME','bbdms');

// Google Maps API Key
define('GOOGLE_MAPS_API_KEY', 'AIzaSyCLuo_vGbYmLK0dBQ0DtKxBTUqPgfA-z6E'); // Get it from https://console.cloud.google.com/

// Establish database connection.
try
{
$dbh = new PDO(
    "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
    DB_USER,
    DB_PASS,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
);
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
?>