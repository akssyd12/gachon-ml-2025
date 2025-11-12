<?php

// Redis 세션 핸들러 등록 파일 불러오기
// (이 파일에는 session_set_save_handler() 설정이 들어 있음)
include_once("./redis_session.php");

// -----------------------------------------------------------
// ① session_start()
// -----------------------------------------------------------
// - 클라이언트의 쿠키에서 PHPSESSID를 확인함
//   (예: PHPSESSID=8f94e2b78e3d1e1a2c4f7b99c8a5e6f2)
// - 쿠키가 없으면 PHP가 새 세션 ID를 생성함
//
// - 이후 PHP 내부적으로 다음 호출 순서가 자동으로 진행됨:
//     1) open($save_path, $session_name)   → 세션 핸들러 초기화
//     2) read($session_id)                 → Redis에서 세션 데이터 조회
//
//   read($session_id)는 예를 들어 다음과 같은 Redis 키를 조회함:
//       get("session:8f94e2b78e3d1e1a2c4f7b99c8a5e6f2")
//
//   Redis에 저장된 데이터 예시:
//       username|s:9:"홍길동";userid|s:3:"kky";
//
//   PHP 엔진은 이 문자열을 자동으로 "역직렬화(unserialize)" 하여
//   $_SESSION 배열에 다음과 같이 복원함:
//       $_SESSION['username'] = "홍길동";
//       $_SESSION['userid']   = "kky";
//
//   즉, read()는 데이터를 Redis에서 가져오고
//   PHP가 $_SESSION 전역 배열에 자동으로 채워 넣는 단계이다.
session_start();

// -----------------------------------------------------------
// ② $_SESSION 데이터 접근
// -----------------------------------------------------------
// 위 단계에서 Redis → PHP 메모리로 이미 복원된 데이터이므로,
// 직접 할당 없이 곧바로 $_SESSION 값 사용 가능
echo "사용자 이름 : {$_SESSION['username']}<br>";
echo "사용자 아이디 : {$_SESSION['useremail']}<br>";

// -----------------------------------------------------------
// ③ 스크립트 종료 시 자동 호출
// -----------------------------------------------------------
// - 세션 데이터가 변경되지 않았더라도 write($session_id, $session_data)가 호출됨
//   → PHP가 현재 $_SESSION 배열을 다시 직렬화하여 Redis에 setex()로 저장
// - close() 호출로 세션 처리가 완료됨

?>