<?php
    require_once __DIR__ . '/../config.php';

    // SEO 메타데이터
    $page_title = "OpenZoa - 전국 매장 오픈시간 휴무일 주차 위치 정보";
    $page_desc = "OpenZoa는 전국 매장과 프랜차이즈의 오픈시간, 휴무일, 주차 가능 여부, 위치 정보를 한눈에 제공합니다. 쇼핑 전 매장 정보를 쉽고 빠르게 확인하세요.";
    $page_keyword = "openzoa, 매장 오픈시간, 휴무일, 주차장, 위치정보, 마트 휴무일, 백화점 영업시간, 편의점 오픈시간, 쇼핑몰 주차정보";
    $body_class = "site-main";

    // 브랜드 & 매장 수 불러오기
    $sql = "
        SELECT b.id, b.name, b.slug, b.logo, COUNT(s.id) AS store_count
        FROM brands b
        LEFT JOIN stores s ON s.brand_id = b.id
        GROUP BY b.id, b.name, b.slug, b.logo
        ORDER BY b.name ASC
    ";
    $result = $mysqli->query($sql);
    $brands = $result->fetch_all(MYSQLI_ASSOC);

    include __DIR__ . '/../includes/head.php'; 
    include __DIR__ . '/../includes/header.php'; 
    include __DIR__ . '/../includes/store-search.php'; 
?>
        
        <!-- 브랜드 목록 -->
        <section class="brand-list">
            <ul>
                <?php if (!empty($brands)): ?>
                    <?php foreach ($brands as $brand): ?>
                        <li>
                            <a href="/brand/<?= htmlspecialchars($brand['slug']) ?>">
                                <img src="<?= htmlspecialchars($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>">
                                <span><?= $brand['store_count'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>등록된 브랜드가 없습니다.</li>
                <?php endif; ?>
            </ul>
        </section>
<?php
    include __DIR__ . '/../includes/footer.php'; 
?>