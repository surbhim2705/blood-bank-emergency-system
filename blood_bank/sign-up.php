<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['submit'])) {
    // 1. DATA COLLECTION & SANITIZATION
    $fullname = trim($_POST['fullname']);
    $mobile = trim($_POST['mobileno']);
    $email = trim($_POST['emailid']);
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $blodgroup = $_POST['bloodgroup'];
    $address = trim($_POST['address']);
    $message = trim($_POST['message']);
    $password = md5($_POST['password']);
    $status = 1;

    // 2. COMPREHENSIVE VALIDATION
    $error = "";
    
    // Check for empty fields
    if(empty($fullname) || empty($mobile) || empty($email) || empty($address) || empty($password)) {
        $error = "All fields are required.";
    } 
    // Mobile Validation (Exactly 10 digits)
    elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = "Mobile number must be exactly 10 numeric digits.";
    }
    // Age Validation (20-60)
    elseif ($age < 20 || $age > 60) {
        $error = "Age must be between 20 and 60 years to register as a donor.";
    }
    // Email Format Validation
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    
    if(empty($error)) {
        // 3. UNIQUENESS CHECK (Email & Mobile)
        $check = "SELECT id FROM tblblooddonars WHERE EmailId=:email OR MobileNumber=:mobile";
        $check_query = $dbh->prepare($check);
        $check_query->bindParam(':email', $email, PDO::PARAM_STR);
        $check_query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $check_query->execute();
        
        if($check_query->rowCount() > 0) {
            $error = "Registration Failed: This Email or Mobile Number is already registered.";
        }
    }

    if(!empty($error)) {
        echo "<script>alert('$error');</script>";
    } else {
        /**
         * 4. ADDRESS TO LAT/LNG CONVERSION
         */
        include_once('includes/geolocation.php');
        $coords = getCoordsFree($address);
        
        $latitude = null;
        $longitude = null;
        $geocoded_success = false;

        if($coords) {
            $latitude = $coords['lat'];
            $longitude = $coords['lon'];
            $geocoded_success = true;
        }

        // 5. DATABASE INSERT
        $sql = "INSERT INTO tblblooddonars(FullName, MobileNumber, EmailId, Age, Gender, BloodGroup, Address, latitude, longitude, Message, status, Password) 
                VALUES(:fullname, :mobile, :email, :age, :gender, :blodgroup, :address, :latitude, :longitude, :message, :status, :password)";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':age', $age, PDO::PARAM_STR);
        $query->bindParam(':gender', $gender, PDO::PARAM_STR);
        $query->bindParam(':blodgroup', $blodgroup, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);
        $query->bindParam(':latitude', $latitude, PDO::PARAM_STR);
        $query->bindParam(':longitude', $longitude, PDO::PARAM_STR);
        $query->bindParam(':message', $message, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        
        if($query->execute()) {
            if ($geocoded_success) {
                echo "<script>alert('Success! You are now registered as a donor.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Success! You are registered as a donor. (Note: Your exact location could not be verified on the map, but your address has been successfully saved.)'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('Database Error. Please try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>Blood Bank Management System | Donate Blood</title>
	
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
			left: 0;
			bottom: 0;
			height: 3px;
			width: 60px;
			background: #e63946;
			border-radius: 2px;
		}
	</style>
</head>

<body>
	<?php include('includes/header.php');?>

	<!-- Page Hero Section -->
	<div class="page-hero">
		<div class="container">
			<h2>Become a Donor</h2>
			<div class="breadcrumb-custom">
				<a href="index.php"><i class="fas fa-home mr-2"></i>Home</a>
				<span>/</span>
				<span class="current">Donate Blood</span>
			</div>
		</div>
	</div>

	<!-- Registration Form Section -->
	<section class="about-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<div class="form-card">
						<h3 class="section-title text-center mx-auto mb-5" style="width: fit-content;">Register as a Donor</h3>
						
						<form action="#" method="post" name="signup" onsubmit="return checkpass();">
							<div class="row">
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Full Name</label>
									<input type="text" class="form-control form-control-custom" name="fullname" id="fullname" placeholder="John Doe" required>
								</div>
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Mobile Number</label>
									<input type="text" class="form-control form-control-custom" name="mobileno" id="mobileno" required="true" placeholder="10 Digit Mobile Number" minlength="10" maxlength="10" pattern="[0-9]{10}" title="Please enter exactly 10 digits">
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Email Id</label>
									<input type="email" name="emailid" class="form-control form-control-custom" placeholder="john@example.com" required>
								</div>
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Age</label>
									<input type="number" class="form-control form-control-custom" name="age" id="age" placeholder="20 - 60" min="20" max="60" required>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Gender</label>
									<select name="gender" class="form-control form-control-custom" required>
										<option value="">Select Gender</option>
										<option value="Male">Male</option>
										<option value="Female">Female</option>
									</select>
								</div>
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Blood Group</label>
									<select name="bloodgroup" class="form-control form-control-custom" required>
										<option value="">Select Blood Group</option>
										<?php $sql = "SELECT * from  tblbloodgroup ";
										$query = $dbh -> prepare($sql);
										$query->execute();
										$results=$query->fetchAll(PDO::FETCH_OBJ);
										if($query->rowCount() > 0) {
											foreach($results as $result) { ?>  
											<option value="<?php echo htmlentities($result->BloodGroup);?>"><?php echo htmlentities($result->BloodGroup);?></option>
										<?php }} ?>
									</select>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Password</label>
									<input type="password" class="form-control form-control-custom" name="password" id="password" placeholder="Create a strong password" required>
								</div>
								<div class="col-md-6 form-group mb-4">
									<label class="form-label-custom">Address</label>
									<input type="text" class="form-control form-control-custom" name="address" id="address" required placeholder="123 Main St, City">
								</div>
							</div>
							
							<div class="form-group mb-4">
								<label class="form-label-custom">Any Message or Medical Condition?</label>
								<textarea class="form-control form-control-custom" rows="4" name="message" placeholder="Optional notes for the blood bank staff..." required></textarea>
							</div>
							
							<div class="text-center">
								<button type="submit" class="submit-btn" name="submit"><i class="fas fa-heart mr-2"></i> Register Now</button>
								<p class="mt-4" style="color: #455a64;">
									Already Registered? <a href="login.php" style="color: #e63946; font-weight: 600;">Sign in here</a>
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