<?php
require_once __DIR__ . '/../config.php';

// =======================
// 1. JSON 파일 경로
// =======================
$jsonFile = __DIR__ . '/merged/busan_2025-10-16_merged.json';

if (!file_exists($jsonFile)) {
    die("❌ JSON 파일을 찾을 수 없습니다: {$jsonFile}");
}

$jsonData = json_decode(file_get_contents($jsonFile), true);
if (!$jsonData || empty($jsonData['item'])) {
    die("❌ JSON 데이터가 비어있거나 잘못되었습니다.");
}

echo "✅ 총 " . count($jsonData['item']) . "개의 스타벅스 매장 데이터를 불러왔습니다.<br>";

// =======================
// 2. 고정 브랜드 & 지역 설정
// =======================
$brand_id = 6; // 스타벅스 고정 ID

$region_id = null;
$result = $mysqli->query("SELECT id, name FROM regions");
while ($row = $result->fetch_assoc()) {
    if (mb_strpos($jsonData['location'], $row['name']) !== false) {
        $region_id = $row['id'];
        break;
    }
}
if (!$region_id) {
    die("❌ '부산' 지역을 regions 테이블에서 찾을 수 없습니다.");
}

// =======================
// 3. 랜덤 public_id 생성
// =======================
function generatePublicId($mysqli, $length = 10) {
    do {
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= random_int(0, 9);
        }

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM stores WHERE public_id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);

    return $id;
}

// =======================
// 4. INSERT 준비
// =======================
$stmt = $mysqli->prepare("
    INSERT INTO stores 
    (brand_id, region_id, name, public_id, road_address, customer_center,
     shopping_time, closed_days, parking_info, lat, lng, directions, images)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("❌ 쿼리 준비 실패: " . $mysqli->error);
}

// =======================
// 5. 매장 데이터 삽입
// =======================
$success = 0;
$fail = 0;

foreach ($jsonData['item'] as $store) {
    $name = trim($store['name'] ?? '');
    $address = trim($store['address'] ?? '');
    $phone = trim($store['phone'] ?? '');
    $hours = !empty($store['hours']) ? implode(", ", $store['hours']) : '';
    $parking = trim($store['parking'] ?? '');
    $directions = trim($store['directions'] ?? '');
    $images = !empty($store['images']) ? json_encode($store['images'], JSON_UNESCAPED_UNICODE) : null;
    $lat = !empty($store['latitude']) ? (float)$store['latitude'] : 0;
    $lng = !empty($store['longitude']) ? (float)$store['longitude'] : 0;
    $closed_days = '연중무휴'; // 고정값

    if ($name === '' || $address === '') {
        echo "⚠️ 누락된 데이터로 스킵됨: {$name}<br>";
        $fail++;
        continue;
    }

    $public_id = generatePublicId($mysqli);

    $stmt->bind_param(
        "iisssssssdsss",
        $brand_id,
        $region_id,
        $name,
        $public_id,
        $address,
        $phone,
        $hours,
        $closed_days,
        $parking,
        $lat,
        $lng,
        $directions,
        $images
    );

    if ($stmt->execute()) {
        echo "✅ [{$name}] 저장 완료 (ID: {$public_id})<br>";
        $success++;
    } else {
        echo "❌ [{$name}] 저장 실패: " . $stmt->error . "<br>";
        $fail++;
    }
}

$stmt->close();

echo "<br>🎉 완료! 성공 {$success}건 / 실패 {$fail}건<br>";
?>
