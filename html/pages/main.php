<?php
    require_once __DIR__ . '/../config.php';

    $body_class = "site-main";
    $page_title = "OpenZoa - 전국 매장 오픈시간 휴무일 주차 위치 정보";
    $page_desc = "OpenZoa는 전국 매장과 프랜차이즈의 오픈시간, 휴무일, 주차 가능 여부, 위치 정보를 한눈에 제공합니다. 쇼핑 전 매장 정보를 쉽고 빠르게 확인하세요.";
    $page_keyword = "openzoa, 매장 오픈시간, 휴무일, 주차장, 위치정보, 마트 휴무일, 백화점 영업시간, 편의점 오픈시간, 쇼핑몰 주차정보";

    include __DIR__ . '/../includes/head.php'; 
    include __DIR__ . '/../includes/header.php'; 
?>

        <section class="store-search">
            <form action="/" method="get">
                <input type="text" name="q" placeholder="예: 이마트 평촌점" aria-label="점포 검색" required>
                <button type="submit" aria-label="검색">검색하기</button>
            </form>
        </section>

        <section class="store-list">
            <ul>
                <li>
                    <a href="/">
                        <img src="/assets/img/logo/emart.webp" alt="이마트">
                        <span>1</span>
                    </a>
                </li>
                <li>
                    <a href="/">
                        <img src="/assets/img/logo/starbucks.webp" alt="스타벅스">
                        <span>13</span>
                    </a>
                </li>
                <li>
                    <a href="/">
                        <img src="/assets/img/logo/ediya.webp" alt="이디야">
                        <span>111</span>
                    </a>
                </li>
                <li>
                    <a href="/">
                        <img src="/assets/img/logo/subway.webp" alt="서브웨이">
                        <span>57</span>
                    </a>
                </li>
                <li>
                    <a href="/">
                        <img src="/assets/img/logo/bergerking.webp" alt="버거킹">
                        <span>33</span>
                    </a>
                </li>
                <li>
                    <a href="/">
                        <img src="/assets/img/logo/theventi.webp" alt="더벤티">
                        <span>1</span>
                    </a>
                </li>
            </ul>
        </section>
<?php
    include __DIR__ . '/../includes/footer.php'; 
?>