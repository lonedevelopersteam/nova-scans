<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat datang di {{ env('APP_NAME', 'Default Title') }}</title>
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f3f4f6; /* Light gray background similar to the image */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center the content inside the box */
        }
        .alert-text {
            background-color: #f1f1f1; /* Light grey background for the alert text */
            border-left: 4px solid #0073aa; /* Blue left border similar to the image */
            padding: 15px;
            margin-top: 20px;
            text-align: left; /* Align alert text to the left */
        }
        .btn-custom {
            background-color: #0073aa; /* WordPress blue button */
            border-color: #0073aa;
            color: #fff;
        }
        .btn-custom:hover {
            background-color: #005a87; /* Darker blue on hover */
            border-color: #005a87;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="alert-text">
        <p class="mb-0">Admin belum ada, anda diharapkan untuk segera membuat admin terlebih dahulu agar dapat mengakses keseluruhan fitur.</p>
    </div>

    <a href="{{ route('create.admin') }}" class="btn btn-custom mt-3">Mulai</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
