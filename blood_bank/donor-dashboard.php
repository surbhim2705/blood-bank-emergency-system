<?php
session_start();
include('includes/config.php');

// Protect page
if(!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION['donor_id'];

// Fetch donor details from tblblooddonars
$sql_donor = "SELECT * FROM tblblooddonars WHERE id = :id";
$query_donor = $dbh->prepare($sql_donor);
$query_donor->bindParam(':id', $donor_id, PDO::PARAM_INT);
$query_donor->execute();
$donor = $query_donor->fetch(PDO::FETCH_OBJ);

// Fetch stats
$sql_total = "SELECT COUNT(*) FROM blood_requests WHERE donor_id = :id";
$q_total = $dbh->prepare($sql_total);
$q_total->execute(['id' => $donor_id]);
$total_requests = $q_total->fetchColumn();

$sql_accepted = "SELECT COUNT(*) FROM blood_requests WHERE donor_id = :id AND status = 'Accepted'";
$q_acc = $dbh->prepare($sql_accepted);
$q_acc->execute(['id' => $donor_id]);
$accepted_requests = $q_acc->fetchColumn();

$sql_pending = "SELECT COUNT(*) FROM blood_requests WHERE donor_id = :id AND status IN ('Pending', 'Confirmed')";
$q_pen = $dbh->prepare($sql_pending);
$q_pen->execute(['id' => $donor_id]);
$pending_requests = $q_pen->fetchColumn();


// Fetch all requests for table
$sql_reqs = "SELECT * FROM blood_requests WHERE donor_id = :id AND request_date >= DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY id DESC";
$query_reqs = $dbh->prepare($sql_reqs);
$query_reqs->execute(['id' => $donor_id]);
$requests = $query_reqs->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard | Blood Bank</title>
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
            margin: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--secondary-color);
            color: white;
            padding: 20px 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 0 25px 30px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            padding: 5px 20px;
        }

        .sidebar-menu a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .sidebar-menu a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu a.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        /* Navbar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Dashboard Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: none;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .icon-total { background: rgba(69, 123, 157, 0.1); color: #457b9d; }
        .icon-accepted { background: rgba(42, 157, 143, 0.1); color: #2a9d8f; }
        .icon-pending { background: rgba(233, 196, 106, 0.1); color: #e9c46a; }

        /* Table */
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-top: 30px;
        }

        .table thead th {
            border: none;
            background: #f8f9fa;
            padding: 15px;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .table tbody td {
            padding: 20px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f1f1;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-accepted { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .badge-confirmed { background: #cfe2ff; color: #084298; }


        .btn-action {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-heartbeat me-2"></i> BLOOD BANK
    </div>
    <ul class="sidebar-menu">
        <li><a href="donor-dashboard.php" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li><a href="#"><i class="fas fa-paper-plane"></i> Requests</a></li>
        <li><a href="donor-profile.php"><i class="fas fa-user"></i> Profile</a></li>
        <hr style="border-color: rgba(255,255,255,0.1)">
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Navbar -->
    <div class="top-navbar">
        <h4 class="fw-bold mb-0">Overview</h4>
        <div class="user-profile">
            <div class="text-end d-none d-sm-block">
                <p class="mb-0 fw-bold"><?php echo htmlentities($donor->FullName); ?></p>
                <small class="text-muted">Blood Group: <?php echo htmlentities($donor->BloodGroup); ?></small>
            </div>
            <div class="user-avatar">
                <?php echo strtoupper(substr($donor->FullName, 0, 1)); ?>
            </div>
        </div>
    </div>


    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px; background-color: #d4edda; color: #155724;">
            <i class="fas fa-check-circle me-2"></i> <?php echo htmlentities($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px; background-color: #f8d7da; color: #721c24;">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlentities($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-total">
                    <i class="fas fa-list-ul"></i>
                </div>
                <h6 class="text-muted mb-1">Total Requests</h6>
                <h3 class="fw-bold mb-0"><?php echo $total_requests; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-accepted">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h6 class="text-muted mb-1">Accepted</h6>
                <h3 class="fw-bold mb-0"><?php echo $accepted_requests; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
                <h6 class="text-muted mb-1">Pending</h6>
                <h3 class="fw-bold mb-0"><?php echo $pending_requests; ?></h3>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Recent Blood Requests</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Requester Name</th>
                        <th>Contact</th>
                        <th>Blood Group</th>
                        <th>Requested Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($requests): foreach($requests as $req): ?>
                    <tr id="request-<?php echo htmlentities($req->id); ?>">
                        <td class="fw-bold text-secondary">#<?php echo htmlentities($req->id); ?></td>
                        <td class="fw-bold"><?php echo htmlentities($req->requester_name); ?></td>
                        <td><?php echo htmlentities($req->contact); ?></td>
                        <td><span class="badge bg-danger"><?php echo htmlentities($req->blood_group); ?></span></td>
                        <td class="text-muted small"><?php echo date('M d, Y h:i A', strtotime($req->request_date)); ?></td>
                        <td>
                            <span class="status-badge badge-<?php echo strtolower($req->status); ?>">
                                <?php echo $req->status; ?>
                            </span>
                        </td>
                        <td>
                            <?php if($req->status == 'Pending' || $req->status == 'Confirmed'): ?>
                                <a href="update-request.php?id=<?php echo $req->id; ?>&status=Accepted" class="btn btn-success btn-action me-2">
                                    <i class="fas fa-check me-1"></i> Accept
                                </a>
                                <a href="update-request.php?id=<?php echo $req->id; ?>&status=Rejected" class="btn btn-outline-danger btn-action">
                                    <i class="fas fa-times me-1"></i> Reject
                                </a>
                            <?php elseif($req->status == 'Accepted'): ?>
                                <a href="update-request.php?id=<?php echo $req->id; ?>&status=Rejected" class="btn btn-outline-danger btn-action">
                                    <i class="fas fa-times me-1"></i> Reject
                                </a>
                            <?php elseif($req->status == 'Rejected'): ?>
                                <a href="update-request.php?id=<?php echo $req->id; ?>&status=Accepted" class="btn btn-success btn-action">
                                    <i class="fas fa-check me-1"></i> Accept
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">Processed</span>
                            <?php endif; ?>

                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No blood requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
