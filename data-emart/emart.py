from datetime import datetime
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import os, json, time, random

# ========================
# 0. 유틸 함수
# ========================
USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:123.0) Gecko/20100101 Firefox/123.0",
    "Mozilla/5.0 (X11; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Edge/121.0.0.0 Safari/537.36",
]
random_agent = random.choice(USER_AGENTS)


def safe_text(selector):
    try:
        return browser.find_element(By.CSS_SELECTOR, selector).text.strip()
    except:
        return ""


def get_coordinates():
    try:
        map_div = browser.find_element(By.CSS_SELECTOR, "#map")
        return {
            "lat": map_div.get_attribute("data-x"),
            "lng": map_div.get_attribute("data-y")
        }
    except:
        return {"lat": None, "lng": None}


# ========================
# 1. 날짜 기반 파일 설정
# ========================
current_date = datetime.now().strftime("%Y-%m-%d")
current_year = datetime.now().strftime("%Y")

# data-emart 폴더 기준으로 저장
base_folder = os.path.dirname(os.path.abspath(__file__))
year_folder = os.path.join(base_folder, current_year)
os.makedirs(year_folder, exist_ok=True)

json_filename = f"{current_date}.json"
json_path = os.path.join(year_folder, json_filename)


# ========================
# 2. 웹드라이버 설정
# ========================
options = Options()
options.add_argument(f"user-agent={random_agent}")
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-dev-shm-usage")
options.add_argument("--disable-gpu")
options.add_argument("--window-size=1440,2000")
options.add_argument("--lang=ko-KR")

browser = webdriver.Chrome(options=options)
wait = WebDriverWait(browser, 15)


# ========================
# 3. 페이지 열기
# ========================
url = "https://store.emart.com/branch/list.do"
browser.get(url)
time.sleep(3)


# ========================
# 4. 점포 목록 가져오기
# ========================
store_links = wait.until(
    EC.presence_of_all_elements_located((By.CSS_SELECTOR, "#branchList li a"))
)


# ========================
# 5. 점포 클릭 및 스크롤 반복
# ========================
results = []

for i in range(len(store_links)):   
    # ▶ 점포 클릭
    store_links[i].click()
    print(f"\n▶ {i+1}번째 점포 클릭 →", store_links[i].text.strip())
    time.sleep(random.uniform(1.5, 2.5))

    # ▶ 데이터 수집
    store_name = safe_text(".store-header h2")
    shopping_time = safe_text(".intro-wrap li:nth-of-type(1) p")
    closed_days = safe_text(".intro-wrap li.closed-day p")
    customer_center = safe_text(".intro-wrap li:nth-of-type(3) p")
    parking_info = safe_text(".intro-wrap li:nth-of-type(4) p")
    coords = get_coordinates()
    road_address = safe_text(".paper-address-paired dd.data:nth-of-type(1)")
    lot_address = safe_text(".paper-address-paired dd.data:nth-of-type(2)")
    directions = safe_text(".map-info li:nth-of-type(3) p")

    # ▶ 데이터 저장
    results.append({
        "store_name": store_name,
        "shopping_time": shopping_time,
        "closed_days": closed_days,
        "customer_center": customer_center,
        "parking_info": parking_info,
        "coordinates": coords,
        "road_address": road_address,
        "lot_address": lot_address,
        "directions": directions
    })
    print(f"  → {store_name} 정보 저장 완료")

    # ▶ 최상단으로 이동
    top_element = browser.find_element(By.ID, "conTop")
    browser.execute_script("arguments[0].scrollIntoView(true);", top_element)
    time.sleep(random.uniform(1.5, 2.5))

    # ▶ 스크롤 내리기
    target = browser.find_element(By.CSS_SELECTOR, ".result-branch.ern-result-branch")
    browser.execute_script("arguments[0].scrollBy(0,24);", target)
    time.sleep(random.uniform(1.5, 2.5))


# ========================
# 6. 종류별 카운트
# ========================
categories = [
    "스타필드마켓",
    "이마트",
    "트레이더스 홀세일 클럽",
    "에브리데이",
    "노브랜드",
    "몰리스",
    "일렉트로마트 스타필드"
]

summary = {cat: 0 for cat in categories}

for store in results:
    name = store.get("store_name", "")
    for cat in categories:
        if cat in name:
            summary[cat] += 1
            break  # 첫 번째 일치한 카테고리에서 중단


# ========================
# 7. JSON 저장
# ========================
final_output = {
    "date": current_date,
    "summary": summary,
    "stores": results
}

with open(json_path, "w", encoding="utf-8") as f:
    json.dump(final_output, f, ensure_ascii=False, indent=2)

print(f"\n✅ 전체 {len(results)}개 점포 데이터 저장 완료 → {json_path}")

browser.quit()
