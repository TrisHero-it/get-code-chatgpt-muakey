<div class="container mt-5">
    <h3>
        Sửa thông tin tiền
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="?act=money-update" method="post" id="moneyForm">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($money['id'] ?? 1) ?>">
        <div class="form-group mt-3">
            <label for="balance">Số tiền còn lại (€) <span class="text-danger">*</span></label>
            <input type="text"
                class="form-control"
                id="balance"
                name="balance"
                placeholder="Nhập số tiền còn lại"
                value="<?php echo htmlspecialchars($money['balance'] ?? 0) ?>"
                step="0.01"
                required>
        </div>
        <div class="form-group mt-3">
            <label for="code">Code <span class="text-danger">*</span></label>
            <input type="text"
                class="form-control"
                id="code"
                name="code"
                placeholder="Nhập code"
                value="<?php echo htmlspecialchars($money['code'] ?? '') ?>"
                required>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
            <a href="?act=orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">