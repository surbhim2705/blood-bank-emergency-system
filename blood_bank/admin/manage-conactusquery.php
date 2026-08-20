<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    if (isset($_REQUEST['eid'])) {
        $eid = intval($_GET['eid']);
        $status = 1;
        $sql = "UPDATE tblcontactusquery SET status=:status WHERE id=:eid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query->execute();
        $msg = "Query marked as read successfully";
    }

    if (isset($_REQUEST['del'])) {
        $did = intval($_GET['del']);
        $sql = "DELETE FROM tblcontactusquery WHERE id = :did";
        $query = $dbh->prepare($sql);
        $query->bindParam(':did', $did, PDO::PARAM_INT);
        $query->execute();
        $msg = "Contact query deleted successfully";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Queries | BBDMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
</head>
<body>

    <?php include('includes/leftbar.php'); ?>

    <div class="main-content">
        <header class="header">
            <div>
                <h1>Contact Queries</h1>
                <p style="color: var(--text-dim);">Review and respond to inquiries from the public.</p>
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
                            <th>Sender Details</th>
                            <th>Contact Info</th>
                            <th>Message Content</th>
                            <th>Posting Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * from tblcontactusquery ORDER BY id DESC";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        if($query->rowCount() > 0) {
                            foreach($results as $result) { ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlentities($result->Name); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-dim);">User Inquiry</div>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem;"><i class="fa-solid fa-envelope" style="font-size: 0.7rem; color: var(--primary-color);"></i> <?php echo htmlentities($result->EmailId); ?></div>
                                    <div style="font-size: 0.85rem; color: var(--text-dim);"><i class="fa-solid fa-phone" style="font-size: 0.7rem;"></i> <?php echo htmlentities($result->ContactNumber); ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem; max-width: 300px; line-height: 1.4; color: var(--text-main);">
                                        <?php echo htmlentities($result->Message); ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; color: var(--text-dim);">
                                        <i class="fa-solid fa-calendar-alt" style="margin-right: 5px;"></i>
                                        <?php echo date('M d, Y', strtotime($result->PostingDate)); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($result->status == 1): ?>
                                        <span class="badge badge-success">Read</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <?php if($result->status != 1): ?>
                                            <a href="manage-conactusquery.php?eid=<?php echo $result->id;?>" class="btn-modern btn-outline-modern" style="padding: 0.4rem 0.8rem; color: #10b981;" onclick="return confirm('Mark this query as read?')" title="Mark as Read">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="manage-conactusquery.php?del=<?php echo $result->id;?>" class="btn-modern btn-outline-modern" style="padding: 0.4rem 0.8rem; color: var(--danger);" onclick="return confirm('Delete this query permanently?')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-dim);">No queries found.</td>
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
