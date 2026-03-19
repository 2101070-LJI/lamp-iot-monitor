<?php
/**
 * monitor.php - 단순 텍스트 기반 실시간 모니터링 페이지
 * 최신 센서 데이터를 테이블로 표시 (자동 갱신)
 */

header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('localhost', 'sensor_user', 'sensor_pass123', 'sensor_db');
$conn->set_charset('utf8mb4');

$rows = $conn->query("
    SELECT * FROM sensor_data
    ORDER BY recorded_at DESC
    LIMIT 50
");

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <title>Sensor Monitor</title>
    <style>
        body { font-family: monospace; background:#111; color:#0f0; margin:20px; }
        h1   { color:#0f0; border-bottom:1px solid #0f0; }
        table{ border-collapse:collapse; width:100%; }
        th,td{ border:1px solid #0a0; padding:6px 12px; text-align:right; }
        th   { background:#0a0; color:#111; }
        tr:hover{ background:#0a0; }
        .warn{ color:#ff0; }
        .ts  { color:#888; font-size:.85em; }
    </style>
</head>
<body>
<h1>[ IoT Sensor Monitor ] — <?= date('Y-m-d H:i:s') ?> &nbsp; (5s auto-refresh)</h1>
<table>
    <thead>
        <tr>
            <th>ID</th><th>Sensor</th>
            <th>Temp(°C)</th><th>Humid(%)</th>
            <th>Press(hPa)</th><th>Light(lux)</th>
            <th>Status</th><th>Time</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($r = $rows->fetch_assoc()): ?>
        <tr class="<?= $r['status']==='WARN'?'warn':'' ?>">
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['sensor_id']) ?></td>
            <td><?= $r['temperature'] ?></td>
            <td><?= $r['humidity'] ?></td>
            <td><?= $r['pressure'] ?></td>
            <td><?= $r['light'] ?></td>
            <td><?= $r['status'] ?></td>
            <td class="ts"><?= $r['recorded_at'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
