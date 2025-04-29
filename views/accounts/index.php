<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <h3>
            Danh sách tài khoản
        </h3>
        <div class="d-flex" justify-content-between>
            <a href="?act=add" class="btn btn-primary">Thêm tài khoản</a>
            <select onchange="window.location.href = '?act=list&type=' + this.value" name="type" class="form-select" id="type">
                <option value="">Tất cả</option>
                <option <?php echo isset($_GET['type']) && $_GET['type'] == 'Netflix' ? 'selected' : '' ?> value="Netflix">Netflix</option>
                <option <?php echo isset($_GET['type']) && $_GET['type'] == 'Capcut' ? 'selected' : '' ?> value="Capcut">Capcut</option>
            </select>
        </div>
    </div>
    <table class="table mt-3">
        <thead>
            <tr>
                <th scope="col">Email</th>
                <th scope="col">Password</th>
                <th scope="col">Type</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($accounts as $account) {
            ?>
                <tr>
                    <td><?php echo $account['email'] ?></td>
                    <td><?php echo $account['password'] ?></td>
                    <td><span class="badge bg-<?php echo $account['type'] == 'Netflix' ? 'danger' : 'primary' ?>"><?php echo $account['type'] ?></span></td>
                    <td>
                        <a onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?')" href="?act=delete&id=<?php echo $account['id'] ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>