<div class="container mt-5">
    <h3>
        Thêm code payment
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
    <form action="?act=payment-codes-store" method="post" id="paymentCodesForm">
        <div class="form-group mt-3">
            <label for="codes">Danh sách code payment <span class="text-danger">*</span></label>
            <textarea
                class="form-control"
                id="codes"
                name="codes"
                rows="10"
                placeholder="Paste các code payment vào đây, mỗi code một dòng. Ví dụ:&#10;0384694276073790&#10;0867316874011972"
                required></textarea>
            <small class="form-text text-muted">
                Mỗi code sẽ được thêm vào database với balance = 10 €. Các code trùng lặp sẽ được bỏ qua.
            </small>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Thêm code</button>
            <a href="?act=orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">