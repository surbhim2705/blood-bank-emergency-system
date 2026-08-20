<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

$donor_id = $_GET['cid'] ?? 0;
$confirmed = false;
$error = "";

// Fetch donor details
$donor = null;
if($donor_id > 0) {
    $sql = "SELECT * FROM tblblooddonars WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $donor_id, PDO::PARAM_INT);
    $query->execute();
    $donor = $query->fetch(PDO::FETCH_OBJ);
}

// Handle Confirmation
if(isset($_POST['confirm_donor']) && $donor) {
    $requester_name = $_POST['requester_name'];
    $requester_contact = $_POST['requester_contact'];
    $required_for = $_POST['required_for'];
    $blood_group = $donor->BloodGroup;

    // Prevent duplicate confirmations
    $check_sql = "SELECT id FROM blood_requests WHERE donor_id = :did AND requester_name = :rname AND status = 'Confirmed'";
    $check_query = $dbh->prepare($check_sql);
    $check_query->execute([':did' => $donor_id, ':rname' => $requester_name]);
    
    if($check_query->rowCount() == 0) {
        // 1. Insert into tblbloodrequirer first to get the ID for the admin panel
        $msg = "Confirmed donation request for " . $donor->FullName . " (Required for: " . $required_for . ")";
        $sql_req = "INSERT INTO tblbloodrequirer (BloodDonarID, name, ContactNumber, BloodRequirefor, Message) VALUES (:did, :rname, :rcontact, :rf, :msg)";
        $q_req = $dbh->prepare($sql_req);
        $q_req->execute([':did' => $donor_id, ':rname' => $requester_name, ':rcontact' => $requester_contact, ':rf' => $required_for, ':msg' => $msg]);
        $requirer_id = $dbh->lastInsertId();

        // 2. Insert into blood_requests for the donor dashboard, linked via requirer_id
        $sql_insert = "INSERT INTO blood_requests (donor_id, requirer_id, requester_name, contact, blood_group, required_for, status) VALUES (:did, :rid, :rname, :rcontact, :bg, :rf, 'Confirmed')";
        $query_insert = $dbh->prepare($sql_insert);
        $query_insert->execute([
            ':did' => $donor_id,
            ':rid' => $requirer_id,
            ':rname' => $requester_name,
            ':rcontact' => $requester_contact,
            ':bg' => $blood_group,
            ':rf' => $required_for
        ]);



        $confirmed = true;
    } else {
        $error = "You have already confirmed this donor.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Donor | Blood Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #e63946;
            --secondary-color: #1d3557;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            padding-top: 0;
        }


        .donor-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .donor-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .donor-avatar {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.2);
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .info-label {
            color: #6c757d;
            font-weight: 500;
        }

        .info-value {
            color: var(--secondary-color);
            font-weight: 700;
        }

        .btn-confirm {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 700;
            width: 100%;
            margin-top: 30px;
            transition: all 0.3s ease;
        }

        .btn-confirm:hover:not(:disabled) {
            background: #d62839;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(230, 57, 70, 0.3);
        }

        .btn-confirm:disabled {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .success-box {
            text-align: center;
            background: #d4edda;
            color: #155724;
            padding: 30px;
            border-radius: 20px;
            margin-top: 20px;
        }

        .status-badge {
            background: #e9ecef;
            color: #495057;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include('includes/header.php');?>


<div class="container my-5">

    <div class="donor-card">
        <?php if(!$confirmed): ?>
            <div class="donor-header">
                <div class="donor-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="fw-bold"><?php echo $donor ? htmlentities($donor->FullName) : 'Donor Not Found'; ?></h3>
                <?php if($donor): ?>
                    <div class="status-badge">Available for Donation</div>
                <?php endif; ?>
            </div>

            <?php if($donor): ?>
                <div class="donor-info">
                    <div class="info-item">
                        <span class="info-label">Blood Group</span>
                        <span class="info-value text-danger"><?php echo htmlentities($donor->BloodGroup); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Number</span>
                        <span class="info-value"><?php echo htmlentities($donor->MobileNumber); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Location</span>
                        <span class="info-value"><?php echo htmlentities($donor->Address); ?></span>
                    </div>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Your Name</label>
                        <input type="text" name="requester_name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Required For (Patient Name / Relation)</label>
                        <input type="text" name="required_for" class="form-control" placeholder="e.g. My Father, John Smith, etc." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Your Contact Number</label>

                        <input type="text" name="requester_contact" class="form-control" placeholder="Enter your phone number" required>
                    </div>
                    
                    <button type="submit" name="confirm_donor" class="btn-confirm">
                        <i class="fas fa-check-circle me-2"></i> Confirm Donor
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Please select a donor from the list first.</div>
                <div class="text-center mt-3">
                    <a href="search-donor.php" class="btn btn-secondary">Back to Search</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="success-box">
                <i class="fas fa-check-circle fa-4x mb-3"></i>
                <h2 class="fw-bold">Donation Confirmed!</h2>
                <p class="mb-4">You have successfully linked with <strong><?php echo htmlentities($donor->FullName); ?></strong>.</p>
                <div class="status-badge bg-success text-white">Status: Donor Selected</div>
                <div class="mt-4 d-flex gap-2 justify-content-center">
                    <a href="index.php" class="btn btn-outline-success rounded-pill">Return Home</a>
                    <a href="nearest-donors.php?bg=<?php echo urlencode($donor->BloodGroup); ?>&city=<?php echo urlencode($donor->Address); ?>" class="btn btn-success rounded-pill">View More Nearby</a>
                </div>

            </div>
            
            <button class="btn-confirm" disabled>
                <i class="fas fa-lock me-2"></i> Donor Selected
            </button>
        <?php endif; ?>
    </div>
</div>

<?php include('includes/footer.php');?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/form-validation.js"></script>
</body>
</html>