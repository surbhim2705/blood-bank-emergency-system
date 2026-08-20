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
    $adminid=$_SESSION['alogin'];
    $AName=$_POST['adminname'];
  $mobno=$_POST['mobilenumber'];
  $email=$_POST['email'];
  $sql="update tbladmin set AdminName=:adminname,MobileNumber=:mobilenumber,Email=:email where UserName=:aid";
     $query = $dbh->prepare($sql);
     $query->bindParam(':adminname',$AName,PDO::PARAM_STR);
     $query->bindParam(':email',$email,PDO::PARAM_STR);
     $query->bindParam(':mobilenumber',$mobno,PDO::PARAM_STR);
     $query->bindParam(':aid',$adminid,PDO::PARAM_STR);
$query->execute();

    echo '<script>alert("Your profile has been updated")</script>';
    echo "<script>window.location.href ='profile.php'</script>";

  }
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<title>Admin Profile | Innovative Admin</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
</head>

<body>
	<?php include('includes/leftbar.php');?>

	<div class="main-content">
        <header class="header">
            <div>
                <h1>Admin Profile</h1>
                <p style="color: var(--text-dim);">Manage your administrative account details.</p>
            </div>
        </header>

        <div class="form-card" style="margin: 0 auto; max-width: 800px;">
            <form method="post">
                <?php
                $sql="SELECT * from  tbladmin";
                $query = $dbh -> prepare($sql);
                $query->execute();
                $results=$query->fetchAll(PDO::FETCH_OBJ);
                if($query->rowCount() > 0)
                {
                foreach($results as $row)
                { ?>
                
                <div class="form-group">
                    <label><i class="fa-solid fa-user-shield me-2" style="color: var(--primary-color);"></i> Admin Full Name</label>
                    <input type="text" name="adminname" value="<?php echo htmlentities($row->AdminName);?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-id-badge me-2" style="color: var(--primary-color);"></i> Username (Account ID)</label>
                    <input type="text" value="<?php echo htmlentities($row->UserName);?>" readonly style="background: rgba(255,255,255,0.02); color: var(--text-dim);">
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-phone me-2" style="color: var(--primary-color);"></i> Contact Number</label>
                    <input type="text" name="mobilenumber" value="<?php echo htmlentities($row->MobileNumber);?>" maxlength="10" required pattern="[0-9]+">
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-envelope me-2" style="color: var(--primary-color);"></i> Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlentities($row->Email);?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-check me-2" style="color: var(--primary-color);"></i> Account Registered On</label>
                    <input type="text" value="<?php echo htmlentities($row->AdminRegdate);?>" readonly style="background: rgba(255,255,255,0.02); color: var(--text-dim);">
                </div>

                <div style="margin-top: 2.5rem;">
                    <button class="btn-modern btn-primary-modern" name="submit" type="submit" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-save"></i> Update Profile Information
                    </button>
                </div>
                <?php }} ?>
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