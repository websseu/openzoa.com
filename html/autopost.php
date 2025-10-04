<?php
require_once( 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/includes/image.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/media.php' );

// JSON 파일 URL
$json_url = "https://websseu.github.io/data_emart/emart/2025-10-02.json";

// JSON 가져오기
$response = wp_remote_get($json_url);
if (is_wp_error($response)) {
    echo "❌ JSON 데이터를 불러올 수 없습니다.";
    return;
}

$data = json_decode( wp_remote_retrieve_body($response), true );

// ✅ 첫 번째 매장만 사용
$store = $data[0];    // JSON이 배열이므로 바로 첫 번째 요소

// ----------------------------
// 글 제목 = 매장명
// ----------------------------
$post_title = $store['store_name'];

// ----------------------------
// 글 본문
// ----------------------------
$content  = "<h2>{$store['store_name']}</h2>\n";
$content .= "<p><strong>영업시간:</strong> {$store['shopping_time']}</p>\n";
$content .= "<p><strong>휴점일:</strong> {$store['closed_days']}</p>\n";
$content .= "<p><strong>고객센터:</strong> {$store['customer_center']}</p>\n";
$content .= "<p><strong>주차 정보:</strong> {$store['parking_info']}</p>\n";
$content .= "<p><strong>도로명 주소:</strong> {$store['road_address']}</p>\n";
$content .= "<p><strong>지번 주소:</strong> {$store['lot_address']}</p>\n";
$content .= "<p><strong>위도:</strong> {$store['coordinates']['lat']} / ";
$content .= "<strong>경도:</strong> {$store['coordinates']['lng']}</p>\n";

$directions = nl2br($store['directions']); // 줄바꿈 유지
$content .= "<h3>찾아가는 길</h3>\n<p>{$directions}</p>\n";

// ----------------------------
// 글 등록
// ----------------------------
$new_post = [
    'post_title'   => $post_title,
    'post_content' => $content,
    'post_status'  => 'publish',
    'post_author'  => 1,
    'post_type'    => 'post',
    'post_name'    => sanitize_title($post_title),
];

$post_id = wp_insert_post($new_post);

if ($post_id) {
    // 카테고리 지정 (예: ID=13)
    wp_set_post_terms($post_id, [13], 'category');
    echo "✅ 글 작성 완료: {$post_title}";
} else {
    echo "❌ 글 작성 실패";
}
?>
