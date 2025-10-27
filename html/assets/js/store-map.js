document.addEventListener('DOMContentLoaded', function () {
    // 좌표 및 상점 정보는 data-* 속성에서 가져오기
    const mapContainer = document.getElementById('map');
    if (!mapContainer) return;

    const lat = parseFloat(mapContainer.dataset.lat);
    const lng = parseFloat(mapContainer.dataset.lng);
    const name = mapContainer.dataset.name;
    const time = mapContainer.dataset.time;
    const phone = mapContainer.dataset.phone;

    // 지도 옵션
    const mapOptions = {
        center: new naver.maps.LatLng(lat, lng),
        zoom: 15
    };

    // 지도 생성
    const map = new naver.maps.Map(mapContainer, mapOptions);

    // 마커 생성
    const marker = new naver.maps.Marker({
        position: new naver.maps.LatLng(lat, lng),
        map: map,
        title: name
    });

    // 인포윈도우
    const infowindow = new naver.maps.InfoWindow({
        content: `
            <div style="padding:8px;">
                <p><strong style="display:block;margin-bottom:1px;">${name}</strong></p>
                ${phone ? `<p style="min-width:150px;font-size:14px;">📱 ${phone}</p>` : ''}
            </div>`
    });

    // 페이지 로드시 바로 열기
    infowindow.open(map, marker);

    // 마커 클릭 시 토글
    naver.maps.Event.addListener(marker, 'click', function () {
        if (infowindow.getMap()) {
            infowindow.close();
        } else {
            infowindow.open(map, marker);
        }
    });
});
