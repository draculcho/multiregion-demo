<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Multi-Region Demo</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: Arial, sans-serif;
      background-color: %%BG_COLOR%%;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      background: rgba(255,255,255,0.92);
      padding: 50px 70px;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.18);
      text-align: center;
      min-width: 380px;
    }
    h1 { color: #232f3e; margin-bottom: 30px; font-size: 24px; }
    .row { margin: 16px 0; }
    .label { color: #888; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
    .value { font-size: 22px; font-weight: bold; color: #232f3e; margin-top: 4px; }
    .value.highlight { color: #ff9900; }
    .value.green { color: #1a9c3e; }
  </style>
</head>
<body>
  <div class="card">
    <h1>AWS Multi-Region Demo</h1>

    <div class="row">
      <div class="label">Instance IP</div>
      <div class="value highlight"><?= htmlspecialchars($_SERVER['SERVER_ADDR'] ?? 'N/A') ?></div>
    </div>

    <div class="row">
      <div class="label">AWS Region</div>
      <div class="value highlight">%%AWS_REGION%%</div>
    </div>

    <div class="row">
      <div class="label">DB Host</div>
      <div class="value green"><?= DB_HOST ?: 'N/A' ?></div>
    </div>

    <div class="row">
      <div class="label">App URL</div>
      <div class="value green"><?= OS_URL ?: 'N/A' ?></div>
    </div>
  </div>
</body>
</html>
