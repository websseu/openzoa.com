<?php
require_once __DIR__ . '/config.php';

$logFile = __DIR__ . '/autoemart.log';
$progressFile = __DIR__ . '/progress.txt';

// ===============================
// 1. 현재 진행 중인 ID 불러오기
// ===============================
if (file_exists($progressFile)) {
    $currentId = (int) file_get_contents($progressFile);
} else {
    $currentId = 1; // 첫 실행이면 1부터 시작
}

file_put_contents($logFile, date('Y-m-d H:i:s') . " 실행 시작 (ID={$currentId})\n", FILE_APPEND);

// ===============================
// 2. parking_info 업데이트 실행
// ===============================
$stmt = $mysqli->prepare("UPDATE stores SET parking_info = '1' WHERE id = ?");
$stmt->bind_param("i", $currentId);

if ($stmt->execute()) {
    file_put_contents($logFile, "✅ ID={$currentId} parking_info 업데이트 완료\n", FILE_APPEND);
} else {
    file_put_contents($logFile, "❌ ID={$currentId} 오류 발생: " . $stmt->error . "\n", FILE_APPEND);
}
$stmt->close();

// ===============================
// 3. 다음 실행을 위해 progress.txt 증가
// ===============================
$nextId = $currentId + 1;
file_put_contents($progressFile, $nextId);

file_put_contents($logFile, "다음 실행 예정 ID={$nextId}\n", FILE_APPEND);
file_put_contents($logFile, "종료 시간: " . date('Y-m-d H:i:s') . "\n\n", FILE_APPEND);
