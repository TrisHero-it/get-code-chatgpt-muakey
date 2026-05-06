<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <div>
            <h3>
                Danh sách đơn hàng MidasBuy Japan Token
            </h3>
        </div>
        <div class="d-flex" style="gap: 8px; height: 42px;">
            <a href="?act=orders-dashboard" class="btn btn-secondary">Dashboard</a>
            <a href="?act=add-midas-token-orders" class="btn btn-primary">Thêm đơn hàng</a>
            <select onchange="window.location.href = '?act=midas-japan-orders&status=' + this.value" name="status" class="form-select" id="status">
                <option value="">Tất cả</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?> value="pending">Đang chờ</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'success' ? 'selected' : '' ?> value="success">Thành công</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : '' ?> value="cancelled">Đã huỷ</option>
                <option <?php echo isset($_GET['status']) && $_GET['status'] == 'refunded' ? 'selected' : '' ?> value="refunded">Đã hoàn tiền</option>
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

    <!-- Form tìm kiếm -->
    <div class="card mt-3 mb-3">
        <div class="card-body">
            <form method="GET" action="" class="d-flex gap-2 align-items-end">
                <input type="hidden" name="act" value="midas-japan-orders">
                <?php if (isset($_GET['status']) && $_GET['status'] != ''): ?>
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($_GET['status']) ?>">
                <?php endif; ?>
                <div class="flex-grow-1">
                    <label for="search" class="form-label">Tìm kiếm đơn hàng</label>
                    <input type="text"
                        class="form-control"
                        id="search"
                        name="search"
                        placeholder="Nhập Order ID, UID hoặc Card..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                        <?php
                        $statusParam = isset($_GET['status']) && $_GET['status'] != '' ? '&status=' . htmlspecialchars($_GET['status']) : '';
                        ?>
                        <a href="?act=midas-japan-orders<?php echo $statusParam ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Xóa
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <table class="table mt-3">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Code</th>
                <th scope="col">Tokens</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Ngày sử dụng</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($codes)) {
            ?>
                <tr>
                    <td colspan="8" class="text-center">Không có code nào</td>
                </tr>
                <?php
            } else {
                foreach ($codes as $code) {
                    $s = $code['status'] ?? 'unused';
                    if ($s === 'used') {
                        $statusClass = 'success';
                        $statusText = 'Thành công';
                    } elseif ($s === 'error') {
                        $statusClass = 'danger';
                        $statusText = 'Code lỗi';
                    } elseif ($s === 'unused') {
                        $statusClass = 'info';
                        $statusText = 'Đã hoàn tiền';
                    }
                ?>
                    <tr>
                        <td><?php echo $code['id'] ?></td>
                        <td><?php echo !empty($code['code']) ? htmlspecialchars($code['code']) : '<span class="text-muted">-</span>' ?></td>
                        <td><?php echo htmlspecialchars($code['tokens'] ?? 'N/A') ?></td>
                        <td><span class="badge bg-<?php echo $statusClass ?>"><?php echo $statusText ?></span></td>
                        <td><?php echo isset($code['used_at']) && $code['used_at'] !== null && $code['used_at'] !== '' ? date('d/m/Y H:i', strtotime($code['used_at'])) : '—' ?></td>
                        <td>
                            <div class="d-flex" style="gap: 5px;">
                                <?php if (!empty($code['image'])): ?>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Xem ảnh"
                                        data-bs-toggle="modal"
                                        data-bs-target="#imageModal<?php echo $code['id'] ?>">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <?php endif; ?>
                                <a href="?act=edit-midas-token-order&id=<?php echo $order['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($s === 'cancelled'): ?>
                                    <a onclick="return confirm('Bạn có chắc chắn muốn hoàn tiền cho đơn hàng này không?')" href="?act=midas-japan-order-refund&id=<?php echo $order['id'] ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-money-bill-wave"></i> Hoàn tiền
                                    </a>
                                <?php endif; ?>
                                <a onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này không?')" href="?act=delete-midas-token-order&id=<?php echo $order['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
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
                $searchParam = isset($_GET['search']) && $_GET['search'] != '' ? '&search=' . urlencode($_GET['search']) : '';

                if ($currentPage > 1):
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=midas-japan-orders&page=<?php echo $currentPage - 1 ?><?php echo $statusParam ?><?php echo $searchParam ?>">Trước</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Trước</span>
                    </li>
                <?php endif; ?>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);

                if ($startPage > 1):
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=midas-japan-orders&page=1<?php echo $statusParam ?><?php echo $searchParam ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?act=midas-japan-orders&page=<?php echo $i ?><?php echo $statusParam ?><?php echo $searchParam ?>"><?php echo $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=midas-japan-orders&page=<?php echo $totalPages ?><?php echo $statusParam ?><?php echo $searchParam ?>"><?php echo $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?act=midas-japan-orders&page=<?php echo $currentPage + 1 ?><?php echo $statusParam ?><?php echo $searchParam ?>">Sau</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Sau</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="text-center text-muted mb-3">
            <small>Trang <?php echo $currentPage ?> / <?php echo $totalPages ?></small>
        </div>
    <?php endif; ?>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">