<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{ 

if(isset($_POST['submit']))
  {
$fullname=$_POST['fullname'];
$mobile=$_POST['mobileno'];
$email=$_POST['emailid'];
$age=$_POST['age'];
$gender=$_POST['gender'];
$blodgroup=$_POST['bloodgroup'];
$address=$_POST['address'];
$message=$_POST['message'];
$status=1;
$sql="INSERT INTO  tblblooddonars(FullName,MobileNumber,EmailId,Age,Gender,BloodGroup,Address,Message,status) VALUES(:fullname,:mobile,:email,:age,:gender,:blodgroup,:address,:message,:status)";
$query = $dbh->prepare($sql);
$query->bindParam(':fullname',$fullname,PDO::PARAM_STR);
$query->bindParam(':mobile',$mobile,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':age',$age,PDO::PARAM_STR);
$query->bindParam(':gender',$gender,PDO::PARAM_STR);
$query->bindParam(':blodgroup',$blodgroup,PDO::PARAM_STR);
$query->bindParam(':address',$address,PDO::PARAM_STR);
$query->bindParam(':message',$message,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Your info submitted successfully";
}
else 
{
$error="Something went wrong. Please try again";
}

}


	?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<title>Add Donor | Innovative Admin</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
</head>

<body>
	<?php include('includes/leftbar.php');?>

	<div class="main-content">
        <header class="header">
            <div>
                <h1>Register New Donor</h1>
                <p style="color: var(--text-dim);">Onboard a new life-saver to the database.</p>
            </div>
            <a href="donor-list.php" class="btn-modern btn-outline-modern">
                <i class="fa-solid fa-users"></i> View All Donors
            </a>
        </header>

        <div class="form-card" style="max-width: 1000px; margin: 0 auto;">
            <form method="post" enctype="multipart/form-data">
                <?php if($error){?><div class="badge badge-danger mb-4" style="display: block; text-align: center; font-size: 0.9rem;"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="badge badge-success mb-4" style="display: block; text-align: center; font-size: 0.9rem;"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>
                
                <div class="dashboard-row" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                    <div class="form-group">
                        <label><i class="fa-solid fa-user me-2"></i> Full Name</label>
                        <input type="text" name="fullname" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-phone me-2"></i> Mobile Number</label>
                        <input type="text" name="mobileno" maxlength="10" placeholder="9876543210" required>
                    </div>
                </div>

                <div class="dashboard-row" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                    <div class="form-group">
                        <label><i class="fa-solid fa-envelope me-2"></i> Email Address</label>
                        <input type="email" name="emailid" placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-cake-candles me-2"></i> Age</label>
                        <input type="number" name="age" placeholder="25" required>
                    </div>
                </div>

                <div class="dashboard-row" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                    <div class="form-group">
                        <label><i class="fa-solid fa-venus-mars me-2"></i> Gender</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-droplet me-2"></i> Blood Group</label>
                        <select name="bloodgroup" required>
                            <option value="">Select Group</option>
                            <?php $sql = "SELECT * from  tblbloodgroup ";
                            $query = $dbh -> prepare($sql);
                            $query->execute();
                            $results=$query->fetchAll(PDO::FETCH_OBJ);
                            foreach($results as $result) { ?>	
                            <option value="<?php echo htmlentities($result->BloodGroup);?>"><?php echo htmlentities($result->BloodGroup);?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-location-dot me-2"></i> Residential Address</label>
                    <textarea name="address" rows="3" placeholder="Enter full address..."></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-message me-2"></i> Medical Note / Message</label>
                    <textarea name="message" rows="3" placeholder="Any health conditions or notes..."></textarea>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button type="reset" class="btn-modern btn-outline-modern" style="flex: 1; justify-content: center;">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                    <button class="btn-modern btn-primary-modern" name="submit" type="submit" style="flex: 2; justify-content: center;">
                        <i class="fa-solid fa-heart"></i> Register Donor
                    </button>
                </div>
            </form>
        </div>
	</div>

	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
<script src="../js/form-validation.js"></script>
</body>
</html>
<?php } ?>