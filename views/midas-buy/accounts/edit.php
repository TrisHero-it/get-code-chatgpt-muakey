<div class="container mt-5">
    <h3>
        Sửa tài khoản MidasBuy
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="?act=midas-account-update" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($account['id'] ?? '') ?>">
        <div class="form-group mt-3">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" value="<?php echo htmlspecialchars($account['email'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="uid">UID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="uid" name="uid" placeholder="Nhập UID" value="<?php echo htmlspecialchars($account['uid'] ?? '') ?>" required>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
            <a href="?act=midas-accounts" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">