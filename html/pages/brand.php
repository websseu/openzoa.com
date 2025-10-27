<?php
    require_once __DIR__ . '/../config.php';

    // slug 값 받아오기
    $slug = $_GET['slug'] ?? '';
    if (!$slug) {
        die("브랜드를 선택해주세요.");
    }

    // slug로 브랜드 조회
    $stmt = $mysqli->prepare("SELECT id, name, logo FROM brands WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $brand = $stmt->get_result()->fetch_assoc();
    $brand_id = (int)$brand['id'];

    if (!$brand) {
        die("브랜드 정보를 찾을 수 없습니다.");
    }

    // 매장 조회
    $stmt = $mysqli->prepare("SELECT * FROM stores WHERE brand_id = ? ORDER BY name ASC");
    $stmt->bind_param("i", $brand_id);      
    $stmt->execute();
    $stores = $stmt->get_result();

    // SEO 메타데이터
    $page_title   = "OpenZoa - [{$brand['name']}] 전국 매장 오픈시간 휴무일 주차 위치 정보";
    $page_desc    = "OpenZoa는 [{$brand['name']}] 전국 매장과 프랜차이즈의 오픈시간, 휴무일, 주차 가능 여부, 위치 정보를 한눈에 제공합니다.";
    $page_keyword = "openzoa, {$brand['name']} 오픈시간, {$brand['name']} 휴무일, {$brand['name']} 주차장, {$brand['name']} 위치정보, {$brand['name']} 영업시간";
    $body_class   = "site-brand";

    include __DIR__ . '/../includes/head.php';
    include __DIR__ . '/../includes/header.php';
    include __DIR__ . '/../includes/store-search.php'; 
?>
        
        <!-- 매장 리스트 -->
        <section class="store-list">
            <h2>
                <img src="<?= htmlspecialchars($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>">
                <?= htmlspecialchars($brand['name']) ?> 매장 리스트
            </h2>
            <p>총 <?= $stores->num_rows ?>개 매장을 확인할 수 있습니다.</p>

            <!-- 매장 테이블 -->
            <div class="store-table">
                <table>
                    <thead>
                        <tr>
                            <th>매장명</th>
                            <th>운영시간</th>
                            <th>휴무일</th>
                            <th>자세히 보기</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stores->num_rows > 0): ?>
                            <?php while ($store = $stores->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <a href="/store/<?= $store['public_id'] ?>" class="line-hover">
                                            <?= htmlspecialchars($store['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($store['shopping_time']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($store['closed_days'])) ?></td>
                                    <td>
                                        <a href="/store/<?= $store['public_id'] ?>" class="line-hover">
                                            자세히 보기
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="center">등록된 매장이 없습니다.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

<?php
    include __DIR__ . '/../includes/footer.php'; 
?>