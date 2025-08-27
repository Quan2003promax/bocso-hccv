function statusBadgeHtml(status) {
    switch (status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
        case 'received':
            return '<span class="badge bg-info text-dark">Đã tiếp nhận</span>';
        case 'processing':
            return '<span class="badge bg-primary text-white">Đang xử lý</span>';
        case 'completed':
            return '<span class="badge bg-success text-white">Hoàn thành</span>';
        case 'returned':
            return '<span class="badge bg-secondary text-white">Trả hồ sơ</span>';
        default:
            return '<span class="badge bg-light text-dark">Không rõ</span>';
    }
}

function makeRowHtml(item, stt) {
    return `
      <tr data-id="${item.id}" data-queue="${item.queue_number}" class="fade-in">
        <td class="text-center">${stt}</td>
        <td><span class="badge bg-primary fs-6">${item.queue_number}</span></td>
        <td>${item.full_name ?? ''}</td>
        <td>${item.department ?? ''}</td>
        <td>${item.created_at ?? ''}</td>
        <td class="text-center status-cell">${statusBadgeHtml(item.new_status)}</td>
      </tr>
    `;
}

function reindexRows() {
    const rows = document.querySelectorAll('#queue-tbody tr');
    let i = 1;
    rows.forEach(r => {
        const sttCell = r.querySelector('td:first-child');
        if (sttCell) sttCell.textContent = i++;
    });
}

function upsertRow(item) {
    const tbody = document.getElementById('queue-tbody');
    if (!tbody) return;

    let row = document.querySelector(`tr[data-id="${item.id}"]`);

    if (item.new_status === 'completed') {
        if (row) {
            row.classList.remove('fade-in');
            row.classList.add('fade-out');
            row.addEventListener('animationend', () => {
                row.remove();
                reindexRows();
                showEmptyIfNeeded();
            }, {
                once: true
            });
        }
        return;
    }

    if (!row) {
        const stt = tbody.querySelectorAll('tr').length + 1;
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = makeRowHtml(item, stt);
        const newRow = wrapper.firstElementChild;
        tbody.prepend(newRow);
        removeEmptyRowIfExists();
        reindexRows();
        return;
    }

    const cell = row.querySelector('.status-cell');
    if (cell) cell.innerHTML = statusBadgeHtml(item.new_status);
}

function removeEmptyRowIfExists() {
    const empty = document.querySelector('#queue-tbody td[colspan="6"]');
    if (empty) empty.parentElement.remove();
}

function showEmptyIfNeeded() {
    const tbody = document.getElementById('queue-tbody');
    if (!tbody) return;
    if (tbody.querySelectorAll('tr').length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td colspan="6" class="text-center py-4 text-muted">
          <i class="fas fa-inbox fa-2x mb-3"></i><br>Chưa có đăng ký nào
        </td>
      `;
        tbody.appendChild(tr);
    }
}

// Socket listener
echo.channel('status')
    .listen('.status.updated', (e) => {
        upsertRow(e);
    });

function statusBadgeHtml(status) {
    switch (status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
        case 'received':
            return '<span class="badge bg-info text-dark">Đã tiếp nhận</span>';
        case 'processing':
            return '<span class="badge bg-primary text-white">Đang xử lý</span>';
        case 'completed':
            return '<span class="badge bg-success text-white">Hoàn thành</span>';
        case 'returned':
            return '<span class="badge bg-secondary text-white">Trả hồ sơ</span>';
        default:
            return '<span class="badge bg-light text-dark">Không rõ</span>';
    }
}

function makeRowHtml(item, stt) {
    return `
      <tr data-id="${item.id}" data-queue="${item.queue_number}" class="fade-in">
        <td class="text-center">${stt}</td>
        <td><span class="badge bg-primary fs-6">${item.queue_number}</span></td>
        <td>${item.full_name ?? ''}</td>
        <td>${item.department ?? ''}</td>
        <td>${item.created_at ?? ''}</td>
        <td class="text-center status-cell">${statusBadgeHtml(item.new_status)}</td>
      </tr>
    `;
}

function reindexRows() {
    const rows = document.querySelectorAll('#queue-tbody tr');
    let i = 1;
    rows.forEach(r => {
        const sttCell = r.querySelector('td:first-child');
        if (sttCell) sttCell.textContent = i++;
    });
}

function upsertRow(item) {
    const tbody = document.getElementById('queue-tbody');
    if (!tbody) return;

    let row = document.querySelector(`tr[data-id="${item.id}"]`);

    if (item.new_status === 'completed') {
        if (row) {
            row.classList.remove('fade-in');
            row.classList.add('fade-out');
            row.addEventListener('animationend', () => {
                row.remove();
                reindexRows();
                showEmptyIfNeeded();
            }, {
                once: true
            });
        }
        return;
    }

    if (!row) {
        const stt = tbody.querySelectorAll('tr').length + 1;
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = makeRowHtml(item, stt);
        const newRow = wrapper.firstElementChild;
        tbody.prepend(newRow);
        removeEmptyRowIfExists();
        reindexRows();
        return;
    }

    const cell = row.querySelector('.status-cell');
    if (cell) cell.innerHTML = statusBadgeHtml(item.new_status);
}

function removeEmptyRowIfExists() {
    const empty = document.querySelector('#queue-tbody td[colspan="6"]');
    if (empty) empty.parentElement.remove();
}

function showEmptyIfNeeded() {
    const tbody = document.getElementById('queue-tbody');
    if (!tbody) return;
    if (tbody.querySelectorAll('tr').length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td colspan="6" class="text-center py-4 text-muted">
          <i class="fas fa-inbox fa-2x mb-3"></i><br>Chưa có đăng ký nào
        </td>
      `;
        tbody.appendChild(tr);
    }
}

