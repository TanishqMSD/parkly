<?php
    include "dbconn.php";


    // SQL query to create the vehicle table if it doesn't exist
    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS vehicle (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    vehicle_name VARCHAR(50) NOT NULL,
                    vnumber VARCHAR(20) NOT NULL UNIQUE,
                    vtype VARCHAR(20) NOT NULL,
                    mno VARCHAR(15) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );";
    
    // Execute the query to create the table
    if ($conn->query($sqlCreateTable) === TRUE) {
        // Table created successfully or already exists
    } else {
        echo "Error creating table: " . $conn->error;
    }
    
    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS customer (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    u_name VARCHAR(50) NOT NULL,
                    mobileno VARCHAR(10) NOT NULL,
                    u_pass VARCHAR(15)
                );";
    
    if ($conn->query($sqlCreateTable) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
    
    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS ParkingSlot (
            SlotID INT PRIMARY KEY AUTO_INCREMENT,
            IsOccupied BOOLEAN DEFAULT FALSE,
            vnumber VARCHAR(20) UNIQUE NULL
        );";
    
    if ($conn->query($sqlCreateTable) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
    
    $sqlCreateTable = "INSERT INTO ParkingSlot (IsOccupied, vnumber)
    VALUES 
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL),
        (FALSE, NULL);
    ";
    
    if ($conn->query($sqlCreateTable) === TRUE) {
        echo "Parking slots inserted successfully!";
    } else {
        echo "Error inserting parking slots: " . $conn->error;
    }
    
    
    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS ParkingBill (
                BillID INT PRIMARY KEY AUTO_INCREMENT,
                vehicle_number VARCHAR(20),
                Cost DECIMAL(10, 2),
                IssuedTime DATETIME,
                checkout_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );";
    
    if ($conn->query($sqlCreateTable) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
    
    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS Slots (
        slot_id INT,
        vehicle_number VARCHAR(20),
        PRIMARY KEY (slot_id, vehicle_number),
        FOREIGN KEY (slot_id) REFERENCES ParkingSlot(SlotID),
        FOREIGN KEY (vehicle_number) REFERENCES Vehicle(vnumber)
    );
    ";
    
    if ($conn->query($sqlCreateTable) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $sqlCreateTable = "CREATE TABLE IF NOT EXISTS ParkingHistory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slot_number INT NOT NULL,
        vehicle_number VARCHAR(20) NOT NULL,
        checkin_time DATETIME NOT NULL,
        checkout_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_amount INT NOT NULL,
        mno VARCHAR(15) NOT NULL
    );

    ";
    
    if ($conn->query($sqlCreateTable) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

?>