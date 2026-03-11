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
    <li><a href="petowner_homepage.php" class="nav-link" onclick="showLoadingOverlay()">Home</a></li>
    <li><a href="previous_medication.php" class="nav-link" onclick="showLoadingOverlay()">Record Pet Medication</a></li>
    <li><a href="previous_symptoms.php" class="nav-link" onclick="showLoadingOverlay()">Pet Symptoms Tracker</a></li>
    <li><a href="appointment.php" class="nav-link" onclick="showLoadingOverlay()">Check Pet Appointment</a></li>
</ul>

<div class="user-info">
    <a href="#" class="dropdown-toggle">
        <span><img src="images/user.png" alt="User Icon">Pet Owner</span>
    </a>
    <div class="dropdown-menu">
        <a href="petowner_profile.php" class="nav-link" onclick="showLoadingOverlay()">Profile</a>
        <a href="petowner_logout.php" class="nav-link" onclick="showLoadingOverlay()">Logout</a>
    </div>
</div>
