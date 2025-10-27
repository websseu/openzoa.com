<?php
    session_start();

    // 로그인 세션 검사
    if (empty($_SESSION['admin_auth'])) {
        header("Location: admin-login.php");
        exit;
    }

    // 세션 만료 
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > ($_SESSION['expire_time'] ?? 18000)) {
        session_unset();
        session_destroy();
        header("Location: admin-login.php?expired=1");
        exit;
    }

    require_once __DIR__ . '/../config.php';

    $q = trim($_GET['q'] ?? '');
    $order_by = $_GET['order_by'] ?? 'created_at';
    $order_dir = $_GET['order_dir'] ?? 'DESC';
    
    // 허용된 정렬 컬럼만 사용
    $allowed_columns = ['id', 'brand_name', 'store_name', 'road_address', 'created_at'];
    if (!in_array($order_by, $allowed_columns)) {
        $order_by = 'created_at';
    }
    
    // 정렬 방향 검증
    $order_dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';
    
    $sql = "
        SELECT s.id, b.name AS brand_name, s.name AS store_name, s.road_address,
            s.customer_center, s.parking_info, s.shopping_time, s.created_at
        FROM stores s
        LEFT JOIN brands b ON s.brand_id = b.id
    ";
    if ($q !== '') {
        $sql .= " WHERE s.name LIKE CONCAT('%', ?, '%') OR s.road_address LIKE CONCAT('%', ?, '%')";
    }
    
    // 정렬 적용
    if ($order_by === 'id') {
        $sql .= " ORDER BY s.id $order_dir";
    } elseif ($order_by === 'brand_name') {
        $sql .= " ORDER BY b.name $order_dir";
    } elseif ($order_by === 'store_name') {
        $sql .= " ORDER BY s.name $order_dir";
    } elseif ($order_by === 'road_address') {
        $sql .= " ORDER BY s.road_address $order_dir";
    } else {
        $sql .= " ORDER BY s.created_at $order_dir";
    }
    
    $stmt = $mysqli->prepare($sql);
    if ($q !== '') $stmt->bind_param('ss', $q, $q);
    $stmt->execute();
    $result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>매장 관리 | openzoa 관리자</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-site">
    <header>
        <h1><a href="/">openzoa 관리자</a></h1>
    </header>

    <main>
        <form class="search" method="get" action="">
            <input type="text" name="q" placeholder="매장명 또는 주소 검색" value="<?= htmlspecialchars($q) ?>">
            <button type="submit">검색</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>
                            <a href="?order_by=id&order_dir=<?= ($order_by === 'id' && $order_dir === 'ASC') ? 'DESC' : 'ASC' ?><?= $q ? '&q=' . urlencode($q) : '' ?>">
                                ID <?= $order_by === 'id' ? ($order_dir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="?order_by=brand_name&order_dir=<?= ($order_by === 'brand_name' && $order_dir === 'ASC') ? 'DESC' : 'ASC' ?><?= $q ? '&q=' . urlencode($q) : '' ?>">
                                브랜드 <?= $order_by === 'brand_name' ? ($order_dir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="?order_by=store_name&order_dir=<?= ($order_by === 'store_name' && $order_dir === 'ASC') ? 'DESC' : 'ASC' ?><?= $q ? '&q=' . urlencode($q) : '' ?>">
                                매장명 <?= $order_by === 'store_name' ? ($order_dir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="?order_by=road_address&order_dir=<?= ($order_by === 'road_address' && $order_dir === 'ASC') ? 'DESC' : 'ASC' ?><?= $q ? '&q=' . urlencode($q) : '' ?>">
                                주소 <?= $order_by === 'road_address' ? ($order_dir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>대표번호</th>
                        <th>주차</th>
                        <th>영업시간</th>
                        <th>
                            <a href="?order_by=created_at&order_dir=<?= ($order_by === 'created_at' && $order_dir === 'ASC') ? 'DESC' : 'ASC' ?><?= $q ? '&q=' . urlencode($q) : '' ?>">
                                등록일 <?= $order_by === 'created_at' ? ($order_dir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['brand_name']) ?></td>
                                <td><?= htmlspecialchars($row['store_name']) ?></td>
                                <td><?= htmlspecialchars($row['road_address']) ?></td>
                                <td><?= htmlspecialchars($row['customer_center'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['parking_info'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['shopping_time'] ?? '-') ?></td>
                                <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8">등록된 매장이 없습니다.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
