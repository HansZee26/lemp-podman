<?php
// coba koneksi ke beberapa host yang mungkin (container vs host)
$hosts = ['mariadb', 'podman-mariadb', '127.0.0.1'];
$dbname = 'test';   // atau classy, kalau sudah dibuat
$user = 'USERNAME';
$pass = 'YOUR_PASSWORD';

$connected = false;
$lastException = null;
foreach ($hosts as $h) {
    try {
        $pdo = new PDO("mysql:host=$h;dbname=$dbname;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $connected = true;
        $used_host = $h;
        break;
    } catch (PDOException $e) {
        $lastException = $e;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dev Server — Nginx + PHP-FPM + MariaDB</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">

  <!-- External stylesheet -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="container">
    <header class="hero">
      <div>
        <h1>🚀 Nginx + PHP-FPM + MariaDB</h1>
        <p class="lead">Simple dev landing page — tampilkan status koneksi database dan info cepat.</p>
      </div>
      <div class="meta">
        <a class="btn" href="/phpmyadmin" target="_blank">Open phpMyAdmin</a>
        <a class="btn btn-ghost" href="/" target="_self">Refresh</a>
      </div>
    </header>

    <section class="card">
      <h2>Server info</h2>
      <div class="grid">
        <div class="tile">
          <div class="tile-title">PHP</div>
          <div class="tile-body"><?= phpversion(); ?></div>
        </div>

        <div class="tile">
          <div class="tile-title">Nginx</div>
          <div class="tile-body">Proxy / container</div>
        </div>

        <div class="tile status-tile">
          <div class="tile-title">MariaDB</div>
          <?php if ($connected): ?>
            <div class="tile-body success">
              <strong>Connected</strong><span class="muted"> (host: <?= htmlspecialchars($used_host) ?>)</span>
            </div>
          <?php else: ?>
            <div class="tile-body error">
              <strong>Connection failed</strong>
              <div class="muted small">Tried hosts: <?= implode(', ', $hosts) ?></div>
              <details class="muted small">
                <summary class="small">Error details</summary>
                <pre><?= htmlspecialchars($lastException ? $lastException->getMessage() : 'No exception') ?></pre>
              </details>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <hr>

      <div class="codebox">
        <h3>Quick test query</h3>
        <?php if ($connected): ?>
          <?php
            try {
              $stmt = $pdo->query("SELECT NOW() as now");
              $row = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
              $row = ['now' => 'Query failed: '.$e->getMessage()];
            }
          ?>
          <pre class="mono">SELECT NOW();  →  <?= htmlspecialchars($row['now'] ?? 'n/a') ?></pre>
        <?php else: ?>
          <p class="muted">Tidak ada koneksi database, tidak bisa menjalankan query.</p>
        <?php endif; ?>
      </div>
    </section>

    <footer class="footer">
      <small>Made for local development — put your website files into <code>htdocs/</code>.</small>
    </footer>
  </main>
</body>
</html>

