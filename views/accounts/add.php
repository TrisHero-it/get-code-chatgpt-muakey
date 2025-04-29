<div class="container mt-5">
    <h3>
        Thêm tài khoản
    </h3>
    <form action="?act=store" method="post">
        <div class="form-group  mt-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
        </div>
        <div class="form-group  mt-3">
            <label for="password">Password</label>
            <input type="text" class="form-control" id="password" name="password" placeholder="Nhập password" required>
        </div>
        <div class="form-group mt-3">
            <label for="type">Type</label>
            <select class="form-control" id="type" name="type">
                <option value="Netflix">Netflix</option>
                <option value="CapCut">CapCut</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Thêm</button>
    </form>
</div>