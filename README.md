Mình sẽ **phân tích nghiệp vụ trước**, sau đó đề xuất **các bảng dữ liệu (database tables)** cần có, kèm **giải thích rõ vì sao cần bảng đó** và **quan hệ giữa các bảng**. Cách này phù hợp để bạn làm app nội bộ hoặc mở rộng sau này (báo cáo, thống kê, mua sắm).

---

## I. Phân tích yêu cầu nghiệp vụ (Business Analysis)

### Vấn đề hiện tại

- Có **thao tác thay bình ắc quy cho xe nâng**
- Có **ghi nhận số giờ công tơ mét trên xe** khi:
    - Lắp bình vào
    - Lấy bình ra

- Nhưng:
    - ❌ Không liên kết được **bình nào – xe nào – chạy bao lâu**
    - ❌ Không tính được **tổng số giờ hoạt động của từng bình**
    - ❌ Dữ liệu đang rời rạc (chat, giấy, Excel)

---

### Mục tiêu của App

1. Ghi nhận **sự kiện thay bình**
2. Tính chính xác:

    ```
    Số giờ hoạt động = Giờ thay ra – Giờ lắp vào
    (Cùng xe, cùng bình)
    ```

3. Theo dõi:
    - Tuổi thọ bình
    - Tần suất thay
    - Chất lượng bình theo thời gian

4. Hỗ trợ phòng:
    - Kỹ thuật
    - Mua sắm (quyết định thay bình mới)

---

## II. Các thực thể chính (Core Entities)

Từ nghiệp vụ, ta có **5 thực thể bắt buộc**:

1. Xe nâng
2. Bình ắc quy
3. Sự kiện lắp bình
4. Sự kiện tháo bình
5. Người thao tác (nhân sự)

➡️ Tuy nhiên, để **tránh lỗi và dễ tính toán**, ta **gộp lắp + tháo vào 1 bảng lịch sử sử dụng bình**.

---

## III. Các bảng dữ liệu đề xuất

---

## 1️⃣ Bảng `forklifts` – Xe nâng

Lưu thông tin **xe nâng**

| Tên cột    | Kiểu     | Ý nghĩa                                     |
| ---------- | -------- | ------------------------------------------- |
| id         | bigint   | Khóa chính                                  |
| code       | string   | Mã xe (Komatsu 01, Komatsu 19, Toyota 3.5t) |
| brand      | string   | KOMATSU / TOYOTA                            |
| model      | string   | (tuỳ chọn)                                  |
| status     | enum     | active / inactive                           |
| created_at | datetime |                                             |
| updated_at | datetime |                                             |

📌 **Lý do cần bảng này**

- 1 xe thay **rất nhiều bình**
- Dễ lọc báo cáo theo xe

---

## 2️⃣ Bảng `batteries` – Bình ắc quy

Lưu thông tin **mỗi bình**

| Tên cột             | Kiểu          | Ý nghĩa                             |
| ------------------- | ------------- | ----------------------------------- |
| id                  | bigint        | Khóa chính                          |
| battery_code        | string        | Số bình (19, 11, VTI 445, VTI 470…) |
| type                | string        | Axit / Lithium (nếu có)             |
| capacity            | string        | (tuỳ chọn)                          |
| status              | enum          | in_use / standby / broken           |
| total_working_hours | decimal(10,2) | Tổng giờ đã chạy (cộng dồn)         |
| created_at          | datetime      |                                     |
| updated_at          | datetime      |                                     |

📌 **Quan trọng**

- `total_working_hours` giúp:
    - Biết bình nào sắp “hết đời”
    - So sánh chất lượng các bình

---

## 3️⃣ Bảng `battery_usages` – Lịch sử sử dụng bình (QUAN TRỌNG NHẤT)

👉 Đây là **bảng trung tâm của toàn hệ thống**

| Tên cột         | Kiểu          | Ý nghĩa                           |
| --------------- | ------------- | --------------------------------- |
| id              | bigint        | Khóa chính                        |
| forklift_id     | bigint        | FK → forklifts                    |
| battery_id      | bigint        | FK → batteries                    |
| charger_bar     | int           | Số vạch hiển thị máy nạp          |
| battery_voltage | decimal(6,2)  | Vạch pin hiển thị (VD: 25.2 / 10) |
| hour_in         | decimal(10,2) | Số giờ lắp vào                    |
| hour_out        | decimal(10,2) | Số giờ thay ra                    |
| working_hours   | decimal(10,2) | = hour_out - hour_in              |
| installed_at    | datetime      | Thời điểm lắp                     |
| removed_at      | datetime      | Thời điểm tháo                    |
| installed_by    | bigint        | Người lắp                         |
| removed_by      | bigint        | Người tháo                        |
| status          | enum          | running / finished                |
| created_at      | datetime      |                                   |
| updated_at      | datetime      |                                   |

📌 **Cách hoạt động**

- Khi **lắp bình** → tạo record:
    - Có `hour_in`
    - `hour_out = NULL`
    - `status = running`

- Khi **tháo bình**:
    - Update `hour_out`
    - Tự động tính `working_hours`
    - Update `battery.total_working_hours += working_hours`

📌 **Đây chính là chỗ giải quyết triệt để vấn đề của sếp**

---

## 4️⃣ Bảng `users` – Nhân sự thao tác

Ai ghi nhận dữ liệu

| Tên cột    | Kiểu                               |
| ---------- | ---------------------------------- |
| id         | bigint                             |
| name       | string                             |
| department | string                             |
| role       | enum (admin / technician / viewer) |
| created_at | datetime                           |

📌 Dùng để:

- Truy vết dữ liệu
- Tránh ghi sai, ghi thiếu

---

## 5️⃣ (Tuỳ chọn) Bảng `battery_chargers`

Nếu sau này muốn quản lý **máy nạp**

| Tên cột    | Kiểu     |
| ---------- | -------- |
| id         | bigint   |
| code       | string   |
| location   | string   |
| created_at | datetime |

---

## IV. Quan hệ giữa các bảng

```
forklifts 1 --- n battery_usages n --- 1 batteries
users 1 --- n battery_usages
```

---

## V. Áp vào ví dụ thực tế của bạn

### Ví dụ:

> Komatsu 01 – Bình 19
> Giờ vào: 31923.3
> Giờ ra: 31925.6

Trong `battery_usages`:

| forklift_id | battery_id | hour_in | hour_out | working_hours |
| ----------- | ---------- | ------- | -------- | ------------- |
| Komatsu 01  | 19         | 31923.3 | 31925.6  | **2.3**       |

➡️ `battery.total_working_hours += 2.3`

---

## VI. Báo cáo có thể làm ngay

Từ các bảng trên, app có thể:

- Tổng giờ chạy **theo bình**
- Bình nào chạy nhiều nhất
- Xe nào thay bình nhiều nhất
- Bình nào sắp vượt ngưỡng (VD: 1500h → đề xuất mua mới)

---

## VII. Gợi ý tiếp theo (nếu bạn muốn)

Mình có thể:

- Vẽ **ERD sơ đồ database**
- Đề xuất **API / Laravel migration**
- Gợi ý **UI form nhập liệu cho công nhân**
- Viết luôn **logic tính giờ trong Laravel**

👉 Bạn đang làm app này bằng **Laravel + MySQL** đúng không?
