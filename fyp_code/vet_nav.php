<script src="showloading.js"></script>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;">
    
    <img src="images/Animation - 1732178074480.gif" alt="Loading..." style="width: 200px; height: 200px;">
    <p style="margin-top: 20px; font-size: 18px;">Loading...</p>
</div>

<ul>
    <li><a href="vet_homepage.php" class="nav-link" onclick="showLoadingOverlay()">Home</a></li>
    <li><a href="previous_schedule.php" class="nav-link" onclick="showLoadingOverlay()">Schedule Management</a></li>
    <li><a href="vetReport.php" class="nav-link" onclick="showLoadingOverlay()">Pet Symptom Trend Analytic</a></li>
    <li><a href="vetReport2.php" class="nav-link" onclick="showLoadingOverlay()">Appointment Status Analytic</a></li>
</ul>

    <div class="user-info">
    <a href="#" class="dropdown-toggle">
        <span><img src="images/user.png" alt="User  Icon">Veterinarian</span>
    </a>
    <div class="dropdown-menu">
        <a href="vet_profile.php" class="nav-link" onclick="showLoadingOverlay()">Profile</a>
        <a href="vet_logout.php" class="nav-link" onclick="showLoadingOverlay()">Logout</a>
    </div>
</div>