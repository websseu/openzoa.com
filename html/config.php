<?php
// ============================
// 개발용 에러 표시
// ============================
ini_set('display_errors', 1);      // 브라우저에 에러 출력
error_reporting(E_ALL);           // 모든 에러 보고

// ============================
// DB 및 사이트 설정
// ============================
define('DB_HOST', 'localhost');          // 데이터베이스 호스트
define('DB_USER', 'root');               // DB 사용자명
define('DB_PASS', 'qwer1234!@#$');       // DB 비밀번호
define('DB_NAME', 'openzoa');            // DB 이름
define('DB_CHARSET', 'utf8mb4');         // 문자셋

define('BASE_URL', 'https://openzoa.com');  // 사이트 기본 URL
define('SITE_NAME', 'openzoa');            // 사이트 이름

// ============================
// DB 연결
// ============================
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 연결 오류 확인
if ($mysqli->connect_error) {
    die("❌ 데이터베이스 연결 실패: " . $mysqli->connect_error);
}

// 문자셋 설정
if (!$mysqli->set_charset(DB_CHARSET)) {
    die("❌ 문자셋 설정 실패: " . $mysqli->error);
}
