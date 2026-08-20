<?php
session_start();
include('../includes/config.php');
if(isset($_POST['submit']))
  {
    $email=$_POST['email'];
$mobile=$_POST['mobile'];
$newpassword=md5($_POST['newpassword']);
  $sql ="SELECT Email FROM tbladmin WHERE Email=:email and MobileNumber=:mobile";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
$con="update tbladmin set Password=:newpassword where Email=:email and MobileNumber=:mobile";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':email', $email, PDO::PARAM_STR);
$chngpwd1-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();
echo "<script>alert('Your Password succesfully changed');</script>";
}
else {
echo "<script>alert('Email id or Mobile no is invalid');</script>"; 
}
}

?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>BloodBank & Donor Management System | Forgot Password</title>
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">
	<style>
		body {
			font-family: 'Inter', sans-serif;
			background: url('img/banner.png') no-repeat center center fixed;
            background-size: cover;
			margin: 0;
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
            position: relative;
		}

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.7);
            z-index: 1;
        }

		.login-wrapper {
			width: 100%;
			max-width: 520px;
			padding: 20px;
            position: relative;
            z-index: 2;
            animation: fadeIn 0.8s ease-out;
		}

		.login-card {
			background: rgba(30, 41, 59, 0.72);
			backdrop-filter: blur(18px);
			border-radius: 24px;
			padding: 45px 40px;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
			border: 1px solid rgba(255, 255, 255, 0.1);
		}

		.login-icon {
			width: 70px;
			height: 70px;
			background: linear-gradient(45deg, #ff4d4d, #e74c3c);
			color: white;
			border-radius: 18px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 2rem;
			margin: 0 auto 25px;
			box-shadow: 0 10px 20px rgba(255, 77, 77, 0.3);
		}

		.login-title {
			color: #fff;
			font-weight: 800;
			text-align: center;
			margin-bottom: 5px;
			font-size: 1.75rem;
		}

        .login-subtitle {
            color: #94a3b8;
            text-align: center;
            margin-bottom: 35px;
            font-size: 0.95rem;
        }

		.form-group {
			margin-bottom: 20px;
		}

		.form-label {
			display: block;
			font-weight: 500;
			color: #cbd5e1;
			margin-bottom: 8px;
			font-size: 0.85rem;
		}

		.form-control-custom {
			width: 100%;
			background: rgba(255, 255, 255, 0.05);
			border: 1px solid rgba(255, 255, 255, 0.1);
			border-radius: 12px;
			padding: 14px 18px;
			font-size: 1rem;
			color: #fff;
			transition: all 0.3s ease;
			box-sizing: border-box;
		}

		.form-control-custom:focus {
			background: rgba(255, 255, 255, 0.1);
			border-color: #ff4d4d;
			outline: none;
            box-shadow: 0 0 0 4px rgba(255, 77, 77, 0.1);
		}

		.submit-btn {
			background: linear-gradient(45deg, #ff4d4d, #e74c3c);
			color: white;
			border: none;
			border-radius: 12px;
			padding: 14px;
			font-weight: 700;
			font-size: 1rem;
			transition: all 0.3s ease;
			cursor: pointer;
			width: 100%;
			margin-top: 15px;
			box-shadow: 0 10px 15px -3px rgba(255, 77, 77, 0.3);
		}

		.submit-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 20px 25px -5px rgba(255, 77, 77, 0.4);
		}

		.forgot-pass,
		.back-home {
			display: block;
			text-align: center;
			margin-top: 20px;
			color: #94a3b8;
			text-decoration: none;
			font-size: 0.85rem;
			transition: color 0.3s;
		}

		.forgot-pass:hover,
		.back-home:hover {
			color: #ff4d4d;
		}

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
	</style>
	<script type="text/javascript">
function valid()
{
if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
{
alert("New Password and Confirm Password Field do not match  !!");
document.chngpwd.confirmpassword.focus();
return false;
}
return true;
}
</script>
</head>

<body>
	<div class="login-wrapper">
		<div class="login-card">
			<div class="login-icon">
				<i class="fas fa-lock"></i>
			</div>
			<h1 class="login-title">Reset Admin Password</h1>
            <p class="login-subtitle">Securely update your admin password</p>

			<form method="post" name="chngpwd" onsubmit="return valid();">
				<div class="form-group">
					<label class="form-label">Email Address</label>
					<input type="email" class="form-control-custom" placeholder="Enter email address" required="true" name="email">
				</div>

				<div class="form-group">
					<label class="form-label">Mobile Number</label>
					<input type="text" class="form-control-custom" name="mobile" placeholder="Enter mobile number" required="true" maxlength="10" pattern="[0-9]+">
				</div>

				<div class="form-group">
					<label class="form-label">New Password</label>
					<input class="form-control-custom" type="password" name="newpassword" placeholder="New password" required="true"/>
				</div>

				<div class="form-group">
					<label class="form-label">Confirm Password</label>
					<input class="form-control-custom" type="password" name="confirmpassword" placeholder="Confirm password" required="true" />
				</div>

				<button class="submit-btn" name="submit" type="submit">Reset Password</button>
				<a href="index.php" class="forgot-pass">Back to login</a>
			</form>

			<a href="../index.php" class="back-home"><i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Return to Public Site</a>
		</div>
	</div>
	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

<script src="../js/form-validation.js"></script>
</body>

</html>