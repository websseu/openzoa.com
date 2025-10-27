# openzoa.com



## 폴더 구조
```
/var/www/html
 ├─ index.php                → 메인 진입
 ├─ /pages
 │    ├─ main.php            → 메인 페이지 (브랜드 목록)
 │    ├─ brand.php           → 브랜드별 매장 리스트
 │    └─ store.php           → 매장 상세 페이지
 ├─ /includes
 │    ├─ head.php
 │    ├─ header.php
 │    └─ footer.php
 ├─ /assets
 │    ├─ css/style.css
 │    └─ img/logo/*.webp
 └─ config.php


## 테이블
CREATE TABLE stores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,           → 내부 PK
    brand_id INT UNSIGNED NOT NULL,                       → 브랜드 FK
    region_id INT UNSIGNED NOT NULL,                      → 지역 FK
    name VARCHAR(255) NOT NULL,                           → 매장명
    public_id CHAR(10) NOT NULL UNIQUE,                   → 랜덤 10자리 번호
    road_address VARCHAR(255) NOT NULL,                   → 도로명 주소
    lot_address VARCHAR(255) DEFAULT NULL,                → 지번 주소
    customer_center VARCHAR(50) DEFAULT NULL,             → 고객센터 번호
    shopping_time VARCHAR(100) DEFAULT NULL,              → 운영시간
    closed_days VARCHAR(100) DEFAULT NULL,                → 휴무일
    parking_info VARCHAR(100) DEFAULT NULL,               → 주차 정보
    lat DECIMAL(10,7) NOT NULL,                           → 위도
    lng DECIMAL(10,7) NOT NULL,                           → 경도
    directions TEXT DEFAULT NULL,                         → 오시는 길
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,       → 생성일
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, → 수정일
    INDEX idx_brand (brand_id),
    INDEX idx_region (region_id),
    INDEX idx_public_id (public_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


## 데이터 입력
INSERT INTO brands (name, slug, logo) VALUES
('이마트', 'emart', '/assets/img/logo/emart.webp'),
('트레이더스', 'traders', '/assets/img/logo/traders.webp'),
('에브리데이', 'everyday', '/assets/img/logo/everyday.webp'),
('노브랜드', 'nobrand', '/assets/img/logo/nobrand.webp'),
('스타필드 마켓', 'starfieldmarket', '/assets/img/logo/starfieldmarket.webp');

INSERT INTO regions (name, slug) VALUES
('서울', 'seoul'),
('부산', 'busan'),
('대구', 'daegu'),
('인천', 'incheon'),
('광주', 'gwangju'),
('대전', 'daejeon'),
('울산', 'ulsan'),
('세종', 'sejong'),
('경기', 'gyeonggi'),
('강원', 'gangwon'),
('충북', 'chungbuk'),
('충남', 'chungnam'),
('전북', 'jeolbuk'),
('전남', 'jeolnam'),
('경북', 'gyeongbuk'),
('경남', 'gyeongnam'),
('제주', 'jeju');

```
https://websseu.github.io/data_emart/emart/2025-10-15.json

https://websseu.github.io/data_starbucks/details/2025/busan/busan_2025-10-16.json

https://websseu.github.io/data_starbucks/location/2025/busan/busan_2025-10-16.json