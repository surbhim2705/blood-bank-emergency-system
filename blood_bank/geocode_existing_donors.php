<?php
include('includes/config.php');
include('includes/geolocation.php');

echo "<h2>Donor Geocoding Utility</h2>";
echo "Attempting to update donors missing coordinates...<br><br>";

// Find donors who have NULL or 0 for coordinates
$sql = "SELECT id, FullName, Address FROM tblblooddonars WHERE latitude IS NULL OR latitude = 0 OR longitude IS NULL OR longitude = 0";
$query = $dbh->prepare($sql);
$query->execute();
$donors = $query->fetchAll(PDO::FETCH_OBJ);

if(count($donors) == 0) {
    echo "Great! All donors already have coordinates. Try searching for a well-known city like 'Pune' or 'Mumbai'.";
    exit;
}

$success = 0;
$failed = 0;

foreach($donors as $donor) {
    echo "Processing: <strong>" . htmlentities($donor->FullName) . "</strong> (" . htmlentities($donor->Address) . ")... ";
    
    // Use the function from geolocation.php
    $coords = getCoordsFree($donor->Address);
    
    if($coords) {
        $updateSql = "UPDATE tblblooddonars SET latitude = :lat, longitude = :lon WHERE id = :id";
        $updateQuery = $dbh->prepare($updateSql);
        $updateQuery->bindParam(':lat', $coords['lat']);
        $updateQuery->bindParam(':lon', $coords['lon']);
        $updateQuery->bindParam(':id', $donor->id);
        $updateQuery->execute();
        
        echo "<span style='color:green'>SUCCESS (Lat: {$coords['lat']}, Lon: {$coords['lon']})</span><br>";
        $success++;
    } else {
        echo "<span style='color:red'>FAILED (Location not recognized)</span><br>";
        $failed++;
    }
    
    // Sleep briefly to respect API rate limits
    usleep(500000); 
}

echo "<br><strong>Update Complete!</strong><br>";
echo "Successfully updated: $success donors.<br>";
echo "Failed to recognize: $failed donors.<br>";
echo "<br><a href='search-donor.php'>Go back to Search</a>";
?>
