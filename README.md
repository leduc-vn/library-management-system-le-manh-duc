# 📚 Library Management System

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat&logo=vite&logoColor=white)
![Git](https://img.shields.io/badge/Git-F05032?style=flat&logo=git&logoColor=white)

Hệ thống quản lý thư viện được xây dựng bằng **Laravel** (PHP) + **MySQL**, hỗ trợ quản lý sách, tác giả, nhà xuất bản, người dùng và mượn/trả sách.

---

## 🚀 Tính năng

### 🔐 Admin
- 📊 Dashboard thống kê tổng quan
- 📖 Quản lý sách (thêm, sửa, xóa, xem chi tiết)
- 🗂️ Quản lý danh mục sách
- 🏢 Quản lý nhà xuất bản
- ✍️ Quản lý tác giả
- 👤 Quản lý người dùng
- 🔄 Quản lý mượn/trả sách

### 👤 Reader (Độc giả)
- 📋 Xem danh sách sách đã mượn
- 🔍 Lọc sách theo trạng thái: Tất cả / Chờ đến lấy / Đang mượn / Đã trả / Quá hạn
- ⏰ Xem hạn trả sách (cảnh báo đỏ khi quá hạn)
- 📅 Xem ngày mượn và thời gian yêu cầu

---

## 🛠️ Công nghệ sử dụng

| Thành phần | Công nghệ |
|------------|-----------|
| Backend | Laravel (PHP) |
| Frontend | Blade Template, CSS, JavaScript |
| Database | MySQL |
| Build Tool | Vite / Node.js |
| Version Control | Git |

---

## ⚙️ Yêu cầu hệ thống

- PHP >= 8.0
- Composer
- Node.js >= 16 & npm
- MySQL >= 5.7
- Git

---

## 📦 Cài đặt

### 1. Clone repository

```bash
git clone https://github.com/leduc-vn/Library-management-system-le-manh-duc.git
cd Library-management-system-le-manh-duc
```

### 2. Cài đặt dependencies PHP

```bash
composer install
```

### 3. Cài đặt dependencies Node.js

```bash
npm install
```

### 4. Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Mở file `.env` và chỉnh sửa thông tin database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qltv
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Tạo database & migrate

```bash
php artisan migrate
php artisan db:seed
```

### 6. Chạy ứng dụng

Mở **2 terminal** và chạy:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

Truy cập: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 📁 Cấu trúc thư mục

```
qltv/
├── app/
│   ├── Http/Controllers/   # Xử lý logic
│   └── Models/             # Model database
├── database/
│   ├── migrations/         # Cấu trúc bảng
│   └── seeders/            # Dữ liệu mẫu
├── resources/
│   └── views/              # Giao diện Blade
├── routes/
│   └── web.php             # Định nghĩa routes
├── public/                 # Assets public
└── .env                    # Cấu hình môi trường
```

---

## 📸 Giao diện Demo

### 🔐 Admin — Quản lý sách
> Hiển thị danh sách sách kèm thông tin tổng số, số lượng còn lại và trạng thái mượn.

![Admin - Manage Books](https://github.com/user-attachments/assets/0d06d197-16b9-420c-b82c-ccfe3ad472c8)

### 👤 Reader — Sách đã mượn
> Độc giả xem danh sách sách đã mượn, lọc theo trạng thái (Chờ đến lấy / Đang mượn / Đã trả / Quá hạn) và theo dõi hạn trả.

![Reader - Borrowed Books](https://github.com/user-attachments/assets/5577db06-2e19-4420-9d16-9c1f794409f7)

---

## 👨‍💻 Tác giả

**Lê Mạnh Đức**  
GitHub: [@leduc-vn](https://github.com/leduc-vn)

---

## 📄 License

Dự án này được phát triển cho mục đích học tập.
