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
    <form action="?act=midas-japan-order-update" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id'] ?? '') ?>">
        <div class="form-group mt-3">
            <label for="order_id">Mã đơn hàng (Order ID)</label>
            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Mã đơn hàng từ MidasBuy (số, tùy chọn)" value="<?php echo htmlspecialchars($order['order_id'] ?? '') ?>">
        </div>
        <div class="form-group mt-3">
            <label for="uid">UID <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="uid" name="uid" placeholder="Nhập UID (số)" value="<?php echo htmlspecialchars($order['uid'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="card">Card <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="card" name="card" placeholder="Nhập card (tối đa 30 ký tự)" maxlength="30" value="<?php echo htmlspecialchars($order['card'] ?? '') ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="sales_agent_id">Sales Agent ID <span class="text-muted">(Tùy chọn)</span></label>
            <input type="number" class="form-control" id="sales_agent_id" name="sales_agent_id" placeholder="Để trống nếu không có" value="<?php echo isset($order['sales_agent_id']) && $order['sales_agent_id'] !== '' && $order['sales_agent_id'] !== null ? (int)$order['sales_agent_id'] : '' ?>" min="1" step="1" style="max-width: 200px;">
        </div>
        <div class="form-group mt-3">
            <label for="image">Image</label>
            <?php if (!empty($order['image'])): ?>
                <div class="mb-2">
                    <small class="text-muted">Ảnh hiện tại:</small><br>
                    <a href="<?php echo htmlspecialchars($order['image']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> Xem ảnh hiện tại
                    </a>
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small class="form-text text-muted">Chọn file ảnh mới để thay thế (để trống nếu giữ nguyên ảnh hiện tại)</small>
        </div>
        <div class="form-group mt-3">
            <label for="status">Trạng thái</label>
            <select class="form-control" id="status" name="status">
                <option value="pending" <?php echo (isset($order['status']) && $order['status'] == 'pending') ? 'selected' : '' ?>>Đang chờ</option>
                <option value="success" <?php echo (isset($order['status']) && $order['status'] == 'success') ? 'selected' : '' ?>>Thành công</option>
                <option value="cancelled" <?php echo (isset($order['status']) && $order['status'] == 'cancelled') ? 'selected' : '' ?>>Đã huỷ</option>
            </select>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Cập nhật đơn hàng</button>
            <a href="?act=midas-japan-orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">