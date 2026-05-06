<div class="container mt-5">
    <h3>
        Thêm đơn hàng MidasBuy Token
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <hr class="my-4">
    <form action="?act=update-midas-token-order&id=<?php echo isset($_GET['id']) ? urlencode($_GET['id']) : ''; ?>" method="POST" id="form_order" enctype="multipart/form-data">
        <div class="form-group mt-3">
            <label for="order_id">Mã đơn hàng (Order ID)</label>
            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Mã đơn hàng từ MidasBuy (số, tùy chọn)" value="<?php echo isset($order['order_id']) ? htmlspecialchars($order['order_id']) : ''; ?>">
        </div>
        <div class="form-group mt-3">
            <label for="uid">UID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="uid" name="uid" placeholder="Nhập UID hoặc dán để phân tích" value="<?php echo isset($order['uid']) ? htmlspecialchars($order['uid']) : ''; ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="product_id">Token <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="token" name="product_id" placeholder="Nhập Token hoặc dán để phân tích" value="<?php echo isset($order['product_id']) ? htmlspecialchars($order['product_id']) : ''; ?>" required>
        </div>
        <div class="form-group mt-3">
            <label for="card">Code</label>
            <input type="text" class="form-control" id="code" name="code" placeholder="Nhập code" maxlength="30" value="<?php echo isset($order['code']) ? htmlspecialchars($order['code']) : ''; ?>">
        </div>
        <div class="form-group mt-3">
            <label for="image">Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small class="form-text text-muted">Chọn file ảnh mới để thay thế (để trống nếu giữ nguyên ảnh hiện tại)</small>
        </div>
        <div class="form-group mt-3">
            <label for="sales_agent_id">Sales Agent ID <span class="text-muted">(Tùy chọn)</span></label>
            <input type="number" class="form-control" id="sales_agent_id" name="sales_agent_id" placeholder="Nhập ID đại lý (để trống nếu không có)" value="<?php echo isset($order['sale_agent_id']) ? htmlspecialchars($order['sale_agent_id']) : ''; ?>" min="1" step="1" style="max-width: 200px;">
        </div>
        <div class="form-group mt-3">
            <label for="status">Trạng thái</label>
            <select class="form-control" id="status" name="status">
                <option value="pending">Đang chờ</option>
                <option value="success">Thành công</option>
                <option value="cancelled">Đã huỷ</option>
            </select>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Cập nhập đơn hàng</button>
            <a href="?act=midas-token-orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">