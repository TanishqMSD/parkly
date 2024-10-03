<?php

include "dbconn.php";

// Get today's date
$today = date('Y-m-d');

// Get the current week (ISO format)
$currentWeek = date('Y-W');

// Get the current month
$currentMonth = date('Y-m');

// 1. Total cost and vehicle count
$sql_total = "SELECT SUM(Cost) AS total_cost, COUNT(DISTINCT vehicle_number) AS total_vehicles FROM ParkingBill";
$result_total = $conn->query($sql_total);
$totalCost = 0;
$totalVehicles = 0;
if ($result_total->num_rows > 0) {
    $row = $result_total->fetch_assoc();
    $totalCost = $row['total_cost'];
    $totalVehicles = $row['total_vehicles'];
}

// 2. Daily cost and vehicle count
$sql_daily = "SELECT SUM(Cost) AS daily_cost, COUNT(DISTINCT vehicle_number) AS daily_vehicles FROM ParkingBill WHERE DATE(IssuedTime) = '$today'";
$result_daily = $conn->query($sql_daily);
$dailyCost = 0;
$dailyVehicles = 0;
if ($result_daily->num_rows > 0) {
    $row = $result_daily->fetch_assoc();
    $dailyCost = $row['daily_cost'];
    $dailyVehicles = $row['daily_vehicles'];
}

// 3. Weekly cost and vehicle count
$sql_weekly = "SELECT SUM(Cost) AS weekly_cost, COUNT(DISTINCT vehicle_number) AS weekly_vehicles FROM ParkingBill WHERE YEARWEEK(IssuedTime, 1) = YEARWEEK(CURDATE(), 1)";
$result_weekly = $conn->query($sql_weekly);
$weeklyCost = 0;
$weeklyVehicles = 0;
if ($result_weekly->num_rows > 0) {
    $row = $result_weekly->fetch_assoc();
    $weeklyCost = $row['weekly_cost'];
    $weeklyVehicles = $row['weekly_vehicles'];
}

// 4. Monthly cost and vehicle count
$sql_monthly = "SELECT SUM(Cost) AS monthly_cost, COUNT(DISTINCT vehicle_number) AS monthly_vehicles FROM ParkingBill WHERE DATE_FORMAT(IssuedTime, '%Y-%m') = '$currentMonth'";
$result_monthly = $conn->query($sql_monthly);
$monthlyCost = 0;
$monthlyVehicles = 0;
if ($result_monthly->num_rows > 0) {
    $row = $result_monthly->fetch_assoc();
    $monthlyCost = $row['monthly_cost'];
    $monthlyVehicles = $row['monthly_vehicles'];
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Statistics - Car Parking System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Dark mode adjustments */
        .bg-gray-900 {
            background-color: #111827;
        }

        .bg-gray-800 {
            background-color: #1f2937;
        }

        .text-gray-400 {
            color: #cbd5e1;
        }

        .text-white {
            color: #ffffff;
        }

        .font-bold {
            font-weight: 700;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
        }

        .modal-content {
            background-color: #1f2937;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #444;
            width: 80%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }

        .toggle-label {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            background-color: #ccc;
            border-radius: 9999px;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .toggle-checkbox:checked + .toggle-label {
            background-color: #4ade80;
        }

        .toggle-ball {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .toggle-checkbox:checked + .toggle-label .toggle-ball {
            transform: translateX(20px);
        }

        /* Button styles */
        button, .bg-blue-600 {
            transition: background-color 0.3s ease;
        }

        button:hover, .bg-blue-600:hover {
            background-color: #2563eb;
        }

        /* Card style */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 24px;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <header class="bg-gray-800 shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="home.html" class="text-2xl font-bold text-white">Parkly</a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="index.html" class="text-gray-400 hover:text-white transition duration-300">Register</a></li>
                    <li><a href="dashboard.php" class="text-gray-400 hover:text-white transition duration-300">Dashboard</a></li>
                    <li><a href="slots.html" class="text-gray-400 hover:text-white transition duration-300">Slots</a></li>
                    <li><a href="report.php" class="text-gray-400 hover:text-white transition duration-300">Reports</a></li>
                    <div class="flex items-center ml-4">
                        <span class="text-gray-400 mr-2">Dark Mode</span>
                        <input type="checkbox" id="dark-toggle" class="toggle-checkbox hidden">
                        <label for="dark-toggle" class="toggle-label">
                            <span class="toggle-ball"></span>
                        </label>
                    </div>
                </ul>
            </nav>
        </div>
    </header>

    <section class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold text-center mb-10">Parking Statistics</h2>

        <!-- Statistics Section -->
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg text-white mb-10 card">
            <h3 class="text-lg font-bold">Today's Parking Data</h3>
            <div class="mt-4">
                <p id="totalVehicles">Vehicles Parked Today: <span class="font-bold"><?php echo $dailyVehicles; ?></span></p>
                <p id="totalEarnings">Daily Cost: <span class="font-bold">₹ <?php echo number_format($dailyCost, 2); ?></span></p>
            </div>
        </div>

        <!-- Toggle for Additional Statistics -->
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg text-white mb-10 card">
            <h3 class="text-lg font-bold">Detailed Statistics</h3>
            <div id="dropdownContent" class="hidden mt-6">
                <!-- Total Cost & Vehicles -->
                <div class="mt-4 bg-gray-900 p-6 rounded-lg shadow-md card">
                    <h3 class="text-lg font-bold">Total Cost & Vehicles</h3>
                    <div class="flex justify-between mt-4">
                        <p>Total Vehicles Parked: <span class="font-bold"><?php echo $totalVehicles; ?></span></p>
                        <p>Total Cost: <span class="font-bold">₹ <?php echo number_format($totalCost, 2); ?></span></p>
                    </div>
                </div>

                <!-- Weekly Cost & Vehicles -->
                <div class="mt-4 bg-gray-900 p-6 rounded-lg shadow-md card">
                    <h3 class="text-lg font-bold">This Week's Cost & Vehicles</h3>
                    <div class="flex justify-between mt-4">
                        <p>Vehicles Parked This Week: <span class="font-bold"><?php echo $weeklyVehicles; ?></span></p>
                        <p>Weekly Cost: <span class="font-bold">₹ <?php echo number_format($weeklyCost, 2); ?></span></p>
                    </div>
                </div>

                <!-- Monthly Cost & Vehicles -->
                <div class="mt-4 bg-gray-900 p-6 rounded-lg shadow-md card">
                    <h3 class="text-lg font-bold">This Month's Cost & Vehicles</h3>
                    <div class="flex justify-between mt-4">
                        <p>Vehicles Parked This Month: <span class="font-bold"><?php echo $monthlyVehicles; ?></span></p>
                        <p>Monthly Cost: <span class="font-bold">₹ <?php echo number_format($monthlyCost, 2); ?></span></p>
                    </div>
                </div>
            </div>

            <button id="dropdownButton" class="bg-blue-600 text-white px-4 py-2 rounded w-full text-center mt-4">
                Show Statistics
            </button>
        </div>

        <!-- History Logs -->
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg text-white mb-10 card">
            <h3 class="text-lg font-bold">History Logs</h3>
            <div id="historyContainer" class="mt-4">
                <button id="toggleHistory" class="bg-blue-600 text-white px-4 py-2 rounded">Show History</button>
                <div id="history" class="hidden mt-4 bg-gray-900 p-4 rounded">
                    <ul id="historyList" class="space-y-2">
                        <!-- History items will be dynamically added here -->
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="text-gray-400 float-right cursor-pointer">&times;</span>
            <h2 class="text-xl font-bold mb-4">Parking Token</h2>
            <p><strong class="text-gray-400">Token Number:</strong> <?php echo $token; ?></p>
        </div>
    </div>

    <script>
        // Dropdown Button for Statistics
        document.getElementById('dropdownButton').addEventListener('click', function() {
            var content = document.getElementById('dropdownContent');
            content.classList.toggle('hidden');
        });

        // Show and Hide History Logs
        document.getElementById('toggleHistory').addEventListener('click', function() {
            var history = document.getElementById('history');
            history.classList.toggle('hidden');
        });

        // Modal for Confirmation
        var modal = document.getElementById('confirmationModal');
        var closeModal = document.getElementById('closeModal');
        closeModal.onclick = function() {
            modal.style.display = "none";
        }
    </script>
</body>
</html>

