<div class="container mt-5">
    <h3>
        Thêm đơn hàng
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="?act=order-store" method="post" id="orderForm">
        <div class="form-group mt-3">
            <label for="order_data">Dán thông tin đơn hàng <span class="text-muted">(Tùy chọn)</span></label>
            <textarea class="form-control" id="order_data" name="order_data" rows="6"
                placeholder="Dán thông tin đơn hàng vào đây (ví dụ:&#10;Mã đơn hàng: 798163&#10;Tên sản phẩm: Chuyển vùng Steam nạp USD x 1&#10;Mã backup Steam: 3JQYY66 XRXFPD7 7RTCTH7&#10;Tài khoản Steam cần chuyển: ThoaiThanh2112&#10;Mật Khẩu Steam cần chuyển: thanhtang99+&#10;Ngày mua: 15:08:04 18/12/2025)"></textarea>
            <small class="form-text text-muted">Hệ thống sẽ tự động nhận diện Mã đơn hàng, Username và Password từ thông tin bạn dán.</small>
        </div>

        <div class="form-group mt-3">
            <label for="order_id">Mã đơn hàng <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Nhập mã đơn hàng hoặc dán thông tin ở trên" required>
        </div>
        <div class="form-group mt-3">
            <label for="customer_name">Username <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập username hoặc dán thông tin ở trên" required>
        </div>
        <div class="form-group mt-3">
            <label for="customer_email">Password <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="password" name="password" placeholder="Nhập password hoặc dán thông tin ở trên" required>
        </div>
        <div class="form-group mt-3">
            <label for="backup_steam">Mã backup steam <span class="text-muted">(Tùy chọn - từ 1 đến 3 mã, mỗi mã 7 ký tự)</span></label>
            <input type="text"
                class="form-control"
                id="backup_steam"
                name="backup_code"
                placeholder="Nhập mã backup steam (ví dụ: 3JQYY66 hoặc 3JQYY66 XRXFPD7 7RTCTH7)"
                maxlength="23">
            <small class="form-text text-muted">Có thể nhập từ 1 đến 3 mã backup steam, mỗi mã 7 ký tự, cách nhau bởi khoảng trắng (khoảng trắng sẽ tự động bị loại bỏ)</small>
            <div class="invalid-feedback">
                Mã backup steam phải có từ 1 đến 3 mã, mỗi mã đúng 7 ký tự.
            </div>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Thêm đơn hàng</button>
            <a href="?act=orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderDataTextarea = document.getElementById('order_data');
        const orderIdInput = document.getElementById('order_id');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const backupInput = document.getElementById('backup_steam');

        // Hàm parse thông tin đơn hàng từ textarea
        function parseOrderData(text) {
            const result = {
                order_id: '',
                username: '',
                password: ''
            };

            // Parse Mã đơn hàng: "Mã đơn hàng: 798163"
            const orderIdMatch = text.match(/Mã đơn hàng:\s*(\d+)/i);
            if (orderIdMatch) {
                result.order_id = orderIdMatch[1].trim();
            }

            // Parse Username: "Tài khoản Steam cần chuyển: ThoaiThanh2112"
            const usernameMatch = text.match(/Tài khoản Steam:\s*(.+?)(?:\n|$)/i);
            if (usernameMatch) {
                result.username = usernameMatch[1].trim();
            }

            // Parse Password: "Mật Khẩu Steam cần chuyển: thanhtang99+"
            const passwordMatch = text.match(/Mật Khẩu Steam:\s*(.+?)(?:\n|$)/i);
            if (passwordMatch) {
                result.password = passwordMatch[1].trim();
            }

            return result;
        }

        // Xử lý khi thay đổi textarea
        orderDataTextarea.addEventListener('input', function() {
            const text = this.value;
            if (text.trim()) {
                const parsed = parseOrderData(text);

                // Điền vào các input
                if (parsed.order_id) {
                    orderIdInput.value = parsed.order_id;
                }
                if (parsed.username) {
                    usernameInput.value = parsed.username;
                }
                if (parsed.password) {
                    passwordInput.value = parsed.password;
                }
            }
        });

        // Xử lý trim khoảng trắng đầu cuối cho username và password
        function trimInput(input) {
            input.value = input.value.trim();
        }

        // Xử lý khi paste vào username
        usernameInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                trimInput(this);
            }, 0);
        });

        // Xử lý khi paste vào password
        passwordInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                trimInput(this);
            }, 0);
        });

        // Xử lý khi rời khỏi trường (blur) - trim khoảng trắng
        usernameInput.addEventListener('blur', function() {
            trimInput(this);
        });

        passwordInput.addEventListener('blur', function() {
            trimInput(this);
        });

        // Xử lý khi nhập backup code - cho phép từ 1-3 mã, mỗi mã 7 ký tự
        backupInput.addEventListener('input', function(e) {
            // Bỏ tất cả khoảng trắng
            let value = this.value.replace(/\s/g, '');

            // Chia thành các mã 7 ký tự
            const codes = [];
            for (let i = 0; i < value.length; i += 7) {
                const code = value.substring(i, i + 7);
                if (code.length === 7) {
                    codes.push(code);
                }
            }

            // Chỉ cho phép tối đa 3 mã
            if (codes.length > 3) {
                codes.splice(3);
            }

            // Ghép lại với khoảng trắng
            this.value = codes.join(' ');

            // Validate: phải có từ 1-3 mã, mỗi mã đúng 7 ký tự
            const isValid = codes.length >= 1 && codes.length <= 3 && codes.every(code => code.length === 7);

            if (isValid && codes.length > 0) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value.length > 0) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });

        // Validate khi submit form
        const form = backupInput.closest('form');
        form.addEventListener('submit', function(e) {
            // Validate backup code
            const backupValue = backupInput.value.replace(/\s/g, '');
            if (backupValue.length > 0) {
                // Kiểm tra có phải là bội số của 7 và từ 7 đến 21 ký tự (1-3 mã)
                if (backupValue.length % 7 !== 0 || backupValue.length < 7 || backupValue.length > 21) {
                    e.preventDefault();
                    backupInput.classList.add('is-invalid');
                    backupInput.focus();
                    alert('Mã backup steam phải có từ 1 đến 3 mã, mỗi mã đúng 7 ký tự!');
                    return false;
                }
            }
        });
    });
</script>

<style>
    .form-control.is-valid {
        border-color: #28a745;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-valid:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }

    .form-control.is-invalid~.invalid-feedback {
        display: block;
    }

    .form-text {
        margin-top: 0.25rem;
        font-size: 0.875rem;
    }

    .text-danger {
        color: #dc3545 !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">