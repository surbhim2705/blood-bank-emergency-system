<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    if (isset($_REQUEST['hidden'])) {
        $eid = intval($_GET['hidden']);
        $status = "0";
        $sql = "UPDATE tblblooddonars SET Status=:status WHERE id=:eid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query->execute();
        $msg = "Donor details hidden Successfully";
    }

    if (isset($_REQUEST['public'])) {
        $aeid = intval($_GET['public']);
        $status = 1;
        $sql = "UPDATE tblblooddonars SET Status=:status WHERE id=:aeid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
        $query->execute();
        $msg = "Donor details made public successfully";
    }

    if (isset($_REQUEST['del'])) {
        $did = intval($_GET['del']);
        $sql = "DELETE FROM tblblooddonars WHERE id = :did";
        $query = $dbh->prepare($sql);
        $query->bindParam(':did', $did, PDO::PARAM_INT);
        $query->execute();
        $msg = "Donor record deleted successfully";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Directory | BBDMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
</head>
<body>

    <?php include('includes/leftbar.php'); ?>

    <div class="main-content">
        <header class="header">
            <div>
                <h1>Donor Directory</h1>
                <p style="color: var(--text-dim);">Manage and monitor all registered blood donors.</p>
            </div>
            <div>
                <a href="download-records.php" class="btn-modern btn-primary-modern">
                    <i class="fa-solid fa-download"></i> Download List
                </a>
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
                            <th>Donor</th>
                            <th>Contact Info</th>
                            <th>Attributes</th>
                            <th>Blood Group</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * from tblblooddonars";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        if($query->rowCount() > 0) {
                            foreach($results as $result) { ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlentities($result->FullName); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-dim); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <i class="fa-solid fa-location-dot" style="font-size: 0.7rem;"></i> <?php echo htmlentities($result->Address); ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem;"><i class="fa-solid fa-phone" style="font-size: 0.7rem; color: var(--primary-color);"></i> <?php echo htmlentities($result->MobileNumber); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-dim);"><?php echo htmlentities($result->EmailId); ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem;"><?php echo htmlentities($result->Gender); ?>, <?php echo htmlentities($result->Age); ?> yrs</div>
                                    <div style="font-size: 0.75rem; color: var(--text-dim); font-style: italic;">"<?php echo htmlentities(substr($result->Message, 0, 30)); ?>..."</div>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(255, 77, 77, 0.1); color: var(--primary-color); font-weight: 700; font-size: 1rem;">
                                        <?php echo htmlentities($result->BloodGroup); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($result->status == 1 || $result->Status == 1): ?>
                                        <span class="badge badge-success">Public</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <?php if($result->status == 1 || $result->Status == 1): ?>
                                            <a href="donor-list.php?hidden=<?php echo $result->id; ?>" class="btn-modern btn-outline-modern" style="padding: 0.4rem 0.8rem;" onclick="return confirm('Hide this donor from public search?')" title="Hide">
                                                <i class="fa-solid fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="donor-list.php?public=<?php echo $result->id; ?>" class="btn-modern btn-outline-modern" style="padding: 0.4rem 0.8rem;" onclick="return confirm('Make this donor public?')" title="Make Public">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="donor-list.php?del=<?php echo $result->id; ?>" class="btn-modern btn-outline-modern" style="padding: 0.4rem 0.8rem; color: var(--danger);" onclick="return confirm('Delete this donor permanently?')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } 
                        } else { ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-dim);">No donors found in the registry.</td>
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