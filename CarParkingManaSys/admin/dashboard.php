<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">

    <style>
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
            background-color: #1f2937;
        }

        .dark .bg-gray-200 {
            background-color: #374151;
        }

        .dark .hover\:bg-gray-100 {
            background-color: #1f2937;
        }

        .toggle-checkbox:checked+.toggle-label .toggle-ball {
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

        .toggle-checkbox:checked+.toggle-label {
            background-color: #4ade80;
            /* Color when checked */
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



        /* Custom styles for parking slots */
        .slot {
            height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 2px solid #ddd;
            transition: transform 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            padding: 10px;
        }
        .slot-number {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #fff;
            color: #333;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .slot:hover {
            transform: scale(1.05);
        }
        .slot-info {
            transition: opacity 0.2s ease;
        }
        .slot p {
            font-size: 0.875rem;
            font-weight: bold;
            color: #fff;
            margin: 0;
        }
        .checkout-button {
            display: none;
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            background-color: #fff;
            color: #333;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            transition: background-color 0.2s ease;
            margin-top: 5px;
        }
        .slot:hover .checkout-button {
            display: block;
        }
        .slot:hover .slot-info {
            opacity: 0;
        }
        .slot:hover .checkout-button {
            display: block;
            position: relative;
            z-index: 1;
        }

        
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <header class="bg-white dark:bg-gray-800 shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="home.html" class="text-2xl font-bold dark:text-white">Parkly</a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="index.html" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Register</a></li>
                    <li><a href="dashboard.php" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Dashboard</a></li>
                    <li><a href="slots.html" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Slots</a></li>
                    <li><a href="report.php" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Reports</a></li>
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
        <h2 class="text-3xl font-bold text-center mb-10 dark:text-gray-200">Parking Dashboard</h2>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-6">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-200 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Slot</th>
                        <th class="py-3 px-6 text-left">Vehicle Number</th>
                        <th class="py-3 px-6 text-left">Car Model</th>
                        <th class="py-3 px-6 text-left">Car Type</th>
                        <th class="py-3 px-6 text-left">Mobile Number</th>
                        
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    <?php
                    include "dbconn.php";

                    // Fetch vehicle data from the database
                    $sql = "SELECT vnumber, vehicle_name, vtype, mno FROM vehicle";
                    $result = $conn->query($sql);

                    // Debugging code to check if query is working
                    if ($result) {
                        if ($result->num_rows > 0) {
                            $slot = 1; // Initialize slot number

                            // Loop through each row and display the data in the table
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800'>";
                                echo "<td class='py-3 px-6 text-left'>Slot " . $slot . "</td>"; // Dynamic slot value
                                echo "<td class='py-3 px-6 text-left'>" . $row['vnumber'] . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $row['vehicle_name'] . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $row['vtype'] . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $row['mno'] . "</td>";
                                
                                echo "</tr>";

                                $slot++; // Increment slot for next entry
                            }
                        } else {
                            echo "<tr><td colspan='6' class='py-3 px-6 text-center'>No vehicles found</td></tr>";
                        }
                    } else {
                        echo "Error fetching data: " . $conn->error;
                    }

                    $conn->close();
                    ?>
                </tbody>

            </table>
        </div>

    </section>

    <script src="index.js"></script>

    <!-- <script>
        const carList = document.getElementById('carList');
        const parkingSlots = JSON.parse(localStorage.getItem('parkingSlots')) || Array(20).fill(null);

        // Function to display the list of cars in the dashboard
        function displayCarList() {
            carList.innerHTML = ''; // Clear existing data

            parkingSlots.forEach((slot, index) => {
                if (slot) {
                    const row = `
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="py-3 px-6 text-left">${index + 1}</td>
                            <td class="py-3 px-6 text-left">${slot.carNumber}</td>
                            <td class="py-3 px-6 text-left">${slot.carName}</td>
                            <td class="py-3 px-6 text-left">${slot.carType}-Wheeler</td>
                            <td class="py-3 px-6 text-left">${slot.mobileNumber}</td>
                            <td class="py-3 px-6 text-left">${slot.checkinTime}</td>
                            <td class="py-3 px-6 text-center">
                                <button class="checkout bg-red-500 text-white px-4 py-2 rounded" data-slot="${index}">
                                    Checkout
                                </button>
                            </td>
                        </tr>
                    `;
                    carList.insertAdjacentHTML('beforeend', row);
                }
            });
        }

        // Function to update report statistics
        function updateReport() {
            if (typeof(Storage) !== 'undefined') {
                // Notify the report page to refresh its data
                if (window.localStorage) {
                    window.localStorage.setItem('updateReport', new Date().toISOString());
                }
            }
        }

        // Function to checkout the car and calculate the bill
        function checkoutCar(slotIndex) {
            const slot = parkingSlots[slotIndex];

            const checkinTime = new Date(slot.checkinTime).getTime();
            const checkoutTime = new Date().getTime();
            const durationHours = (checkoutTime - checkinTime) / (1000 * 60 * 60); // Duration in hours
            const ratePerHour = 50;
            const bill = Math.round(durationHours * ratePerHour);

            alert(`Car Number ${slot.carNumber} checked out. Bill: ₹${bill}`);

            // Free the parking slot and update localStorage
            parkingSlots[slotIndex] = null;
            localStorage.setItem('parkingSlots', JSON.stringify(parkingSlots));

            // Update report page
            updateReport();

            // Refresh the list
            displayCarList();
        }

        // Event listener for checkout buttons
        carList.addEventListener('click', function(event) {
            if (event.target.classList.contains('checkout')) {
                const slotIndex = event.target.getAttribute('data-slot');
                checkoutCar(slotIndex);
            }
        });

        // Initial call to display the car list
        displayCarList();
    </script> -->

    <!-- Dark mode script -->

</body>

</html>