<?php
include('database.php');
session_start();

if (isset($_POST['idx'])) {
    $idx = (int)$_POST['idx']; // 숫자형 강제 변환
    $email = mysqli_real_escape_string($connection, $_SESSION['useremail']); // 로그인 사용자 이메일

    $query = "SELECT * FROM task WHERE idx = {$idx} AND email = '{$email}'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query Failed: ' . mysqli_error($connection));
    }

    $json = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $json[] = array(
            'idx' => $row['idx'],
            'email' => $row['email'],
            'task_name' => $row['task_name'],
            'task_description' => $row['task_description'],
            'task_datetime' => $row['task_datetime']
        );
    }

    // 단일 행만 반환
    echo json_encode($json[0], JSON_UNESCAPED_UNICODE);
}
?>