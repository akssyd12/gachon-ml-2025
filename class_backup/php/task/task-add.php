<?php
include('database.php');
session_start();

if (isset($_POST['task_name']) && isset($_POST['task_description'])) {
    $task_name = mysqli_real_escape_string($connection, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($connection, $_POST['task_description']);
    $email = mysqli_real_escape_string($connection, $_SESSION['useremail']); // 로그인 세션 이메일

    $query = "INSERT INTO task (email, task_name, task_description, task_datetime)
              VALUES ('$email', '$task_name', '$task_description', NOW())";

    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query Failed: ' . mysqli_error($connection));
    }

    echo "Task Added Successfully";
}
?>