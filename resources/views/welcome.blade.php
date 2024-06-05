<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
        }
        .bg {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
        }
        .error-title {
            font-size: 100px;
            font-weight: bold;
        }
        .error-message {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .home-button {
            padding: 10px 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="bg">
        <div>
            <div class="error-title">404</div>
            <div class="error-message">Oops! The page you are looking for does not exist.</div>
            <a href="{{ url('/') }}" class="btn btn-primary home-button">Go Home</a>
        </div>
    </div>
</body>
</html>
