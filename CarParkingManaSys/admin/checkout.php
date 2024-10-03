<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include "dbconn.php";

// Get the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Validate the input data
if (!isset($data['slotID']) || !isset($data['vehicleNumber']) || !isset($data['checkinTime']) || !isset($data['checkOutTime']) || !isset($data['totalAmount'])) {
    echo json_encode(["success" => false, "error" => "Invalid input data."]);
    exit;
}

// Check if the parking slot is occupied
$stmt = $conn->prepare("SELECT IsOccupied FROM ParkingSlot WHERE SlotID = ? AND IsOccupied = TRUE");
$stmt->bind_param("i", $data['slotID']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Slot is occupied, proceed with checkout
    $vehicleNumber = $data['vehicleNumber'];
    $checkinTime = $data['checkinTime'];
    $checkOutTime = $data['checkOutTime'];
    $totalAmount = $data['totalAmount'];

    // Start a database transaction
    $conn->begin_transaction();

    try {
        // Mark the slot as not occupied in ParkingSlot table
        $stmt = $conn->prepare("UPDATE ParkingSlot SET IsOccupied = FALSE, vnumber = NULL WHERE SlotID = ?");
        $stmt->bind_param("i", $data['slotID']);
        $stmt->execute();

        // Remove the vehicle from Slots table
        $stmt = $conn->prepare("DELETE FROM Slots WHERE slot_id = ?");
        $stmt->bind_param("i", $data['slotID']);
        $stmt->execute();

        // Fetch the mobile number from the vehicle table
        $stmt = $conn->prepare("SELECT mno FROM vehicle WHERE vnumber = ?");
        $stmt->bind_param("s", $vehicleNumber);  // Correct binding for vehicle number (string)
        $stmt->execute();
        $mobno = $stmt->get_result();

        // Check if vehicle exists
        if ($mobno->num_rows > 0) {
            $row = $mobno->fetch_assoc();
            $mno = $row['mno'];  // Get the mobile number

            // Remove vehicle from vehicle table
            $stmt = $conn->prepare("DELETE FROM vehicle WHERE vnumber = ?");
            $stmt->bind_param("s", $vehicleNumber);  // Correct binding for vehicle number
            $stmt->execute();

            // Log parking history
            $stmt = $conn->prepare("INSERT INTO ParkingHistory (slot_number, vehicle_number, checkin_time, total_amount, mno) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issds", $data['slotID'], $vehicleNumber, $checkinTime, $totalAmount, $mno);  // Correct binding for amount as double
            $stmt->execute();

            // Insert into ParkingBill table
            $stmt = $conn->prepare("INSERT INTO ParkingBill (vehicle_number, Cost, IssuedTime) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $vehicleNumber, $totalAmount, $checkinTime);
            $stmt->execute();

            // Commit the transaction
            $conn->commit();
            echo json_encode(["success" => true]);

        } else {
            // Vehicle not found in the database
            throw new Exception("Vehicle number not found in the database.");
        }

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

} else {
    echo json_encode(["success" => false, "error" => "Slot is already empty or does not exist."]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
