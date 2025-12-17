<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <div>
            <h3>Danh sách wwm_orders</h3>
        </div>
        <div class="d-flex" style="gap: 8px; height: 42px;">
            <a href="?act=orders-dashboard" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="?act=wwm-order-add" class="btn btn-primary">Thêm wwm_order</a>
            <select onchange="window.location.href = '?act=wwm-orders&category=' + this.value" name="category" class="form-select" id="category" style="width: auto;">
                <option value="">Tất cả category</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <option <?php echo isset($_GET['category']) && $_GET['category'] == $cat ? 'selected' : '' ?> value="<?php echo htmlspecialchars($cat) ?>">
                            <?php echo htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <a href="?act=wwm-order-delete-all"
                onclick="return confirm('Bạn có chắc chắn muốn xóa TẤT CẢ WWM orders ngoại trừ các orders đang chờ (pending)?\n\nHành động này không thể hoàn tác!')"
                class="btn btn-warning">
                <i class="fas fa-trash-alt"></i> Xóa tất cả (trừ pending)
            </a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <table class="table mt-3">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Order ID</th>
                <th scope="col">UID</th>
                <th scope="col">Product ID</th>
                <th scope="col">Status</th>
                <th scope="col">Category</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($wwmOrders)) {
            ?>
                <tr>
                    <td colspan="9" class="text-center">Không có dữ liệu nào</td>
                </tr>
                <?php
            } else {
                foreach ($wwmOrders as $order) {
                    $statusClass = '';
                    $statusText = '';
                    switch ($order['status']) {
                        case 'pending':
                            $statusClass = 'warning';
                            $statusText = 'Đang chờ';
                            break;
                        case 'processing':
                            $statusClass = 'info';
                            $statusText = 'Đang xử lý';
                            break;
                        case 'completed':
                            $statusClass = 'success';
                            $statusText = 'Hoàn thành';
                            break;
                        case 'cancelled':
                            $statusClass = 'danger';
                            $statusText = 'Đã hủy';
                            break;
                        default:
                            $statusClass = 'danger';
                            $statusText = $order['status'];
                    }
                ?>
                    <tr>
                        <td><?php echo $order['id'] ?></td>
                        <td><strong><?php echo htmlspecialchars($order['order_id'] ?? 'N/A') ?></strong></td>
                        <td><strong><?php echo htmlspecialchars($order['uid'] ?? 'N/A') ?></strong></td>
                        <td>
                            <?php
                            $productFound = false;
                            // Tìm trong iosProducts (Where Winds Meet)
                            foreach ($iosProducts as $product) {
                                if ($product['goodsid'] == $order['product_id']) {
                                    echo htmlspecialchars($product['goodsinfo']);
                                    $productFound = true;
                                    break;
                                }
                            }
                            // Nếu không tìm thấy, tìm trong productsOneHuman
                            if (!$productFound && isset($productsOneHuman)) {
                                foreach ($productsOneHuman as $product) {
                                    if ($product['goodsid'] == $order['product_id']) {
                                        echo htmlspecialchars($product['goodsinfo']);
                                        $productFound = true;
                                        break;
                                    }
                                }
                            }
                            // Nếu vẫn không tìm thấy, hiển thị product_id
                            if (!$productFound) {
                                echo htmlspecialchars($order['product_id'] ?? 'N/A');
                            }
                            ?>
                        </td>

                        <td><span class="badge bg-<?php echo $statusClass ?>"><?php echo $statusText ?></span></td>
                        <td><?php echo htmlspecialchars($order['category'] ?? 'N/A') ?></td>
                        <td><?php echo isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : 'N/A' ?></td>
                        <td>
                            <div class="d-flex" style="gap: 5px;">
                                <?php if (!empty($order['image'])): ?>
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $order['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                <?php endif; ?>
                                <a href="?act=wwm-order-edit&id=<?php echo $order['id'] ?>"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a onclick="return confirm('Bạn có chắc chắn muốn xóa wwm_order này không?')"
                                    href="?act=wwm-order-delete&id=<?php echo $order['id'] ?>"
                                    class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modals for Image -->
<?php
if (!empty($wwmOrders)) {
    foreach ($wwmOrders as $order) {
        if (!empty($order['image'])) {
?>
            <!-- Modal for Order ID <?php echo $order['id'] ?> -->
            <div class="modal fade" id="imageModal<?php echo $order['id'] ?>" tabindex="-1" aria-labelledby="imageModalLabel<?php echo $order['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel<?php echo $order['id'] ?>">
                                Image - Đơn hàng #<?php echo htmlspecialchars($order['order_id'] ?? $order['id']) ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="<?php echo htmlspecialchars($order['image']) ?>"
                                alt="Image"
                                class="img-fluid"
                                style="max-height: 70vh; border-radius: 4px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
<?php
        }
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">