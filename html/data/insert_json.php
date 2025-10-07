<?php
require_once __DIR__ . '/../config.php';   

// JSON 파일 경로
$jsonFile = __DIR__ . '/emart/2025/2025-10-04.json';

if (!file_exists($jsonFile)) {
    die("❌ JSON 파일을 찾을 수 없습니다.");
}

// JSON 로드 및 디코딩
$jsonData = json_decode(file_get_contents($jsonFile), true);

if (!$jsonData || empty($jsonData['stores'])) {
    die("❌ JSON 데이터가 비어있습니다.");
}

echo "총 " . count($jsonData['stores']) . "개 점포를 불러왔습니다.<br>";

// 브랜드 목록 가져오기
$brands = [];
$result = $mysqli->query("SELECT id, name FROM brands");
while ($row = $result->fetch_assoc()) {
    $brands[$row['name']] = $row['id'];
}

// 지역 목록 가져오기
$regions = [];
$result = $mysqli->query("SELECT id, name FROM regions");
while ($row = $result->fetch_assoc()) {
    $regions[$row['name']] = $row['id'];
}

// =======================
// JSON → DB 저장
// =======================
foreach ($jsonData['stores'] as $store) {

    // 브랜드 자동 감지
    $brand_id = null;
    foreach ($brands as $brand_name => $id) {
        if (mb_strpos($store['store_name'], $brand_name) !== false) {
            $brand_id = $id;
            break;
        }
    }

    if (!$brand_id) {
        echo "⚠️ 브랜드 매칭 실패: {$store['store_name']}<br>";
        continue;
    }

    // 지역 자동 감지 (road_address 기준)
    $region_id = null;
    foreach ($regions as $region_name => $id) {
        if (mb_strpos($store['road_address'], $region_name) !== false) {
            $region_id = $id;
            break;
        }
    }

    if (!$region_id) {
        echo "⚠️ 지역 매칭 실패: {$store['store_name']} ({$store['road_address']})<br>";
        continue;
    }

    // ======================
    // DB 저장
    // ======================
    $stmt = $mysqli->prepare("
        INSERT INTO stores 
        (brand_id, region_id, name, road_address, lot_address, customer_center,
         shopping_time, closed_days, parking_info, lat, lng, directions)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iisssssssdss",
        $brand_id,
        $region_id,
        $store['store_name'],
        $store['road_address'],
        $store['lot_address'],
        $store['customer_center'],
        $store['shopping_time'],
        $store['closed_days'],
        $store['parking_info'],
        $store['coordinates']['lat'],
        $store['coordinates']['lng'],
        $store['directions']
    );

    if ($stmt->execute()) {
        echo "저장 성공: {$store['store_name']}<br>";
    } else {
        echo "저장 실패: {$store['store_name']} - " . $stmt->error . "<br>";
    }

    $stmt->close();
}

echo "<br>🎉 데이터 입력 완료!";
?>