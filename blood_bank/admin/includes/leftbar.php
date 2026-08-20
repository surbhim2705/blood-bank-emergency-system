<div class="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-droplet fa-2x"></i>
        <h2>BBDMS PRO</h2>
    </div>
    <ul class="nav-links">
        <li><a href="dashboard.php" id="nav-dashboard"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li>
            <a href="manage-bloodgroup.php" id="nav-bloodgroups"><i class="fa-solid fa-vials"></i> Blood Groups</a>
        </li>

        <li><a href="donor-list.php" id="nav-donors"><i class="fa-solid fa-users"></i> Donor List</a></li>
        <li><a href="blood-requests.php" id="nav-requests"><i class="fa-solid fa-hand-holding-heart"></i> Blood Requests</a></li>
        <li><a href="manage-conactusquery.php" id="nav-queries"><i class="fa-solid fa-envelope"></i> Contact Queries</a></li>

        <li style="margin-top: auto;"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>

<script>
    // Active link highlighting
    document.addEventListener('DOMContentLoaded', function() {
        const path = window.location.pathname.split('/').pop();
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            if (link.getAttribute('href') === path) {
                link.classList.add('active');
            }
        });
    });
</script>