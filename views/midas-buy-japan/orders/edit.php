<div class="container mt-5">
    <h3>
        Sửa đơn hàng MidasBuy Japan
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="?act=midas-japan-order-update" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id'] ?? '') ?>">
        <div class="form-group mt-3">
            <label for="uid">UID <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="uid" name="uid" placeholder="Nhập UID (số)" value="<?php echo htmlspecialchars($order['uid'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="card">Card <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="card" name="card" placeholder="Nhập card (tối đa 30 ký tự)" maxlength="30" value="<?php echo htmlspecialchars($order['card'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="image">Image (URL hoặc text)</label>
            <input type="text" class="form-control" id="image" name="image" placeholder="Nhập URL ảnh hoặc để trống" value="<?php echo htmlspecialchars($order['image'] ?? '') ?>">
        </div>
        <div class="form-group mt-3">
            <label for="status">Trạng thái</label>
            <select class="form-control" id="status" name="status">
                <option value="pending" <?php echo (isset($order['status']) && $order['status'] == 'pending') ? 'selected' : '' ?>>Đang chờ</option>
                <option value="success" <?php echo (isset($order['status']) && $order['status'] == 'success') ? 'selected' : '' ?>>Thành công</option>
            </select>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Cập nhật đơn hàng</button>
            <a href="?act=midas-japan-orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
