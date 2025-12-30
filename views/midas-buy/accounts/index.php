<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <h3>
            Danh sách tài khoản MidasBuy
        </h3>
        <div class="d-flex" style="gap: 8px;">
            <a href="?act=midas-order-add" class="btn btn-primary">Thêm đơn hàng</a>
            <a href="?act=midas-orders" class="btn btn-info">Danh sách đơn hàng</a>
            <a href="?act=midas-account-add" class="btn btn-primary">Thêm tài khoản</a>
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
                <th scope="col">Email</th>
                <th scope="col">UID</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($accounts)) {
            ?>
                <tr>
                    <td colspan="4" class="text-center">Không có tài khoản nào</td>
                </tr>
                <?php
            } else {
                foreach ($accounts as $account) {
                ?>
                    <tr>
                        <td><?php echo $account['id'] ?></td>
                        <td><?php echo htmlspecialchars($account['email']) ?></td>
                        <td><?php echo htmlspecialchars($account['uid']) ?></td>
                        <td>
                            <div class="d-flex" style="gap: 5px;">
                                <a href="?act=midas-account-edit&id=<?php echo $account['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?')" href="?act=midas-account-delete&id=<?php echo $account['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">