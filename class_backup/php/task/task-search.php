<?php
include('database.php');
session_start();

$search = $_POST['search'];

if (!empty($search)) {
    $email = mysqli_real_escape_string($connection, $_SESSION['useremail']); // 로그인 사용자 확인
    $search = mysqli_real_escape_string($connection, $search);              // 검색어 인젝션 방지

    // 해당 사용자(email) 기준으로 task_name LIKE 검색
    $query = "SELECT * FROM task 
              WHERE email = '$email' 
              AND task_name LIKE '{$search}%'";

    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query Error: ' . mysqli_error($connection));
    }

    $json = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $json[] = array(
            'idx' => $row['idx'],
            'task_name' => $row['task_name'],
            'task_description' => $row['task_description'],
            'task_datetime' => $row['task_datetime']
        );
    }

    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>