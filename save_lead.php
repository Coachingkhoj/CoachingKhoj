<?php
header("Content-Type: application/json");

// Apni Database details yahan daalein
$db_host = "localhost";
$db_user = "YOUR_DB_USER";
$db_pass = "YOUR_DB_PASSWORD";
$db_name = "YOUR_DB_NAME";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection Failed"]);
    exit();
}

$name  = trim($_POST["student_name"] ?? "");
$phone = trim($_POST["phone_number"] ?? "");
$exam  = trim($_POST["target_exam"] ?? "");
$city  = trim($_POST["preferred_city"] ?? "");
$class = trim($_POST["current_class"] ?? "");

if (!empty($name) && !empty($phone) && !empty($exam) && !empty($city) && !empty($class)) {
    $stmt = $conn->prepare("INSERT INTO neet_jee_leads (student_name, phone_number, target_exam, preferred_city, current_class) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $phone, $exam, $city, $class);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Could not save lead"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
}
$conn->close();
?>