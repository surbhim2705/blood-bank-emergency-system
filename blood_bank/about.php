<?php
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>Blood Bank Management System | About Us</title>
	
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
		.about-content-card {
			background: white;
			border-radius: 20px;
			padding: 50px;
			box-shadow: 0 15px 40px rgba(0,0,0,0.05);
			position: relative;
			margin-bottom: 80px;
		}
		.about-icon {
			width: 80px;
			height: 80px;
			background: #e63946;
			color: white;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 2rem;
			margin: -90px auto 30px;
			box-shadow: 0 10px 25px rgba(230, 57, 70, 0.4);
		}
		.about-title {
			font-weight: 700;
			color: #1d3557;
			margin-bottom: 25px;
			text-align: center;
		}
		.about-text {
			color: #455a64;
			line-height: 1.9;
			font-size: 1.05rem;
			text-align: justify;
		}
	</style>
</head>

<body>
	<?php include('includes/header.php');?>

	<!-- Page Hero Section -->
	<div class="page-hero">
		<div class="container">
			<h2>About Us</h2>
			<div class="breadcrumb-custom">
				<a href="index.php"><i class="fas fa-home mr-2"></i>Home</a>
				<span>/</span>
				<span class="current">About Us</span>
			</div>
		</div>
	</div>

	<!-- about content -->
	<section class="about-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<?php 
					$pagetype="aboutus";
					$sql = "SELECT type,detail,PageName from tblpages where type=:pagetype";
					$query = $dbh -> prepare($sql);
					$query->bindParam(':pagetype',$pagetype,PDO::PARAM_STR);
					$query->execute();
					$results=$query->fetchAll(PDO::FETCH_OBJ);
					if($query->rowCount() > 0)
					{
						foreach($results as $result)
						{ ?>
						<div class="about-content-card">
							<div class="about-icon">
								<i class="fas fa-heartbeat"></i>
							</div>
							<h3 class="about-title"><?php echo htmlentities($result->PageName); ?></h3>
							<div class="about-text">
								<?php  echo $result->detail; ?>
							</div>
						</div>
					<?php } } ?>
				</div>
			</div>
		</div>
	</section>

	<?php include('includes/footer.php');?>

	<!-- Js files -->
	<script src="js/jquery-2.2.3.min.js"></script>
	<script src="js/bootstrap.js"></script>
</body>

</html>