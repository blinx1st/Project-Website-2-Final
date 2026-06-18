<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($data['title'] ?? 'CLB Tin Học VNUIS') ?></title>
    <link rel="stylesheet" href="<?= asset_url('Content/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('Content/Site.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('Content/StyleAdmin.css') ?>">
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: #f5f7fb;
            color: #16213e;
        }

        a {
            text-decoration: none;
        }

        .topbar {
            background: #063b87;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .15);
        }

        .topbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .brand {
            color: #fff;
            font-weight: 800;
            display: flex;
            align-items: center;
            flex: 0 0 auto;
            gap: 9px;
            font-size: 18px;
        }

        .brand span {
            font-size: 20px;
            width: auto;
            white-space: nowrap;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .topbar-nav-area {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex: 1;
            gap: 12px;
            min-width: 0;
        }

        .navlinks {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
            flex: 1;
        }

        .navlinks a {
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            line-height: 1;
            padding: 8px 9px;
            border-radius: 6px;
            white-space: nowrap;
            transition: background .15s ease, color .15s ease;
        }

        .navlinks a:hover {
            color: #b9f15c;
            background: rgba(255, 255, 255, .1);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex: 0 0 auto;
        }

        .account-toggle,
        .login-pill {
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            min-height: 38px;
            padding: 7px 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            max-width: 240px;
            transition: background .15s ease, border-color .15s ease;
        }

        .account-toggle:hover,
        .account-toggle:focus,
        .login-pill:hover {
            color: #fff;
            background: rgba(255, 255, 255, .2);
            border-color: rgba(255, 255, 255, .42);
            outline: 0;
        }

        .account-greeting {
            font-weight: 600;
            opacity: .88;
        }

        .account-name {
            display: inline-block;
            max-width: 125px;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: bottom;
            white-space: nowrap;
        }

        .account-dropdown {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(11, 31, 70, .18);
            margin-top: 8px;
            min-width: 190px;
            padding: 7px;
        }

        .account-dropdown .dropdown-item {
            border-radius: 6px;
            color: #19345d;
            font-weight: 700;
            padding: 9px 11px;
        }

        .account-dropdown .dropdown-item:hover {
            background: #eef4ff;
            color: #063b87;
        }

        .account-dropdown .dropdown-item-danger {
            color: #c82333;
        }

        .account-dropdown .dropdown-item-danger:hover {
            background: #fff0f1;
            color: #a71d2a;
        }

        .page {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 18px;
            min-height: 60vh;
        }

        .panel {
            background: #fff;
            border: 1px solid #dce4f2;
            border-radius: 8px;
            padding: 22px;
            box-shadow: 0 6px 18px rgba(15, 42, 80, .08);
        }

        .page-title {
            font-weight: 800;
            color: #063b87;
            margin-bottom: 18px;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .btn-main,
        .btn-back,
        .btn-danger-soft {
            border: 0;
            border-radius: 6px;
            padding: 9px 14px;
            font-weight: 700;
            display: inline-block;
        }

        .btn-main {
            background: #0d6efd;
            color: #fff;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
        }

        .btn-danger-soft {
            background: #dc3545;
            color: #fff;
        }

        td {
            vertical-align: middle;
        }

        .thumb {
            width: 110px;
            max-height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .form-field label {
            font-weight: 700;
            margin-bottom: 6px;
            display: block;
            width: 100%;
            padding-left: .75rem;
            text-align: left;
            box-sizing: border-box;
            transform: translateX(160px);
        }

        .form-field textarea {
            min-height: 120px;
        }

        .mail-panel {
            max-width: 850px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .mail-panel .page-title {
            width: 100%;
            text-align: center;
        }

        .mail-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
            max-width: 640px;
            align-items: center;
        }

        .mail-stack {
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
            align-items: center;
        }

        .mail-panel .form-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            width: 100%;
            max-width: 620px;
            min-width: 0;
            align-items: center;
        }

        .mail-panel .form-field label {
            width: 100%;
            margin-bottom: 0;
            color: #19345d;
            line-height: 1.25;
        }

        .mail-panel .form-control {
            width: 100%;
            min-height: 44px;
            border-color: #cfd9ea;
            border-radius: 7px;
            box-shadow: none;
        }

        .mail-panel .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, .12);
        }

        .mail-panel .form-control[readonly] {
            background: #f2f6fc;
            color: #344767;
        }

        .mail-panel textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .mail-actions {
            width: 100%;
            max-width: 620px;
            justify-content: center;
            margin: 2px 0 0;
        }

        .mail-actions .btn-main {
            min-width: 170px;
            text-align: center;
        }

        .footer {
            background: #063b87;
            color: #fff;
            margin-top: 42px;
            padding: 22px;
            text-align: center;
        }

        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .login-box {
            max-width: 980px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: minmax(260px, 1fr) minmax(260px, 1fr);
            gap: 24px;
            align-items: center;
        }

        .login-box img {
            width: 100%;
            border-radius: 8px;
        }

        @media (max-width: 720px) {

            .topbar-inner,
            .topbar-nav-area,
            .navlinks,
            .topbar-actions {
                align-items: flex-start;
                justify-content: flex-start;
            }

            .topbar-inner {
                flex-direction: column;
                padding: 10px 14px;
            }

            .topbar-nav-area {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }

            .navlinks {
                width: 100%;
            }

            .navlinks a {
                font-size: 12px;
                padding: 7px 8px;
            }

            .topbar-actions,
            .account-menu,
            .account-toggle,
            .login-pill {
                width: 100%;
            }

            .account-toggle,
            .login-pill {
                max-width: none;
            }

            .account-dropdown {
                width: 100%;
            }

            .login-box {
                grid-template-columns: 1fr;
            }

            .mail-actions {
                justify-content: stretch;
            }

            .mail-actions .btn-main {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="<?= url_for('TrangChu_64131060', current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : (current_role() === 'TV' ? 'MemberPage_64131060' : 'TrangChu_64131060'))) ?>">
                <img src="<?= asset_url('image/Logo_EmptyVNU.png') ?>" alt="Logo">
                <span>VNU-IS</span>
            </a>
            <div class="topbar-nav-area">
                <nav class="navlinks">
                    <?php if (current_role() === 'TVCN'): ?>
                        <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_AdminPage_64131060') ?>">GIỚI THIỆU</a>
                        <a href="<?= url_for('BaiDang_Admin_64131060', 'BaiDang_Admin_64131060') ?>">TIN TỨC</a>
                        <a href="<?= url_for('CLB_Admin_64131060', 'CLB_Admin_64131060') ?>">CLB</a>
                        <a href="<?= url_for('SuKien_Admin_64131060', 'TimKiemSuKien_Admin_64131060') ?>">SỰ KIỆN</a>
                        <a href="<?= url_for('CheckinSuKien_Admin_64131060', 'CheckinSuKien_Admin_64131060') ?>">CHECK-IN</a>
                        <a href="<?= url_for('DiemRenLuyen_Admin_64131060', 'DiemRenLuyen_Admin_64131060') ?>">ĐIỂM RÈN LUYỆN</a>
                        <a href="<?= url_for('ChungNhan_Admin_64131060', 'ChungNhan_Admin_64131060') ?>">CHỨNG NHẬN</a>
                        <a href="<?= url_for('BaoCao_Admin_64131060', 'ThongKe') ?>">BÁO CÁO</a>
                        <a href="<?= url_for('ThanhVien_Admin_64131060', 'TimKiemTV_Admin_64131060') ?>">THÀNH VIÊN</a>
                        <a href="<?= url_for('DiemDanh_Admin_64131060', 'Create') ?>">ĐIỂM DANH</a>
                        <a href="<?= url_for('Email_64131060', 'SendMail_Admin_64131060') ?>">MAIL</a>
                        <a href="<?= url_for('ThanhVien_Admin_64131060', 'Admin_Page_64131060') ?>">TRANG CÁ NHÂN</a>
                    <?php elseif (current_role() === 'TVTG'): ?>
                        <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_AssitantPage_64131060') ?>">GIỚI THIỆU</a>
                        <a href="<?= url_for('BaiDang_Assitant_64131060', 'BaiDang_Assitant_64131060') ?>">TIN TỨC</a>
                        <a href="<?= url_for('CLB_Assitant_64131060', 'CLB_Assitant_64131060') ?>">CLB</a>
                        <a href="<?= url_for('SuKien_Assitant_64131060', 'TimKiemSuKien_Assitant_64131060') ?>">SỰ KIỆN</a>
                        <a href="<?= url_for('CheckinSuKien_Assitant_64131060', 'CheckinSuKien_Assitant_64131060') ?>">CHECK-IN</a>
                        <a href="<?= url_for('DiemRenLuyen_Assitant_64131060', 'DiemRenLuyen_Assitant_64131060') ?>">ĐIỂM RÈN LUYỆN</a>
                        <a href="<?= url_for('ChungNhan_Assitant_64131060', 'ChungNhan_Assitant_64131060') ?>">CHỨNG NHẬN</a>
                        <a href="<?= url_for('ThanhVien_Assitant_64131060', 'TimKiemTV_Assitant_64131060') ?>">THÀNH VIÊN</a>
                        <a href="<?= url_for('DiemDanh_Assitant_64131060', 'Create') ?>">ĐIỂM DANH</a>
                        <a href="<?= url_for('Email_64131060', 'SendMail_Asstant_64131060') ?>">MAIL</a>
                        <a href="<?= url_for('ThanhVien_Assitant_64131060', 'Assitant_Page_64131060') ?>">TRANG CÁ NHÂN</a>
                    <?php elseif (current_role() === 'TV'): ?>
                        <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_MemberPage_64131060') ?>">GIỚI THIỆU</a>
                        <a href="<?= url_for('BaiDang_Member_64131060', 'BaiDang_Member_64131060') ?>">TIN TỨC</a>
                        <a href="<?= url_for('SuKien_Member_64131060', 'TimKiemSuKien_Member_64131060') ?>">SỰ KIỆN</a>
                        <a href="<?= url_for('ThanhVienSuKien_Member_64131060', 'ThanhVienSuKien_Member_64131060') ?>">LỊCH SỬ</a>
                        <a href="<?= url_for('CheckinSuKien_Member_64131060', 'CheckinSuKien_Member_64131060') ?>">CHECK-IN</a>
                        <a href="<?= url_for('DiemRenLuyen_Member_64131060', 'DiemRenLuyen_Member_64131060') ?>">ĐIỂM RÈN LUYỆN</a>
                        <a href="<?= url_for('ChungNhan_Member_64131060', 'ChungNhan_Member_64131060') ?>">CHỨNG NHẬN</a>
                        <a href="<?= url_for('DiemDanh_Member_64131060', 'Create') ?>">ĐIỂM DANH</a>
                        <a href="<?= url_for('Email_64131060', 'SendMail_Member_64131060') ?>">MAIL</a>
                        <a href="<?= url_for('ThanhVien_Member_64131060', 'Member_Page_64131060') ?>">TRANG CÁ NHÂN</a>
                    <?php else: ?>
                        <a href="<?= url_for('TrangChu_64131060', 'TrangChu_64131060') ?>">TRANG CHỦ</a>
                        <a href="<?= url_for('TrangChu_64131060', 'GioiThieu_64131060') ?>">GIỚI THIỆU</a>
                        <a href="<?= url_for('BaiDang_64131060', 'BaiDang_64131060') ?>">TIN TỨC</a>
                        <a href="<?= url_for('SuKien_64131060', 'TimKiemSuKien_64131060') ?>">SỰ KIỆN</a>
                    <?php endif; ?>
                </nav>
                <div class="topbar-actions">
                    <?php if (current_role()): ?>
                        <div class="dropdown account-menu">
                            <button class="account-toggle dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="account-greeting">Xin chào,</span>
                                <span class="account-name"><?= h(current_user_name()) ?></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end account-dropdown" aria-labelledby="accountDropdown">
                                <a class="dropdown-item" href="<?= url_for('Login_64131060', 'DoiMatKhau_64131060') ?>">Đổi mật khẩu</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item dropdown-item-danger" href="<?= url_for('Login_64131060', 'Logout_64131060') ?>">Đăng xuất</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a class="login-pill" href="<?= url_for('Login_64131060', 'Login_64131060') ?>">Đăng nhập</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="page"><?= $content ?></main>
    <footer class="footer">CLB TIN HỌC KHOA CNTT - VNUIS | Copyright &copy; VNUIS - <?= date('Y') ?></footer>
    <script>
        window.APP_BASE_URL = "<?= h(base_url()) ?>";
    </script>
    <script src="<?= asset_url('Scripts/jquery-3.4.1.min.js') ?>"></script>
    <script src="<?= asset_url('Scripts/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= asset_url('Scripts/app-api.js') ?>"></script>
</body>

</html>