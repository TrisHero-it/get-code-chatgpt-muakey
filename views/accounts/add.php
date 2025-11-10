<div class="container mt-5">
    <h3>
        Thêm tài khoản
    </h3>
    <form action="?act=store" method="post" enctype="multipart/form-data">
        <div class="form-group  mt-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email">
        </div>
        <div class="form-group  mt-3">
            <label for="password">Password</label>
            <input type="text" class="form-control" id="password" name="password" placeholder="Nhập password">
        </div>
        <div class="form-group mt-3">
            <label for="type">Type</label>
            <select class="form-control" id="type" name="type">
                <option value="Netflix">Netflix</option>
                <option value="CapCut">CapCut</option>
            </select>
        </div>
        <div class="form-group mt-3">
            <input class="form-control" type="file" name="excel_file" accept=".xlsx, .xls">
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3">Thêm</button>
            <a href="?act=export" class="btn btn-success mt-3">Xuất Excel mẫu</a>
        </div>
    </form>
</div>