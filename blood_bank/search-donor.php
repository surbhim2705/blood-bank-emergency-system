<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');
include('includes/geolocation.php');

if(isset($_POST['sub']))
{
    $bloodgroup=$_POST['bloodgroup'];
    $location=$_POST['location']; 
    $urgency=$_POST['urgency'] ?? 'normal';
    
    // Redirect to the new innovative nearest donors page
    header("Location: nearest-donors.php?bg=" . urlencode($bloodgroup) . "&city=" . urlencode($location) . "&urgency=" . urlencode($urgency));
    exit();
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Innovative Search | Blood Bank</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/fontawesome-all.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .search-hero {
            background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%);
            padding: 100px 0 150px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .search-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
        }

        .search-container {
            max-width: 900px;
            margin: -80px auto 100px;
            position: relative;
            z-index: 10;
            padding: 0 20px;
        }

        .glass-search-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 40px;
            padding: 50px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.5);
        }

        .section-title {
            font-weight: 800;
            color: #1d3557;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        /* Innovative Blood Group Selector */
        .blood-group-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 40px;
        }

        .blood-option {
            position: relative;
        }

        .blood-option input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .blood-box {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            padding: 20px 10px;
            text-align: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: #495057;
            transition: all 0.3s ease;
            cursor: pointer;
            display: block;
        }

        .blood-option input:checked + .blood-box {
            background: #e63946;
            border-color: #e63946;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.3);
        }

        .blood-box:hover {
            border-color: #e63946;
            color: #e63946;
        }

        /* Innovative Location Input */
        .location-input-group {
            position: relative;
            margin-bottom: 40px;
        }

        .location-input-group i {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: #e63946;
            font-size: 1.2rem;
        }

        .form-control-innovative {
            width: 100%;
            padding: 20px 25px 20px 60px;
            border-radius: 25px;
            border: 2px solid #e9ecef;
            background: #f8f9fa;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control-innovative:focus {
            background: white;
            border-color: #e63946;
            box-shadow: 0 10px 25px rgba(230, 57, 70, 0.1);
        }

        .btn-search-innovative {
            background: linear-gradient(45deg, #1d3557, #457b9d);
            color: white;
            border: none;
            padding: 20px 50px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1.2rem;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .btn-search-innovative:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(29, 53, 87, 0.3);
            background: linear-gradient(45deg, #e63946, #b0101d);
        }

        @media (max-width: 768px) {
            .blood-group-grid { grid-template-columns: repeat(2, 1fr); }
            .glass-search-card { padding: 30px 20px; }
        }
    </style>
</head>

<body>
    <?php include('includes/header.php');?>

    <div class="search-hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Find Your Guardian Angel</h1>
            <p class="lead opacity-75">Innovative search technology to find the nearest life-savers in seconds.</p>
        </div>
    </div>

    <div class="search-container">
        <div class="glass-search-card">
            <form method="post">
                <h3 class="section-title text-center"><i class="fas fa-tint text-danger me-2"></i> Select Blood Group</h3>
                
                <div class="blood-group-grid">
                    <?php 
                    $bloodgroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                    foreach($bloodgroups as $bg): ?>
                        <label class="blood-option">
                            <input type="radio" name="bloodgroup" value="<?php echo $bg; ?>" required>
                            <span class="blood-box"><?php echo $bg; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <h3 class="section-title text-center"><i class="fas fa-map-marker-alt text-danger me-2"></i> Where are you located?</h3>
                
                <div class="location-input-group">
                    <i class="fas fa-search-location"></i>
                    <input type="text" name="location" class="form-control-innovative" placeholder="Enter your city or area..." required>
                </div>

                <h3 class="section-title text-center"><i class="fas fa-clock text-danger me-2"></i> How urgent is your requirement?</h3>
                
                <div class="blood-group-grid" style="grid-template-columns: repeat(3, 1fr);">
                    <label class="blood-option">
                        <input type="radio" name="urgency" value="high" required>
                        <span class="blood-box">
                            <i class="fas fa-bolt mb-1"></i><br>
                            High
                        </span>
                    </label>
                    <label class="blood-option">
                        <input type="radio" name="urgency" value="medium" checked>
                        <span class="blood-box">
                            <i class="fas fa-hourglass-half mb-1"></i><br>
                            Medium
                        </span>
                    </label>
                    <label class="blood-option">
                        <input type="radio" name="urgency" value="low">
                        <span class="blood-box">
                            <i class="fas fa-calendar-alt mb-1"></i><br>
                            Low
                        </span>
                    </label>
                </div>

                <button type="submit" name="sub" class="btn-search-innovative mt-4">
                    <span>Search Nearest Donors</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <?php include('includes/footer.php');?>

    <script src="js/jquery-2.2.3.min.js"></script>
    <script src="js/bootstrap.js"></script>
<script src="js/form-validation.js"></script>
</body>

</html>