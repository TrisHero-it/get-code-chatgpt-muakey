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
                                    <th scope="col">Email</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Branch</th>
                                    <th scope="col">Thời gian</th>
                                    <th scope="col">Website</th>
                                </tr>
                            </thead>
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
                                ?>
                                        <tr>
                                            <th scope="row">
                                                <div class="media align-items-center">
                                                    <?php
                                                    if ($item['from']['name'] == 'ChatGPT' || $item['from']['name'] == 'Sora') {
                                                    ?>
                                                        <a href="#" class="avatar rounded-circle mr-3">
                                                            <img alt="Image placeholder" src="css/images/chatgpt.png">
                                                        </a>
                                                        <div class="media-body">
                                                            <span class="mb-0 text-sm">Chat GPT</span>
                                                        </div>
                                                    <?php
                                                    } else if ($item['from']['name'] == 'Netflix') {
                                                    ?>
                                                        <a href="#" class="avatar rounded-circle mr-3">
                                                            <img alt="Image placeholder" src="css/images/netflix.jpg">
                                                        </a>
                                                        <div class="media-body">
                                                            <span class="mb-0 text-sm">Netflix</span>
                                                        </div>
                                                    <?php
                                                    } else if ($item['from']['name'] == 'Perplexity') {
                                                    ?>
                                                        <a href="#" class="avatar rounded-circle mr-3">
                                                            <img alt="Image placeholder" src="css/images/Perplexity-logo.png">
                                                        </a>
                                                        <div class="media-body">
                                                            <span class="mb-0 text-sm">Perplexity</span>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </th>
                                            <td>
                                                <span class="badge badge-dot mr-4">
                                                    <?php echo $item['to'][0]['address'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot mr-4" style="color: green;">
                                                    <?php
                                                    if ($item['from']['name'] == 'Perplexity') {
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
                                                                    const tempDiv = $('<div>').html(response.html[0]);

                                                                    // Lấy nội dung trong span
                                                                    const code = tempDiv.find('span[style="line-height: 24px !important; font-size: 16px; font-size: 16px !important; white-space: pre-wrap;"]')
                                                                        .text().trim();
                                                                    document.getElementById('code<?php echo $item['@id'] ?>').innerHTML = code;
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
                                                <span class="badge badge-dot mr-4">
                                                    <?php echo $item['from']['name'] ?>
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
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>