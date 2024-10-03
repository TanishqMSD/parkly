<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
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
                    <tr>
                        <th>Slot</th>
                        <th>Vehicle Number</th>
                        <th>Car Model</th>
                        <th>Car Type</th>
                        <th>Mobile Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
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
                                echo $row['vnumber'];
                                echo "<td class='py-3 px-6 text-left'>" . $row['vehicle_name'] . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $row['vtype'] . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $row['mno'] . "</td>";
                                echo "<td class='py-3 px-6 text-center'>
                      <button class='bg-blue-500 text-white px-4 py-1 rounded'>Edit</button>
                      <button class='bg-red-500 text-white px-4 py-1 rounded'>Delete</button>
                      </td>";
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
</body>

</html>
