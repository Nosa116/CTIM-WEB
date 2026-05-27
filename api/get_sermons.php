<?php
require_once __DIR__ . '/../config/db_connect.php';

// Set header to JSON
header('Content-Type: application/json');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

$sql = "SELECT id, title, speaker, date_preached, category, cover_image_path, audio_file_path, created_at 
        FROM sermons 
        WHERE 1=1";

if (!empty($search)) {
    $searchEscaped = $conn->real_escape_string($search);
    $sql .= " AND (title LIKE '%$searchEscaped%' OR speaker LIKE '%$searchEscaped%' OR category LIKE '%$searchEscaped%')";
}

if (!empty($category) && strtolower($category) !== 'all') {
    $categoryEscaped = $conn->real_escape_string($category);
    $sql .= " AND category = '$categoryEscaped'";
}

$sql .= " ORDER BY date_preached DESC, created_at DESC 
        LIMIT $limit";
        
$result = $conn->query($sql);

$sermons = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Format the date nicely
        $date = new DateTime($row['date_preached']);
        $row['formatted_date'] = $date->format('M jS, Y');
        $sermons[] = $row;
    }
}

echo json_encode(["status" => "success", "data" => $sermons]);
?>
