<?php
require_once 'app/config/database.php';
require_once 'app/models/Registration.php';
require_once 'app/controllers/RegisterController.php';

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();
    
    if ($db) {
        $controller = new RegisterController($db);
        $result = $controller->register();
        $message = $result['message'];
        $status = $result['status'];
    } else {
        $message = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน - Phichai Run 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#E30613',
                        secondary: '#8B0000',
                        accent: '#FFD700'
                    }
                }
            }
        }
    </script>
    <!-- jQuery + Select2 for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Tidy Select2 results as compact card-like rows using Tailwind-compatible classes */
        .select2-container .select2-results__option .select2-result-item { padding: .5rem; border-radius: .375rem; }
        .select2-container--open .select2-results__option.select2-results__option--highlighted .select2-result-item { background: rgba(227,6,19,0.06); }
        .select2-container .select2-selection__rendered { line-height: 1.2; }
        .select2-container .select2-selection__choice { background: #fee2e2; border-color: #fecaca; }
        .select2-container .select2-results__option .text-sm { font-size: .85rem; color: #6b7280; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></style>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-50 bg-secondary text-white py-4 shadow-lg">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold tracking-wider flex items-center gap-2">
                <img src="assets/images/logo-1.png" alt="Logo" class="h-10 w-10 rounded-full border-2 border-accent">
                PHICHAI RUN 2026
            </a>
            <a href="index.php" class="text-white hover:text-accent transition">
                <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
            </a>
        </div>
    </nav>

    <div class="pt-24 pb-12 min-h-screen flex items-center justify-center bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
        <div class="container mx-auto px-4 max-w-3xl">
            
            <?php if ($message): ?>
                <div class="mb-8 p-4 rounded-lg <?php echo $status === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?> text-center">
                    <i class="fas <?php echo $status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> text-xl mr-2"></i>
                    <span class="font-bold"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-primary p-6 text-center text-white">
                    <h2 class="text-3xl font-bold">ลงทะเบียนเข้าร่วมงาน</h2>
                    <p class="opacity-90">กรอกข้อมูลให้ครบถ้วนเพื่อสมัครวิ่ง</p>
                </div>
                
                <form id="regForm" action="register.php" method="POST" enctype="multipart/form-data" class="p-8">
                    <!-- Progress / Steps indicator -->
                    <div class="mb-6">
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div id="progressBar" class="h-2 bg-primary rounded-full" style="width:20%"></div>
                        </div>
                        <div class="flex justify-between text-sm mt-2 text-gray-600">
                            <div>Step 1</div>
                            <div>Step 2</div>
                            <div>Step 3</div>
                            <div>Step 4</div>
                            <div>Step 5</div>
                        </div>
                    </div>

                    <!-- Hidden compatibility fields -->
                    <input type="hidden" name="full_name" id="hidden_full_name" value="">
                    <input type="hidden" name="bib_name" id="hidden_bib_name" value="">
                    <input type="hidden" name="emergency_contact_name" id="hidden_emg_name" value="">
                    <input type="hidden" name="emergency_contact_phone" id="hidden_emg_phone" value="">

                    <!-- STEP 1: Personal -->
                    <div class="form-step" data-step="1">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2 mb-4">ข้อมูลส่วนตัว</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">คำนำหน้า</label>
                                <select name="prefix" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                    <option value="">เลือกคำนำหน้า</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                    <option value="อื่นๆ">อื่นๆ</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">ชื่อ (ภาษาไทย)</label>
                                <input type="text" name="first_name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="รักดี">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">นามสกุล (ภาษาไทย)</label>
                                <input type="text" name="last_name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="มีชัย">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="button" class="next-btn bg-primary text-white font-bold py-2 px-6 rounded-lg">ถัดไป</button>
                        </div>
                    </div>

                    <!-- STEP 2: Contact/Address -->
                    <div class="form-step hidden" data-step="2">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2 mb-4">ข้อมูลติดต่อ</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">เบอร์โทรศัพท์</label>
                                <input type="tel" name="phone" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="08x-xxx-xxxx">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">อีเมล</label>
                                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="example@email.com">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-gray-700 font-bold mb-2">ที่อยู่จัดส่งเอกสาร</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm text-gray-600">รหัสไปรษณีย์</label>
                                    <input type="text" id="postal_code" name="postal_code" maxlength="5" pattern="\d{5}" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="เช่น 54000">
                                    <p id="postal_help" class="text-sm text-gray-500 mt-1">กรอกรหัสไปรษณีย์ 5 หลัก เพื่อค้น ตำบล/อำเภอ/จังหวัด อัตโนมัติ</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">ตำบล (Tambon)</label>
                                    <input type="text" id="subdistrict" name="subdistrict"  placeholder="ตำบล" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">อำเภอ / จังหวัด</label>
                                    <input type="text" id="district_province" name="district_province" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-100" placeholder="อำเภอ / จังหวัด">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="text-sm text-gray-600">ที่อยู่ (บ้านเลขที่ ถนน หมู่บ้าน)</label>
                                <textarea id="address" name="address" rows="2" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="บ้านเลขที่, ถนน, หมู่บ้าน"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">ก่อนหน้า</button>
                            <button type="button" class="next-btn bg-primary text-white font-bold py-2 px-6 rounded-lg">ถัดไป</button>
                        </div>
                    </div>

                    <!-- STEP 3: Race/Signup Type -->
                    <div class="form-step hidden" data-step="3">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2 mb-4">ข้อมูลการสมัคร</h3>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">ประเภทการสมัคร</label>
                            <select name="category" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                <option value="">เลือกประเภทการสมัคร</option>
                                <option value="Student Run 3.5KM">Student Run 3.5 km</option>
                                <option value="Charity Walk 3.5KM">เดินการกุศล 3.5 km</option>
                                <option value="Fun Run 5KM">Fun Run 5 km</option>
                                <option value="Mini Marathon 11.5KM">Mini Marathon 11.5 km</option>
                                <option value="Merch Shirt 250THB">สั่งซื้อเสื้อที่ระลึก 250 บาท</option>
                            </select>
                        </div>

                        <!-- Placeholder for conditional fields depending on category -->
                        <div id="category-conditional" class="mt-4">
                            <!-- Conditions will be injected here later -->
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">ก่อนหน้า</button>
                            <button type="button" class="next-btn bg-primary text-white font-bold py-2 px-6 rounded-lg">ถัดไป</button>
                        </div>
                    </div>

                    <!-- STEP 4: Shirt / Extras -->
                    <div class="form-step hidden" data-step="4">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2 mb-4">ไซส์เสื้อ / รายการพิเศษ</h3>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">ไซส์เสื้อ</label>
                            <select name="shirt_size" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                <option value="">เลือกไซส์</option>
                                <option value="XS">XS (34")</option>
                                <option value="S">S (36")</option>
                                <option value="M">M (38")</option>
                                <option value="L">L (40")</option>
                                <option value="XL">XL (42")</option>
                                <option value="2XL">2XL (44")</option>
                                <option value="3XL">3XL (46")</option>
                            </select>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">ก่อนหน้า</button>
                            <button type="button" class="next-btn bg-primary text-white font-bold py-2 px-6 rounded-lg">ถัดไป</button>
                        </div>
                    </div>

                    <!-- STEP 5: Payment -->
                    <div class="form-step hidden" data-step="5">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2 mb-4">หลักฐานการชำระเงิน</h3>
                        <div class="bg-gray-100 p-4 rounded-lg mb-4">
                            <p class="font-bold text-gray-700">โอนเงินเข้าบัญชี: ธนาคารกสิกรไทย</p>
                            <p class="text-gray-600">ชื่อบัญชี: Phichai Run 2026</p>
                            <p class="text-gray-600">เลขที่บัญชี: 123-4-56789-0</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">แนบสลิปโอนเงิน</label>
                            <input type="file" name="payment_slip" accept="image/*" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary bg-white">
                            <p class="text-sm text-gray-500 mt-1">รองรับไฟล์ภาพ .jpg, .jpeg, .png</p>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">ก่อนหน้า</button>
                            <button type="submit" class="bg-primary text-white font-bold py-2 px-6 rounded-lg">ยืนยันการสมัคร</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-gray-900 text-white py-6 text-center">
        <p>&copy; 2025 Phichai Run. All rights reserved.</p>
    </footer>

    <script>
        // Simple postal code lookup: tries zippopotam.us and handles failures gracefully.
        // If you prefer a different Thai postal API, replace the URL below.
        (function() {
            const postalInput = document.getElementById('postal_code');
            let subdistrictInput = document.getElementById('subdistrict'); // may remain input or be used alongside a dynamic select
            const districtProvinceInput = document.getElementById('district_province');
            const postalHelp = document.getElementById('postal_help');

            if (!postalInput) return;

            // lazy-load local Thai postal code map (used first)
            // the JSON file in assets/data is an array of objects; we'll fetch it only on first lookup and convert into a map keyed by 5-digit zipcode
            let thaiPostalMap = null; // will be { '10100': [ { subdistrict, district, province }, ... ], ... }
            async function loadLocalPostal() {
                if (thaiPostalMap !== null) return; // already loaded or attempted
                try {
                    const r = await fetch('assets/data/th_postal_codes.json');
                    if (!r.ok) throw new Error('fetch-failed');
                    const data = await r.json();
                    const map = {};
                    if (Array.isArray(data)) {
                        // build arrays of entries per zipcode
                        data.forEach(item => {
                            if (!item || !item.zipcode) return;
                            const code = String(item.zipcode).padStart(5, '0');
                            const entry = {
                                subdistrict: (item.district || '').trim(),
                                district: (item.amphoe || '').trim(),
                                province: (item.province || '').trim()
                            };
                            if (!map[code]) map[code] = [];
                            map[code].push(entry);
                        });

                        // dedupe and sort each list: unique by subdistrict|district|province, sort by province->district->subdistrict
                        Object.keys(map).forEach(code => {
                            const seen = new Set();
                            const list = [];
                            map[code].forEach(it => {
                                const key = [it.subdistrict, it.district, it.province].join('|');
                                if (!seen.has(key)) {
                                    seen.add(key);
                                    list.push(it);
                                }
                            });
                            list.sort((a,b) => {
                                if (a.province !== b.province) return a.province.localeCompare(b.province);
                                if (a.district !== b.district) return a.district.localeCompare(b.district);
                                return a.subdistrict.localeCompare(b.subdistrict);
                            });
                            map[code] = list;
                        });
                    } else if (typeof data === 'object' && data !== null) {
                        // if already a map of arrays, normalize values to arrays
                        Object.keys(data).forEach(k => {
                            const v = data[k];
                            if (Array.isArray(v)) map[k] = v.map(it => ({ subdistrict: (it.subdistrict||'').trim(), district: (it.district||'').trim(), province: (it.province||'').trim() }));
                        });
                    }
                    thaiPostalMap = map;
                } catch (e) {
                    console.warn('Failed to load local Thai postal map', e);
                    thaiPostalMap = {}; // avoid retrying repeatedly in same session
                }
            }

            async function lookupPostal(postal) {
                // basic validation
                if (!/^[0-9]{5}$/.test(postal)) {
                    if (postalHelp) postalHelp.textContent = 'รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก';
                    subdistrictInput.value = '';
                    districtProvinceInput.value = '';
                    // allow manual input
                    // if a dynamic select exists, remove it and restore input
                    const existingSelect = document.getElementById('subdistrict_select');
                    if (existingSelect) {
                        existingSelect.remove();
                        subdistrictInput.name = 'subdistrict';
                        subdistrictInput.classList.remove('hidden');
                    }
                    subdistrictInput.readOnly = false;
                    districtProvinceInput.readOnly = false;
                    subdistrictInput.classList.remove('bg-gray-100');
                    districtProvinceInput.classList.remove('bg-gray-100');
                    return;
                }

                if (postalHelp) postalHelp.textContent = 'กำลังค้นหาข้อมูลที่อยู่...';

                // 1) Try local Thai postal map (lazy load if needed)
                // first check sessionStorage cache for this postal specifically
                let entries = null;
                try {
                    const cached = sessionStorage.getItem('postal_' + postal);
                    if (cached) entries = JSON.parse(cached);
                } catch (e) {
                    // ignore storage errors
                }

                if (!entries) {
                    if (thaiPostalMap === null) {
                        await loadLocalPostal();
                    }
                    if (thaiPostalMap && thaiPostalMap[postal]) {
                        entries = thaiPostalMap[postal];
                        try { sessionStorage.setItem('postal_' + postal, JSON.stringify(entries)); } catch(e){}
                    }
                }

                if (entries) {
                    // entries is an array of { subdistrict, district, province }
                    if (Array.isArray(entries) && entries.length > 1) {
                        // create a select so user can choose correct subdistrict
                        // remove any previous select
                        const existingSelect = document.getElementById('subdistrict_select');
                        if (existingSelect) existingSelect.remove();

                        // create select element
                        const sel = document.createElement('select');
                        sel.id = 'subdistrict_select';
                        sel.name = 'subdistrict';
                        sel.className = subdistrictInput.className + ' mt-1';
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = 'เลือกตำบล (ตำแหน่งที่พบ ' + entries.length + ' รายการ)';
                        sel.appendChild(placeholder);

                        // keep a Set to avoid duplicates
                        const seen = new Set();
                        entries.forEach((it) => {
                            const name = (it.subdistrict || '').trim();
                            if (!name || seen.has(name)) return;
                            seen.add(name);
                            const opt = document.createElement('option');
                            opt.value = name;
                            opt.textContent = name + ' — ' + (it.district || '') + (it.province ? (' / ' + it.province) : '');
                            // encode amphoe/province data on option for later
                            opt.dataset.amphoe = it.district || '';
                            opt.dataset.province = it.province || '';
                            sel.appendChild(opt);
                        });

                        // container: insert after the original input; we'll initialize Select2 for searchable dropdown
                        const container = document.createElement('div');
                        container.id = 'subdistrict_container';
                        container.className = 'mt-1';
                        container.appendChild(sel);
                        subdistrictInput.parentNode.insertBefore(container, subdistrictInput.nextSibling);
                        // remove name from original input so form posts the select value
                        subdistrictInput.name = '';
                        // hide original input visually but keep it in DOM
                        subdistrictInput.classList.add('hidden');

                        // initialize Select2 if available
                        if (window.jQuery && typeof jQuery().select2 === 'function') {
                            // destroy previous instance if any
                            if (window.jQuery && jQuery('#subdistrict_select').data('select2')) {
                                jQuery('#subdistrict_select').select2('destroy');
                            }
                            jQuery(sel).select2({
                                placeholder: 'เลือกตำบล (พิมพ์เพื่อค้น)',
                                width: '100%',
                                allowClear: true,
                                language: {
                                    noResults: function() { return 'ไม่พบผลลัพธ์'; },
                                    searching: function() { return 'กำลังค้นหา...'; }
                                },
                                escapeMarkup: function(markup) { return markup; }, // allow custom HTML
                                templateResult: function(data) {
                                    if (!data.id) return data.text; // placeholder or empty
                                    const el = data.element;
                                    const amphoe = el ? (el.dataset.amphoe || '') : '';
                                    const province = el ? (el.dataset.province || '') : '';
                                    const sub = data.text || '';
                                    const extra = (amphoe || province) ? ('<div class="text-sm text-gray-500">' + (amphoe ? amphoe : '') + (province ? (' / ' + province) : '') + '</div>') : '';
                                    return '<div class="select2-result-item"><div class="font-medium">' + sub + '</div>' + extra + '</div>';
                                },
                                templateSelection: function(data) {
                                    if (!data.id) return data.text;
                                    // show subdistrict only (keeps selection compact), append amphoe/province lightly
                                    const el = data.element;
                                    const amphoe = el ? (el.dataset.amphoe || '') : '';
                                    const province = el ? (el.dataset.province || '') : '';
                                    const sub = data.text || '';
                                    if (amphoe || province) return sub + ' — ' + amphoe + (province ? (' / ' + province) : '');
                                    return sub;
                                }
                            });

                            // when selection changes via select2, update district/province
                            jQuery(sel).on('change', function() {
                                const o = this.options[this.selectedIndex];
                                const amphoe = o && o.dataset ? o.dataset.amphoe : '';
                                const province = o && o.dataset ? o.dataset.province : '';
                                districtProvinceInput.value = ((amphoe || '') + (province ? (' / ' + province) : '')).trim();
                                districtProvinceInput.readOnly = true;
                                districtProvinceInput.classList.add('bg-gray-100');
                            });
                        } else {
                            // fallback: basic select change handler
                            sel.addEventListener('change', (e) => {
                                const o = sel.options[sel.selectedIndex];
                                const amphoe = o && o.dataset ? o.dataset.amphoe : '';
                                const province = o && o.dataset ? o.dataset.province : '';
                                districtProvinceInput.value = ((amphoe || '') + (province ? (' / ' + province) : '')).trim();
                                districtProvinceInput.readOnly = true;
                                districtProvinceInput.classList.add('bg-gray-100');
                            });
                        }

                        if (postalHelp) postalHelp.textContent = 'พบหลายตำบลสำหรับรหัสนี้ — โปรดเลือกตำบลที่ถูกต้อง (พิมพ์เพื่อค้น)';
                        return;
                    }

                    // single entry: use the input field and populate read-only
                    const p = Array.isArray(entries) ? entries[0] : entries;
                    // remove any dynamic select if present
                    const existingSelect2 = document.getElementById('subdistrict_select');
                    const existingContainer = document.getElementById('subdistrict_container');
                    if (existingSelect2) {
                        // if select2 instance exists, destroy it first
                        try {
                            if (window.jQuery && jQuery(existingSelect2).data('select2')) {
                                jQuery(existingSelect2).select2('destroy');
                            }
                        } catch (e) {}
                        if (existingContainer) existingContainer.remove();
                        subdistrictInput.classList.remove('hidden');
                        subdistrictInput.name = 'subdistrict';
                    }

                    subdistrictInput.value = p.subdistrict || '';
                    districtProvinceInput.value = ((p.district || '') + (p.province ? (' / ' + p.province) : '')).trim();
                    subdistrictInput.readOnly = true;
                    districtProvinceInput.readOnly = true;
                    subdistrictInput.classList.add('bg-gray-100');
                    districtProvinceInput.classList.add('bg-gray-100');
                    if (postalHelp) postalHelp.textContent = 'พบข้อมูลจากฐานข้อมูลภายใน — ตรวจสอบความถูกต้องก่อนส่ง';
                    return;
                }

                // 2) Fallback to public API (may not cover all Thai codes) - keep for extra coverage
                try {
                    const resp = await fetch('https://api.zippopotam.us/th/' + postal);
                    if (!resp.ok) throw new Error('not-found');
                    const data = await resp.json();
                    console.debug('postal lookup data (external):', data);
                    if (data && data.places && data.places.length) {
                        const place = data.places[0];
                        subdistrictInput.value = place['place name'] || '';
                        districtProvinceInput.value = (place['state'] ? place['state'] : '') + (place['county'] ? (' / ' + place['county']) : '');
                        subdistrictInput.readOnly = true;
                        districtProvinceInput.readOnly = true;
                        subdistrictInput.classList.add('bg-gray-100');
                        districtProvinceInput.classList.add('bg-gray-100');
                        if (postalHelp) postalHelp.textContent = 'พบข้อมูลจากบริการภายนอก — ตรวจสอบความถูกต้องก่อนส่ง';
                        return;
                    }
                    throw new Error('no-data');
                } catch (err) {
                    console.warn('postal lookup failed:', err);
                    // fallback: allow manual input and show message
                    subdistrictInput.value = '';
                    districtProvinceInput.value = '';
                    subdistrictInput.readOnly = false;
                    districtProvinceInput.readOnly = false;
                    subdistrictInput.classList.remove('bg-gray-100');
                    districtProvinceInput.classList.remove('bg-gray-100');
                    if (postalHelp) postalHelp.textContent = 'ไม่พบข้อมูลอัตโนมัติสำหรับรหัสนี้ — กรุณากรอก ตำบล/อำเภอ/จังหวัด ด้วยตนเอง';
                }
            }

            let timeout = null;
            postalInput.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const v = e.target.value.trim();
                    if (v.length === 5) {
                        lookupPostal(v);
                    } else {
                        subdistrictInput.value = '';
                        districtProvinceInput.value = '';
                        postalHelp.textContent = 'รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก';
                    }
                }, 500);
            });

            // calculate age from birth_date and compose hidden full_name before submit to keep backend compatibility
            const birthInput = document.querySelector('input[name="birth_date"]');
            const ageInput = document.querySelector('input[name="age"]');
            if (birthInput && ageInput) {
                const calcAge = (dob) => {
                    const today = new Date();
                    const birth = new Date(dob);
                    if (isNaN(birth)) return '';
                    let age = today.getFullYear() - birth.getFullYear();
                    const m = today.getMonth() - birth.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                        age--;
                    }
                    return age;
                };

                birthInput.addEventListener('change', (e) => {
                    const v = e.target.value;
                    const a = calcAge(v);
                    if (a !== '' && !isNaN(a)) {
                        ageInput.value = a;
                    }
                });
            }

            // compose hidden full_name before submit to keep backend compatibility
            const form = document.getElementById('regForm');
            if (form) {
                form.addEventListener('submit', (ev) => {
                    const prefix = (document.querySelector('select[name="prefix"]') || {}).value || '';
                    const first = (document.querySelector('input[name="first_name"]') || {}).value || '';
                    const last = (document.querySelector('input[name="last_name"]') || {}).value || '';
                    const hiddenFull = document.getElementById('hidden_full_name');
                    if (hiddenFull) hiddenFull.value = [prefix, first, last].filter(Boolean).join(' ').trim();

                    // ensure hidden bib_name exists (left empty)
                    const hiddenBib = document.getElementById('hidden_bib_name');
                    if (hiddenBib && !hiddenBib.value) hiddenBib.value = '';

                    // also set hidden emergency contact fields (kept empty)
                    const hiddenEmgName = document.getElementById('hidden_emg_name');
                    const hiddenEmgPhone = document.getElementById('hidden_emg_phone');
                    if (hiddenEmgName) hiddenEmgName.value = '';
                    if (hiddenEmgPhone) hiddenEmgPhone.value = '';
                });

                // Stepper logic
                const steps = Array.from(document.querySelectorAll('.form-step'));
                let currentStep = 0; // 0-based index

                const updateSteps = () => {
                    steps.forEach((s, idx) => {
                        if (idx === currentStep) {
                            s.classList.remove('hidden');
                        } else {
                            s.classList.add('hidden');
                        }
                    });
                    const progress = Math.round(((currentStep + 1) / steps.length) * 100);
                    const progressBar = document.getElementById('progressBar');
                    if (progressBar) progressBar.style.width = progress + '%';
                };

                // attach next/prev handlers
                document.addEventListener('click', (e) => {
                    if (e.target.matches('.next-btn')) {
                        // basic validation for current step: required fields
                        const currentFields = steps[currentStep].querySelectorAll('[required]');
                        for (const f of currentFields) {
                            if (!f.value) {
                                f.focus();
                                return; // stop advancing
                            }
                        }
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateSteps();
                        }
                    }
                    if (e.target.matches('.prev-btn')) {
                        if (currentStep > 0) {
                            currentStep--;
                            updateSteps();
                        }
                    }
                });

                // category change placeholder for conditional fields
                const categorySelect = document.querySelector('select[name="category"]');
                const conditionalDiv = document.getElementById('category-conditional');
                if (categorySelect && conditionalDiv) {
                    categorySelect.addEventListener('change', (ev) => {
                        // clear
                        conditionalDiv.innerHTML = '';
                        const v = ev.target.value;
                        // placeholder logic: inject example fields depending on selection
                        if (v === 'Student Run 3.5KM') {
                            conditionalDiv.innerHTML = '<p class="text-sm text-gray-600">กรุณาอัพโหลดสำเนาบัตรนักเรียน (ถ้ามี)</p><input type="file" name="student_id_file" accept="image/*" class="mt-2">';
                        } else if (v === 'Merch Shirt 250THB') {
                            conditionalDiv.innerHTML = '<p class="text-sm text-gray-600">คุณกำลังสั่งซื้อเสื้อที่ระลึก</p>';
                        }
                    });
                }

                // initialize
                updateSteps();
            }
        })();
    </script>
</body>
</html>
