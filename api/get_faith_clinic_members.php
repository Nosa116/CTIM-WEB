<?php
// api/get_faith_clinic_members.php
require_once __DIR__ . '/faith_clinic_init.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$sql = "SELECT m.id, m.name, m.phone, m.address, m.created_at, COUNT(c.id) AS case_count 
        FROM faith_clinic_members m 
        LEFT JOIN faith_clinic_cases c ON m.id = c.member_id";

if (!empty($query)) {
    // Prevent SQL injection by escaping the search query
    $search = $conn->real_escape_string($query);
    $sql .= " WHERE m.name LIKE '%$search%' OR m.phone LIKE '%$search%'";
}

$sql .= " GROUP BY m.id ORDER BY m.name ASC";

$result = $conn->query($sql);
$members = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['case_count'] = (int)$row['case_count'];
        $members[] = $row;
    }
}

echo json_encode(["status" => "success", "data" => $members]);
$conn->close();
?>
