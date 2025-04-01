<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: white;
            text-align: center;
        }
        .btn-custom {
            border-radius: 6px;
            background: coral;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: orange;
            color: white;
        }
    </style>
</head>
<body>

    <div class="thank-you-container">
        <h2>Order has beed cancelled</h2>
        <div class="mt-4">
            <a href="index.php" class="btn btn-custom">Home</a>
        </div>
    </div>

</body>
</html>
