echo.channel('laravel_database_status')
    .listen('.status.updated', (e) => {
        // tìm tbody theo department_id
        const tbody = document.querySelector(`#queue-tbody-${e.department_id}`);
        if (!tbody) return;

        // nếu có row "Chưa có đăng ký" thì xoá
        const emptyRow = tbody.querySelector('td[colspan]');
        if (emptyRow) emptyRow.parentElement.remove();

        // tìm xem record đã có chưa
        let row = tbody.querySelector(`tr[data-id="${e.id}"]`);
        if (!row) {
            // tạo row mới
            row = document.createElement('tr');
            row.setAttribute('data-id', e.id);
            row.innerHTML = `
              <td class="text-center">
                <span class="badge bg-primary fs-6">${e.queue_number}</span>
              </td>
              <td class="text-truncate" title="${e.full_name}">
                ${e.full_name}
              </td>
              <td class="text-center status-cell">
                ${statusBadgeHtml(e.new_status)}
              </td>
            `;
            // prepend vào đúng tbody
            row.classList.add('fade-in');
            row.addEventListener('animationend', () => row.classList.remove('fade-in'), { once: true });
            tbody.prepend(row);
        } else {
            // update status nếu row đã có
            const cell = row.querySelector('.status-cell');
            if (cell) cell.innerHTML = statusBadgeHtml(e.new_status);

            // nếu completed thì fade-out rồi xoá
            if (e.new_status === 'completed') {
                row.classList.add('fade-out');
                row.addEventListener('animationend', () => row.remove(), { once: true });
            }
        }
    });

function statusBadgeHtml(status) {
    switch (status) {
        case 'pending':    return '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
        case 'received':   return '<span class="badge bg-info text-dark">Đã tiếp nhận</span>';
        case 'processing': return '<span class="badge bg-primary text-white">Đang xử lý</span>';
        case 'completed':  return '<span class="badge bg-success text-white">Hoàn thành</span>';
        case 'returned':   return '<span class="badge bg-secondary text-white">Trả hồ sơ</span>';
        default:           return '<span class="badge bg-secondary text-white">Không xác định</span>';
    }
}
