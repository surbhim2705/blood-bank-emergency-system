<?php
/**
 * Nearest Donors Feature - Quick Integration Guide
 * 
 * This file demonstrates how to use the geolocation functions
 * in other parts of the application
 */

// =============================================================================
// BASIC USAGE EXAMPLES
// =============================================================================

// Include the geolocation helper
// include('includes/geolocation.php');

/**
 * EXAMPLE 1: Calculate distance between two cities
 */
function example_calculate_distance() {
    // Get coordinates for two cities
    $city1_coords = getCityCoordinates('Pune');
    $city2_coords = getCityCoordinates('Kolhapur');
    
    if($city1_coords && $city2_coords) {
        $distance = calculateDistance(
            $city1_coords['lat'],
            $city1_coords['lon'],
            $city2_coords['lat'],
            $city2_coords['lon']
        );
        echo "Distance between Pune and Kolhapur: " . $distance . " km";
        // Output: Distance between Pune and Kolhapur: 226.45 km
    }
}

/**
 * EXAMPLE 2: Find nearest donors for a blood group in a city
 */
function example_find_nearest_donors($bloodGroup, $userCity) {
    global $dbh;
    include('includes/geolocation.php');
    
    // Get all donors with matching blood group
    $sql = "SELECT FullName, MobileNumber, EmailId, BloodGroup, Address 
            FROM tblblooddonars 
            WHERE BloodGroup = :bg AND status = 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bg', $bloodGroup, PDO::PARAM_STR);
    $query->execute();
    $donors = $query->fetchAll(PDO::FETCH_OBJ);
    
    // Sort by distance from user's city
    $sortedDonors = sortDonorsByDistance($donors, $userCity);
    
    // Get top 5 nearest
    $topDonors = array_slice($sortedDonors, 0, 5);
    
    return $topDonors;
}

/**
 * EXAMPLE 3: Display donors with distance information
 */
function example_display_donors_with_distance($donors) {
    foreach($donors as $donor) {
        $distance = isset($donor->distance) ? formatDistance($donor->distance) : 'Unknown';
        $category = isset($donor->distance) ? getDistanceCategory($donor->distance) : null;
        
        echo "
        <div class='donor-card'>
            <h4>" . htmlentities($donor->FullName) . "</h4>
            <p>Blood Group: " . htmlentities($donor->BloodGroup) . "</p>
            <p>Distance: " . $distance . "</p>
            <p>Phone: " . htmlentities($donor->MobileNumber) . "</p>
            <p>Email: " . htmlentities($donor->EmailId) . "</p>
        </div>
        ";
    }
}

/**
 * EXAMPLE 4: Custom sorting with distance limits
 */
function example_find_donors_within_distance($bloodGroup, $userCity, $maxDistance = 50) {
    global $dbh;
    include('includes/geolocation.php');
    
    // Get all donors with matching blood group
    $sql = "SELECT FullName, MobileNumber, EmailId, BloodGroup, Address 
            FROM tblblooddonars 
            WHERE BloodGroup = :bg AND status = 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bg', $bloodGroup, PDO::PARAM_STR);
    $query->execute();
    $donors = $query->fetchAll(PDO::FETCH_OBJ);
    
    // Sort by distance
    $sortedDonors = sortDonorsByDistance($donors, $userCity);
    
    // Filter by distance limit
    $nearbyDonors = array_filter($sortedDonors, function($donor) use ($maxDistance) {
        return isset($donor->distance) && $donor->distance < $maxDistance;
    });
    
    return $nearbyDonors;
}

/**
 * EXAMPLE 5: Get distance category for UI styling
 */
function example_distance_category() {
    $distances = [2, 10, 30, 100];
    
    foreach($distances as $distance) {
        $category = getDistanceCategory($distance);
        echo "Distance: " . $distance . " km => " . $category['label'] . " (Class: " . $category['class'] . ")\n";
    }
    
    /*
    Output:
    Distance: 2 km => Very Close (Class: badge-success)
    Distance: 10 km => Close (Class: badge-info)
    Distance: 30 km => Nearby (Class: badge-warning)
    Distance: 100 km => Available (Class: badge-secondary)
    */
}

/**
 * EXAMPLE 6: Integrate into custom API endpoint
 */
function api_get_nearest_donors() {
    global $dbh;
    include('includes/geolocation.php');
    
    header('Content-Type: application/json');
    
    $bloodGroup = $_GET['blood_group'] ?? null;
    $location = $_GET['location'] ?? null;
    
    if(!$bloodGroup || !$location) {
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }
    
    // Get donors
    $sql = "SELECT FullName, MobileNumber, EmailId, BloodGroup, Address 
            FROM tblblooddonars 
            WHERE BloodGroup = :bg AND status = 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bg', $bloodGroup, PDO::PARAM_STR);
    $query->execute();
    $donors = $query->fetchAll(PDO::FETCH_OBJ);
    
    // Sort by distance
    $sortedDonors = sortDonorsByDistance($donors, $location);
    $topDonors = array_slice($sortedDonors, 0, 10);
    
    echo json_encode(['success' => true, 'donors' => $topDonors]);
}

/**
 * EXAMPLE 7: Advanced filtering with multiple criteria
 */
function example_advanced_donor_search($bloodGroup, $location, $maxDistance = 50, $minAge = 18, $maxAge = 65) {
    global $dbh;
    include('includes/geolocation.php');
    
    $sql = "SELECT FullName, MobileNumber, EmailId, BloodGroup, Address, Age, Gender
            FROM tblblooddonars 
            WHERE BloodGroup = :bg 
            AND status = 1 
            AND Age >= :minAge 
            AND Age <= :maxAge";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':bg', $bloodGroup, PDO::PARAM_STR);
    $query->bindParam(':minAge', $minAge, PDO::PARAM_INT);
    $query->bindParam(':maxAge', $maxAge, PDO::PARAM_INT);
    $query->execute();
    $donors = $query->fetchAll(PDO::FETCH_OBJ);
    
    // Sort by distance
    $sortedDonors = sortDonorsByDistance($donors, $location);
    
    // Filter by distance
    $filteredDonors = array_filter($sortedDonors, function($donor) use ($maxDistance) {
        return isset($donor->distance) && $donor->distance <= $maxDistance;
    });
    
    return $filteredDonors;
}

/**
 * EXAMPLE 8: Generate donor report with distance analysis
 */
function example_generate_distance_report() {
    global $dbh;
    include('includes/geolocation.php');
    
    $sql = "SELECT BloodGroup, COUNT(*) as count FROM tblblooddonars WHERE status = 1 GROUP BY BloodGroup";
    $query = $dbh->prepare($sql);
    $query->execute();
    $bloodGroups = $query->fetchAll(PDO::FETCH_OBJ);
    
    $report = [];
    
    foreach($bloodGroups as $bg) {
        $donorSql = "SELECT FullName, Address FROM tblblooddonars WHERE BloodGroup = :bg AND status = 1";
        $dQuery = $dbh->prepare($donorSql);
        $dQuery->bindParam(':bg', $bg->BloodGroup, PDO::PARAM_STR);
        $dQuery->execute();
        $donors = $dQuery->fetchAll(PDO::FETCH_OBJ);
        
        $report[] = [
            'blood_group' => $bg->BloodGroup,
            'total_donors' => $bg->count,
            'locations' => array_unique(array_map(function($d) { return $d->Address; }, $donors))
        ];
    }
    
    return $report;
}

/**
 * EXAMPLE 9: Add geolocation data to existing database records
 */
function example_update_donor_coordinates() {
    global $dbh;
    include('includes/geolocation.php');
    
    // Note: This would require adding latitude and longitude columns to tblblooddonars
    // ALTER TABLE tblblooddonars ADD COLUMN latitude DECIMAL(10, 8), ADD COLUMN longitude DECIMAL(11, 8);
    
    $sql = "SELECT id, Address FROM tblblooddonars";
    $query = $dbh->prepare($sql);
    $query->execute();
    $donors = $query->fetchAll(PDO::FETCH_OBJ);
    
    foreach($donors as $donor) {
        $coords = getCityCoordinates($donor->Address);
        if($coords) {
            $updateSql = "UPDATE tblblooddonars SET latitude = :lat, longitude = :lon WHERE id = :id";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':lat', $coords['lat'], PDO::PARAM_STR);
            $updateQuery->bindParam(':lon', $coords['lon'], PDO::PARAM_STR);
            $updateQuery->bindParam(':id', $donor->id, PDO::PARAM_INT);
            $updateQuery->execute();
        }
    }
}

/**
 * EXAMPLE 10: Cache distance calculations for performance
 */
class DonorDistanceCache {
    private static $cache = [];
    
    public static function getDistance($lat1, $lon1, $lat2, $lon2) {
        include('includes/geolocation.php');
        
        $key = md5("$lat1-$lon1-$lat2-$lon2");
        
        if(!isset(self::$cache[$key])) {
            self::$cache[$key] = calculateDistance($lat1, $lon1, $lat2, $lon2);
        }
        
        return self::$cache[$key];
    }
    
    public static function clearCache() {
        self::$cache = [];
    }
}

// =============================================================================
// INTEGRATION CHECKLIST
// =============================================================================

/*
When integrating nearest donors feature into new pages:

☐ Include geolocation.php at top:
  include('includes/geolocation.php');

☐ Get user location from form/input:
  $userCity = $_POST['city'] ?? $_GET['location'] ?? null;

☐ Fetch matching donors from database:
  $sql = "SELECT * FROM tblblooddonars WHERE BloodGroup = :bg AND status = 1";

☐ Sort donors by distance:
  $sortedDonors = sortDonorsByDistance($donors, $userCity);

☐ Display distance information:
  $distance = formatDistance($donor->distance);

☐ Style distance badges:
  $category = getDistanceCategory($donor->distance);

☐ Add distance column to database queries if needed:
  ALTER TABLE tblblooddonars ADD latitude DECIMAL(10, 8), longitude DECIMAL(11, 8);

☐ Update city coordinates mapping if adding new areas

☐ Test with multiple blood groups and locations
*/

// =============================================================================
// PERFORMANCE TIPS
// =============================================================================

/*
1. Cache distance calculations to avoid recalculating
2. Limit results to top 10-20 donors for faster rendering
3. Use database indexing on BloodGroup and status columns
4. Implement pagination for large result sets
5. Pre-calculate distances for frequently searched locations
6. Use prepared statements to prevent SQL injection
7. Implement query result caching with TTL

Example of query optimization:
SELECT FullName, MobileNumber, EmailId, BloodGroup, Address 
FROM tblblooddonars 
WHERE BloodGroup = :bg 
  AND status = 1 
ORDER BY Address ASC
LIMIT 100;
*/

?>
