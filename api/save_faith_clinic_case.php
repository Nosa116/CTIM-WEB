<?php
// api/save_faith_clinic_case.php
require_once __DIR__ . '/faith_clinic_init.php';

// Support both form-urlencoded/multipart and raw JSON requests
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$id = isset($input['id']) ? (int)$input['id'] : 0;
$member_id = isset($input['member_id']) ? (int)$input['member_id'] : 0;
$case_date = isset($input['case_date']) ? trim($input['case_date']) : '';
$narration = isset($input['narration']) ? trim($input['narration']) : '';
$remark = isset($input['remark']) ? trim($input['remark']) : '';

// Validation
if ($member_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Please select a valid member."]);
    exit();
}

if (empty($narration)) {
    echo json_encode(["status" => "error", "message" => "Narration is required."]);
    exit();
}

if (empty($remark)) {
    echo json_encode(["status" => "error", "message" => "Counselor's remark is required."]);
    exit();
}

// Default date to current date if not provided
if (empty($case_date)) {
    $case_date = date('Y-m-d');
}

if ($id > 0) {
    // Update existing case
    $stmt = $conn->prepare("UPDATE faith_clinic_cases SET member_id = ?, case_date = ?, narration = ?, remark = ? WHERE id = ?");
    $stmt->bind_param("isssi", $member_id, $case_date, $narration, $remark, $id);
} else {
    // Insert new case
    $stmt = $conn->prepare("INSERT INTO faith_clinic_cases (member_id, case_date, narration, remark) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $member_id, $case_date, $narration, $remark);
}

if ($stmt->execute()) {
    $caseId = $id > 0 ? $id : $conn->insert_id;
    echo json_encode([
        "status" => "success",
        "message" => $id > 0 ? "Case record updated successfully." : "Case record saved successfully.",
        "data" => [
            "id" => $caseId,
            "member_id" => $member_id,
            "case_date" => $case_date,
            "narration" => $narration,
            "remark" => $remark
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Database operation failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
