<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Success</title>
  <!-- Link to Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Link to Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(45deg, #00c6ff, #0072ff, #00c6ff, #0072ff);
      background-size: 400% 400%;
      animation: gradientFlow 6s ease infinite;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      font-family: 'Arial', sans-serif;
    }

    /* Background flow effect */
    @keyframes gradientFlow {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }

    .container {
      text-align: center;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 100%;
    }

    .icon-success {
      font-size: 80px;
      color: #28a745;
      margin-bottom: 20px;
      padding: 10px;
      border-radius: 50%;
      background-color: #d4edda;
      width: 120px;
      height: 120px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .alert-heading {
      font-size: 1.8rem;
      font-weight: bold;
      color: #333;
    }

    .alert-message {
      font-size: 1.2rem;
      color: #555;
      margin: 20px 0;
    }

    .alert-footer {
      font-size: 1rem;
      color: #777;
      margin-top: 20px;
    }

    .btn {
      background-color: #0072ff;
      color: white;
      padding: 10px 30px;
      font-size: 1.1rem;
      border-radius: 25px;
      border: none;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(0, 114, 255, 0.3);
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #005bb5;
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Payment success icon -->
  <div class="icon-success">
    <i class="bi bi-check-circle"></i> <!-- Payment success icon -->
  </div>
  <h4 class="alert-heading">Payment Successful!</h4>
  <p class="alert-message">Your payment has been successfully processed. Thank you for your purchase.</p>

 
</div>

<!-- Include Bootstrap and necessary JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>

