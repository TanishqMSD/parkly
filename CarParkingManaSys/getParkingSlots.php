<?php
include "dbconn.php";

// Fetch all parking slots from the database
$sql = "SELECT SlotID, IsOccupied, vnumber FROM ParkingSlot"; // Change to select all slots
$result = $conn->query($sql);

$parkingSlots = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $parkingSlots[] = [
            'slotID' => $row['SlotID'],
            'isOccupied' => $row['IsOccupied'] === '1', // Assuming IsOccupied is stored as a boolean (0 or 1)
            'vehicleNumber' => $row['vnumber'] ? $row['vnumber'] : 'Available' // Use 'Available' if vnumber is null
        ];
    }
}

$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($parkingSlots);
?>
