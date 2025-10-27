<?php
    require_once __DIR__ . '/../config.php';

    // public_id 값 받아오기
    $public_id = $_GET['id'] ?? '';

    if (!$public_id) {
        die("잘못된 접근입니다.");
    }

    // public_id로 매장 조회
    $stmt = $mysqli->prepare("
        SELECT s.*, b.name AS brand_name, b.logo AS brand_logo, r.name AS region_name
        FROM stores s
        JOIN brands b ON s.brand_id = b.id
        JOIN regions r ON s.region_id = r.id
        WHERE s.public_id = ?
    ");
    $stmt->bind_param("s", $public_id);
    $stmt->execute();
    $store = $stmt->get_result()->fetch_assoc();

    if (!$store) {
        die("매장 정보를 찾을 수 없습니다.");
    }

    // SEO 메타데이터
    $page_title   = "OpenZoa - {$store['name']} 매장 상세 정보";
    $page_desc    = "{$store['brand_name']} {$store['name']} 매장의 운영시간, 휴무일, 주차, 위치 정보를 확인하세요.";
    $page_keyword = "openzoa, {$store['brand_name']}, {$store['name']}, 매장정보, 오픈시간, 주차정보";
    $body_class   = "site-store";

    include __DIR__ . '/../includes/head.php';
    include __DIR__ . '/../includes/header.php';
    include __DIR__ . '/../includes/store-search.php'; 
?>
        
        <!-- 스토어 상세페이지 -->
        <section class="store-detail">
            <h2>
                <img src="<?= htmlspecialchars($store['brand_logo']) ?>" alt="<?= htmlspecialchars($store['brand_name']) ?>">
                <?= htmlspecialchars($store['name']) ?>
            </h2>
            <ul>
                <?php if (!empty($store['road_address'])): ?>
                    <li><strong>주소:</strong> <?= htmlspecialchars($store['road_address']) ?></li>
                <?php endif; ?>

                <?php if (!empty($store['shopping_time'])): ?>
                    <li><strong>운영시간:</strong> <?= htmlspecialchars($store['shopping_time']) ?></li>
                <?php endif; ?>

                <?php if (!empty($store['closed_days'])): ?>
                    <li><strong>휴무일:</strong> <?= htmlspecialchars($store['closed_days']) ?></li>
                <?php endif; ?>

                <?php if (!empty($store['parking_info'])): ?>
                    <li><strong>주차:</strong> <?= htmlspecialchars($store['parking_info']) ?></li>
                <?php endif; ?>

                <?php if (!empty($store['customer_center'])): ?>
                    <li>
                        <strong>전화:</strong>
                        <a href="tel:<?= htmlspecialchars($store['customer_center']) ?>" class="line">
                            <?= htmlspecialchars($store['customer_center']) ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <?php if (!empty($store['lat']) && !empty($store['lng'])): ?>
                <div class="map">
                    <h3>앱으로 확인하기</h3>

                    <!-- 네이버 포털 검색 열기 -->
                    <a target="_blank" rel="noopener noreferrer" href="https://search.naver.com/search.naver?query=<?= rawurlencode($store['name']) ?>">
                        <img src="/assets/img/logo/naver.webp" alt="네이버 포털 검색에서 열기" class="icon">
                    </a>

                    <!-- 카카오 웹페이지 열기 -->
                    <a target="_blank" class="only-pc" rel="noopener noreferrer" href="https://map.kakao.com/?q=<?= rawurlencode($store['name'] ?? '') ?>">
                        <img src="/assets/img/logo/kakaomap.webp" alt="카카오 웹페이지 열기" class="icon">
                    </a>

                    <!-- 카카오 지도 앱 길찾기 열기(모바일 용) -->
                    <a target="_blank" class="only-ios only-android" rel="noopener noreferrer" href="kakaomap://route?ep=<?= $store['lat'] ?>,<?= $store['lng'] ?>&by=CAR">
                        <img src="/assets/img/logo/kakaomap.webp" alt="카카오맵 길찾기 열기" class="icon">
                    </a>

                    <!-- 네이버 웹페이지 열기 -->
                    <a target="_blank" rel="noopener noreferrer" href="https://map.naver.com/v5/search/<?= rawurlencode($store['name']) ?>">
                        <img src="/assets/img/logo/navermap.webp" alt="네이버 지도 웹에서 열기" class="icon">
                    </a>

                    <!-- 티맵 길찾기 앱 열기(모바일 용) -->
                    <a target="_blank" class="only-ios only-android" rel="noopener noreferrer" href="tmap://route?goalname=<?= rawurlencode($store['name']) ?>&goalx=<?= $store['lng'] ?>&goaly=<?= $store['lat'] ?>">
                        <img src="/assets/img/logo/tmap.webp" alt="티맵 길찾기 열기" class="icon">
                    </a>
 
                    <!-- 구글 맵 길찾기(사이트/모바일 용) -->
                    <a target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps/dir/?api=1&destination=<?= $store['lat'] ?>,<?= $store['lng'] ?>">
                        <img src="/assets/img/logo/googlemaps.webp" alt="Google Maps 길찾기" class="icon">
                    </a>

                    <p class="small mt5 only-ios only-android">원활한 사용을 위해 해당 앱 설치가 필요합니다.</p>
                </div>

                <!-- 지도 표시 -->
                <div 
                    id="map"
                    class="area"
                    data-lat="<?= htmlspecialchars($store['lat']) ?>"
                    data-lng="<?= htmlspecialchars($store['lng']) ?>"
                    data-name="<?= htmlspecialchars($store['name']) ?>"
                    data-time="<?= htmlspecialchars($store['shopping_time']) ?>"
                    data-phone="<?= htmlspecialchars($store['customer_center']) ?>">
                </div>
            <?php endif; ?>
        </section>
<?php
    include __DIR__ . '/../includes/footer.php'; 
?>