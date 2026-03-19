<?php
/**
 * index.php - IoT 센서 실시간 모니터링 대시보드
 * 캡스톤 디자인 3주차 과제
 */

// DB 접속 설정
define('DB_HOST', 'localhost');
define('DB_USER', 'sensor_user');
define('DB_PASS', 'sensor_pass123');
define('DB_NAME', 'sensor_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die('<div style="color:red;padding:20px;">DB 연결 실패: ' . $conn->connect_error . '</div>');
}

// 최신 데이터 (센서별 마지막 값)
$latest_sql = "
    SELECT sd.*
    FROM sensor_data sd
    INNER JOIN (
        SELECT sensor_id, MAX(recorded_at) AS max_time
        FROM sensor_data
        GROUP BY sensor_id
    ) t ON sd.sensor_id = t.sensor_id AND sd.recorded_at = t.max_time
    ORDER BY sd.sensor_id
";
$latest = $conn->query($latest_sql);

// 전체 레코드 수
$count_row = $conn->query("SELECT COUNT(*) AS total FROM sensor_data")->fetch_assoc();
$total_rows = $count_row['total'];

// 최근 30건 (그래프용)
$history_sql = "
    SELECT sensor_id, temperature, humidity, pressure, light, recorded_at
    FROM sensor_data
    ORDER BY recorded_at DESC
    LIMIT 30
";
$history = $conn->query($history_sql);
$history_data = [];
while ($row = $history->fetch_assoc()) {
    $history_data[] = $row;
}
$history_data = array_reverse($history_data);

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5">
    <title>IoT 센서 모니터링 대시보드</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header>
    <h1>IoT 센서 실시간 모니터링</h1>
    <p class="subtitle">
        총 레코드: <strong><?= number_format($total_rows) ?></strong> 건
        &nbsp;|&nbsp;
        마지막 갱신: <strong><?= date('Y-m-d H:i:s') ?></strong>
        &nbsp;|&nbsp;
        <span class="live-badge">LIVE</span> 5초 자동 갱신
    </p>
</header>

<main>
    <!-- 센서 카드 -->
    <section class="cards">
        <?php while ($row = $latest->fetch_assoc()): ?>
        <div class="card <?= $row['status'] === 'WARN' ? 'card-warn' : '' ?>">
            <div class="card-header">
                <span class="sensor-id"><?= htmlspecialchars($row['sensor_id']) ?></span>
                <span class="badge <?= $row['status'] === 'WARN' ? 'badge-warn' : 'badge-ok' ?>">
                    <?= $row['status'] ?>
                </span>
            </div>
            <h3><?= htmlspecialchars($row['sensor_name']) ?></h3>
            <div class="metrics">
                <div class="metric">
                    <span class="label">온도</span>
                    <span class="value"><?= $row['temperature'] ?><small>°C</small></span>
                </div>
                <div class="metric">
                    <span class="label">습도</span>
                    <span class="value"><?= $row['humidity'] ?><small>%</small></span>
                </div>
                <div class="metric">
                    <span class="label">기압</span>
                    <span class="value"><?= $row['pressure'] ?><small>hPa</small></span>
                </div>
                <div class="metric">
                    <span class="label">조도</span>
                    <span class="value"><?= $row['light'] ?><small>lux</small></span>
                </div>
            </div>
            <p class="timestamp"><?= $row['recorded_at'] ?></p>
        </div>
        <?php endwhile; ?>
    </section>

    <!-- 온도 그래프 -->
    <section class="chart-section">
        <h2>온도 추이 (최근 30건)</h2>
        <canvas id="tempChart"></canvas>
    </section>

    <!-- 습도 그래프 -->
    <section class="chart-section">
        <h2>습도 추이 (최근 30건)</h2>
        <canvas id="humidChart"></canvas>
    </section>

    <!-- 최근 데이터 테이블 -->
    <section class="table-section">
        <h2>최근 수집 데이터</h2>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>센서 ID</th>
                    <th>센서명</th>
                    <th>온도(°C)</th>
                    <th>습도(%)</th>
                    <th>기압(hPa)</th>
                    <th>조도(lux)</th>
                    <th>상태</th>
                    <th>기록 시각</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice(array_reverse($history_data), 0, 20) as $r): ?>
                <tr class="<?= $r['status'] === 'WARN' ? 'row-warn' : '' ?>">
                    <td><?= htmlspecialchars($r['sensor_id']) ?></td>
                    <td><?= htmlspecialchars($r['sensor_id']) ?></td>
                    <td><?= $r['temperature'] ?></td>
                    <td><?= $r['humidity'] ?></td>
                    <td><?= $r['pressure'] ?></td>
                    <td><?= $r['light'] ?></td>
                    <td><span class="badge <?= $r['status'] === 'WARN' ? 'badge-warn' : 'badge-ok' ?>"><?= $r['status'] ?></span></td>
                    <td><?= $r['recorded_at'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>

<footer>
    <p>캡스톤 디자인 3주차 &mdash; LAMP Stack IoT Monitoring &copy; <?= date('Y') ?></p>
</footer>

<script>
// ── Chart.js 그래프 ────────────────────────────────────────────
const raw = <?= json_encode($history_data) ?>;

const labels    = raw.map(r => r.recorded_at.slice(11, 19));
const tempData  = raw.map(r => parseFloat(r.temperature));
const humidData = raw.map(r => parseFloat(r.humidity));

const COLORS = ['#4e79a7','#f28e2b','#e15759','#76b7b2','#59a14f'];

// 센서별 분리
const sensors = [...new Set(raw.map(r => r.sensor_id))];

function buildDatasets(field) {
    return sensors.map((sid, i) => ({
        label: sid,
        data: raw.filter(r => r.sensor_id === sid)
                  .map(r => parseFloat(r[field])),
        borderColor: COLORS[i % COLORS.length],
        backgroundColor: COLORS[i % COLORS.length] + '33',
        tension: 0.3,
        fill: false,
    }));
}

function makeChart(id, field, label) {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [...new Set(raw.map(r => r.recorded_at.slice(11, 19)))],
            datasets: buildDatasets(field),
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { title: { display: true, text: label } } }
        }
    });
}

makeChart('tempChart',  'temperature', '온도 (°C)');
makeChart('humidChart', 'humidity',    '습도 (%)');
</script>
</body>
</html>
