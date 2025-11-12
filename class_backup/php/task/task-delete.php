<?php
include('database.php');
session_start();

if (isset($_POST['idx'])) {
    $idx = (int)$_POST['idx']; // 정수형 변환으로 보안 강화
    $email = mysqli_real_escape_string($connection, $_SESSION['useremail']); // 로그인 사용자 이메일

    // 자기 자신의 task만 삭제 가능하도록 제한
    $query = "DELETE FROM task WHERE idx = $idx AND email = '$email'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query Failed: ' . mysqli_error($connection));
    }

    echo "Task Deleted Successfully";
}
?>