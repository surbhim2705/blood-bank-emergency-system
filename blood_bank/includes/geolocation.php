<?php
/**
 * Geolocation Helper Functions
 * Provides functions for distance calculation and city coordinate mapping
 */

// Function to calculate distance between two coordinates using Haversine formula
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // Kilometers
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earth_radius * $c;
    
    return round($distance, 2);
}

// Function to get coordinates from address (Robust Version with Indian Context)
function getCoordsFree($address) {
    if (empty(trim($address))) return null;

    // 1. IMPROVE ADDRESS NORMALIZATION
    $clean_address = trim($address);
    $clean_address = preg_replace('/,+/', ',', $clean_address); // Remove duplicate commas
    
    // Ensure "India" is appended for better resolution
    if (stripos($clean_address, 'India') === false) {
        $clean_address .= ", India";
    }

    // 2. MULTI-STAGE GEOCODING ATTEMPTS
    
    // Attempt 1: Full cleaned address
    $coords = callNominatimAPI($clean_address);
    if ($coords) return $coords;

    // Attempt 2: Fallback Stage 1 - Pincode Extraction (6 digits)
    if (preg_match('/\b(\d{6})\b/', $clean_address, $matches)) {
        $pincode = $matches[1];
        $pincode_query = "{$pincode}, India";
        $coords = callNominatimAPI($pincode_query);
        if ($coords) return $coords;
    }

    // Attempt 3: Fallback Stage 2 - Stripping specific parts from the left (comma-separated)
    $parts = array_map('trim', explode(',', $clean_address));
    if (count($parts) > 2) {
        // Try stripping the first 1 or 2 parts (like house number, shop, lane)
        for ($i = 1; $i < count($parts) - 1; $i++) {
            $fallback_parts = array_slice($parts, $i);
            $fallback_address = implode(', ', $fallback_parts);
            $coords = callNominatimAPI($fallback_address);
            if ($coords) return $coords;
            if ($i >= 2) break; // Limit requests to prevent slow loading
        }
    }

    // Attempt 4: Fallback Stage 3 - Space-separated fallback if no commas and multiple words
    if (strpos($clean_address, ',') === false) {
        $words = array_map('trim', explode(' ', $clean_address));
        if (count($words) > 2) {
            $fallback_address = implode(' ', array_slice($words, -3));
            $coords = callNominatimAPI($fallback_address);
            if ($coords) return $coords;
        }
    }

    return null;
}

// Internal helper for API calls with result selection logic
function callNominatimAPI($query) {
    $formatted_query = urlencode($query);
    $url = "https://nominatim.openstreetmap.org/search?q={$formatted_query}&format=json&limit=3&addressdetails=1";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'BloodBankSystem/1.2 (Robust-Indian-Geocoder)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if(!empty($data)) {
        // HANDLE AMBIGUOUS LOCATIONS: Prioritize Cities/Towns over Streets or Shops
        foreach($data as $result) {
            $type = $result['type'] ?? '';
            $importance = $result['importance'] ?? 0;
            
            // Prefer administrative areas, cities, or high importance matches
            if (in_array($type, ['city', 'town', 'village', 'administrative', 'suburb']) || $importance > 0.5) {
                return array(
                    'lat' => $result['lat'], 
                    'lon' => $result['lon'],
                    'name' => $result['display_name']
                );
            }
        }
        // Last resort: return the first result
        return array('lat' => $data[0]['lat'], 'lon' => $data[0]['lon'], 'name' => $data[0]['display_name']);
    }
    return null;
}

// Function to sort donors by distance using DB coordinates
function sortDonorsByDistance(&$donors, $requesterCity) {
    // 1. Get coordinates for the location the user is searching from
    $requesterCoords = getCoordsFree($requesterCity);
    
    if(!$requesterCoords) return $donors;

    foreach($donors as &$donor) {
        // 2. Use latitude/longitude from DB if available
        if(!empty($donor->latitude) && !empty($donor->longitude)) {
            $donorLat = $donor->latitude;
            $donorLon = $donor->longitude;
        } else {
            // Fallback for old donors: try to geocode their address on the fly
            $donorCoords = getCoordsFree($donor->Address);
            if($donorCoords) {
                $donorLat = $donorCoords['lat'];
                $donorLon = $donorCoords['lon'];
            } else {
                $donor->distance = 9999;
                continue;
            }
        }
        
        // 3. Calculate exact distance
        $distance = calculateDistance(
            $requesterCoords['lat'], 
            $requesterCoords['lon'], 
            $donorLat, 
            $donorLon
        );
        $donor->distance = $distance;
    }
    
    // 4. Sort by distance (nearest first)
    usort($donors, function($a, $b) {
        $distA = $a->distance ?? 9999;
        $distB = $b->distance ?? 9999;
        return ($distA < $distB) ? -1 : 1;
    });
    
    return $donors;
}

// Function to get distance category for UI display
function getDistanceCategory($distance) {
    if($distance < 5) {
        return array('label' => 'Very Close', 'class' => 'badge-success', 'icon' => 'fa-star');
    } elseif($distance < 15) {
        return array('label' => 'Close', 'class' => 'badge-info', 'icon' => 'fa-thumbs-up');
    } elseif($distance < 50) {
        return array('label' => 'Nearby', 'class' => 'badge-warning', 'icon' => 'fa-arrow-right');
    } else {
        return array('label' => 'Available', 'class' => 'badge-secondary', 'icon' => 'fa-check');
    }
}

// Function to format distance for display
function formatDistance($distance) {
    if($distance >= 9999) {
        return 'Location not found';
    }
    if($distance < 1) {
        return round($distance * 1000) . ' m';
    }
    return $distance . ' km';
}
?>
