<?php 
    include "dbconn.php";
    include "tablescreation.php";

    session_start();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect input data
        $mno = $_POST['mobile_number'];
        $u_pass = $_POST['password'];

        // Checking for user
        $checkUser = "SELECT * FROM customer WHERE mobileno = '$mno'";
        $result = $conn->query($checkUser);

        if ($result->num_rows <= 0) {
            $conn->close();
            echo "<script>alert('User with this mobile number does not exists!'); window.location.href = 'login.html';</script>";
        } else {
            // Checking for user's password
            $checkUser = "SELECT * FROM customer WHERE mobileno = '$mno'";
            $result = $conn->query($checkUser);
            $row = $result->fetch_assoc();
            
            if($row["u_pass"] == $u_pass)
            {
                echo "<script>alert('Logged in successfully!'); window.location.href = 'availability.php';</script>";
                $_SESSION["mnum"] = $mno;
            }
            else
            {
                echo "<script>alert('Incorrect Password!'); window.location.href = 'login.html';</script>";
            }
        }
       $conn->close();
    }
?>
