<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    if (isset($_GET['del'])) {
        $id = intval($_GET['del']);
        $sql = "DELETE FROM tblbloodgroup WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $msg = "Blood group deleted successfully";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blood Groups | BBDMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
</head>
<body>

    <?php include('includes/leftbar.php'); ?>

    <div class="main-content">
        <header class="header">
            <div>
                <h1>Manage Blood Groups</h1>
                <p style="color: var(--text-dim);">Register and categorize available blood groups.</p>
            </div>
            <div>
                <a href="add-bloodgroup.php" class="btn-modern btn-primary-modern">
                    <i class="fa-solid fa-plus"></i> Add New Group
                </a>
            </div>
        </header>

        <?php if($msg): ?>
            <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                <i class="fa-solid fa-circle-check"></i> <?php echo htmlentities($msg); ?>
            </div>
        <?php endif; ?>

        <div class="table-card" style="max-width: 800px;">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Blood Group</th>
                            <th>Creation Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * from tblbloodgroup";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;
                        if($query->rowCount() > 0) {
                            foreach($results as $result) { ?>
                            <tr>
                                <td><?php echo $cnt; ?></td>
                                <td>
                                    <span class="badge" style="background: rgba(255, 77, 77, 0.1); color: var(--primary-color); font-weight: 800; font-size: 1.1rem; padding: 0.6rem 1.2rem;">
                                        <?php echo htmlentities($result->BloodGroup); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem; color: var(--text-dim);">
                                        <i class="fa-solid fa-calendar-days" style="margin-right: 5px;"></i>
                                        <?php echo date('M d, Y', strtotime($result->PostingDate)); ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="manage-bloodgroup.php?del=<?php echo $result->id;?>" class="btn-modern btn-outline-modern" style="padding: 0.5rem 1rem; color: var(--danger);" onclick="return confirm('Are you sure you want to delete this blood group?');" title="Delete">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $cnt++; } 
                        } else { ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-dim);">No blood groups listed.</td>
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
