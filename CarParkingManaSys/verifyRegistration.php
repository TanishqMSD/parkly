<?php 
    include "dbconn.php";
    include "tablescreation.php";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect input data
        $u_name = $_POST['username'];
        $mno = $_POST['mobile_number'];
        $u_pass = $_POST['password'];

        $checkUser = "SELECT * FROM customer WHERE mobileno = '$mno'";
        $result = $conn->query($checkUser);

        if ($result->num_rows > 0) {
            $conn->close();
            echo "<script>alert('This number already exists!'); window.location.href = 'register.html';</script>";
        } else {
            $sql = "INSERT INTO customer (u_name, mobileno, u_pass)
                    VALUES ('$u_name', '$mno', '$u_pass')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('New user registered successfully!'); window.location.href = 'login.html';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
       $conn->close();
    }
?>
