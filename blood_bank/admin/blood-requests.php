<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {

    // For deleting requests if needed
    if (isset($_GET['del'])) {
        $id = intval($_GET['del']);
        
        // Delete from the status table first
        $sql_status = "DELETE FROM blood_requests WHERE requirer_id = :id";
        $query_status = $dbh->prepare($sql_status);
        $query_status->bindParam(':id', $id, PDO::PARAM_INT);
        $query_status->execute();

        // Then delete from the primary requirer table
        $sql = "DELETE FROM tblbloodrequirer WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        
        $msg = "Request deleted successfully from all modules";
    }

    // For accepting requests
    if (isset($_GET['aeid'])) {
        $aeid = intval($_GET['aeid']);
        $status = "Accepted";
        
        $check = $dbh->prepare("SELECT id FROM blood_requests WHERE requirer_id = :aeid");
        $check->execute([':aeid' => $aeid]);
        
        if ($check->rowCount() > 0) {
            $sql = "UPDATE blood_requests SET status = :status WHERE requirer_id = :aeid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':aeid', $aeid, PDO::PARAM_INT);
        } else {
            $sql = "INSERT INTO blood_requests (requirer_id, status, donor_id, requester_name, contact, blood_group) 
                    SELECT id, :status, BloodDonarID, name, ContactNumber, BloodRequirefor 
                    FROM tblbloodrequirer WHERE id = :aeid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':aeid', $aeid, PDO::PARAM_INT);
        }
        $query->execute();
        $msg = "Request accepted successfully";
    }

    // For rejecting requests
    if (isset($_GET['reid'])) {
        $reid = intval($_GET['reid']);
        $status = "Rejected";
        
        $check = $dbh->prepare("SELECT id FROM blood_requests WHERE requirer_id = :reid");
        $check->execute([':reid' => $reid]);
        
        if ($check->rowCount() > 0) {
            $sql = "UPDATE blood_requests SET status = :status WHERE requirer_id = :reid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':reid', $reid, PDO::PARAM_INT);
        } else {
            $sql = "INSERT INTO blood_requests (requirer_id, status, donor_id, requester_name, contact, blood_group) 
                    SELECT id, :status, BloodDonarID, name, ContactNumber, BloodRequirefor 
                    FROM tblbloodrequirer WHERE id = :reid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':reid', $reid, PDO::PARAM_INT);
        }
        $query->execute();
        $msg = "Request rejected successfully";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests | BBDMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
</head>
<body>

    <?php include('includes/leftbar.php'); ?>

    <div class="main-content">
        <header class="header">
            <div>
                <h1>Blood Requests</h1>
                <p style="color: var(--text-dim);">Monitor and process urgent blood requirements.</p>
            </div>
        </header>

        <?php if($msg): ?>
            <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                <i class="fa-solid fa-circle-check"></i> <?php echo htmlentities($msg); ?>
            </div>
        <?php endif; ?>

        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Requested By</th>
                            <th>Requirement</th>
                            <th>Target Donor</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT r.id as reqid, r.BloodDonarID, r.name as reqname, r.EmailId as reqemail, r.ContactNumber as reqcontact, r.BloodRequirefor, r.Message, r.ApplyDate, d.FullName as donorname, d.BloodGroup as donorgroup, br.status as requeststatus 
                                FROM tblbloodrequirer r 
                                LEFT JOIN tblblooddonars d ON d.id = r.BloodDonarID 
                                LEFT JOIN blood_requests br ON br.requirer_id = r.id 
                                ORDER BY r.ApplyDate DESC";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        if($query->rowCount() > 0) {
                            foreach($results as $row) { 
                                $status = $row->requeststatus ?? 'Pending';
                                ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlentities($row->reqname); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-dim);"><?php echo htmlentities($row->reqcontact); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-dim);"><?php echo date('M d, Y', strtotime($row->ApplyDate)); ?></div>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(52, 152, 219, 0.1); color: #3498db; font-weight: 700;">
                                        <?php echo htmlentities($row->BloodRequirefor); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row->donorname): ?>
                                        <div style="font-size: 0.9rem; font-weight: 500;"><?php echo htmlentities($row->donorname); ?></div>
                                        <div style="font-size: 0.75rem; color: var(--primary-color);">Group: <?php echo htmlentities($row->donorgroup); ?></div>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(148, 163, 184, 0.1); color: var(--text-dim);">General Request</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; max-width: 200px; color: var(--text-dim); font-style: italic;">
                                        "<?php echo htmlentities($row->Message); ?>"
                                    </div>
                                </td>
                                <td>
                                    <?php if($status == 'Accepted'): ?>
                                        <span class="badge badge-success">Accepted</span>
                                    <?php elseif($status == 'Rejected'): ?>
                                        <span class="badge badge-danger">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(255, 255, 255, 0.05); color: var(--text-dim);">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <?php if($status != 'Accepted'): ?>
                                            <a href="blood-requests.php?aeid=<?php echo $row->reqid;?>" class="btn-modern" style="padding: 0.4rem 0.8rem; background: rgba(16, 185, 129, 0.2); color: #10b981;" onclick="return confirm('Accept this request?')" title="Accept">
                                                <i class="fa-solid fa-check"></i> Accept
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($status != 'Rejected'): ?>
                                            <a href="blood-requests.php?reid=<?php echo $row->reqid;?>" class="btn-modern" style="padding: 0.4rem 0.8rem; background: rgba(239, 68, 68, 0.2); color: #ef4444;" onclick="return confirm('Reject this request?')" title="Reject">
                                                <i class="fa-solid fa-times"></i> Reject
                                            </a>
                                        <?php endif; ?>

                                        <a href="blood-requests.php?del=<?php echo $row->reqid;?>" class="btn-modern btn-outline-modern" style="padding: 0.4rem 0.8rem;" onclick="return confirm('Delete this record?')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-dim);">No blood requests found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
<?php } ?>
