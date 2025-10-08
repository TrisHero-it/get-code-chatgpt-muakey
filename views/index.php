<?php if (isset($token)) {
    if ($token == 1) {
?>
        <script>
            window.location.href = 'https://2fa.muakey.com/?id=<?php echo $accountChatgpt['code'] ?>';
        </script>;
<?php
        exit;
    }
}  ?>
<div class="main-content">
    <div class="container mt-5">
        <!-- Table -->
        <div class="d-flex justify-content-between">
            <?php if (isset($_GET['email'])) {
                $email = strtolower($_GET['email']);
            } ?>
            <h2 class="mb-5">Nhận code : <span style="color: red;"><?php echo $email ?? 'Vui lòng điền email để lấy code ' ?></span></h2>


            <!-- Button trigger modal -->
            <button type="button" id="click" style="height: 22px;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Tìm kiếm theo email
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Vui lòng điền email để lấy code</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="email" class="form-control" style="width: 95%;" name="email" id="email" placeholder="Nhập email">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
if (!isset($_GET['email']) || $_GET['email'] == '') {
    echo "<script>
                setTimeout(function() {
                    document.getElementById('click').click();
                }, 100);
            </script>";
}
?>

<div class="main-content">
    <div class="container mt-5">
        <!-- Table -->
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Netflix</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Link</th>
                                    <th scope="col">Phân loại</th>
                                    <th scope="col">Tiêu đề</th>
                                    <th scope="col">Thời gian</th>
                                    <th scope="col">Website</th>
                                </tr>
                            </thead>
                            <?php if (isset($_GET['email'])) { ?>
                                <tbody>
                                    <?php
                                    foreach ($results as $item) {
                                        $date = new DateTime($item['createdAt'], new DateTimeZone('UTC'));
                                        $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                                        $now = new DateTime("now", new DateTimeZone('Asia/Ho_Chi_Minh'));
                                        $createdAtPlus15 = clone $date;
                                        $createdAtPlus15->add(new DateInterval('PT15M')); // PT15M = Plus 15 Minutes
                                        if ($createdAtPlus15 <= $now) {
                                            continue;
                                        } else {
                                            if (($item['from']['name'] == 'Netflix' && $item['to'][0]['address'] == $email)) {

                                    ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-dot mr-4" style="color: green;">
                                                            <?php
                                                            if ($item['from']['name'] == 'Netflix' && $item['to'][0]['address'] == $email) {
                                                                echo "<span id='code" . $item['@id'] . "'></span>";
                                                            ?>
                                                                <script>
                                                                    $.ajax({
                                                                        url: "https://api.mail.tm" + "<?php echo $item['@id'] ?>",
                                                                        method: "GET",
                                                                        headers: {
                                                                            "Authorization": "Bearer <?php echo $token ?>"
                                                                        },
                                                                        success: function(response) {
                                                                            const html = response.html[0];
                                                                            const parser = new DOMParser();
                                                                            const doc = parser.parseFromString(html, 'text/html');
                                                                            const aTag = doc.querySelector('a.h5[href*="netflix.com/account/travel/verify"]');

                                                                            if (aTag) {
                                                                                document.getElementById('code<?php echo $item['@id'] ?>').innerHTML = `<a class="btn btn-primary" href="${aTag.href}"> click here </a>`;
                                                                            } else {
                                                                                document.getElementById('code<?php echo $item['@id'] ?>').innerHTML = `ERROR NOT FOUND`;
                                                                            }
                                                                        }
                                                                    });
                                                                </script>

                                                            <?php
                                                            }
                                                            ?>
                                                        </span>
                                                    </td>

                                                    <th scope="row">
                                                        <div class="media align-items-center">
                                                            <a href="#" class="avatar rounded-circle mr-3">
                                                                <img alt="Image placeholder" src="css/images/netflix.jpg">
                                                            </a>
                                                            <div class="media-body">
                                                                <span class="mb-0 text-sm">Netflix</span>
                                                            </div>
                                                        </div>
                                                    </th>

                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-dot mr-4" style="color: red;">
                                                                <?php echo $item['subject'] ?>
                                                            </span>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-dot mr-4" style="color: black">
                                                                <?php echo $date->format('H:i:s') ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="https://muakey.com/">muakey.com</a>
                                                    </td>
                                                </tr>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>