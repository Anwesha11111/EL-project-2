<?php
// Open (and create if not exists) the SQLite database
$db = new SQLite3('spirulina1.db');

// Create table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS bioelectricity_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    voltage REAL NOT NULL,
    sensor TEXT NOT NULL,
    humidity INTEGER,
    lighting TEXT NOT NULL,
    intensity INTEGER,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Collect POST data
$voltage = isset($_POST['voltage']) ? floatval($_POST['voltage']) : null;
$sensor = isset($_POST['sensor']) ? $_POST['sensor'] : null;
$humidity = isset($_POST['humidity']) && $_POST['humidity'] !== "" ? intval($_POST['humidity']) : null;
$lighting = isset($_POST['lighting']) ? $_POST['lighting'] : null;
$intensity = isset($_POST['intensity']) && $_POST['intensity'] !== "" ? intval($_POST['intensity']) : null;

// Validate required fields
if ($voltage === null || $sensor === null || $lighting === null) {
    echo "<div style='color:#fff;background:#c00;padding:1em;border-radius:1em;'>Missing required fields.</div>";
    exit;
}

// Prepare and execute insert statement
$stmt = $db->prepare("INSERT INTO bioelectricity_data (voltage, sensor, humidity, lighting, intensity) VALUES (:voltage, :sensor, :humidity, :lighting, :intensity)");
$stmt->bindValue(':voltage', $voltage, SQLITE3_FLOAT);
$stmt->bindValue(':sensor', $sensor, SQLITE3_TEXT);
$stmt->bindValue(':humidity', $humidity, SQLITE3_INTEGER);
$stmt->bindValue(':lighting', $lighting, SQLITE3_TEXT);
$stmt->bindValue(':intensity', $intensity, SQLITE3_INTEGER);

$result = $stmt->execute();

if ($result) {
    echo "<div style='background:#1c92d2;color:#181818;padding:2em;border-radius:1em;max-width:400px;margin:40px auto;text-align:center;'>
        <h2 style='font-family:Space Grotesk,sans-serif;margin:0 0 10px 0;'>Data Submitted!</h2>
        <ul style='list-style:none;padding:0;text-align:left;font-size:1.1em;'>
            <li><strong>Output Voltage:</strong> <span style='color:#fff;background:#222;padding:2px 8px;border-radius:6px;'>{$voltage} V</span></li>
            <li><strong>Sensor Used:</strong> <span style='color:#fff;background:#222;padding:2px 8px;border-radius:6px;'>{$sensor}</span></li>
            <li><strong>Humidity:</strong> <span style='color:#fff;background:#222;padding:2px 8px;border-radius:6px;'>" . ($humidity !== null ? "{$humidity} %" : "<em>Not provided</em>") . "</span></li>
            <li><strong>Lighting Condition:</strong> <span style='color:#fff;background:#222;padding:2px 8px;border-radius:6px;'>{$lighting}</span></li>
            <li><strong>Sunlight Intensity:</strong> <span style='color:#fff;background:#222;padding:2px 8px;border-radius:6px;'>" . ($intensity !== null ? "{$intensity} lux" : "<em>Not provided</em>") . "</span></li>
        </ul>
        <div style='margin-top:18px;font-size:1.1em;color:#181818;'>Thank you! Your data has been recorded.</div>
        <a href='index.html' style='display:inline-block;margin-top:20px;padding:10px 20px;background:#181818;color:#1c92d2;border-radius:8px;text-decoration:none;font-family:Space Grotesk,sans-serif;font-weight:700;'>Go Back Home</a>
    </div>";
} else {
    echo "<div style='color:#fff;background:#c00;padding:1em;border-radius:1em;'>Error: " . $db->lastErrorMsg() . "</div>";
}

$db->close();
?>
