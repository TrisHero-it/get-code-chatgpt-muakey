<div class="container mt-5">
    <h3>
        Sửa đơn hàng
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="?act=order-update" method="post" id="orderForm">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id'] ?? '') ?>">
        <div class="form-group mt-3">
            <label for="order_id">Mã đơn hàng <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Nhập mã đơn hàng" value="<?php echo htmlspecialchars($order['order_id'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="username">Username <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập username" value="<?php echo htmlspecialchars($order['username'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="password">Password <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="password" name="password" placeholder="Nhập password" value="<?php echo htmlspecialchars($order['password'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="backup_steam">Mã backup steam <span class="text-muted">(Tùy chọn)</span></label>
            <input type="text"
                class="form-control"
                id="backup_steam"
                name="backup_code"
                placeholder="Nhập mã backup steam (7 ký tự)"
                value="<?php echo htmlspecialchars($order['backup_code'] ?? '') ?>"
                maxlength="7">
            <small class="form-text text-muted">Nếu nhập mã backup steam thì phải có đúng 7 ký tự (khoảng trắng sẽ tự động bị loại bỏ)</small>
            <div class="invalid-feedback">
                Mã backup steam phải có đúng 7 ký tự hoặc để trống.
            </div>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3" id="updateOrderButton">Cập nhật đơn hàng</button>
            <a href="?act=orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backupInput = document.getElementById('backup_steam');

        // Xử lý khi paste - tự động bỏ khoảng trắng
        backupInput.addEventListener('paste', function(e) {
            e.preventDefault();
            let pastedText = (e.clipboardData || window.clipboardData).getData('text');
            // Bỏ tất cả khoảng trắng
            pastedText = pastedText.replace(/\s/g, '');
            // Chỉ lấy 7 ký tự đầu tiên
            pastedText = pastedText.substring(0, 7);
            this.value = pastedText;

            // Trigger input event để validate
            this.dispatchEvent(new Event('input'));
        });

        // Xử lý khi nhập - tự động bỏ khoảng trắng
        backupInput.addEventListener('input', function(e) {
            // Bỏ tất cả khoảng trắng
            let value = this.value.replace(/\s/g, '');
            // Chỉ cho phép 7 ký tự
            value = value.substring(0, 7);
            this.value = value;

            // Validate và hiển thị feedback
            if (this.value.length === 7) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value.length > 0) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });

        // Xử lý khi keypress - ngăn nhập khoảng trắng
        backupInput.addEventListener('keypress', function(e) {
            // Ngăn nhập khoảng trắng
            if (e.key === ' ') {
                e.preventDefault();
            }
        });

        // Validate khi submit form
        const form = backupInput.closest('form');
        form.addEventListener('submit', function(e) {
            // Loại bỏ khoảng trắng một lần nữa trước khi validate
            backupInput.value = backupInput.value.replace(/\s/g, '');

            // Chỉ validate nếu có giá trị (không rỗng)
            if (backupInput.value.length > 0 && backupInput.value.length !== 7) {
                e.preventDefault();
                backupInput.classList.add('is-invalid');
                backupInput.focus();
                alert('Mã backup steam phải có đúng 7 ký tự hoặc để trống!');
                return false;
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