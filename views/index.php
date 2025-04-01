<div class="main-content">
    <div class="container mt-5">
        <!-- Table -->
        <h2 class="mb-5">Nhận code</h2>
        <div class="row">

            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Danh sách code</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Phân loại</th>
                                    <th scope="col">email</th>
                                    <th scope="col">code</th>
                                    <th scope="col">Users</th>
                                    <th scope="col">Thời gian</th>
                                    <th scope="col">Website</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($results as $result) {
                                    $response = json_decode($result, true);
                                    $response = $response['hydra:member'];
                                    foreach ($response as $item) {

                                        $date = new DateTime($item['createdAt'], new DateTimeZone('UTC'));
                                        $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                                        $now = new DateTime("now", new DateTimeZone('Asia/Ho_Chi_Minh'));
                                        $createdAtPlus15 = clone $date;
                                        $createdAtPlus15->add(new DateInterval('PT15M')); // PT15M = Plus 15 Minutes
                                        if ($createdAtPlus15 <= $now) {
                                            continue;
                                        } else {
                                ?>

                                            <tr>
                                                <th scope="row">
                                                    <div class="media align-items-center">
                                                        <?php
                                                        if (strpos(strtolower($item['subject']), 'chatgpt') !== false) {
                                                        ?>
                                                            <a href="#" class="avatar rounded-circle mr-3">
                                                                <img alt="Image placeholder" src="images/chatgpt.png">
                                                            </a>
                                                            <div class="media-body">
                                                                <span class="mb-0 text-sm">Chat GPT</span>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </th>
                                                <td>
                                                    <span class="badge badge-dot mr-4">
                                                        <?php echo $item['from']['address'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-dot mr-4">
                                                        <?php
                                                        preg_match('/\b\d{6}\b/', $item['subject'], $matches);
                                                        if (!empty($matches)) {
                                                            echo $matches[0];
                                                        } else {
                                                            echo "Không tìm thấy mã số.";
                                                        }
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-dot mr-4">
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-dot mr-4">
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
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>