<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Using md5 for compatibility with existing tblblooddonars

    $sql = "SELECT id, FullName FROM tblblooddonars WHERE EmailId = :email AND Password = :password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $donor = $query->fetch(PDO::FETCH_OBJ);

    if($donor) {
        $_SESSION['donor_id'] = $donor->id;
        $_SESSION['donor_name'] = $donor->FullName;
        header("Location: donor-dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Bank Management System | Donor Login</title>
    
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>

    <!-- Custom-Files -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/fontawesome-all.css">

    <!-- Modern Web-Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .page-hero {
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.9), rgba(176, 16, 29, 0.9)), url('images/banner1.jpg') center/cover fixed;
            padding: 100px 0 60px;
            text-align: center;
            color: white;
            margin-bottom: 50px;
            position: relative;
        }
        .page-hero h2 {
            font-weight: 800;
            font-size: 3rem;
            text-shadow: 0 4px 10px rgba(0,0,0,0.2);
            margin-bottom: 15px;
        }
        .breadcrumb-custom {
            background: rgba(255, 255, 255, 0.1);
            display: inline-flex;
            border-radius: 50px;
            padding: 10px 25px;
            backdrop-filter: blur(5px);
        }
        .breadcrumb-custom a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }
        .breadcrumb-custom span {
            color: rgba(255,255,255,0.7);
            margin: 0 10px;
        }
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.05);
            margin-bottom: 80px;
        }
        .form-control-custom {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 1rem;
            color: #455a64;
            transition: all 0.3s ease;
            height: auto;
        }
        .form-control-custom:focus {
            background: white;
            border-color: #e63946;
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
            outline: none;
        }
        .submit-btn {
            background: linear-gradient(45deg, #e63946, #b0101d);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.3);
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(230, 57, 70, 0.4);
        }
        .form-label-custom {
            font-weight: 600;
            color: #1d3557;
            margin-bottom: 8px;
        }
        .section-title {
            color: #1d3557;
            font-weight: 800;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            height: 3px;
            width: 60px;
            background: #e63946;
            border-radius: 2px;
        }
        .login-icon {
            width: 80px;
            height: 80px;
            background: #1d3557;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: -90px auto 30px;
            box-shadow: 0 10px 25px rgba(29, 53, 87, 0.4);
        }
        .error-alert {
            background: rgba(230, 57, 70, 0.1);
            border-left: 4px solid #e63946;
            color: #e63946;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php');?>

    <!-- Page Hero Section -->
    <div class="page-hero">
        <div class="container">
            <h2>Donor Login</h2>
            <div class="breadcrumb-custom">
                <a href="index.php"><i class="fas fa-home mr-2"></i>Home</a>
                <span>/</span>
                <span class="current">Login</span>
            </div>
        </div>
    </div>

    <!-- Login Section -->
    <section class="about-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="form-card mt-5">
                        <div class="login-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="section-title text-center mx-auto mb-4" style="width: fit-content;">Welcome Back</h3>
                        
                        <?php if(isset($error)): ?>
                            <div class="error-alert">
                                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="#" method="post" name="login">
                            <div class="form-group mb-4">
                                <label class="form-label-custom">Email Address</label>
                                <input type="email" class="form-control form-control-custom" name="email" placeholder="john@example.com" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label-custom">Password</label>
                                <input type="password" class="form-control form-control-custom" name="password" id="password" placeholder="Enter your password" required>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="submit-btn" name="login"><i class="fas fa-sign-in-alt mr-2"></i> Sign In</button>
                                
                                <p class="mt-4 mb-0" style="color: #455a64;">
                                    Don't have an account? 
                                    <a href="sign-up.php" style="color: #e63946; font-weight: 600;">Register Now</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('includes/footer.php');?>

    <!-- Js files -->
    <script src="js/jquery-2.2.3.min.js"></script>
    <script src="js/bootstrap.js"></script>
<script src="js/form-validation.js"></script>
</body>

</html>