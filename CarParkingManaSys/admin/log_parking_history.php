<?php
include "dbconn.php";

// Get the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Prepare to insert into parking history
$stmt = $conn->prepare("INSERT INTO ParkingHistory (slot_number, vehicle_number, checkin_time, total_amount) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $data['slotNumber'], $data['vehicleNumber'], $data['checkinTime'], $data['totalAmount']);

// Execute the query and check for success
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
