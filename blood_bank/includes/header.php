<?php error_reporting(0);
session_start(); ?>
<!-- header -->
<style>
    /* Innovative Header Styling */
    .innovative-header {
        position: sticky;
        top: 0;
        z-index: 999;
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(15px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .top-bar-innovative {
        background: linear-gradient(90deg, #1d3557, #162a45);
        font-size: 0.85rem;
        padding: 8px 0;
    }
    .top-bar-innovative p, .top-bar-innovative a, .top-bar-innovative span, .top-bar-innovative i {
        color: rgba(255,255,255,0.85) !important;
        transition: color 0.3s ease;
    }
    .top-bar-innovative a:hover, .top-bar-innovative a:hover i {
        color: #e63946 !important;
    }
    .top-bar-innovative .text-danger {
        color: #e63946 !important;
    }

    /* Logo Glowing Pulse */
    .navbar-brand {
        font-size: 2rem !important;
        position: relative;
        text-decoration: none !important;
    }
    .navbar-brand i {
        color: #e63946;
        animation: heartbeat 1.5s infinite;
        display: inline-block;
    }
    @keyframes heartbeat {
        0% { transform: scale(1); }
        15% { transform: scale(1.15); text-shadow: 0 0 10px rgba(230, 57, 70, 0.5); }
        30% { transform: scale(1); }
        45% { transform: scale(1.15); text-shadow: 0 0 10px rgba(230, 57, 70, 0.5); }
        60% { transform: scale(1); }
        100% { transform: scale(1); }
    }

    /* Interactive Menu Links */
    .navbar-nav .nav-item .nav-link {
        font-weight: 600 !important;
        color: #1d3557 !important;
        margin: 0 8px;
        position: relative;
        padding: 10px 20px !important;
        transition: all 0.3s ease;
        border-radius: 30px;
        z-index: 1;
    }
    .navbar-nav .nav-item .nav-link::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(230, 57, 70, 0.1);
        border-radius: 30px;
        z-index: -1;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .navbar-nav .nav-item .nav-link:hover,
    .navbar-nav .nav-item.active .nav-link {
        color: #e63946 !important;
    }
    .navbar-nav .nav-item .nav-link:hover::before,
    .navbar-nav .nav-item.active .nav-link::before {
        opacity: 1;
        transform: scale(1);
    }

    /* Animated Gradient Login Button */
    .login-btn-innovative {
        background: linear-gradient(45deg, #e63946, #b0101d, #e63946);
        background-size: 200% auto;
        color: white !important;
        border-radius: 50px !important;
        padding: 12px 30px !important;
        font-weight: bold;
        transition: 0.5s;
        border: none !important;
        box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        text-decoration: none !important;
    }
    .login-btn-innovative:hover {
        background-position: right center;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(230, 57, 70, 0.5);
    }

    /* Floating Dropdown */
    .dropdown-menu {
        border: none !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        border-radius: 15px !important;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        padding: 15px 0 !important;
    }
    .dropdown-item {
        padding: 12px 25px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
        color: #1d3557 !important;
    }
    .dropdown-item:hover {
        background-color: rgba(230, 57, 70, 0.05) !important;
        color: #e63946 !important;
        transform: translateX(10px);
    }
</style>

<header>
    <div class="top-bar-innovative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 d-none d-md-block">
                    <ul class="list-inline m-0">
                        <li class="list-inline-item mr-3"><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="list-inline-item mr-3"><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="list-inline-item mr-3"><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li class="list-inline-item"><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
                <?php
                $pagetype = "contactus";
                $sql = "SELECT * from tblcontactusinfo";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                <div class="col-md-6 text-center text-md-right">
                    <span class="mr-4"><i class="fas fa-phone-alt text-danger"></i> <?php echo $result->ContactNo; ?></span>
                    <span><i class="fas fa-envelope text-danger"></i> <a href="mailto:<?php echo $result->EmailId; ?>"><?php echo $result->EmailId; ?></a></span>
                </div>
                <?php }} ?>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light innovative-header py-2">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="index.php">
                <span style="color:#1d3557;">Blood</span><span style="color:#e63946;">Bank</span>
                <i class="fas fa-tint ml-1"></i>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="sign-up.php">Donate Blood</a></li>
                    <li class="nav-item"><a class="nav-link" href="search-donor.php">Request Blood</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    
                    <?php if (isset($_SESSION['donor_id'])) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                My Account
                            </a>
                            <div class="dropdown-menu shadow">
                                <a class="dropdown-item" href="donor-dashboard.php">Dashboard</a>
                                <a class="dropdown-item" href="donor-profile.php">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item ml-lg-4">
                            <a href="login.php" class="login-btn-innovative">Donor Login</a>
                        </li>
                        <li class="nav-item ml-lg-2">
                            <a href="admin/index.php" class="login-btn-innovative" style="background: linear-gradient(45deg, #1d3557, #457b9d, #1d3557) !important;">
                                <i class="fas fa-user-shield mr-1"></i> Admin
                            </a>
                        </li>

                    <?php } ?>

                </ul>
            </div>
        </div>
    </nav>
</header>