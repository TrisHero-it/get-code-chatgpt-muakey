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

            <?php
            if (!isset($_GET['email'])) {
            ?>
                <script>
                    setTimeout(function() {
                        document.getElementById('click').click();
                    }, 100);
                </script>
            <?php
            }
            ?>

        </div>
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Code ChatGPT</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Phân loại</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Code</th>
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
                                            if (($item['from']['name'] != 'Netflix' && $item['from']['name'] != 'CapCut') && $item['to'][0]['address'] == $email) {
                                    ?>
                                                <tr>
                                                    <th scope="row">
                                                        <div class="media align-items-center">
                                                            <a href="#" class="avatar rounded-circle mr-3">
                                                                <img alt="Image placeholder" src="css/images/chatgpt.png">
                                                            </a>
                                                            <div class="media-body">
                                                                <span class="mb-0 text-sm">Chat GPT</span>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <td>
                                                        <span class="badge badge-dot mr-4" style="color: black">
                                                            <?php echo $item['to'][0]['address'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-dot mr-4" style="color: green;">
                                                            <?php
                                                            preg_match('/\b\d{6}\b/', $item['subject'], $matches);
                                                            if (!empty($matches)) {
                                                                echo $matches[0];
                                                            } else {
                                                                echo "<span style='color: red;'>Error</span>";
                                                            }
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-dot mr-4" style="color: black">
                                                                <?php echo $date->format('d/m/Y H:i:s') ?>
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

<div class="main-content">
    <div class="container mt-5">
        <!-- Table -->
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Code CapCut <span style="color: red;">(Nhớ đặt lại mật khẩu như bên dưới)</span></h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Phân loại</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Mật khẩu đặt lại</th>
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
                                            if (($item['from']['name'] == 'CapCut' && $item['to'][0]['address'] == $email)) {
                                    ?>
                                                <tr>
                                                    <th scope="row">
                                                        <div class="media align-items-center">
                                                            <a href="#" class="avatar rounded-circle mr-3">
                                                                <img alt="Image placeholder" src="css/images/capcut.png">
                                                            </a>
                                                            <div class="media-body">
                                                                <span class="mb-0 text-sm">CapCut</span>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <td>
                                                        <span class="badge badge-dot mr-4" style="color: black">
                                                            <?php echo $item['to'][0]['address'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-dot mr-4" style="color: green;">
                                                            <?php
                                                            if ($item['from']['name'] == 'CapCut') {
                                                                echo "<span id='code" . $item['@id'] . "'></span>";
                                                            ?>
                                                                <script>
                                                                    $.ajax({
                                                                        url: "https://api.mail.tm" + "<?php echo $item['@id'] ?>",
                                                                        method: "GET",
                                                                        headers: {
                                                                            "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM2Njk3MTQsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibWFuaGJpZ2F5QHB0Y3QubmV0IiwiaWQiOiI2N2VlM2IxYzQ0ZDU2Y2E0MTEwNzMwZDciLCJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIvYWNjb3VudHMvNjdlZTNiMWM0NGQ1NmNhNDExMDczMGQ3Il19fQ.a_i4rTY3qxmMuATGGhTmsulBluWqLukWrs1-s6_kN7zvXw40U8dlYqks8bj0p8SNmiF8Jqs5YQ_ykpUPR-azlg"
                                                                        },
                                                                        success: function(response) {
                                                                            const htmlContent = response.html[0];
                                                                            const parser = new DOMParser();
                                                                            const doc = parser.parseFromString(htmlContent, 'text/html');
                                                                            const p = doc.querySelector('p[style="margin-bottom:20px;color:#16161d;font-weight:600"]');
                                                                            if (p) {
                                                                                const number = p.textContent.trim().match(/\d+/)[0];
                                                                                document.getElementById('code<?php echo $item['@id'] ?>').innerHTML = number;
                                                                            }
                                                                        }
                                                                    });
                                                                </script>
                                                            <?php
                                                            } else {
                                                                preg_match('/\b\d{6}\b/', $item['subject'], $matches);
                                                                if (!empty($matches)) {
                                                                    echo $matches[0];
                                                                } else {
                                                                    echo "<span style='color: red;'>Error</span>";
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-dot mr-4" style="color: red;">
                                                                <?php if ($accountCapcut != null) {
                                                                    echo $accountCapcut['password'];
                                                                } ?>
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
                                    <th scope="col">Phân loại</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Tiêu đề</th>
                                    <th scope="col">Link</th>
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
                                                        <span class="badge badge-dot mr-4" style="color: black">
                                                            <?php echo $item['to'][0]['address'] ?>
                                                        </span>
                                                    </td>


                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-dot mr-4" style="color: red;">
                                                                <?php echo $item['subject'] ?>
                                                            </span>
                                                        </div>
                                                    </td>

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
                                                                            text = response.text;
                                                                            const nhanMaIndex = text.indexOf("Nhận mã");

                                                                            if (nhanMaIndex !== -1) {
                                                                                // Cắt text từ "Nhận mã" trở đi
                                                                                const subText = text.substring(nhanMaIndex);

                                                                                // Tìm link trong đoạn mới
                                                                                const match = subText.match(/\[(https?:\/\/.*?)\]/);

                                                                                if (match) {
                                                                                    document.getElementById('code<?php echo $item['@id'] ?>').innerHTML = `
                                                                                    <a href="${match[1]}" class="btn btn-success" target="_blank">Nhận mã</a>
                                                                                `;
                                                                                } else {

                                                                                }
                                                                            } else {
                                                                                const newIndex = text.indexOf("Đúng, đây là tôi");
                                                                                if (newIndex !== -1) {
                                                                                    // Cắt text từ "Nhận mã" trở đi
                                                                                    const newSubText = text.substring(newIndex);

                                                                                    // Tìm link trong đoạn mới
                                                                                    const newMatch = newSubText.match(/\[(https?:\/\/.*?)\]/);

                                                                                    if (newMatch) {
                                                                                        document.getElementById('code<?php echo $item['@id'] ?>').innerHTML = `
                                                                                    <a href="${newMatch[1]}" class="btn btn-danger" target="_blank">Đây là tôi</a>
                                                                                `;
                                                                                    } else {

                                                                                    }
                                                                                }

                                                                            }

                                                                        }
                                                                    });
                                                                </script>

                                                            <?php
                                                            }
                                                            ?>
                                                        </span>
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