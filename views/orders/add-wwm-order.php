<div class="container mt-5">
    <h3>Thêm wwm_orders</h3>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="?act=wwm-order-store" method="post" id="wwmOrderForm">
        <div class="form-group mt-3">
            <label for="order_data">Dán thông tin đơn hàng <span class="text-danger">*</span></label>
            <textarea class="form-control" id="order_data" name="order_data" rows="6"
                placeholder="Dán thông tin đơn hàng vào đây (ví dụ:&#10;Mã đơn hàng: 836396&#10;Tên sản phẩm: Elite Battle Pass Where Winds Meet ID x 1&#10;Character ID Where Winds Meet: 4039549251&#10;Ngày mua: 15:26:40 03/01/2026&#10;&#10;Hoặc One Human:&#10;Mã đơn hàng: 816315&#10;Tên sản phẩm: 2340 Crystgin Once Human Chỉ Cần ID x 1&#10;UID Once Human: 1541918960&#10;Sever Once Human: E_Dream-Y0002&#10;Region Once Human: Southeast Asia Server&#10;Ngày mua: 10:07:59 26/12/2025)" required></textarea>
            <small class="form-text text-muted">Hệ thống sẽ tự động nhận diện Order ID, UID và Product ID từ thông tin bạn dán. <strong>Lưu ý: UID phải có đúng 10 chữ số (ví dụ: 4059837621)</strong></small>
        </div>

        <!-- Hidden fields để lưu giá trị đã parse -->
        <input type="hidden" id="order_id" name="order_id" value="">
        <input type="hidden" id="uid" name="uid" value="">
        <input type="hidden" id="product_id" name="product_id" value="">
        <input type="hidden" id="category" name="category" value="">
        <input type="hidden" id="server" name="server" value="">
        <input type="hidden" id="region" name="region" value="">
        <input type="hidden" id="continue_add" name="continue_add" value="0">

        <!-- Preview thông tin đã parse -->
        <div id="parsedInfo" class="alert alert-info mt-3" style="display: none;">
            <h6>Thông tin đã nhận diện:</h6>
            <ul class="mb-0">
                <li><strong>Order ID:</strong> <span id="preview_order_id">-</span></li>
                <li><strong>UID:</strong> <span id="preview_uid">-</span></li>
                <li><strong>Product ID:</strong> <span id="preview_product_id">-</span></li>
                <li><strong>Category:</strong> <span id="preview_category">-</span></li>
                <li id="preview_server_li" style="display: none;"><strong>Server:</strong> <span id="preview_server">-</span></li>
                <li id="preview_region_li" style="display: none;"><strong>Region:</strong> <span id="preview_region">-</span></li>
            </ul>
        </div>

        <div class="d-flex" style="gap: 8px">
            <button type="submit" class="btn btn-primary mt-3" id="submitBtn" disabled>Thêm wwm_orders</button>
            <button type="button" class="btn btn-success mt-3" id="continueAddBtn" disabled>
                <i class="fas fa-plus-circle"></i> Thêm tiếp
            </button>
            <a href="?act=wwm-orders" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderDataTextarea = document.getElementById('order_data');
        const orderIdInput = document.getElementById('order_id');
        const uidInput = document.getElementById('uid');
        const productIdInput = document.getElementById('product_id');
        const categoryInput = document.getElementById('category');
        const serverInput = document.getElementById('server');
        const regionInput = document.getElementById('region');
        const continueAddInput = document.getElementById('continue_add');
        const parsedInfoDiv = document.getElementById('parsedInfo');
        const submitBtn = document.getElementById('submitBtn');
        const continueAddBtn = document.getElementById('continueAddBtn');

        // Danh sách products từ PHP
        const iosProducts = <?php echo json_encode($iosProducts); ?>;
        const productsOneHuman = <?php echo json_encode($productsOneHuman ?? []); ?>;

        // Danh sách product IDs thuộc "where winds meet"
        const wwmProductIds = iosProducts.map(product => product.goodsid);

        // Danh sách product IDs thuộc "one human"
        const oneHumanProductIds = productsOneHuman.map(product => product.goodsid);

        // Gộp tất cả products để tìm kiếm
        const allProducts = [...iosProducts, ...productsOneHuman];

        function parseOrderData(text) {
            const result = {
                order_id: '',
                uid: '',
                product_id: '',
                category: '',
                server: '',
                region: ''
            };

            // Parse Order ID: "Mã đơn hàng: 797741"
            const orderIdMatch = text.match(/Mã đơn hàng:\s*(\d+)/i);
            if (orderIdMatch) {
                result.order_id = orderIdMatch[1].trim();
            }

            // Parse UID: "Character ID Where Winds Meet: 4039549251" hoặc "UID: User ID: 4033075547" hoặc "UID: 4033075547" hoặc "UID Once Human: 154191896"
            const uidMatch = text.match(/Character ID Where Winds Meet:\s*(\d+)/i) ||
                text.match(/UID:.*?User ID:\s*(\d+)/i) ||
                text.match(/UID Once Human:\s*(\d+)/i) ||
                text.match(/UID:\s*(\d+)/i);
            if (uidMatch) {
                result.uid = uidMatch[1].trim();
            }

            // Parse Server Once Human: "Sever Once Human: E_Dream-Y0002" (lưu ý: có thể là "Sever" hoặc "Server")
            const serverMatch = text.match(/(?:Sever|Server) Once Human:\s*(.+?)(?:\n|$)/i);
            if (serverMatch) {
                result.server = serverMatch[1].trim();
            }

            // Parse Region Once Human: "Region Once Human: Southeast Asia Server"
            const regionMatch = text.match(/Region Once Human:\s*(.+?)(?:\n|$)/i);
            if (regionMatch) {
                result.region = regionMatch[1].trim();
            }

            // Parse Product Name: "Tên sản phẩm: 6000 Echo Beads Where Winds Meet x 1"
            const productNameMatch = text.match(/Tên sản phẩm:\s*(.+?)(?:\n|$)/i);
            if (productNameMatch) {
                const productName = productNameMatch[1].trim();

                // Hàm normalize tên sản phẩm: loại bỏ các phần thừa
                function normalizeProductName(name) {
                    // Loại bỏ " x 1", " x 2" ở cuối
                    name = name.replace(/\s+x\s+\d+$/i, '').trim();
                    // Loại bỏ " ID", " Bản Mobile" ở cuối
                    name = name.replace(/\s+(ID|Bản Mobile)$/i, '').trim();
                    return name;
                }

                const cleanProductName = normalizeProductName(productName);

                // Tìm product_id từ danh sách products (tất cả products)
                for (let product of allProducts) {
                    if (!product.goodsinfo) continue;

                    // Normalize tên sản phẩm trong danh sách
                    const normalizedProductName = normalizeProductName(product.goodsinfo);

                    // So sánh tên đã normalize
                    if (normalizedProductName === cleanProductName ||
                        normalizedProductName.includes(cleanProductName) ||
                        cleanProductName.includes(normalizedProductName)) {
                        result.product_id = product.goodsid;
                        // Kiểm tra category dựa trên product_id
                        if (wwmProductIds.includes(product.goodsid)) {
                            result.category = 'where winds meet';
                        } else if (oneHumanProductIds.includes(product.goodsid)) {
                            result.category = 'one human';
                        }
                        break;
                    }
                }
            }

            return result;
        }

        // Hàm validate UID phải có đúng 10 chữ số
        function isValidUid(uid) {
            if (!uid) return false;
            // Kiểm tra UID phải là số và có đúng 10 chữ số
            return /^\d{10}$/.test(uid);
        }

        function updatePreview(parsed) {
            document.getElementById('preview_order_id').textContent = parsed.order_id || '-';

            // Hiển thị UID với cảnh báo nếu không hợp lệ
            const uidElement = document.getElementById('preview_uid');
            if (parsed.uid) {
                if (isValidUid(parsed.uid)) {
                    uidElement.textContent = parsed.uid;
                    uidElement.style.color = '';
                    uidElement.style.fontWeight = '';
                } else {
                    uidElement.textContent = parsed.uid + ' (⚠️ UID phải có đúng 10 chữ số)';
                    uidElement.style.color = '#dc3545';
                    uidElement.style.fontWeight = 'bold';
                }
            } else {
                uidElement.textContent = '-';
                uidElement.style.color = '';
                uidElement.style.fontWeight = '';
            }

            // Hiển thị tên sản phẩm thay vì product_id
            let productName = '-';
            if (parsed.product_id) {
                const product = allProducts.find(p => p.goodsid == parsed.product_id);
                if (product) {
                    productName = product.goodsinfo + ' (' + parsed.product_id + ')';
                } else {
                    productName = parsed.product_id;
                }
            }
            document.getElementById('preview_product_id').textContent = productName;
            document.getElementById('preview_category').textContent = parsed.category || '-';

            // Hiển thị server và region nếu có (chỉ cho One Human)
            if (parsed.server) {
                document.getElementById('preview_server').textContent = parsed.server;
                document.getElementById('preview_server_li').style.display = 'list-item';
            } else {
                document.getElementById('preview_server_li').style.display = 'none';
            }

            if (parsed.region) {
                document.getElementById('preview_region').textContent = parsed.region;
                document.getElementById('preview_region_li').style.display = 'list-item';
            } else {
                document.getElementById('preview_region_li').style.display = 'none';
            }

            // Hiển thị/ẩn preview và enable/disable submit button
            // Kiểm tra UID phải hợp lệ (10 chữ số)
            const isValid = parsed.order_id && parsed.uid && parsed.product_id && isValidUid(parsed.uid);
            if (parsed.order_id && parsed.uid && parsed.product_id) {
                parsedInfoDiv.style.display = 'block';
                submitBtn.disabled = !isValid;
                continueAddBtn.disabled = !isValid;
            } else {
                parsedInfoDiv.style.display = 'none';
                submitBtn.disabled = true;
                continueAddBtn.disabled = true;
            }
        }

        function handleInput() {
            const text = orderDataTextarea.value;
            if (text.trim()) {
                const parsed = parseOrderData(text);

                // Cập nhật hidden fields
                orderIdInput.value = parsed.order_id;
                uidInput.value = parsed.uid;
                productIdInput.value = parsed.product_id;
                categoryInput.value = parsed.category;
                serverInput.value = parsed.server;
                regionInput.value = parsed.region;

                // Cập nhật preview
                updatePreview(parsed);
            } else {
                parsedInfoDiv.style.display = 'none';
                submitBtn.disabled = true;
                orderIdInput.value = '';
                uidInput.value = '';
                productIdInput.value = '';
                categoryInput.value = '';
                serverInput.value = '';
                regionInput.value = '';
            }
        }

        // Xử lý khi paste hoặc nhập
        orderDataTextarea.addEventListener('paste', function(e) {
            setTimeout(handleInput, 10);
        });

        orderDataTextarea.addEventListener('input', handleInput);

        // Xử lý nút "Thêm tiếp"
        continueAddBtn.addEventListener('click', function() {
            const orderId = orderIdInput.value.trim();
            const uid = uidInput.value.trim();
            const productId = productIdInput.value.trim();

            if (!orderId || !uid || !productId) {
                alert('Vui lòng dán đầy đủ thông tin đơn hàng để hệ thống có thể nhận diện Order ID, UID và Product ID!');
                return false;
            }

            // Validate UID phải có đúng 10 chữ số
            if (!isValidUid(uid)) {
                alert('UID phải có đúng 10 chữ số! Ví dụ: 4059837621');
                return false;
            }

            // Set flag để tiếp tục thêm
            continueAddInput.value = '1';
            // Submit form
            document.getElementById('wwmOrderForm').submit();
        });

        // Validate trước khi submit
        document.getElementById('wwmOrderForm').addEventListener('submit', function(e) {
            const orderId = orderIdInput.value.trim();
            const uid = uidInput.value.trim();
            const productId = productIdInput.value.trim();

            if (!orderId || !uid || !productId) {
                e.preventDefault();
                alert('Vui lòng dán đầy đủ thông tin đơn hàng để hệ thống có thể nhận diện Order ID, UID và Product ID!');
                return false;
            }

            // Validate UID phải có đúng 10 chữ số
            if (!isValidUid(uid)) {
                e.preventDefault();
                alert('UID phải có đúng 10 chữ số! Ví dụ: 4059837621');
                return false;
            }

            // Nếu không phải nút "Thêm tiếp", reset flag
            if (continueAddInput.value !== '1') {
                continueAddInput.value = '0';
            }
        });

        // Clear form khi có success message (để sẵn sàng thêm tiếp)
        <?php if (isset($_GET['success'])): ?>
            setTimeout(function() {
                orderDataTextarea.value = '';
                orderIdInput.value = '';
                uidInput.value = '';
                productIdInput.value = '';
                categoryInput.value = '';
                serverInput.value = '';
                regionInput.value = '';
                continueAddInput.value = '0';
                parsedInfoDiv.style.display = 'none';
                submitBtn.disabled = true;
                continueAddBtn.disabled = true;
                orderDataTextarea.focus();
            }, 100);
        <?php endif; ?>
    });
</script>