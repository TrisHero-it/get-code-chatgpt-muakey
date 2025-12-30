<div class="container mt-5">
    <h3>
        Sửa đơn hàng MidasBuy
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="?act=midas-order-update" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id'] ?? '') ?>">
        <div class="form-group mt-3">
            <label for="order_id">Mã đơn hàng <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Nhập mã đơn hàng" value="<?php echo htmlspecialchars($order['order_id'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="uid">UID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="uid" name="uid" placeholder="Nhập UID" value="<?php echo htmlspecialchars($order['uid'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="token">Token <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="token" name="token" placeholder="Nhập token (số)" value="<?php echo htmlspecialchars($order['token'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="status">Trạng thái <span class="text-danger">*</span></label>
            <select class="form-control" id="status" name="status">
                <option value="pending" <?php echo (isset($order['status']) && $order['status'] == 'pending') ? 'selected' : '' ?>>Đang chờ</option>
                <option value="success" <?php echo (isset($order['status']) && $order['status'] == 'success') ? 'selected' : '' ?>>Thành công</option>
            </select>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Cập nhật đơn hàng</button>
            <a href="?act=midas-orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">