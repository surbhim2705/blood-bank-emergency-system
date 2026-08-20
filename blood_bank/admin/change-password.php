<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
// Code for change password	
if(isset($_POST['submit']))
	{
$password=md5($_POST['password']);
$newpassword=md5($_POST['newpassword']);
$username=$_SESSION['alogin'];
$sql ="SELECT Password FROM tbladmin WHERE UserName=:username and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':username', $username, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
$con="update tbladmin set Password=:newpassword where UserName=:username";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':username', $username, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();
$msg="Your Password succesfully changed";
}
else {
$error="Your current password is not valid.";	
}
}
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<title>Change Password | Innovative Admin</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
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
	<?php include('includes/leftbar.php');?>

	<div class="main-content">
        <header class="header">
            <div>
                <h1>Security Settings</h1>
                <p style="color: var(--text-dim);">Update your administrative login credentials.</p>
            </div>
        </header>

        <div class="form-card" style="margin: 0 auto; max-width: 600px;">
            <form method="post" name="chngpwd" onSubmit="return valid();">
                <?php if($error){?><div class="badge badge-danger mb-4" style="display: block; text-align: center; font-size: 0.9rem;"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="badge badge-success mb-4" style="display: block; text-align: center; font-size: 0.9rem;"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>
                
                <div class="form-group">
                    <label><i class="fa-solid fa-lock me-2" style="color: var(--primary-color);"></i> Current Password</label>
                    <input type="password" name="password" placeholder="Enter current password" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-key me-2" style="color: var(--primary-color);"></i> New Password</label>
                    <input type="password" name="newpassword" id="newpassword" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-check-double me-2" style="color: var(--primary-color);"></i> Confirm New Password</label>
                    <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirm new password" required>
                </div>

                <div style="margin-top: 2.5rem;">
                    <button class="btn-modern btn-primary-modern" name="submit" type="submit" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-shield-halved"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
	</div>

	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
</body>
</html>

<script src="../js/form-validation.js"></script>
</body>

</html>
<?php } ?>