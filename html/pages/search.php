<?php
require_once __DIR__ . '/../config.php';

$q = trim($_GET['q'] ?? '');

// 검색어가 없으면 메시지
if ($q === '') {
    die('검색어를 입력하세요.');
}

// 검색 쿼리
$stmt = $mysqli->prepare("
    SELECT s.public_id, s.name, s.shopping_time, s.closed_days,
           b.name AS brand_name, b.logo AS brand_logo
    FROM stores s
    JOIN brands b ON s.brand_id = b.id
    WHERE s.name LIKE CONCAT('%', ?, '%')       
    ORDER BY s.name ASC
");
$stmt->bind_param('s', $q);
$stmt->execute();
$result = $stmt->get_result();

// SEO 메타데이터
$page_title   = "OpenZoa - '{$q}' 검색 결과 | 전국 매장 오픈·휴무·주차·위치 정보";
$page_desc    = "'{$q}' 관련 전국 매장 오픈시간, 휴무일, 주차, 위치 정보를 한눈에 확인하세요.";
$page_keyword = "openzoa, {$q}, 매장 검색, 오픈시간, 휴무일, 주차정보, 위치정보";
$body_class   = "site-search";

include __DIR__ . '/../includes/head.php'; 
include __DIR__ . '/../includes/header.php'; 
include __DIR__ . '/../includes/store-search.php';
?>

<!-- 검색 결과 -->
<section class="search-result">
    <h2>‘<?= htmlspecialchars($q) ?>’ 검색 결과</h2>
    <p class="mb10">총 <?= $result->num_rows ?>개 매장을 확인할 수 있습니다.</p>

    <div class="store-table">
        <table>
            <thead>
                <tr>
                    <th>브랜드</th>
                    <th>매장명</th>
                    <th>운영시간</th>
                    <th>휴무일</th>
                    <th>자세히 보기</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($store = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="brand">
                                <img src="<?= htmlspecialchars($store['brand_logo']) ?>" alt="<?= htmlspecialchars($store['brand_name']) ?>" >
                                <?= htmlspecialchars($store['brand_name']) ?>
                            </td>
                            <td>
                                <a href="/store/<?= htmlspecialchars($store['public_id']) ?>" class="line-hover">
                                    <?= htmlspecialchars($store['name']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($store['shopping_time'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($store['closed_days'] ?: '-') ?></td>
                            <td>
                                <a href="/store/<?= htmlspecialchars($store['public_id']) ?>" class="line-hover">
                                    자세히 보기
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">‘<?= htmlspecialchars($q) ?>’에 대한 검색 결과가 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
