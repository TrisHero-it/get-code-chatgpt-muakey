<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <div>
            <h3>
                Danh sách đơn hàng MidasBuy
            </h3>
        </div>
        <div class="d-flex" style="gap: 8px; height: 42px;">
            <a href="?act=midas-accounts" class="btn btn-info">Danh sách tài khoản</a>
            <a href="?act=midas-order-add" class="btn btn-primary">Thêm đơn hàng</a>
            <a href="?act=midas-order-delete-all"
                onclick="return confirm('Bạn có chắc chắn muốn xóa TẤT CẢ đơn hàng ngoại trừ đơn đang chờ (pending)?\n\nHành động này không thể hoàn tác!')"
                class="btn btn-warning">
                <i class="fas fa-trash-alt"></i> Xóa tất cả (trừ pending)
            </a>
            <select onchange="window.location.href = '?act=midas-orders&status=' + this.value" name="status" class="form-select" id="status">
                <option value="">Tất cả</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?> value="pending">Đang chờ</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'success' ? 'selected' : '' ?> value="success">Thành công</option>
            </select>
        </div>
    </div>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <table class="table mt-3">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Mã đơn hàng</th>
                <th scope="col">UID</th>
                <th scope="col">Token</th>
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
                    <td colspan="7" class="text-center">Không có đơn hàng nào</td>
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
                        case 'success':
                            $statusClass = 'success';
                            $statusText = 'Thành công';
                            break;
                        default:
                            $statusClass = 'secondary';
                            $statusText = $order['status'];
                    }
                ?>
                    <tr>
                        <td><?php echo $order['id'] ?></td>
                        <td><strong><?php echo htmlspecialchars($order['order_id'] ?? 'N/A') ?></strong></td>
                        <td><?php echo htmlspecialchars($order['uid'] ?? 'N/A') ?></td>
                        <td><?php echo htmlspecialchars($order['token'] ?? 'N/A') ?></td>
                        <td><span class="badge bg-<?php echo $statusClass ?>"><?php echo $statusText ?></span></td>
                        <td><?php echo isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : 'N/A' ?></td>
                        <td>
                            <div class="d-flex" style="gap: 5px;">
                                <a href="?act=midas-order-edit&id=<?php echo $order['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này không?')" href="?act=midas-order-delete&id=<?php echo $order['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
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
                        <a class="page-link" href="?act=midas-orders&page=<?php echo $currentPage - 1 ?><?php echo $statusParam ?>">Trước</a>
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
                        <a class="page-link" href="?act=midas-orders&page=1<?php echo $statusParam ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?act=midas-orders&page=<?php echo $i ?><?php echo $statusParam ?>"><?php echo $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=midas-orders&page=<?php echo $totalPages ?><?php echo $statusParam ?>"><?php echo $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <!-- Nút Next -->
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=midas-orders&page=<?php echo $currentPage + 1 ?><?php echo $statusParam ?>">Sau</a>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">