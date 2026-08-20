<?php 
// DB credentials.
define('DB_HOST','localhost');   // better than localhost
define('DB_PORT','3306');        // 🔥 your MySQL port
define('DB_USER','root');
define('DB_PASS','');            // no password
define('DB_NAME','bbdms');

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