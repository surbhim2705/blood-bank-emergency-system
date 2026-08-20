<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if(strlen($_SESSION['alogin']) == 0)
{	
    header('location:index.php');
    exit();
}
else{
// Code for adding blood group
if(isset($_POST['submit']))
{
    $bloodgroup = trim($_POST['bloodgroup']);

    // Check duplicate
    $checksql = "SELECT BloodGroup FROM tblbloodgroup WHERE BloodGroup = :bloodgroup";
    $checkquery = $dbh->prepare($checksql);
    $checkquery->bindParam(':bloodgroup', $bloodgroup, PDO::PARAM_STR);
    $checkquery->execute();

    if($checkquery->rowCount() > 0)
    {
        $error = "Blood Group already exists";
    }
    else
    {
        $sql = "INSERT INTO tblbloodgroup(BloodGroup) VALUES(:bloodgroup)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bloodgroup', $bloodgroup, PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $dbh->lastInsertId();

        if($lastInsertId)
        {
            $msg = "Blood Group created successfully";
        }
        else
        {
            $error = "Something went wrong. Please try again";
        }
    }
}
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<title>Add Blood Group | Innovative Admin</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
</head>

<body>
	<?php include('includes/leftbar.php');?>

	<div class="main-content">
        <header class="header">
            <div>
                <h1>Add Blood Group</h1>
                <p style="color: var(--text-dim);">Register a new blood group into the system.</p>
            </div>
            <a href="manage-bloodgroup.php" class="btn-modern btn-outline-modern">
                <i class="fa-solid fa-list"></i> Manage Groups
            </a>
        </header>

        <div class="form-card" style="margin: 0 auto;">
            <form method="post" class="form-horizontal">
                <?php if($error){?><div class="badge badge-danger mb-4" style="display: block; text-align: center; font-size: 0.9rem;"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="badge badge-success mb-4" style="display: block; text-align: center; font-size: 0.9rem;"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>
                
                <div class="form-group">
                    <label>Blood Group Name</label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-vial" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--primary-color);"></i>
                        <input type="text" name="bloodgroup" style="padding-left: 45px;" placeholder="e.g. A+, O-, AB+" required>
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <button class="btn-modern btn-primary-modern" name="submit" type="submit" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-plus-circle"></i> Create Blood Group
                    </button>
                </div>
            </form>
        </div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
</body>

</html>

<script src="../js/form-validation.js"></script>
</body>

</html>
<?php } ?>