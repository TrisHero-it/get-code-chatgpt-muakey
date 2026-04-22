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
            <?php if (($_GET['email'])) {
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
                                <button type="submit" class="btn btn-primary" id="searchSubmitBtn">
                                    <span class="search-btn-text">Tìm kiếm</span>
                                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true" id="searchSpinner"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    (function() {
        var form = document.querySelector('#exampleModal form');
        var btn = document.getElementById('searchSubmitBtn');
        var spinner = document.getElementById('searchSpinner');
        var btnText = document.querySelector('#searchSubmitBtn .search-btn-text');
        var emailInput = document.getElementById('email');

        if (!form || !btn || !spinner || !btnText || !emailInput) return;

        form.addEventListener('submit', function() {
            var email = (emailInput.value || '').trim();
            if (!email) return;

            btn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.textContent = 'Đang tìm...';
        });
    })();
</script>

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
                                <tbody id="resultsBody">
                                    <tr id="loadingRow">
                                        <td colspan="5" class="text-center py-4">
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Đang tải kết quả...
                                        </td>
                                    </tr>
                                    <tr id="noResultsRow" class="d-none">
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Không có kết quả
                                        </td>
                                    </tr>
                                    <?php
                                    echo "<script>(function(){\nwindow.__SEARCH_AJAX_PENDING__=0;\nwindow.__updateSearchStatus__=function(){\n  var loadingRow=document.getElementById('loadingRow');\n  var noRow=document.getElementById('noResultsRow');\n  var body=document.getElementById('resultsBody');\n  if(!noRow||!body) return;\n\n  // Chỉ kết luận khi mọi request đã xong\n  if((window.__SEARCH_AJAX_PENDING__||0)!==0) return;\n\n  if(loadingRow) loadingRow.classList.add('d-none');\n\n  var resultRows=body.querySelectorAll('tr[id^=\"row\"]');\n  var hasVisible=false;\n  for(var i=0;i<resultRows.length;i++){\n    var r=resultRows[i];\n    if(!r||!r.isConnected) continue;\n    if(r.style && r.style.display==='none') continue;\n    // nếu display trống thì coi như đang hiện\n    hasVisible=true;\n    break;\n  }\n\n  if(hasVisible) noRow.classList.add('d-none');\n  else noRow.classList.remove('d-none');\n};\n})();</script>";
                                    foreach ($results as $item) {
                                        $date = new DateTime($item['createdAt'], new DateTimeZone('UTC'));
                                        $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                                        $now = new DateTime("now", new DateTimeZone('Asia/Ho_Chi_Minh'));
                                        $createdAtPlus15 = clone $date;
                                        $createdAtPlus15->add(new DateInterval('PT15M')); // PT15M = Plus 15 Minutes
                                        if ($createdAtPlus15 <= $now) {
                                            continue;
                                        } else {

                                            if (($item['from']['name'] == 'Netflix' && $item['to'][0]['address'] == $email) ||
                                                (mb_strtolower($item['to'][0]['address'], 'UTF-8') == $email && $item['from']['name'] == 'Netflix')
                                            ) {

                                    ?>
                                                <tr id="row<?php echo htmlspecialchars($item['@id'], ENT_QUOTES, 'UTF-8') ?>" style="display: none;">
                                                    <td>
                                                        <span class="badge badge-dot mr-4" style="color: green;">
                                                            <?php
                                                            if ($item['from']['name'] == 'Netflix' && strtolower($item['to'][0]['address']) == strtolower($email)) {
                                                                echo "<span id='code" . htmlspecialchars($item['@id'], ENT_QUOTES, 'UTF-8') . "'></span>";

                                                                $text = $item['intro'];
                                                            ?>
                                                                <script>
                                                                    (function() {
                                                                        var rowId = <?php echo json_encode('row' . $item['@id']) ?>;
                                                                        var codeId = <?php echo json_encode('code' . $item['@id']) ?>;
                                                                        window.__SEARCH_AJAX_PENDING__ = (window.__SEARCH_AJAX_PENDING__ || 0) + 1;
                                                                        $.ajax({
                                                                            url: "https://api.mail.tm" + "<?php echo $item['@id'] ?>",
                                                                            method: "GET",
                                                                            headers: {
                                                                                "Authorization": "Bearer <?php echo $item['_token'] ?? $token ?>"
                                                                            },
                                                                            success: function(response) {

                                                                                var row = document.getElementById(rowId);
                                                                                if (!row) return;
                                                                                var html = response && response.html;
                                                                                if (Array.isArray(html)) html = html[0];
                                                                                if (!html) {
                                                                                    row.remove();
                                                                                    return;
                                                                                }
                                                                                var parser = new DOMParser();
                                                                                var doc = parser.parseFromString(html, 'text/html');
                                                                                var aTag = doc.querySelector('a.h5[href*="netflix.com/account/travel/verify"]');

                                                                                if (aTag) {
                                                                                    row.style.display = '';
                                                                                    document.getElementById(codeId).innerHTML = '<a class="btn btn-primary" href=' + JSON.stringify(aTag.href) + '> Click here </a>';
                                                                                } else {
                                                                                    row.remove();
                                                                                }
                                                                            },
                                                                            error: function() {
                                                                                var row = document.getElementById(rowId);
                                                                                if (row) row.remove();
                                                                            },
                                                                            complete: function() {
                                                                                window.__SEARCH_AJAX_PENDING__ = Math.max(0, (window.__SEARCH_AJAX_PENDING__ || 0) - 1);
                                                                                if (typeof window.__updateSearchStatus__ === 'function') window.__updateSearchStatus__();
                                                                            }
                                                                        });
                                                                    })();
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
                                    <script>
                                        (function() {
                                            if (typeof window.__updateSearchStatus__ === 'function') window.__updateSearchStatus__();
                                        })();
                                    </script>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>