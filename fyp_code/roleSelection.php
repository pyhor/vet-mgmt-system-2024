<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Selection | VCMS</title>
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <style>
        body {
            font-family: 'Itim', cursive;
            background-color: #e8ccb2ba;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            background-image: 
        linear-gradient(
            rgba(255, 255, 255, 0.9),
            rgba(232, 234, 231, 0.9)
        ),
        url('images/3f9735541996437da58e7a80b3a10d3d.jpg');
        }

        .container {
            background-color:#FAF3EB ; 
            border-radius: 15px;
            padding: 30px;
            width: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cute-animal {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            background-color: #896541;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cute-animal::before {
            content: '🐾';
            font-size: 50px;
        }

        h1,h2 {
            color: #C29A71;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .role-button {
            font-family: 'Itim', cursive;
            display: block;
            width: 100%;
            background-color: #896541;
            color: white;
            border: none;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .role-button:hover {
            background-color: #6d5133;
            transform: scale(1.05);
        }

        .role-button:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="cute-animal"></div>
        <h1>Welcome to VCMS</h1>
        <h2>Please Select Your Role</h2><br>
        <button class="role-button" onclick="openLogin('petowner_login.php')">Pet Owner</button>
        <button class="role-button" onclick="openLogin('vet_login.php')">Vet</button>
        <button class="role-button" onclick="openLogin('nurse_login.php')">Nurse</button>
    </div>

    <script>
        function openLogin(url) {
            window.open(url, '_blank'); // Open the selected role's login page in a new tab
        }
    </script>
</body>
</html>