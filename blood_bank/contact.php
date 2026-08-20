<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (isset($_POST['send'])) {
	$name = $_POST['fullname'];
	$email = $_POST['email'];
	$contactno = $_POST['contactno'];
	$message = $_POST['message'];
	$sql = "INSERT INTO  tblcontactusquery(name,EmailId,ContactNumber,Message) VALUES(:name,:email,:contactno,:message)";
	$query = $dbh->prepare($sql);
	$query->bindParam(':name', $name, PDO::PARAM_STR);
	$query->bindParam(':email', $email, PDO::PARAM_STR);
	$query->bindParam(':contactno', $contactno, PDO::PARAM_STR);
	$query->bindParam(':message', $message, PDO::PARAM_STR);
	$query->execute();
	$lastInsertId = $dbh->lastInsertId();
	if ($lastInsertId) {

		echo '<script>alert("Query Sent. We will contact you shortly.")</script>';
	} else {
		echo "<script>alert('Something went wrong. Please try again.');</script>";
	}
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>Blood Bank Management System | Contact Us </title>

	<script>
		addEventListener("load", function() {
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
		.contact-info-card {
			background: #1d3557;
			border-radius: 20px;
			padding: 40px;
			color: white;
			height: 100%;
			box-shadow: 0 15px 40px rgba(0,0,0,0.1);
		}
		.contact-form-card {
			background: white;
			border-radius: 20px;
			padding: 50px;
			box-shadow: 0 15px 40px rgba(0,0,0,0.05);
		}
		.form-control-custom {
			background: #f8f9fa;
			border: 1px solid #e9ecef;
			border-radius: 10px;
			padding: 15px 20px;
			font-size: 1rem;
			color: #455a64;
			transition: all 0.3s ease;
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
		}
		.submit-btn:hover {
			transform: translateY(-3px);
			box-shadow: 0 15px 25px rgba(230, 57, 70, 0.4);
		}
		.info-item {
			display: flex;
			align-items: center;
			margin-bottom: 30px;
		}
		.info-icon {
			width: 50px;
			height: 50px;
			background: rgba(255,255,255,0.1);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.2rem;
			color: #e63946;
			margin-right: 20px;
			transition: all 0.3s ease;
		}
		.info-item:hover .info-icon {
			background: #e63946;
			color: white;
		}
	</style>
</head>

<body>
	<?php include('includes/header.php'); ?>

	<!-- Page Hero Section -->
	<div class="page-hero">
		<div class="container">
			<h2>Contact Us</h2>
			<div class="breadcrumb-custom">
				<a href="index.php"><i class="fas fa-home mr-2"></i>Home</a>
				<span>/</span>
				<span class="current">Contact Us</span>
			</div>
		</div>
	</div>

	<!-- contact section -->
	<div class="agileits-contact pb-5">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 mb-5 mb-lg-0">
					<div class="contact-info-card">
						<h3 class="mb-4 font-weight-bold" style="font-size: 1.8rem;">Get in Touch</h3>
						<p class="mb-5" style="opacity: 0.8; line-height: 1.8;">“You never know who might need it—maybe even you one day.” Reach out to us for any queries or emergencies.</p>
						
						<?php
						$pagetype = "contactus";
						$sql = "SELECT * from tblcontactusinfo";
						$query = $dbh->prepare($sql);
						$query->execute();
						$results = $query->fetchAll(PDO::FETCH_OBJ);
						if ($query->rowCount() > 0) {
							foreach ($results as $result) { ?>
						<div class="info-item">
							<div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
							<div>
								<h6 class="font-weight-bold mb-1">Address</h6>
								<p class="m-0" style="opacity: 0.8;"><?php echo $result->Address; ?></p>
							</div>
						</div>
						<div class="info-item">
							<div class="info-icon"><i class="fas fa-phone-alt"></i></div>
							<div>
								<h6 class="font-weight-bold mb-1">Phone</h6>
								<p class="m-0" style="opacity: 0.8;"><?php echo $result->ContactNo; ?></p>
							</div>
						</div>
						<div class="info-item">
							<div class="info-icon"><i class="fas fa-envelope"></i></div>
							<div>
								<h6 class="font-weight-bold mb-1">Email</h6>
								<p class="m-0" style="opacity: 0.8;"><a href="mailto:<?php echo $result->EmailId; ?>" class="text-white"><?php echo $result->EmailId; ?></a></p>
							</div>
						</div>
						<?php } } ?>
					</div>
				</div>

				<div class="col-lg-8">
					<div class="contact-form-card">
						<h3 class="mb-4 font-weight-bold" style="color: #1d3557; font-size: 1.8rem;">Send a Message</h3>
						<form action="#" method="post">
							<div class="row">
								<div class="col-md-6 form-group mb-4">
									<label class="font-weight-bold mb-2" style="color: #1d3557;">Full Name</label>
									<input type="text" class="form-control form-control-custom" id="name" name="fullname" placeholder="John Doe" required>
								</div>
								<div class="col-md-6 form-group mb-4">
									<label class="font-weight-bold mb-2" style="color: #1d3557;">Phone Number</label>
									<input type="tel" class="form-control form-control-custom" id="phone" name="contactno" placeholder="+1 234 567 8900" required>
								</div>
							</div>
							<div class="form-group mb-4">
								<label class="font-weight-bold mb-2" style="color: #1d3557;">Email Address</label>
								<input type="email" class="form-control form-control-custom" id="email" name="email" placeholder="john@example.com" required>
							</div>
							<div class="form-group mb-5">
								<label class="font-weight-bold mb-2" style="color: #1d3557;">Your Message</label>
								<textarea rows="5" class="form-control form-control-custom" id="message" name="message" placeholder="How can we help you?" maxlength="999" required style="resize:none"></textarea>
							</div>
							<div class="form-group mb-0">
								<button type="submit" name="send" class="submit-btn"><i class="fas fa-paper-plane mr-2"></i> Send Message</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include('includes/footer.php'); ?>

	<!-- Js files -->
	<script src="js/jquery-2.2.3.min.js"></script>
	<script src="js/bootstrap.js"></script>
<script src="js/form-validation.js"></script>
</body>

</html>