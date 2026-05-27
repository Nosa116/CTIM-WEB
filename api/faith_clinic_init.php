<?php
// api/faith_clinic_init.php
require_once __DIR__ . '/../config/db_connect.php';

// Set standard headers only if not in HTML rendering mode
if (!defined('FAITH_CLINIC_HTML_MODE')) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
}

// Initialize database tables if they do not exist (Self-healing system)
function initializeFaithClinicTables($conn) {
    // 1. Create Members Table
    $createMembersTable = "CREATE TABLE IF NOT EXISTS `faith_clinic_members` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (!$conn->query($createMembersTable)) {
        echo json_encode(["status" => "error", "message" => "Failed to initialize members table: " . $conn->error]);
        exit();
    }

    // 2. Create Cases Table
    $createCasesTable = "CREATE TABLE IF NOT EXISTS `faith_clinic_cases` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `member_id` int(11) NOT NULL,
        `case_date` date NOT NULL,
        `narration` text NOT NULL,
        `remark` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `member_id` (`member_id`),
        CONSTRAINT `fk_faith_clinic_cases_member` FOREIGN KEY (`member_id`) REFERENCES `faith_clinic_members` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (!$conn->query($createCasesTable)) {
        echo json_encode(["status" => "error", "message" => "Failed to initialize cases table: " . $conn->error]);
        exit();
    }
}

// Run the setup checks
initializeFaithClinicTables($conn);
?>
