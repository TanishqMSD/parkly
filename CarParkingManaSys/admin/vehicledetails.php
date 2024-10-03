<?php
include "dbconn.php";
include "tablescreation.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number = $_POST['vehicle_number'];
    $vehicle_name = $_POST['vehicle_name'];
    $type = $_POST['vehicle_type'];
    $mno = $_POST['mobile_number'];

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM customer WHERE mobileno = ?");
    $stmt->bind_param("s", $mno);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows <= 0) {
        echo "<script>alert('User does not exist!'); window.location.href = 'index.html';</script>";
    } else {
        // Check if vehicle number already exists
        $stmt = $conn->prepare("SELECT * FROM vehicle WHERE vnumber = ?");
        $stmt->bind_param("s", $number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Vehicle with this number already exists!'); window.location.href = 'index.html';</script>";
        } else {
            // Check for available slots
            $stmt = $conn->prepare("SELECT SlotID FROM ParkingSlot WHERE IsOccupied = FALSE LIMIT 1");
            $stmt->execute();
            $resultSlot = $stmt->get_result();

            if ($resultSlot->num_rows > 0) {
                $slot = $resultSlot->fetch_assoc();
                $slotID = $slot['SlotID'];

                // Insert vehicle into vehicle table
                $stmt = $conn->prepare("INSERT INTO vehicle (vehicle_name, vnumber, vtype, mno) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $vehicle_name, $number, $type, $mno);

                if ($stmt->execute() === TRUE) {
                    // Assign vehicle to the available slot
                    $stmt = $conn->prepare("UPDATE ParkingSlot SET IsOccupied = TRUE, vnumber = ? WHERE SlotID = ?");
                    $stmt->bind_param("si", $number, $slotID);

                    if ($stmt->execute() === TRUE) {
                        // Optionally, insert into the slots table as well
                        $stmt = $conn->prepare("INSERT INTO Slots (slot_id, vehicle_number) VALUES (?, ?)");
                        $stmt->bind_param("is", $slotID, $number);
                        
                        if ($stmt->execute() === TRUE) {
                            echo "<script>alert('Vehicle registered and assigned to slot $slotID successfully!'); window.location.href = 'dashboard.php';</script>";
                        } else {
                            error_log("Error inserting into Slots: " . $stmt->error, 3, "errors.log");
                            echo "Error inserting into Slots.";
                        }
                    } else {
                        error_log("Error updating ParkingSlot: " . $stmt->error, 3, "errors.log");
                        echo "Error updating ParkingSlot.";
                    }
                } else {
                    error_log("Error inserting vehicle: " . $stmt->error, 3, "errors.log");
                    echo "Error inserting vehicle.";
                }
            } else {
                echo "<script>alert('No slots available!'); window.location.href = 'index.html';</script>";
            }
        }
    }

    // Close statements and connection
    $stmt->close();
    $conn->close();
}
?>
