<?php
include('includes/config.php');
$q = $dbh->query("DESCRIBE tblhospitals");
echo "<pre>";
print_r($q->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";
?>
