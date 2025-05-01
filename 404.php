<?php
require_once 'admin/config/dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Page Not Found | Beyond Doubt Clothing</title>
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/logo/logo.ico" type="image/x-icon">
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="shop/css/important.css">
    <link rel="stylesheet" href="shop/css/headerfooter.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
            text-align: center;
            margin: 0 auto;
            width: 100%;
        }
        .error-code {
            font-size: clamp(4rem, 15vw, 8rem);
            font-weight: 700;
            margin-bottom: 0;
            background: linear-gradient(195deg, #FF7F50, #FF6347);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            line-height: 1.2;
        }
        .error-text {
            font-size: clamp(1.2rem, 5vw, 1.5rem);
            text-align: center;
            margin-bottom: 1.5rem;
            width: 100%;
        }
        .error-description {
            font-size: clamp(0.9rem, 4vw, 1.1rem);
            text-align: center;
            max-width: 600px;
            margin-bottom: 2rem;
            color: #6c757d;
            margin-left: auto;
            margin-right: auto;
            padding: 0 15px;
        }
        .btn-body {
            padding: 10px 25px;
            border-radius: 5px;
            background: linear-gradient(195deg, #FF7F50, #FF6347);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            box-shadow: 0px 4px 10px rgba(255, 99, 71, 0.3);
            transition: all 0.3s ease;
        }
        .btn-body:hover {
            transform: translateY(-3px);
            box-shadow: 0px 6px 15px rgba(255, 99, 71, 0.4);
        }
        .lost-image {
            max-width: min(200px, 40vw);
            margin-bottom: 2rem;
        }
        
        .button-container {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        @media (max-width: 576px) {
            .button-container {
                flex-direction: column;
                width: 100%;
                padding: 0 15px;
            }
            
            .btn, .btn-body {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- 404 CONTENT -->
    <div class="container error-container">
        <img src="shop/img/logo/logo_admin_light.png" alt="BYD Logo" class="lost-image">
        <h1 class="error-code">404</h1>
        <h2 class="error-text">Page Not Found</h2>
        <p class="error-description">
            Oops! The page you're looking for doesn't exist. It might have been moved, deleted, or never existed in the first place.
        </p>
        <div class="button-container">
            <button onclick="history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </button>
            <button onclick="window.location.href='index.php'" class="btn-body">
                <i class="fas fa-home me-2"></i>Home Page
            </button>
        </div>
    </div>
    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="shop/js/url-cleaner.js"></script>
</body>
</html>