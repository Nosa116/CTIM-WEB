<?php
// api/save_faith_clinic_member.php
require_once __DIR__ . '/faith_clinic_init.php';

// Support both form-urlencoded/multipart and raw JSON requests
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$id = isset($input['id']) ? (int)$input['id'] : 0;
$name = isset($input['name']) ? trim($input['name']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$address = isset($input['address']) ? trim($input['address']) : '';

if (empty($name)) {
    echo json_encode(["status" => "error", "message" => "Member name is required."]);
    exit();
}

if ($id > 0) {
    // Update existing member
    $stmt = $conn->prepare("UPDATE faith_clinic_members SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $address, $id);
} else {
    // Insert new member
    $stmt = $conn->prepare("INSERT INTO faith_clinic_members (name, phone, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $address);
}

if ($stmt->execute()) {
    $memberId = $id > 0 ? $id : $conn->insert_id;
    echo json_encode([
        "status" => "success",
        "message" => $id > 0 ? "Member updated successfully." : "Member registered successfully.",
        "data" => [
            "id" => $memberId,
            "name" => $name,
            "phone" => $phone,
            "address" => $address
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Database operation failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
