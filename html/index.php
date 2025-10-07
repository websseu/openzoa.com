<?php
// 설정 파일 로드
require_once __DIR__ . '/config.php';

// 기본 페이지 경로 설정
$page = $_GET['page'] ?? 'main'; // URL ?page=main 식으로 사용
$pagePath = __DIR__ . '/pages/' . $page . '.php';

// 페이지 존재 여부 확인 후 로드
if (file_exists($pagePath)) {
    require $pagePath;
} else {
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
}
