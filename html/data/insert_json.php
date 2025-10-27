<?php
require_once __DIR__ . '/../config.php';   

// JSON 파일 경로
$jsonFile = __DIR__ . '/emart/2025/2025-10-04.json';

if (!file_exists($jsonFile)) {
    die("JSON 파일을 찾을 수 없습니다.");
}

// JSON 로드 및 디코딩
$jsonData = json_decode(file_get_contents($jsonFile), true);

if (!$jsonData || empty($jsonData['stores'])) {
    die("JSON 데이터가 비어있습니다.");
}

echo "총 " . count($jsonData['stores']) . "개 점포를 불러왔습니다.<br>";

// =======================
// 브랜드 목록 가져오기
// =======================
$brands = [];
$result = $mysqli->query("SELECT id, name FROM brands");
while ($row = $result->fetch_assoc()) {
    $brands[$row['name']] = $row['id'];
}

// =======================
// 지역 목록 가져오기
// =======================
$regions = [];
$result = $mysqli->query("SELECT id, name FROM regions");
while ($row = $result->fetch_assoc()) {
    $regions[$row['name']] = $row['id'];
}

// =======================
// 지역 별칭 매핑
// =======================
$region_aliases = [
    '서울특별시'    => '서울',
    '서울시'       => '서울',
    '부산광역시'    => '부산',
    '대구광역시'    => '대구',
    '인천광역시'    => '인천',
    '광주광역시'    => '광주',
    '대전광역시'    => '대전',
    '울산광역시'    => '울산',
    '세종특별자치시' => '세종',
    '경상북도'     => '경북',
    '경상남도'     => '경남',
    '전라북도'     => '전북',
    '전북특별자치도' => '전북',
    '전라남도'     => '전남',
    '충청북도'     => '충북',
    '충청남도'     => '충남',
    '강원도'       => '강원',
    '강원특별자치도' => '강원',
    '제주특별자치도' => '제주',
    '제주도'       => '제주'
];

// =======================
// 랜덤 번호 생성 함수
// =======================
function generatePublicId($mysqli, $length = 10) {
    do {
        // 10자리 랜덤 숫자
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= random_int(0, 9);
        }

        // 중복 체크
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
        echo "브랜드 매칭 실패: {$store['store_name']}<br>";
        continue;
    }

    // 지역 자동 감지 (road_address 기준)
    $region_id = null;
    $address = $store['road_address'];

    // 주소에서 별칭을 표준 지역명으로 변환
    foreach ($region_aliases as $alias => $canonical) {
        if (mb_strpos($address, $alias) !== false) {
            $address = str_replace($alias, $canonical, $address);
            break;
        }
    }

    // 변환된 주소를 DB의 regions와 매칭
    foreach ($regions as $region_name => $id) {
        if (mb_strpos($address, $region_name) !== false) {
            $region_id = $id;
            break;
        }
    }

    if (!$region_id) {
        echo "⚠️ 지역 매칭 실패: {$store['store_name']} ({$store['road_address']})<br>";
        continue;
    }

    // 랜덤 10자리 번호 생성
    $public_id = generatePublicId($mysqli, 10);

    // DB 저장
    $stmt = $mysqli->prepare("
        INSERT INTO stores 
        (brand_id, region_id, name, public_id, road_address, lot_address, customer_center,
         shopping_time, closed_days, parking_info, lat, lng, directions)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iisssssssdsss",
        $brand_id,
        $region_id,
        $store['store_name'],
        $public_id,
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
        echo "✅ 저장 성공: {$store['store_name']} → [{$public_id}]<br>";
    } else {
        echo "❌ 저장 실패: {$store['store_name']} - " . $stmt->error . "<br>";
    }

    $stmt->close();
}

echo "<br>🎉 데이터 입력 완료!";
?>