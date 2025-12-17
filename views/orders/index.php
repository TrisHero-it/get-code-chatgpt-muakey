<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <div>
            <h3>
                Danh sách đơn hàng
            </h3>
            <?php if ($totalMoney): ?>
                <p class="text-muted mb-0">Số tiền còn lại: <strong class="text-success"><?php echo $totalMoney['balance'] ?> €</strong></p>
                <p class="text-muted mb-0">Code: <strong class="text-success"><?php echo $totalMoney['code'] ?></strong></p>
            <?php else: ?>
                <div class="alert alert-warning mb-0" style="padding: 8px 12px;">
                    <strong><i class="fas fa-exclamation-triangle"></i> Cần phải thêm code!</strong>
                </div>
            <?php endif; ?>
            <div class="d-flex" style="gap: 8px; margin-top: 8px;">
                <a href="?act=money-edit" class="btn btn-sm btn-info">
                    <i class="fas fa-edit"></i> Sửa thông tin tiền
                </a>
                <a href="?act=payment-codes-add" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Thêm code payment
                </a>
                <a href="?act=payment-codes-list" class="btn btn-sm btn-primary">
                    <i class="fas fa-list"></i> Danh sách code
                </a>
            </div>
        </div>
        <div class="d-flex" style="gap: 8px; height: 42px;">
            <a href="?act=orders-dashboard" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="?act=order-add" class="btn btn-primary">Thêm đơn hàng</a>
            <a href="?act=order-delete-all"
                onclick="return confirm('Bạn có chắc chắn muốn xóa TẤT CẢ đơn hàng ngoại trừ đơn đang chờ (pending)?\n\nHành động này không thể hoàn tác!')"
                class="btn btn-warning">
                <i class="fas fa-trash-alt"></i> Xóa tất cả (trừ pending)
            </a>
            <select onchange="window.location.href = '?act=orders&status=' + this.value" name="status" class="form-select" id="status">
                <option value="">Tất cả</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?> value="pending">Đang chờ</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'processing' ? 'selected' : '' ?> value="processing">Đang xử lý</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : '' ?> value="completed">Hoàn thành</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : '' ?> value="cancelled">Đã hủy</option>
            </select>
        </div>
    </div>
    <table class="table mt-3">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Mã đơn hàng</th>
                <th scope="col">Username</th>
                <th scope="col">Password</th>
                <th scope="col">Mã backup steam</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($orders)) {
            ?>
                <tr>
                    <td colspan="9" class="text-center">Không có đơn hàng nào</td>
                </tr>
                <?php
            } else {
                foreach ($orders as $order) {
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
                        <td><?php echo htmlspecialchars($order['username'] ?? 'N/A') ?></td>
                        <td><?php echo htmlspecialchars($order['password'] ?? 'N/A') ?></td>
                        <td><span style="color: #007bff;"><?php echo htmlspecialchars($order['backup_code'] ?? 'N/A') ?></span></td>
                        <td><span class="badge bg-<?php echo $statusClass ?>"><?php echo $statusText ?></span></td>
                        <td><?php echo isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : 'N/A' ?></td>
                        <td>
                            <div class="d-flex" style="gap: 5px;">
                                <?php if (!empty($order['image_error'])): ?>
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $order['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                <?php endif; ?>
                                <a href="?act=order-edit&id=<?php echo $order['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này không?')" href="?act=order-delete&id=<?php echo $order['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>

    <!-- Phân trang -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <?php
                $currentPage = isset($currentPage) ? $currentPage : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
                $statusParam = isset($_GET['status']) && $_GET['status'] != '' ? '&status=' . htmlspecialchars($_GET['status']) : '';

                // Nút Previous
                if ($currentPage > 1):
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=orders&page=<?php echo $currentPage - 1 ?><?php echo $statusParam ?>">Trước</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Trước</span>
                    </li>
                <?php endif; ?>

                <?php
                // Hiển thị các số trang
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                if ($startPage > 1):
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=orders&page=1<?php echo $statusParam ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?act=orders&page=<?php echo $i ?><?php echo $statusParam ?>"><?php echo $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=orders&page=<?php echo $totalPages ?><?php echo $statusParam ?>"><?php echo $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <!-- Nút Next -->
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=orders&page=<?php echo $currentPage + 1 ?><?php echo $statusParam ?>">Sau</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Sau</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="text-center text-muted mb-3">
            <small>Trang <?php echo $currentPage ?> / <?php echo $totalPages ?> (Tổng: <?php echo $totalCount ?> đơn hàng)</small>
        </div>
    <?php endif; ?>
</div>

<!-- Modals for Image Error -->
<?php
if (!empty($orders)) {
    foreach ($orders as $order) {
        if (!empty($order['image_error'])) {
?>
            <!-- Modal for Order ID <?php echo $order['id'] ?> -->
            <div class="modal fade" id="imageModal<?php echo $order['id'] ?>" tabindex="-1" aria-labelledby="imageModalLabel<?php echo $order['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel<?php echo $order['id'] ?>">
                                Image Error - Đơn hàng #<?php echo htmlspecialchars($order['order_id'] ?? $order['id']) ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="<?php echo htmlspecialchars($order['image_error']) ?>"
                                alt="Image Error"
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