<?php
    session_start();
    include "dbconn.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">

    <style>
        /* Dark Mode Styles */
        .dark .bg-white {
            background-color: #1f2937;
        }

        .dark .text-gray-600 {
            color: #9ca3af;
        }

        .dark .text-gray-800 {
            color: #d1d5db;
        }

        .dark .bg-gray-100 {
            background-color: #111827;
        }

        .dark .bg-gray-50 {
            background-color: #1f2937;
        }

        .dark .bg-blue-600 {
            background-color: #2563eb;
        }

        .dark .border-gray-200 {
            border-color: #374151;
        }

        .dark .hover\:bg-gray-100:hover {
            background-color: #374151;
        }

        .dark .bg-gray-200 {
            background-color: #374151;
        }

        /* Dark Mode Toggle Switch Styles */
        .toggle-checkbox:checked + .toggle-label .toggle-ball {
            transform: translateX(100%);
        }

        .toggle-label {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            background-color: #ccc;
            border-radius: 9999px;
            transition: background-color 0.3s;
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
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <header class="bg-white dark:bg-gray-800 shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="home.html" class="text-2xl font-bold dark:text-white">Parkly</a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="availability.php" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Parking Status</a></li>
                    
                    <li><a href="logout.php" class="text-gray-600 dark:text-gray-300 hover:text-red-600">Log Out</a></li>
                    <!-- Dark Mode Toggle Button -->
                    <div class="flex items-center">
                        <span class="text-gray-600 dark:text-gray-300 mr-2">Dark Mode</span>
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
        <h2 class="text-3xl font-bold text-center mb-10 dark:text-gray-200">Parking History</h2>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-200 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Slot</th>
                        <th class="py-3 px-6 text-left">Vehicle Number</th>
                        <th class="py-3 px-6 text-left">Check In</th>
                        <th class="py-3 px-6 text-left">Check Out</th>
                        <th class="py-3 px-6 text-left">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    <!-- PHP code to generate parking history here -->
                    <?php
                        $mnum = $_SESSION['mnum'];
                        $vehicleNumbers = [];
                        $sql = "SELECT vehicle_number FROM ParkingHistory WHERE mno = $mnum";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $vehicleNumbers[] = $row['vehicle_number'];
                            }
                        } else {
                            echo "<tr><td colspan='6' class='py-3 px-6 text-center'>No vehicles found</td></tr>";
                        }

                        if (!empty($vehicleNumbers)) {
                            foreach ($vehicleNumbers as $vnumber) {
                                $sql2 = "SELECT * FROM ParkingHistory WHERE vehicle_number = '$vnumber'";
                                $result2 = $conn->query($sql2);

                                if ($result2 && $result2->num_rows > 0) {
                                    while ($row2 = $result2->fetch_assoc()) {
                                        echo "<tr class='border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800'>";
                                        echo "<td class='py-3 px-6 text-left'>Slot " . $row2['slot_number'] . "</td>";
                                        echo "<td class='py-3 px-6 text-left'>" . $row2['vehicle_number'] . "</td>";
                                        echo "<td class='py-3 px-6 text-left'>" . $row2['checkin_time'] . "</td>";
                                        echo "<td class='py-3 px-6 text-left'>" . $row2['checkout_time'] . "</td>";
                                        echo "<td class='py-3 px-6 text-left'>₹ " . $row2['total_amount'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='py-3 px-6 text-center'>No parking history found for vehicle $vnumber</td></tr>";
                                }
                            }
                        } else {
                            echo "<tr><td colspan='6' class='py-3 px-6 text-center'>No vehicles found for user</td></tr>";
                        }

                        $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold text-center mb-10 dark:text-gray-200">Available Slots</h2>

        <div id="parking-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 max-w-6xl mx-auto"></div>
    </section>

    <script>
        async function loadParkingSlots() {
            try {
                const response = await fetch('getParkingSlots.php');
                const parkingSlots = await response.json();
                const parkingGrid = document.getElementById('parking-grid');
                parkingGrid.innerHTML = '';

                const localStorageSlots = Array(50).fill(null);

                parkingSlots.forEach(slot => {
                    const slotDiv = document.createElement('div');
                    slotDiv.className = `slot p-4 rounded-lg shadow-md text-center transition-colors ${slot.isOccupied ? 'bg-red-500' : 'bg-green-500'} dark:bg-gray-700`;

                    slotDiv.innerHTML = `
                        <div class="text-2xl font-bold text-white">${slot.slotID}</div>
                        <div class="slot-info text-white mt-2">
                            ${slot.isOccupied ? ` (Occupied)` : 'Available'}
                        </div>
                    `;

                    if (slot.isOccupied) {
                        localStorageSlots[slot.slotID - 1] = {
                            vehicleNumber: slot.vehicleNumber,
                            isOccupied: true,
                            checkinTime: slot.checkinTime || new Date().toISOString()
                        };
                    } else {
                        localStorageSlots[slot.slotID - 1] = null;
                    }

                    parkingGrid.appendChild(slotDiv);
                });

                localStorage.setItem('parkingSlots', JSON.stringify(localStorageSlots));
            } catch (error) {
                console.error('Error loading parking slots:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadParkingSlots);

        // Dark Mode Toggle
        const darkToggle = document.getElementById('dark-toggle');
        const htmlElement = document.documentElement;

        // Initialize dark mode based on user preference stored in localStorage
        if (localStorage.getItem('darkMode') === 'enabled') {
            htmlElement.classList.add('dark');
            darkToggle.checked = true;
        }

        // Toggle dark mode and save user preference
        darkToggle.addEventListener('change', function () {
            if (darkToggle.checked) {
                htmlElement.classList.add('dark');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                htmlElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'disabled');
            }
        });
    </script>
</body>

</html>
