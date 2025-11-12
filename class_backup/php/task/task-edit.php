<?php
include('database.php');
session_start();

if (isset($_POST['idx'])) {
    $task_name = mysqli_real_escape_string($connection, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($connection, $_POST['task_description']);
    $idx = (int)$_POST['idx'];
    $email = mysqli_real_escape_string($connection, $_SESSION['useremail']); // 로그인한 사용자만 수정 가능

    $query = "UPDATE task 
              SET task_name = '$task_name', 
                  task_description = '$task_description' 
              WHERE idx = '$idx' AND email = '$email'";

    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query Failed: ' . mysqli_error($connection));
    }

    echo "Task Updated Successfully";
}
?>