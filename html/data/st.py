import requests
import json
import os
from datetime import datetime

# ----------------------------
# 1. 설정
# ----------------------------
BASE_URL_DETAILS = "https://websseu.github.io/data_starbucks/details/2025/"
BASE_URL_LOCATION = "https://websseu.github.io/data_starbucks/location/2025/"
OUTPUT_DIR = "./merged"  # 저장 폴더
DATE = "2025-10-16"

REGIONS = [
    "busan", "chungbuk", "chungnam", "daegu", "daejeon",
    "gangwon", "gwangju", "gyeongbuk", "gyeonggi", "gyeongnam",
    "incheon", "jeju", "jeolbuk", "jeolnam", "sejong", "seoul", "ulsan"
]

# ----------------------------
# 2. 출력 폴더 생성
# ----------------------------
os.makedirs(OUTPUT_DIR, exist_ok=True)

# ----------------------------
# 3. 각 지역 순차 처리
# ----------------------------
for region in REGIONS:
    try:
        print(f"🔹 {region} 병합 중...")

        # URL 조합
        url_details = f"{BASE_URL_DETAILS}{region}/{region}_{DATE}.json"
        url_location = f"{BASE_URL_LOCATION}{region}/{region}_{DATE}.json"

        # 데이터 로드
        details = requests.get(url_details).json()
        locations = requests.get(url_location).json()

        # location → 빠른 검색용 dict
        location_map = {
            loc["name"]: {
                "latitude": loc["latitude"],
                "longitude": loc["longitude"]
            }
            for loc in locations["item"]
        }

        # details 데이터 병합
        merged_items = []
        for item in details["item"]:
            name = item.get("name")
            if name in location_map:
                item["latitude"] = location_map[name]["latitude"]
                item["longitude"] = location_map[name]["longitude"]
            merged_items.append(item)

        # 결과 JSON
        merged_data = {
            "kind": details.get("kind", "Korea Starbucks"),
            "date": DATE,
            "location": details.get("location", region),
            "count": len(merged_items),
            "item": merged_items
        }

        # 저장
        output_path = os.path.join(OUTPUT_DIR, f"{region}_{DATE}_merged.json")
        with open(output_path, "w", encoding="utf-8") as f:
            json.dump(merged_data, f, ensure_ascii=False, indent=2)

        print(f"✅ {region} 완료 ({len(merged_items)}개 매장) → {output_path}")

    except Exception as e:
        print(f"❌ {region} 오류 발생: {e}")

# ----------------------------
# 4. 완료 로그
# ----------------------------
print("\n🎉 모든 지역 병합 완료:", datetime.now().strftime("%Y-%m-%d %H:%M:%S"))
