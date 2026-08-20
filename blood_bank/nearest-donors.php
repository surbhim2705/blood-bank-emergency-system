<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');
include('includes/geolocation.php');

$blood_group = $_GET['bg'] ?? '';
$city = $_GET['city'] ?? '';
$urgency = $_GET['urgency'] ?? 'normal';

if(empty($blood_group)) {
    header("Location: search-donor.php");
    exit();
}

// 1. SMART URGENCY CONFIGURATION & PRICING LOGIC
$urgencyConfig = [
    'high' => [
        'label' => 'High Urgency',
        'color' => '#e63946', // Red
        'badge' => 'bg-danger',
        'range_min' => 0,
        'range_max' => 15, // Showing top nearest
        'sort' => 'distance ASC',
        'limit' => 5,
        'pricing' => function($dist) {
            // Formula: Higher price for closer donors
            // Example: 1km -> ₹1500, 3km -> ₹1300, 5km -> ₹1100
            $rate = 1600 - ($dist * 100);
            return max(800, $rate); // Minimum ₹800 for high urgency
        },
        'desc' => 'Emergency Response: Prioritizing immediate proximity.'
    ],
    'medium' => [
        'label' => 'Medium Urgency',
        'color' => '#fd7e14', // Orange
        'badge' => 'bg-warning',
        'range_min' => 5,
        'range_max' => 30,
        'sort' => 'distance ASC',
        'limit' => 10,
        'pricing' => function($dist) {
            // Formula: Moderate pricing
            // Example: 5km -> ₹900, 10km -> ₹700, 20km -> ₹500
            $rate = 1000 - ($dist * 25);
            return max(400, $rate); // Minimum ₹400 for medium
        },
        'desc' => 'Standard Support: Balancing distance and compensation.'
    ],
    'low' => [
        'label' => 'Low Urgency',
        'color' => '#198754', // Green
        'badge' => 'bg-success',
        'range_min' => 25,
        'range_max' => 100,
        'sort' => 'distance ASC',
        'limit' => 20,
        'pricing' => function($dist) {
            // Formula: Lower rates for farther donors
            // Example: 30km -> ₹400, 40km -> ₹300, 50km -> ₹200
            $rate = 700 - ($dist * 10);
            return max(200, $rate); // Minimum ₹200 for low
        },
        'desc' => 'Flexible Search: Prioritizing lower donor compensation.'
    ]
];

$config = $urgencyConfig[$urgency] ?? $urgencyConfig['medium'];

/**
 * 2. DISTANCE CALCULATION (GEOPROCESSING)
 * We first geocode the user's input city to get coordinates
 */
$userCoords = getCoordsFree($city);
if (!$userCoords) {
    // Fallback: if geocoding fails, we can't do distance math
    $error_msg = "Location not recognized. Showing all matching donors without distance sorting.";
    $sql = "SELECT *, 'N/A' as distance FROM tblblooddonars WHERE BloodGroup = ? AND status = 1 LIMIT ?";
    $query = $dbh->prepare($sql);
    $query->bindValue(1, $blood_group, PDO::PARAM_STR);
    $query->bindValue(2, (int)$config['limit'], PDO::PARAM_INT);
    $query->execute();
} else {
    $userLat = $userCoords['lat'];
    $userLon = $userCoords['lon'];

    /**
     * 3. SQL QUERY WITH BLOOD COMPATIBILITY & HAVERSINE FORMULA
     * This calculates the great-circle distance between user and donor directly in the query
     */
    
    // Determine compatible blood groups based on user selection
    $compatible_groups = [$blood_group];
    $is_positive = (substr($blood_group, -1) === '+');
    
    if ($is_positive) {
        // Positive groups can receive from their own group, O+, and O-
        if ($blood_group !== 'O+') $compatible_groups[] = 'O+';
        if ($blood_group !== 'O-') $compatible_groups[] = 'O-';
    } else {
        // Negative groups can receive from their own group and O-
        if ($blood_group !== 'O-') $compatible_groups[] = 'O-';
    }
    $compatible_groups = array_unique($compatible_groups);
    
    // Prepare placeholders for the IN clause
    $in_placeholders = implode(',', array_fill(0, count($compatible_groups), '?'));

    $sql = "SELECT *, 
            ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) 
            * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) 
            * sin( radians( latitude ) ) ) ) AS distance 
            FROM tblblooddonars 
            WHERE BloodGroup IN ($in_placeholders) 
            AND status = 1 
            HAVING distance BETWEEN ? AND ?
            ORDER BY " . $config['sort'] . " 
            LIMIT ?";
    
    $query = $dbh->prepare($sql);
    $idx = 1;
    $query->bindValue($idx++, $userLat);
    $query->bindValue($idx++, $userLon);
    $query->bindValue($idx++, $userLat);
    foreach($compatible_groups as $group) {
        $query->bindValue($idx++, $group);
    }
    $query->bindValue($idx++, $config['range_min']);
    $query->bindValue($idx++, $config['range_max']);
    $query->bindValue($idx++, (int)$config['limit'], PDO::PARAM_INT);
    $query->execute();
}

$donors = $query->fetchAll(PDO::FETCH_OBJ);
$is_fallback = false;

// If no nearest donors found, search for all available matching donors regardless of distance
if (empty($donors) && $userCoords) {
    $is_fallback = true;
    $sql = "SELECT *, 
            ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) 
            * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) 
            * sin( radians( latitude ) ) ) ) AS distance 
            FROM tblblooddonars 
            WHERE BloodGroup IN ($in_placeholders) 
            AND status = 1 
            ORDER BY " . $config['sort'] . " 
            LIMIT ?";
    
    $query = $dbh->prepare($sql);
    $idx = 1;
    $query->bindValue($idx++, $userLat);
    $query->bindValue($idx++, $userLon);
    $query->bindValue($idx++, $userLat);
    foreach($compatible_groups as $group) {
        $query->bindValue($idx++, $group);
    }
    $query->bindValue($idx++, (int)$config['limit'], PDO::PARAM_INT);
    $query->execute();
    $donors = $query->fetchAll(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Nearest Donors | Blood Bank</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/fontawesome-all.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0f2f5;
        }

        .page-hero {
            background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%);
            padding: 80px 0;
            color: white;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            margin-bottom: 50px;
        }

        .donors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .donor-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .donor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(230, 57, 70, 0.15);
            border-color: rgba(230, 57, 70, 0.3);
        }

        .best-match-badge {
            position: absolute;
            top: 15px;
            right: -30px;
            background: #ffc107;
            color: #000;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            z-index: 10;
        }

        .compatible-badge {
            position: absolute;
            top: 15px;
            right: -30px;
            background: #28a745;
            color: #fff;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            z-index: 10;
        }

        .donor-header {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .donor-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #e63946, #b0101d);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            box-shadow: 0 8px 15px rgba(230, 57, 70, 0.2);
        }

        .donor-title h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            color: #1d3557;
        }

        .blood-badge {
            display: inline-block;
            background: rgba(230, 57, 70, 0.1);
            color: #e63946;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-top: 4px;
        }

        .donor-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 20px;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1d3557;
        }

        .price-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .price-tag {
            display: inline-flex;
            align-items: baseline;
            gap: 4px;
            color: #1d3557;
        }

        .currency { font-size: 1rem; font-weight: 600; }
        .amount { font-size: 2.2rem; font-weight: 800; line-height: 1; }
        .period { font-size: 0.85rem; color: #6c757d; }

        .urgency-context {
            font-size: 0.85rem;
            color: #e63946;
            margin-top: 5px;
            font-weight: 600;
        }

        .btn-contact {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #1d3557;
            color: white;
            padding: 1rem;
            border-radius: 18px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            border: none;
            width: 100%;
        }

        .btn-contact:hover {
            background: #e63946;
            color: white;
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.2);
        }

        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .no-results i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1.5rem;
        }

        .btn-search-again {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 12px 30px;
            background: #e63946;
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
        }

        .animate-up {
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>

<body>
    <?php include('includes/header.php');?>

    <div class="page-hero">
        <div class="container">
            <h1 class="fw-bold mb-3">Intelligent Emergency Matching</h1>
            <p class="lead opacity-75">
                Dynamic results for <?php echo htmlentities($blood_group); ?> in <?php echo htmlentities($city); ?><br>
                <span class="badge <?php echo $config['badge']; ?> mt-2 px-4 py-2" style="border-radius: 50px; font-size: 1rem;">
                    <i class="fas fa-bolt me-1"></i> <?php echo $config['label']; ?>
                </span>
            </p>
        </div>
    </div>

    <div class="container pb-5">
        <?php if($is_fallback): ?>
            <div class="alert alert-warning animate-up" style="border-radius: 20px; background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.2); color: #856404; padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                <i class="fa-solid fa-circle-info" style="font-size: 1.5rem;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 3px;">Notice: No donors found within the "<?php echo $config['label']; ?>" distance limit.</strong>
                    Showing other available matching donors sorted by proximity.
                </div>
            </div>
        <?php endif; ?>

        <?php if($donors): ?>
            <div class="donors-grid">
                <?php foreach($donors as $donor): ?>
                    <div class="donor-card animate-up">
                        <?php if($donor->BloodGroup === $blood_group): ?>
                            <div class="best-match-badge"><i class="fa-solid fa-star"></i> Best Match</div>
                        <?php else: ?>
                            <div class="compatible-badge"><i class="fa-solid fa-check"></i> Compatible</div>
                        <?php endif; ?>
                        
                        <div class="donor-header">
                            <div class="donor-avatar">
                                <?php echo strtoupper(substr($donor->FullName, 0, 1)); ?>
                            </div>
                            <div class="donor-title">
                                <h3><?php echo htmlentities($donor->FullName); ?></h3>
                                <span class="blood-badge"><?php echo htmlentities($donor->BloodGroup); ?></span>
                            </div>
                        </div>

                        <div class="donor-stats">
                            <div class="stat-item">
                                <span class="stat-label">Distance</span>
                                <span class="stat-value">
                                    <?php echo is_numeric($donor->distance) ? round($donor->distance, 1) . " km" : "N/A"; ?>
                                </span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Est. Arrival</span>
                                <span class="stat-value" style="color: #e63946;">
                                    <?php echo is_numeric($donor->distance) ? round($donor->distance * 2) . " mins" : "N/A"; ?>
                                </span>
                            </div>
                        </div>

                        <div class="price-container">
                            <div class="price-tag" style="color: <?php echo $config['color']; ?>;">
                                <span class="currency">₹</span>
                                <span class="amount"><?php 
                                    $price_dist = is_numeric($donor->distance) ? $donor->distance : 0;
                                    $dynamic_rate = $config['pricing']($price_dist);
                                    echo number_format($dynamic_rate, 0); 
                                ?></span>
                                <span class="period">Rate</span>
                            </div>
                            <p class="urgency-context"><?php echo $config['desc']; ?></p>
                        </div>

                        <div class="donor-footer">
                            <a href="contact-blood.php?cid=<?php echo $donor->id; ?>&urgency=<?php echo $urgency; ?>" class="btn-contact">
                                <i class="fa-solid fa-paper-plane"></i> Contact Donor
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fa-solid fa-face-frown"></i>
                <h3>No Donors Found</h3>
                <p>We couldn't find any donors matching your specific criteria in this area. Try adjusting your search or contact a nearby hospital.</p>
                <a href="search-donor.php" class="btn-search-again">Try New Search</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php');?>

    <script src="js/jquery-2.2.3.min.js"></script>
    <script src="js/bootstrap.js"></script>
</body>
</html>
