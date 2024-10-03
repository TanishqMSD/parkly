<?php
    session_start();
    unset($_SESSION['vnumber']);
    unset($_SESSION['mnum']);
    session_destroy();
    echo "<script>window.location.href = 'index.html';</script>";
?>