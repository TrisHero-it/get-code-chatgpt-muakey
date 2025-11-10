<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Danh sách code payment</h3>
        <div class="d-flex" style="gap: 8px;">
            <a href="?act=payment-codes-add" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm code
            </a>
            <a href="?act=payment-codes-delete-all"
                onclick="return confirm('Bạn có chắc chắn muốn xóa TẤT CẢ code payment?\n\nHành động này không thể hoàn tác!')"
                class="btn btn-danger">
                <i class="fas fa-trash-alt"></i> Xóa tất cả
            </a>
            <a href="?act=orders" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <table class="table mt-3">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Code</th>
                <th scope="col">Balance (€)</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($codes)) {
            ?>
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="alert alert-warning mb-0">
                            <strong><i class="fas fa-exclamation-triangle"></i> Cần phải thêm code!</strong>
                            <p class="mb-0 mt-2">Hiện tại chưa có code nào trong hệ thống.</p>
                        </div>
                    </td>
                </tr>
                <?php
            } else {
                foreach ($codes as $codeItem) {
                    $balance = floatval($codeItem['balance'] ?? 0);
                    $statusClass = $balance >= 0.5 ? 'success' : 'danger';
                    $statusText = $balance >= 0.5 ? 'Có thể dùng' : 'Hết tiền';
                ?>
                    <tr>
                        <td><?php echo $codeItem['id'] ?></td>
                        <td><strong><?php echo htmlspecialchars($codeItem['code'] ?? 'N/A') ?></strong></td>
                        <td>
                            <span class="badge bg-<?php echo $statusClass ?>">
                                <?php echo number_format($balance, 2, '.', '') ?> €
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $statusClass ?>"><?php echo $statusText ?></span>
                        </td>
                        <td>
                            <a onclick="return confirm('Bạn có chắc chắn muốn xóa code này không?')"
                                href="?act=payment-code-delete&id=<?php echo $codeItem['id'] ?>"
                                class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">