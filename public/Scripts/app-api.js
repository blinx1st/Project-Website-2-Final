(function () {
    const baseUrl = (window.APP_BASE_URL || '').replace(/\/$/, '');
    const apiMessage = document.getElementById('api-message');

    function showMessage(message, ok) {
        if (!apiMessage) {
            alert(message);
            return;
        }
        apiMessage.style.display = 'block';
        apiMessage.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
        apiMessage.textContent = message;
    }

    async function readJson(response) {
        const payload = await response.json().catch(() => ({}));
        if (!response.ok || payload.success === false) {
            throw new Error(payload.message || 'Thao tác không thành công.');
        }
        return payload;
    }

    document.addEventListener('click', async function (event) {
        const confirmButton = event.target.closest('.js-confirm-attendance');
        if (confirmButton) {
            confirmButton.disabled = true;
            try {
                const body = new URLSearchParams({
                    MaSuKien: confirmButton.dataset.event || '',
                    MaThanhVien: confirmButton.dataset.member || ''
                });
                const payload = await fetch(baseUrl + '/Api_64131060/XacNhanThamGia', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body
                }).then(readJson);
                const row = confirmButton.closest('tr');
                const statusCell = row ? row.querySelector('.js-status-cell') : null;
                if (statusCell) {
                    statusCell.textContent = 'Đã tham gia';
                }
                confirmButton.remove();
                showMessage(payload.message || 'Đã xác nhận tham gia, cộng điểm và cấp chứng nhận.', true);
            } catch (error) {
                confirmButton.disabled = false;
                showMessage(error.message, false);
            }
            return;
        }

        const registerButton = event.target.closest('.js-register-event');
        if (registerButton) {
            registerButton.disabled = true;
            try {
                const url = baseUrl + '/Api_64131060/DangKySuKien?MaSuKien=' + encodeURIComponent(registerButton.dataset.event || '');
                const payload = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(readJson);
                showMessage(payload.message || 'Đăng ký sự kiện thành công.', true);
            } catch (error) {
                registerButton.disabled = false;
                showMessage(error.message, false);
            }
            return;
        }

        const cancelButton = event.target.closest('.js-cancel-registration');
        if (cancelButton) {
            cancelButton.disabled = true;
            try {
                const body = new URLSearchParams({ MaSuKien: cancelButton.dataset.event || '' });
                const payload = await fetch(baseUrl + '/Api_64131060/HuyDangKySuKien', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body
                }).then(readJson);
                showMessage(payload.message || 'Đã hủy đăng ký sự kiện.', true);
            } catch (error) {
                cancelButton.disabled = false;
                showMessage(error.message, false);
            }
        }
    });

    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form[data-validate-resource]');
        if (!form) {
            return;
        }
        const start = form.querySelector('[name="NgayBatDau"]');
        const end = form.querySelector('[name="NgayKetThuc"]');
        if (start && end && start.value && end.value && new Date(end.value) < new Date(start.value)) {
            event.preventDefault();
            showMessage('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.', false);
            end.focus();
            return;
        }
        const checkinOpen = form.querySelector('[name="CheckinMoLuc"]');
        const checkinClose = form.querySelector('[name="CheckinDongLuc"]');
        if (checkinOpen && checkinClose && checkinOpen.value && checkinClose.value && new Date(checkinClose.value) < new Date(checkinOpen.value)) {
            event.preventDefault();
            showMessage('Thời gian đóng QR phải sau hoặc bằng thời gian mở QR.', false);
            checkinClose.focus();
            return;
        }
        const year = form.querySelector('[name="NamHoc"]');
        if (year && year.value && !/^\d{4}-\d{4}$/.test(year.value)) {
            event.preventDefault();
            showMessage('Năm học phải có dạng 2024-2025.', false);
            year.focus();
            return;
        }
        const newPassword = form.querySelector('[name="MatKhauMoi"]');
        const confirmPassword = form.querySelector('[name="NhapLaiMatKhau"]');
        if (newPassword && confirmPassword && newPassword.value !== confirmPassword.value) {
            event.preventDefault();
            showMessage('Mật khẩu nhập lại không khớp.', false);
            confirmPassword.focus();
        }
    });
})();
