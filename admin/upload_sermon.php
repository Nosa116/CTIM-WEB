<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $speaker = $conn->real_escape_string($_POST['speaker']);
    $date_preached = $conn->real_escape_string($_POST['date']);
    $category = $conn->real_escape_string($_POST['category']);
    
    // Directory paths for storage
    $audio_upload_dir = "../uploads/audio/";
    $image_upload_dir = "../uploads/images/";
    
    // Create directories if they don't exist
    if (!file_exists($audio_upload_dir)) { mkdir($audio_upload_dir, 0777, true); }
    if (!file_exists($image_upload_dir)) { mkdir($image_upload_dir, 0777, true); }
    
    $audio_db_path = "";
    $image_db_path = "";
    
    // Handle Cover Image Upload
    if (isset($_FILES["coverImage"]) && $_FILES["coverImage"]["error"] == 0) {
        $img_ext = strtolower(pathinfo($_FILES["coverImage"]["name"], PATHINFO_EXTENSION));
        $valid_img_ext = array("jpg", "jpeg", "png");
        
        if (in_array($img_ext, $valid_img_ext)) {
            $new_img_name = uniqid("img_") . "." . $img_ext;
            $image_file_path = $image_upload_dir . $new_img_name;
            $image_db_path = "uploads/images/" . $new_img_name;
            if (!move_uploaded_file($_FILES["coverImage"]["tmp_name"], $image_file_path)) {
                echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
                exit();
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid image format. Only JPG, JPEG and PNG allowed."]);
            exit();
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Cover image is required."]);
        exit();
    }
    
    // Handle Audio File Upload
    if (isset($_FILES["audioFile"]) && $_FILES["audioFile"]["error"] == 0) {
        $audio_ext = strtolower(pathinfo($_FILES["audioFile"]["name"], PATHINFO_EXTENSION));
        $valid_audio_ext = array("mp3", "mpeg", "wav");
        
        if (in_array($audio_ext, $valid_audio_ext)) {
            $new_audio_name = uniqid("audio_") . "." . $audio_ext;
            $audio_file_path = $audio_upload_dir . $new_audio_name;
            $audio_db_path = "uploads/audio/" . $new_audio_name;
            if (!move_uploaded_file($_FILES["audioFile"]["tmp_name"], $audio_file_path)) {
                echo json_encode(["status" => "error", "message" => "Failed to upload audio."]);
                exit();
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid audio format. Only MP3 allowed."]);
            exit();
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Audio file is required."]);
        exit();
    }
    
    // Insert into database
    $sql = "INSERT INTO sermons (title, speaker, date_preached, category, cover_image_path, audio_file_path) 
            VALUES ('$title', '$speaker', '$date_preached', '$category', '$image_db_path', '$audio_db_path')";
            
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Sermon uploaded successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}
?>
