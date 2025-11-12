<?php
include_once("./database.php");   // DB 연결 설정 파일 포함
$ret = array();                   // 반환용 배열 초기화
session_start();                  // 세션 시작 (로그인 정보 확인용)

// ==============================
// ① 로그인 여부 확인
// ==============================
if (!isset($_SESSION['useremail']) || $_SESSION['useremail'] == "") {
    // 세션에 이메일이 없으면 로그인 안 된 상태
    $ret['result'] = "no";
    $ret['msg'] = "로그인을 해주십시오.";
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}

// ==============================
// ② 사용자 이메일 기준으로 task 데이터 조회
// ==============================
$email = mysqli_real_escape_string($connection, $_SESSION['useremail']); // SQL 인젝션 방지
$query = "SELECT * FROM task WHERE email = '{$email}'";                  // 해당 사용자 task만 조회
$result = mysqli_query($connection, $query);                             // 쿼리 실행

if (!$result) {
    // DB 오류 시 처리
    $ret['result'] = "no";
    $ret['msg'] = "데이터베이스 조회 오류";
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}

// ==============================
// ③ 결과를 JSON 형태로 구성
// ==============================
$json = [];
while ($row = mysqli_fetch_assoc($result)) {
    // mysqli_fetch_assoc()은 컬럼명을 key로 갖는 연관배열 반환
    // 따라서 그대로 JSON 배열에 추가하면 컬럼명 = JSON 키로 유지됨
    $json[] = $row;
}

// ==============================
// ④ 응답 데이터 구성 및 출력
// ==============================
$ret['result'] = "ok";                          // 처리 성공
$ret['msg'] = "정상적으로 데이터를 가져왔습니다.";  // 메시지
$ret['tasks'] = $json;                          // task 목록 데이터
$ret['username'] = $_SESSION['username'];       // 세션의 사용자 이름

// JSON으로 최종 출력 (한글 깨짐 방지 옵션)
echo json_encode($ret, JSON_UNESCAPED_UNICODE);
?>