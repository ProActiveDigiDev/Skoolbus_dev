<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Skoolbus</title>
    <!-- Add any CSS links, meta tags, or other header information here -->
    <style>
        /* Center the content vertically and horizontally */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #fdd219;
        }
        .container {
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        .logo {
            display:flex;
            height: auto;
            margin-bottom:20px;
        }
        .welcome-message {
            font-size: 24px;
            margin-bottom: 20px;
            color: #13343d;
            font-weight: bold;
        }
        .go-to-busstop {
            text-decoration: none;
            font-weight: bold;
            color: #13343d;
            padding:10px;
            border: solid 1px #13343d;
        }
        .go-to-busstop:hover {
            background-color: #13343d;
            color: #fdd219;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Welcome Message -->
        <div class="welcome-message">
            <span>Welcome to</span>
        </div>

        <!-- Logo Section -->
        <div class="logo">
            <img src="{{ asset('storage/branding/logo.png') }}" alt="Logo">
        </div>
        
        <!-- Link to Busstop -->
        <div>
            <a href="/Busstop" class="go-to-busstop">Go to Busstop</a>
        </div>
    </div>
</body>
</html>
