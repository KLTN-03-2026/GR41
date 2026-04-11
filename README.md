# Xây dựng hệ thống bán sách trực tuyến bằng Laravel + Vue.js và Mysql



## Tính năng chính

- Đăng ký và đăng nhập tài khoản người dùng
- Tìm kiếm sản phẩm 
- Xem chi tiết sản phẩm, bao gồm thông tin sản phẩm, giá cả và đánh giá
- Thêm sản phẩm vào giỏ hàng
- Thanh toán sản phẩm
- Quản lý tài khoản
- Quản lý sách 
- Quản lý nhà xuất bản
- Quản lý đánh giá
- Quản lý đơn hàng 
- Quản lý danh mục
- Quản lý nhân viên
- Cấp quyền cho nhân viên
- Quản lí khách hàng

## Công nghệ sử dụng

- Backend: Laravel 
- Frontend: Vue.js
- Cơ sở dữ liệu: MySQL





```bash
      cd backend
```
```bash nếu có file vendor rồi thì bỏ qua
      composer i
```
```bash tạo table trên mysql chỉ chạy 1 lần đầu
      php artisan migrate 
```
```bash tạo dữ liệu mẫu trên mysql chỉ chạy 1 lần đầu
      php artisan db:seed
```
```bash khởi động backend
      
```



```bash
      cd frontend
```
```bash nếu có file node-modules thì bỏ qua
      npm install
```
```bash khởi động frontend mở bằng cmd
      npm run dev
```

## Thông tin sinh viên


- Một ngày tối đa 50 lần chat
php artisan serve