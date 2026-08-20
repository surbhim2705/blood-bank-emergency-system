

<?php
session_start();
error_reporting(0);
include('../includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    // Basic stats already in original logic
    // Basic stats logic
    $sql = "SELECT COUNT(*) from tblbloodgroup";
    $query = $dbh->prepare($sql);
    $query->execute();
    $bg_count = $query->fetchColumn();

    $sql1 = "SELECT COUNT(*) from tblblooddonars";
    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $regbd = $query1->fetchColumn();

    $sql6 = "SELECT COUNT(*) FROM tblcontactusquery";
    $query6 = $dbh->prepare($sql6);
    $query6->execute();
    $totalQueries = $query6->fetchColumn();

    $sql_req = "SELECT COUNT(*) from tblbloodrequirer";
    $query_req = $dbh->prepare($sql_req);
    $query_req->execute();
    $totalreuqests = $query_req->fetchColumn();

    // Data for the innovative chart (Donor Distribution)
    $sql_chart = "SELECT BloodGroup, COUNT(*) as count FROM tblblooddonars GROUP BY BloodGroup";
    $query_chart = $dbh->prepare($sql_chart);
    $query_chart->execute();
    $chart_results = $query_chart->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $data = [];
    foreach($chart_results as $row) {
        $labels[] = $row['BloodGroup'];
        $data[] = $row['count'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innovative Admin Dashboard | BBDMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/innovative-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
            margin-top: 1rem;
        }
        .main-stats-panel {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        .glass-chart-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 2rem;
            height: 400px;
        }
        .activity-feed {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .activity-item {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .action-hub {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
        }
        .hub-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            border-radius: 20px;
            text-align: center;
            text-decoration: none;
            color: white;
            transition: 0.3s;
        }
        .hub-btn:hover {
            background: rgba(255, 77, 77, 0.1);
            border-color: var(--primary-color);
            transform: translateY(-5px);
        }
        .hub-btn i { font-size: 1.5rem; color: var(--primary-color); margin-bottom: 0.5rem; display: block; }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-up { animation: slideUp 0.6s ease forwards; }
    </style>
</head>
<body>

    <?php include('includes/leftbar.php'); ?>

    <div class="main-content">
        <header class="header animate-up">
            <div>
                <h1 style="font-size: 2.2rem; letter-spacing: -1px;">Intelligence Hub</h1>
                <p style="color: var(--text-dim); font-size: 1.1rem;">Operational oversight & system analytics.</p>
            </div>
            <div style="background: rgba(255, 77, 77, 0.1); padding: 0.8rem 1.5rem; border-radius: 100px; border: 1px solid var(--glass-border);">
                <i class="fa-solid fa-circle" style="color: #10b981; font-size: 0.7rem; margin-right: 8px;"></i>
                <span style="font-weight: 600; font-size: 0.9rem;">System Live</span>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="stats-grid animate-up" style="animation-delay: 0.1s;">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(52, 152, 219, 0.1); color: #3498db;"><i class="fa-solid fa-vials"></i></div>
                <div class="stat-info">
                    <h3>Blood Groups</h3>
                    <div class="value"><?php echo $bg_count; ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <h3>Donors</h3>
                    <div class="value"><?php echo $regbd; ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-heart-pulse"></i></div>
                <div class="stat-info">
                    <h3>Requests</h3>
                    <div class="value"><?php echo $totalreuqests; ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;"><i class="fa-solid fa-envelope-open-text"></i></div>
                <div class="stat-info">
                    <h3>Queries</h3>
                    <div class="value"><?php echo $totalQueries; ?></div>
                </div>
            </div>
        </div>

        <div class="dashboard-container" style="grid-template-columns: 1fr;">
            <div class="main-stats-panel animate-up" style="animation-delay: 0.2s;">
                <!-- Chart Card -->
                <div class="glass-chart-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <h2 style="font-size: 1.3rem; margin: 0;">Donor Distribution</h2>
                        <i class="fa-solid fa-chart-simple" style="color: var(--text-dim);"></i>
                    </div>
                    <canvas id="donorDistributionChart"></canvas>
                </div>

                <!-- Quick Actions Hub -->
                <div class="action-hub">
                    <a href="add-donor.php" class="hub-btn">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Add Donor</span>
                    </a>
                    <a href="manage-bloodgroup.php" class="hub-btn">
                        <i class="fa-solid fa-vials"></i>
                        <span>Groups</span>
                    </a>
                    <a href="blood-requests.php" class="hub-btn">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span>Logs</span>
                    </a>

                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('donorDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Donors per Group',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'rgba(255, 77, 77, 0.6)',
                    borderColor: '#ff4d4d',
                    borderWidth: 2,
                    borderRadius: 12,
                    hoverBackgroundColor: '#ff4d4d'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8', font: { family: 'Inter' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { family: 'Inter' } }
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php } ?>