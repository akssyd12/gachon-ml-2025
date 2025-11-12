<?php

include_once("./database.php");
$ret = array();
// 세션 시작
session_start();
if(!isset($_POST['email']) || $_POST['email'] == ""){
    $ret['result'] = "no";
    $ret['msg'] = "이메일 정보가 없습니다.";
    $jsonstring = json_encode($ret, JSON_UNESCAPED_UNICODE);
    echo $jsonstring;
    exit;
}
if(!isset($_POST['password']) || $_POST['password'] == ""){
    $ret['result'] = "no";
    $ret['msg'] = "패스워드 정보가 없습니다.";
    $jsonstring = json_encode($ret, JSON_UNESCAPED_UNICODE);
    echo $jsonstring;
    exit;
}
$query = "SELECT * from user where email='{$_POST['email']}' and pass='{$_POST['password']}'";
$result = mysqli_query($connection, $query);
if (!$result) {
    $ret['result'] = "no";
    $ret['msg'] = "데이터베이스 연결 오류";
    $jsonstring = json_encode($ret, JSON_UNESCAPED_UNICODE);
    echo $jsonstring;
    exit;
}

$row = mysqli_fetch_array($result);
if(!isset($row['email'])){
    $ret['result'] = "no";
    $ret['msg'] = "로그인 정보가 틀립니다.";
    $jsonstring = json_encode($ret, JSON_UNESCAPED_UNICODE);
    echo $jsonstring;
    exit;
}else{
    $_SESSION['username'] = $row['name'];
    $_SESSION['useremail'] = $row['email'];
    
    $ret['result'] = "ok";
    $ret['msg'] = "정상 로그인이 되었습니다.";
    $jsonstring = json_encode($ret, JSON_UNESCAPED_UNICODE);
    echo $jsonstring;
    exit;
}

?>