<?php
// api/get_faith_clinic_cases.php
require_once __DIR__ . '/faith_clinic_init.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date = isset($_GET['date']) ? trim($_GET['date']) : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
$member_id = isset($_GET['member_id']) ? (int)$_GET['member_id'] : 0;

$sql = "SELECT c.id, c.member_id, c.case_date, c.narration, c.remark, c.created_at,
               m.name AS member_name, m.phone AS member_phone, m.address AS member_address
        FROM faith_clinic_cases c
        INNER JOIN faith_clinic_members m ON c.member_id = m.id
        WHERE 1=1";

// 1. Search filter (text matches name, phone, narration, or remark)
if (!empty($search)) {
    $searchEscaped = $conn->real_escape_string($search);
    $sql .= " AND (m.name LIKE '%$searchEscaped%' 
                OR m.phone LIKE '%$searchEscaped%' 
                OR c.narration LIKE '%$searchEscaped%' 
                OR c.remark LIKE '%$searchEscaped%')";
}

// 2. Specific Date filter
if (!empty($date)) {
    $dateEscaped = $conn->real_escape_string($date);
    $sql .= " AND c.case_date = '$dateEscaped'";
}

// 3. Date Range filter
if (!empty($start_date) && !empty($end_date)) {
    $startEscaped = $conn->real_escape_string($start_date);
    $endEscaped = $conn->real_escape_string($end_date);
    $sql .= " AND c.case_date BETWEEN '$startEscaped' AND '$endEscaped'";
}

// 4. Member ID filter
if ($member_id > 0) {
    $sql .= " AND c.member_id = $member_id";
}

// Sort cases newest first
$sql .= " ORDER BY c.case_date DESC, c.created_at DESC";

$result = $conn->query($sql);
$cases = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Format the case date for humans (e.g. May 27th, 2026)
        $dt = new DateTime($row['case_date']);
        $row['formatted_date'] = $dt->format('M jS, Y');
        $row['raw_date'] = $row['case_date'];
        $cases[] = $row;
    }
}

echo json_encode(["status" => "success", "data" => $cases]);
$conn->close();
?>
