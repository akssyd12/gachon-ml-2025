<?php
// Redis에 연결
$redis = new Redis();               // Redis 클라이언트 객체 생성
$redis->connect('myredis', 6379);   // Redis 서버(myredis:6379)에 연결
                                    // 'myredis'는 Docker 네트워크 컨테이너명일 수 있음
session_name("GCTASKID");     // 추가

// 세션 핸들러 설정
session_set_save_handler(
    // open($save_path, $session_name)
    // 세션이 처음 시작될 때 호출됨 (session_start() 시점)
    // 파일 기반 세션이라면 세션 저장 경로를 열지만, Redis는 이미 연결되어 있으므로 특별한 작업 필요 없음
    function ($save_path, $session_name) use ($redis) {
        // $save_path : 세션 파일 저장 경로 (Redis에서는 무시)
        // $session_name : 세션 이름 (보통 PHPSESSID)
        return true; // 정상적으로 열렸음을 반환
    },

    // close()
    // 세션 처리가 끝날 때 (스크립트 종료 시) 호출됨
    // Redis 연결 종료나 정리 작업을 수행할 수 있으나, 여기서는 생략
    function () use ($redis) {
        return true; // 특별한 종료 작업 없음
    },

    // read($session_id)
    // 세션 시작 시, 기존 세션 데이터를 읽기 위해 호출됨
    // Redis에 저장된 "session:<세션ID>" 키에서 세션 데이터를 가져옴
    function ($session_id) use ($redis) {
        // $session_id : 현재 세션을 식별하는 고유 ID
        $data = $redis->get("session:$session_id"); // Redis에서 세션 데이터 읽기
        error_log("Read session data: $data");      // 디버깅용 로그 출력
        return $data ? $data : '';                  // 데이터가 없으면 빈 문자열 반환
    },

    // write($session_id, $session_data)
    // 스크립트 종료 시 세션이 변경되었다면 호출됨
    // 세션 데이터를 Redis에 저장함 (유효기간 3600초 = 1시간)
    function ($session_id, $session_data) use ($redis) {
        // setex(key, TTL, value) : TTL(초) 동안 유효한 키 저장
        return $redis->setex("session:$session_id", 3600, $session_data);
    },

    // destroy($session_id)
    // session_destroy()가 호출될 때 실행됨
    // Redis에서 해당 세션 키를 삭제
    function ($session_id) use ($redis) {
        return $redis->del("session:$session_id");
    },

    // gc($maxlifetime)
    // 오래된 세션 데이터를 정리하는 가비지 컬렉션 단계
    // Redis는 TTL(유효기간)로 자동 만료되므로 별도 처리 불필요
    function ($maxlifetime) use ($redis) {
        return true;
    }
);

// 등록된 핸들러를 활성화하려면 session_start() 호출 필요
// 이후 $_SESSION에 저장되는 값은 Redis를 통해 관리됨

?>
