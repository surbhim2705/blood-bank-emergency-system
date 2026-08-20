<?php
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Bank | Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/fontawesome-all.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #e63946;
            --secondary: #1d3557;
            --accent: #457b9d;
            --light: #f1faee;
            --dark: #1d3557;
        }

        body {
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
            background-color: #fff;
        }

        /* Innovative Hero Section */
        .modern-hero {
            position: relative;
            min-height: 90vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            overflow: hidden;
            padding: 100px 0;
        }

        .hero-content {
            position: relative;
            z-index: 10;
        }

        .hero-title {
            font-weight: 800;
            font-size: 4rem;
            line-height: 1.1;
            color: var(--secondary);
            margin-bottom: 25px;
        }

        .hero-title span {
            color: var(--primary);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 40px;
            max-width: 500px;
        }

        .hero-image-main {
            border-radius: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
            transform: rotate(-2deg);
            transition: all 0.5s ease;
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            z-index: 10;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .card-1 { top: 10%; left: -50px; }
        .card-2 { bottom: 10%; right: -30px; animation-delay: 2s; }

        .stat-box {
            background: white;
            padding: 40px 30px;
            border-radius: 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .btn-modern {
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary-modern {
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 25px rgba(230, 57, 70, 0.3);
        }

        .btn-secondary-modern {
            background: var(--secondary);
            color: white;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php');?>

    <section class="modern-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">A Small Act, A Big Impact</h1>
                    <p class="hero-subtitle">Your simple act of kindness can give someone a second chance at life. Join our community of dedicated life-savers.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="search-donor.php" class="btn btn-modern btn-primary-modern me-md-3 mb-3">Find a Donor</a>
                        <a href="sign-up.php" class="btn btn-modern btn-secondary-modern mb-3">Become a Donor</a>
                    </div>
                </div>
                <div class="col-lg-6 position-relative">
                    <div class="floating-card card-1">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger text-white p-2 rounded-circle"><i class="fas fa-tint"></i></div>
                            <div><strong>Blood Type O+</strong><br><small class="text-muted">High Demand</small></div>
                        </div>
                    </div>
                    <img src="images/blood-donor.jpg" alt="Blood Donation" class="img-fluid hero-image-main">
                    <div class="floating-card card-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success text-white p-2 rounded-circle"><i class="fas fa-check"></i></div>
                            <div><strong>10,000+</strong><br><small class="text-muted">Lives Saved</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-bolt fa-3x text-danger mb-3"></i>
                        <h4>Fastest Match</h4>
                        <p class="text-muted">Our smart algorithm finds the nearest donors in real-time.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-shield-alt fa-3x text-danger mb-3"></i>
                        <h4>100% Verified</h4>
                        <p class="text-muted">Every donor profile is verified for safety and reliability.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <i class="fas fa-mobile-alt fa-3x text-danger mb-3"></i>
                        <h4>Donor Panel</h4>
                        <p class="text-muted">Advanced dashboard for donors to manage requests.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Our Heroic <span class="text-danger">Donors</span></h2>
            </div>
            <div class="row g-4">
                <?php 
                $status=1;
                $sql = "SELECT * from tblblooddonars where status=:status order by rand() limit 3";
                $query = $dbh -> prepare($sql);
                $query->bindParam(':status',$status,PDO::PARAM_STR);
                $query->execute();
                $results=$query->fetchAll(PDO::FETCH_OBJ);
                foreach($results as $result) { ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <img src="images/blood-donor.jpg" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-4 text-center">
                                <span class="badge bg-danger mb-2"><?php echo htmlentities($result->BloodGroup);?></span>
                                <h4 class="fw-bold"><?php echo htmlentities($result->FullName);?></h4>
                                <a href="contact-blood.php?cid=<?php echo $result->id;?>" class="btn btn-outline-danger w-100 mt-3 rounded-pill">Contact Now</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <?php include('includes/footer.php');?>
</body>
</html>