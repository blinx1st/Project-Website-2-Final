<section class="about-page">
    <div class="about-hero">
        <img src="<?= asset_url('Image/BannerVNU.jpg') ?>" alt="CLB Tin Học VNUIS">
        <div class="about-hero-content">
            <span class="about-kicker">VNUIS</span>
            <h1>Câu lạc bộ Tin học VNUIS - Tech</h1>
            <p>Nơi sinh viên yêu công nghệ cùng học tập, thực hành dự án, tổ chức sự kiện chuyên môn và ghi nhận hành trình rèn luyện trong suốt năm học.</p>
            <div class="about-actions">
                <a class="btn-main" href="<?= url_for('Login_64131060', 'Login_64131060') ?>">THAM GIA NGAY</a>
                <a class="btn-back" href="<?= url_for('TrangChu_64131060', $data['homeAction'] ?? 'TrangChu_64131060') ?>">QUAY VỀ</a>
            </div>
        </div>
    </div>

    <div class="about-stats">
        <div><strong>03+</strong><span>Nhóm học tập</span></div>
        <div><strong>10+</strong><span>Sự kiện mỗi năm</span></div>
        <div><strong>100%</strong><span>Ghi nhận điểm rèn luyện</span></div>
    </div>

    <div class="about-section">
        <div>
            <span class="about-kicker">Mục tiêu</span>
            <h2>Học thật, làm thật, kết nối thật</h2>
        </div>
        <p>CLB Tin học VNUIS - Tech tạo môi trường để sinh viên rèn kỹ năng lập trình, làm việc nhóm, thuyết trình và tham gia các hoạt động học thuật. Mỗi sự kiện, buổi học và hoạt động ngoại khóa đều được quản lý rõ ràng để sinh viên dễ đăng ký, theo dõi và nhận chứng nhận.</p>
    </div>

    <div class="about-grid">
        <article>
            <h3>Học tập chuyên môn</h3>
            <p>Các buổi học nhóm, workshop và chia sẻ kinh nghiệm giúp thành viên củng cố kiến thức nền tảng, tiếp cận công nghệ mới và thực hành qua bài tập thực tế.</p>
        </article>
        <article>
            <h3>Sự kiện và phong trào</h3>
            <p>CLB tổ chức cuộc thi, seminar, hoạt động giao lưu và các chương trình hỗ trợ sinh viên phát triển kỹ năng mềm lẫn kỹ năng nghề nghiệp.</p>
        </article>
        <article>
            <h3>Điểm rèn luyện</h3>
            <p>Hoạt động tham gia được xác nhận, cộng điểm theo học kỳ và hỗ trợ xuất dữ liệu khi tổng kết, giúp quá trình ghi nhận minh bạch hơn.</p>
        </article>
        <article>
            <h3>Chứng nhận tham gia</h3>
            <p>Sinh viên hoàn thành sự kiện có thể theo dõi chứng nhận của mình trên hệ thống, phục vụ hồ sơ học tập và hoạt động ngoại khóa.</p>
        </article>
    </div>

    <div class="about-split">
        <img src="<?= asset_url('Image/VNU.png') ?>" alt="InfoTech Club VNUIS">
        <div>
            <span class="about-kicker">Hệ thống quản lý</span>
            <h2>Cổng thông tin dành cho thành viên CLB</h2>
            <ul>
                <li>Đăng ký và theo dõi sự kiện đang mở.</li>
                <li>Điểm danh nhóm học tập và hoạt động ngoại khóa.</li>
                <li>Xem điểm rèn luyện, chứng nhận và tin tức CLB.</li>
                <li>Phân quyền riêng cho Chủ nhiệm, Trợ giảng và Thành viên.</li>
            </ul>
        </div>
    </div>
</section>