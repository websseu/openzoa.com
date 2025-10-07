<?php
require_once __DIR__ . '/config.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenZoa - 전국 매장 오픈시간 휴무일 주차 위치 정보</title>
    <meta name="description" content="OpenZoa는 전국 매장과 프랜차이즈의 오픈시간, 휴무일, 주차 가능 여부, 위치 정보를 한눈에 제공합니다. 쇼핑 전 매장 정보를 쉽고 빠르게 확인하세요.">
    <meta name="keywords" content="openzoa, 매장 오픈시간, 휴무일, 주차장, 위치정보, 마트 휴무일, 백화점 영업시간, 편의점 오픈시간, 쇼핑몰 주차정보">
    <meta name="author" content="webstoryboy">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="ko_KR">
    <meta property="og:site_name" content="OpenZoa">
    <meta property="og:title" content="OpenZoa - 전국 매장 오픈시간 휴무일 주차 위치 정보">
    <meta property="og:description" content="쇼핑 전에 꼭 확인하세요! 매장별 오픈시간, 휴무일, 주차, 위치 정보 제공.">
    <meta property="og:url" content="https://openzoa.com">
    <meta property="og:image" content="https://openzoa.com/assets/img/openzoa.jpg">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="OpenZoa - 매장 오픈시간 & 휴무일 안내">
    <meta name="twitter:description" content="전국 매장 오픈시간 휴무일 주차 위치 정보 한눈에 확인">
    <meta name="twitter:image" content="https://openzoa.com/assets/img/openzoa.jpg">

    <link rel="canonical" href="https://www.openzoa.com/">
    <link rel="shortcut icon" href="assets/img/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="assets/img/favicon-152.png">
    <link rel="icon" href="assets/img/favicon.png">
    <link rel="icon" href="assets/img/favicon-64.png" sizes="64x64">
    <link rel="icon" href="assets/img/favicon-128.png" sizes="128x128">
    <link rel="stylesheet" href="/assets/css/style.css">

    <script src="/assets/js/main.js"></script>
</head>
<body class="site-main site-container">
    <header class="site-header">
        <h1>
            <a href="/">
                open<em>zoa</em>
            </a>
        </h1>
        <p>모든 점포의 오픈시간을 알려드립니다.</p>
    </header>

    <main>
        <section class="store-search">
            <form action="/" method="get">
                <input
                    type="text"
                    name="q"
                    placeholder="예: 이마트 평촌점"
                    aria-label="점포 검색"
                    required
                >
                <button
                    type="submit"
                    aria-label="검색"
                >검색하기</button>
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
    </main>

    <footer>
        <p>© 2025 Openzoa. All rights reserved.</p>
    </footer>
</body>
</html>