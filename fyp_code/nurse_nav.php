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
    <li><a href="nurse_homepage.php" class="nav-link" onclick="showLoadingOverlay()">Home</a></li>
    <li><a href="nurse_livechat.php" class="nav-link" onclick="showLoadingOverlay()">Response Live Chat</a></li>
</ul>


<div class="user-info">
    <a href="#" class="dropdown-toggle">
        <span><img src="images/user.png" alt="User  Icon">Nurse</span>
    </a>
    <div class="dropdown-menu">
        <a href="nurse_profile.php" class="nav-link" onclick="showLoadingOverlay()">Profile</a>
        <a href="nurse_logout.php" class="nav-link" onclick="showLoadingOverlay()">Logout</a>
    </div>
</div>