<?php
// api/delete_faith_clinic_member.php
require_once __DIR__ . '/faith_clinic_init.php';

// Support POST (form data or JSON) and GET
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}
if (!$input) {
    $input = $_GET;
}

$id = isset($input['id']) ? (int)$input['id'] : 0;

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid member ID."]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM faith_clinic_members WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Member and their associated clinic cases deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Member not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete member: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
