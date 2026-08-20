<?php
session_start();
include('../includes/config.php');
if(isset($_POST['login']))
{
$username=$_POST['username'];
$password=md5($_POST['password']);
$sql ="SELECT UserName,Password FROM tbladmin WHERE UserName=:username and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':username', $username, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
$_SESSION['alogin']=$_POST['username'];
echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
} else{
  
  echo "<script>alert('Invalid Details');</script>";

}

}

?>
<!doctype html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<title>Admin Access | BBDMS</title>
	
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

		.forgot-pass {
			display: block;
			text-align: center;
			margin-top: 20px;
			color: #94a3b8;
			text-decoration: none;
			font-size: 0.85rem;
			transition: color 0.3s;
		}

		.forgot-pass:hover {
			color: #ff4d4d;
		}

		.back-home {
			display: block;
			text-align: center;
			margin-top: 30px;
			color: #64748b;
			text-decoration: none;
			font-weight: 500;
            font-size: 0.9rem;
			transition: color 0.3s;
		}

		.back-home:hover {
			color: #fff;
		}

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
	</style>
</head>

<body>
	<div class="login-wrapper">
		<div class="login-card">
			<div class="login-icon">
				<i class="fas fa-lock"></i>
			</div>
			<h1 class="login-title">Admin Login</h1>
            <p class="login-subtitle">Secure access to BBDMS control panel</p>
			
			<form method="post">
				<div class="form-group">
					<label class="form-label">Username</label>
					<input type="text" placeholder="Enter username" name="username" class="form-control-custom" required>
				</div>

				<div class="form-group">
					<label class="form-label">Password</label>
					<input type="password" placeholder="Enter password" name="password" class="form-control-custom" required>
				</div>

				<button class="submit-btn" name="login" type="submit">AUTHENTICATE</button>
				
				<a href="forgot-password.php" class="forgot-pass">Unable to access account?</a>
			</form>
			
			<a href="../index.php" class="back-home"><i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Return to Public Site</a>
		</div>
	</div>
<script src="../js/form-validation.js"></script>
</body>

</html>