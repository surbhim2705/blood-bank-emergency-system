<?php
session_start();
include('includes/config.php');

// Protect page
if(!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION['donor_id'];

// Handle update
if(isset($_POST['update'])) {
    $name = $_POST['name'];
    $blood_group = $_POST['blood_group'];
    $contact = $_POST['contact'];

    $sql = "UPDATE tblblooddonars SET FullName = :name, BloodGroup = :bg, MobileNumber = :contact WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->execute([
        ':name' => $name,
        ':bg' => $blood_group,
        ':contact' => $contact,
        ':id' => $donor_id
    ]);
    $msg = "Profile updated successfully!";
}

// Fetch donor details from tblblooddonars
$sql_donor = "SELECT * FROM tblblooddonars WHERE id = :id";
$query_donor = $dbh->prepare($sql_donor);
$query_donor->bindParam(':id', $donor_id, PDO::PARAM_INT);
$query_donor->execute();
$donor = $query_donor->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Profile | Blood Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #e63946;
            --secondary-color: #1d3557;
            --sidebar-width: 260px;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--secondary-color);
            color: white;
            padding: 20px 0;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 0 25px 30px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 25px;
            transition: 0.3s;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            max-width: 700px;
            margin: 0 auto;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .btn-update {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">BLOOD BANK</div>
    <ul class="sidebar-menu">
        <li><a href="donor-dashboard.php"><i class="fas fa-th-large me-2"></i> Dashboard</a></li>
        <li><a href="#" class="active"><i class="fas fa-user me-2"></i> Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="profile-card">
        <h3 class="fw-bold mb-4">Edit Profile</h3>
        
        <?php if(isset($msg)): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlentities($donor->FullName); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address (Cannot be changed)</label>
                <input type="email" class="form-control" value="<?php echo htmlentities($donor->EmailId); ?>" disabled>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-select form-control" required>
                        <option value="A+" <?php if($donor->BloodGroup == 'A+') echo 'selected'; ?>>A+</option>
                        <option value="A-" <?php if($donor->BloodGroup == 'A-') echo 'selected'; ?>>A-</option>
                        <option value="B+" <?php if($donor->BloodGroup == 'B+') echo 'selected'; ?>>B+</option>
                        <option value="B-" <?php if($donor->BloodGroup == 'B-') echo 'selected'; ?>>B-</option>
                        <option value="O+" <?php if($donor->BloodGroup == 'O+') echo 'selected'; ?>>O+</option>
                        <option value="O-" <?php if($donor->BloodGroup == 'O-') echo 'selected'; ?>>O-</option>
                        <option value="AB+" <?php if($donor->BloodGroup == 'AB+') echo 'selected'; ?>>AB+</option>
                        <option value="AB-" <?php if($donor->BloodGroup == 'AB-') echo 'selected'; ?>>AB-</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlentities($donor->MobileNumber); ?>" required>
                </div>
            </div>
            <button type="submit" name="update" class="btn-update">Save Changes</button>
        </form>
    </div>
</div>

<script src="js/form-validation.js"></script>
</body>
</html>
