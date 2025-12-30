<div class="container mt-5">
    <h3>
        Thêm đơn hàng MidasBuy
    </h3>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="?act=midas-order-store" method="post">
        <div class="form-group mt-3">
            <label for="order_id">Mã đơn hàng <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Nhập mã đơn hàng" required>
        </div>
        <div class="form-group mt-3">
            <label for="uid">UID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="uid" name="uid" placeholder="Nhập UID" required>
        </div>
        <div class="form-group mt-3">
            <label for="token">Token <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="token" name="token" placeholder="Nhập token (số)" required>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Thêm đơn hàng</button>
            <a href="?act=midas-orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">