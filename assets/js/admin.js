/**
 * Admin Panel JavaScript
 * Graphic Design Portfolio & CMS
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // 2. Toast Notification Function
    window.showToast = function(type, message) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-alert pointer-events-auto flex items-center space-x-3 px-4 py-3 rounded-2xl border shadow-xl text-sm font-semibold transition-all duration-300 ${
            type === 'success' ? 'bg-emerald-950 text-emerald-200 border-emerald-800' :
            type === 'error' ? 'bg-rose-950 text-rose-200 border-rose-800' :
            'bg-slate-900 text-slate-200 border-slate-800'
        }`;

        const icon = type === 'success' ? '✓' : (type === 'error' ? '✕' : 'ℹ');
        toast.innerHTML = `<span class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold ${
            type === 'success' ? 'bg-emerald-500 text-slate-950' :
            type === 'error' ? 'bg-rose-500 text-white' :
            'bg-indigo-500 text-white'
        }">${icon}</span><span>${message}</span>`;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    // 3. Image File Upload Live Preview
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    fileInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            const targetId = input.getAttribute('data-preview');
            const previewImg = document.getElementById(targetId);

            if (file && previewImg) {
                if (!['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'].includes(file.type)) {
                    showToast('error', 'Format file harus JPG, PNG, WEBP, atau SVG.');
                    input.value = '';
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    showToast('error', 'Ukuran file maksimal 10MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImg.src = event.target.result;
                    previewImg.classList.remove('hidden');
                    const placeholder = previewImg.parentElement.querySelector('.upload-placeholder');
                    if (placeholder) placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // 4. Portfolio Search / Filter in Table
    const searchInput = document.getElementById('admin-search-input');
    const categorySelect = document.getElementById('admin-category-filter');
    const tableRows = document.querySelectorAll('.portfolio-row');

    function filterAdminTable() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const selectedCat = (categorySelect ? categorySelect.value : 'all');

        let matchCount = 0;
        tableRows.forEach(row => {
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const category = (row.getAttribute('data-category') || '');
            const client = (row.getAttribute('data-client') || '').toLowerCase();

            const matchesQuery = query === '' || title.includes(query) || client.includes(query);
            const matchesCat = selectedCat === 'all' || category === selectedCat;

            if (matchesQuery && matchesCat) {
                row.classList.remove('hidden');
                matchCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        const countEl = document.getElementById('admin-item-count');
        if (countEl) countEl.textContent = matchCount;
    }

    if (searchInput) searchInput.addEventListener('input', filterAdminTable);
    if (categorySelect) categorySelect.addEventListener('change', filterAdminTable);

    // 5. Dynamic Edit Portfolio Modal
    const editButtons = document.querySelectorAll('.btn-edit-portfolio');
    const editModal = document.getElementById('modal-edit-portfolio');

    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');
            const catId = btn.getAttribute('data-category-id');
            const desc = btn.getAttribute('data-desc');
            const client = btn.getAttribute('data-client');
            const tools = btn.getAttribute('data-tools');
            const order = btn.getAttribute('data-order');
            const isActive = btn.getAttribute('data-active') === '1';
            const imgUrl = btn.getAttribute('data-image');

            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_category_id').value = catId;
            document.getElementById('edit_description').value = desc;
            document.getElementById('edit_client_name').value = client;
            document.getElementById('edit_tools_used').value = tools;
            document.getElementById('edit_display_order').value = order;
            document.getElementById('edit_is_active').checked = isActive;

            const preview = document.getElementById('edit_preview_img');
            if (preview && imgUrl) {
                preview.src = imgUrl;
                preview.classList.remove('hidden');
            }

            if (editModal) {
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            }
        });
    });

    // 6. Generic Modal Close Buttons
    const modalCloseButtons = document.querySelectorAll('[data-close-modal]');
    modalCloseButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const modalId = btn.getAttribute('data-close-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });

    // 7. Modal Open Triggers
    const modalOpenButtons = document.querySelectorAll('[data-open-modal]');
    modalOpenButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const modalId = btn.getAttribute('data-open-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        });
    });

    // 8. Delete Confirmation Modal Bindings
    const deleteButtons = document.querySelectorAll('.btn-delete-portfolio');
    const deleteModal = document.getElementById('modal-delete-confirm');
    const deleteIdInput = document.getElementById('delete_item_id');
    const deleteTitleText = document.getElementById('delete_item_title');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');

            if (deleteIdInput) deleteIdInput.value = id;
            if (deleteTitleText) deleteTitleText.textContent = `"${title}"`;

            if (deleteModal) {
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            }
        });
    });
});
