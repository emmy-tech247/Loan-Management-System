<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized");
}

$member_id = $_SESSION['member_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>E-Signature</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 2px;
    color: #333;
  }
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #004080;
    padding: 0 20px;
    flex-wrap: wrap;
    height: 60px;
  }
  .navbar .left, .navbar .right {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
  }
  .navbar a, .dropbtn {
    font-size: 16px;
    color: white;
    text-align: center;
    padding: 18px 25px;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
  }
  .navbar a:not(.logo-link):hover {
    background-color: #007bff;
    padding: 10px 15px;
    border-radius: 5px;
  }
  .navbar img {
    display: block;
    margin: -50px -50px -20px -50px;
    padding: 0;
  }
  h2 {
    text-align: center;
    color: #2c3e50;
    margin: 30px 0 10px;
  }
  form {
    background-color: #fff;
    max-width: 500px;
    margin: 0 auto 40px;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }
  label {
    font-weight: bold;
  }
  input[type="text"], input[type="file"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  canvas {
    display: block;
    margin: 0 auto 10px;
    border: 2px solid #333;
    border-radius: 5px;
    background-color: #fff;
    cursor: crosshair;
    width: 100%;
    max-width: 400px;
    height: auto;
  }
  button {
    background-color: #2ecc71;
    border: none;
    color: white;
    padding: 10px 20px;
    margin: 10px 5px 0 0;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #27ae60;
  }
  button[type="button"] {
    background-color: #e74c3c;
  }
  button[type="button"]:hover {
    background-color: #c0392b;
  }
  .footer, footer {
    text-align: center;
    padding: 30px;
    background-color: #004080;
    color: #fff;
    margin-top: 60px;
    font-size: 16px;
  }
  @media screen and (max-width: 600px) {
    .navbar { flex-direction: column; align-items: flex-start; }
    .navbar .left, .navbar .right { width: 100%; flex-direction: column; align-items: flex-start; }
    .navbar a { width: 100%; padding: 12px 15px; text-align: left; }
    form { padding: 20px; }
    canvas { width: 100% !important; height: auto !important; }
  }
</style>
</head>
<body>

<div class="navbar">
  <div class="left">
    <a href="home.php" class="logo-link">
      <img src="images/logo2.png" alt="Logo" width="80" height="80">
    </a>
  </div>
  <div class="right">
    <a href="member.php">Back</a>
    <a href="e_signature.php">E-Signature</a>
    <a href="document.html">Upload/Download forms</a>
  </div>
</div>

<h2>Mandate Signature</h2>
<form method="post" action="save_signature.php" enctype="multipart/form-data">
  <label for="purpose">Purpose:</label>
  <input type="text" name="purpose" id="purpose" required>

  <p><strong>Draw Signature:</strong></p>
  <canvas id="signature-pad" width="400" height="200"></canvas>
  <input type="hidden" name="signature_data" id="signature_data">
  <button type="button" onclick="clearPad()">Clear</button>

  <hr style="margin: 20px 0;">

  <p><strong>Or Upload Signature Image:</strong></p>
  <input type="file" name="signature_file" accept="image/*">

  <button type="submit" onclick="prepareSignature()">Submit Signature</button>
</form>

<footer>
  <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
</footer>

<script>
  const canvas = document.getElementById('signature-pad');
  const ctx = canvas.getContext('2d');
  let drawing = false;

  canvas.addEventListener('mousedown', () => {
    drawing = true;
    ctx.beginPath();
  });

  canvas.addEventListener('mouseup', () => {
    drawing = false;
    ctx.beginPath();
  });

  canvas.addEventListener('mousemove', draw);

  function draw(e) {
    if (!drawing) return;
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(e.offsetX, e.offsetY);
  }

  function clearPad() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
  }

  function prepareSignature() {
    const dataURL = canvas.toDataURL();
    document.getElementById('signature_data').value = dataURL;
  }
</script>

</body>
</html>
