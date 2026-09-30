/**
 * Home Page Builder & Section Management Controller
 */
const HomePageBuilder = {
    layoutModal: null,
    editSectionModal: null,
    customSectionModal: null,
    announcementsModal: null,
    messagesModal: null,
    testimonialsModal: null,
    quickLinksModal: null,
    draggedItem: null,

    init() {
        this.initModals();
        this.initDragAndDrop();
        this.bindEvents();
    },

    initModals() {
        if (document.getElementById('layoutSelectorModal')) {
            this.layoutModal = new bootstrap.Modal(document.getElementById('layoutSelectorModal'));
        }
        if (document.getElementById('editSectionModal')) {
            this.editSectionModal = new bootstrap.Modal(document.getElementById('editSectionModal'));
        }
        if (document.getElementById('customSectionModal')) {
            this.customSectionModal = new bootstrap.Modal(document.getElementById('customSectionModal'));
        }
        if (document.getElementById('announcementsModal')) {
            this.announcementsModal = new bootstrap.Modal(document.getElementById('announcementsModal'));
        }
        if (document.getElementById('messagesModal')) {
            this.messagesModal = new bootstrap.Modal(document.getElementById('messagesModal'));
        }
        if (document.getElementById('testimonialsModal')) {
            this.testimonialsModal = new bootstrap.Modal(document.getElementById('testimonialsModal'));
        }
        if (document.getElementById('quickLinksModal')) {
            this.quickLinksModal = new bootstrap.Modal(document.getElementById('quickLinksModal'));
        }
    },

    formatUrl(urlTemplate, id) {
        if (!urlTemplate) return '';
        return urlTemplate
            .replace('__ID__', id)
            .replace(':id', id)
            .replace('%3Aid', id);
    },

    initDragAndDrop() {
        const container = document.getElementById('sortableSectionsList');
        if (!container) return;

        const cards = container.querySelectorAll('.section-item-card');

        cards.forEach(card => {
            card.addEventListener('dragstart', (e) => {
                this.draggedItem = card;
                card.classList.add('opacity-50', 'border-primary');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', card.innerHTML);
            });

            card.addEventListener('dragend', () => {
                if (this.draggedItem) {
                    this.draggedItem.classList.remove('opacity-50', 'border-primary');
                    this.draggedItem = null;
                }
                cards.forEach(c => c.classList.remove('border-primary', 'border-2'));
                this.updateOrderNumbers();
            });

            card.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                if (this.draggedItem && this.draggedItem !== card) {
                    const rect = card.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                    container.insertBefore(this.draggedItem, next && card.nextSibling || card);
                }
            });
        });
    },

    updateOrderNumbers() {
        const cards = document.querySelectorAll('#sortableSectionsList .section-item-card');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.section-order-badge');
            if (badge) {
                badge.textContent = '#' + (idx + 1);
            }
        });
    },

    collectOrders() {
        const orders = [];
        const cards = document.querySelectorAll('#sortableSectionsList .section-item-card');
        cards.forEach((card, idx) => {
            orders.push({
                id: parseInt(card.dataset.id, 10),
                order: idx + 1
            });
        });
        return orders;
    },

    saveOrders(silent = false) {
        const orders = this.collectOrders();
        const btn = $('#btnSaveAllOrders');

        if (!silent) {
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
        }

        let formData = new FormData();
        orders.forEach((item, index) => {
            formData.append(`orders[${index}][id]`, item.id);
            formData.append(`orders[${index}][order]`, item.order);
        });

        Ajax.request({
            url: URL_UPDATE_ORDERS,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (!silent) {
                    btn.prop('disabled', false).html('<i class="bi bi-check2-all me-1"></i> Save Order');
                    if (typeof Toast !== 'undefined' && Toast.success) {
                        Toast.success(response.message || 'Section orders saved successfully.');
                    }
                }
            },
            error: () => {
                if (!silent) {
                    btn.prop('disabled', false).html('<i class="bi bi-check2-all me-1"></i> Save Order');
                }
            }
        });
    },

    bindEvents() {
        // Save order button
        $('#btnSaveAllOrders').on('click', () => {
            this.saveOrders(false);
        });

        // Toggle Status switch
        $(document).on('change', '.section-status-switch', (e) => {
            const checkbox = $(e.currentTarget);
            const card = checkbox.closest('.section-item-card');
            const sectionId = card.data('id');
            const isEnabled = checkbox.is(':checked');

            let formData = new FormData();
            formData.append('_method', 'PATCH');
            formData.append('is_enabled', isEnabled ? 1 : 0);

            Ajax.request({
                url: this.formatUrl(URL_TOGGLE_STATUS, sectionId),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if (isEnabled) {
                        card.removeClass('bg-light opacity-75').addClass('bg-white');
                    } else {
                        card.addClass('bg-light opacity-75').removeClass('bg-white');
                    }
                },
                error: () => {
                    checkbox.prop('checked', !isEnabled);
                }
            });
        });

        // Open Layout Selector
        $(document).on('click', '.btn-change-layout', (e) => {
            const card = $(e.currentTarget).closest('.section-item-card');
            this.openLayoutSelector(card);
        });

        // Apply selected layout
        $('#btnApplyLayout').on('click', () => {
            this.applySelectedLayout();
        });

        // Open Edit Section Properties
        $(document).on('click', '.btn-edit-section', (e) => {
            const card = $(e.currentTarget).closest('.section-item-card');
            this.openEditSection(card);
        });

        // Save Edit Section form
        $('#editSectionForm').on('submit', (e) => {
            e.preventDefault();
            this.saveSectionDetails();
        });

        // Open Add Custom Section
        $('#btnAddCustomSection').on('click', () => {
            $('#customSectionForm')[0].reset();
            this.customSectionModal.show();
        });

        // Submit Custom Section form
        $('#customSectionForm').on('submit', (e) => {
            e.preventDefault();
            this.submitCustomSection();
        });

        // Delete Custom Section
        $(document).on('click', '.btn-delete-custom', (e) => {
            const card = $(e.currentTarget).closest('.section-item-card');
            const sectionId = card.data('id');
            const title = card.data('title') || 'this section';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Section?',
                    text: `Are you sure you want to delete "${title}"? This cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.deleteCustomSection(sectionId, card);
                    }
                });
            } else if (confirm(`Are you sure you want to delete "${title}"?`)) {
                this.deleteCustomSection(sectionId, card);
            }
        });

        // ==========================================
        // ANNOUNCEMENTS
        // ==========================================
        $('#btnManageAnnouncements, #btnAddAnnouncement, .btn-open-modal-announcements').on('click', () => {
            $('#announcementFormCard').addClass('d-none');
            $('#announcementForm')[0].reset();
            $('#announcement_id').val('');
            this.announcementsModal.show();
        });

        $('#btnOpenNewAnnouncement').on('click', () => {
            $('#announcementForm')[0].reset();
            $('#announcement_id').val('');
            $('#announcementFormCard').removeClass('d-none');
        });

        $('#btnCancelAnnouncement').on('click', () => {
            $('#announcementFormCard').addClass('d-none');
        });

        $('#announcementForm').on('submit', (e) => {
            e.preventDefault();
            this.saveAnnouncement();
        });

        $(document).on('click', '.btn-edit-ann', (e) => {
            const row = $(e.currentTarget).closest('tr');
            $('#announcement_id').val(row.data('id'));
            $('#ann_title').val(row.data('title'));
            $('#ann_badge').val(row.data('badge'));
            $('#ann_link_url').val(row.data('url'));
            $('#ann_link_text').val(row.data('link-text'));
            $('#ann_content').val(row.data('content'));
            $('#announcementFormCard').removeClass('d-none');
        });

        $(document).on('click', '.btn-delete-ann', (e) => {
            const row = $(e.currentTarget).closest('tr');
            const id = row.data('id');
            if (confirm('Delete this announcement?')) {
                let formData = new FormData();
                formData.append('_method', 'DELETE');

                Ajax.request({
                    url: this.formatUrl(URL_DELETE_ANN, id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        row.fadeOut(() => row.remove());
                    }
                });
            }
        });

        // ==========================================
        // IMPORTANT MESSAGES
        // ==========================================
        $('#btnManageMessages, #btnAddMessage, .btn-open-modal-messages').on('click', () => {
            $('#messageFormCard').addClass('d-none');
            $('#importantMessageForm')[0].reset();
            $('#message_id').val('');
            this.messagesModal.show();
        });

        $('#btnOpenNewMessage').on('click', () => {
            $('#importantMessageForm')[0].reset();
            $('#message_id').val('');
            $('#messageFormCard').removeClass('d-none');
        });

        $('#btnCancelMessage').on('click', () => {
            $('#messageFormCard').addClass('d-none');
        });

        $('#importantMessageForm').on('submit', (e) => {
            e.preventDefault();
            this.saveImportantMessage();
        });

        $(document).on('click', '.btn-edit-msg', (e) => {
            const row = $(e.currentTarget).closest('tr');
            $('#message_id').val(row.data('id'));
            $('#msg_title').val(row.data('title'));
            $('#msg_type').val(row.data('type'));
            $('#msg_action_text').val(row.data('action-text'));
            $('#msg_action_url').val(row.data('action-url'));
            $('#msg_body').val(row.data('message'));
            $('#messageFormCard').removeClass('d-none');
        });

        $(document).on('click', '.btn-delete-msg', (e) => {
            const row = $(e.currentTarget).closest('tr');
            const id = row.data('id');
            if (confirm('Delete this notice?')) {
                let formData = new FormData();
                formData.append('_method', 'DELETE');

                Ajax.request({
                    url: this.formatUrl(URL_DELETE_MSG, id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        row.fadeOut(() => row.remove());
                    }
                });
            }
        });

        // ==========================================
        // TESTIMONIALS
        // ==========================================
        $('#btnManageTestimonials, #btnAddTestimonial, .btn-open-modal-testimonials').on('click', () => {
            $('#testimonialFormCard').addClass('d-none');
            $('#testimonialForm')[0].reset();
            $('#testimonial_id').val('');
            this.testimonialsModal.show();
        });

        $('#btnOpenNewTestimonial').on('click', () => {
            $('#testimonialForm')[0].reset();
            $('#testimonial_id').val('');
            $('#testimonialFormCard').removeClass('d-none');
        });

        $('#btnCancelTestimonial').on('click', () => {
            $('#testimonialFormCard').addClass('d-none');
        });

        $('#testimonialForm').on('submit', (e) => {
            e.preventDefault();
            this.saveTestimonial();
        });

        $(document).on('click', '.btn-edit-testi', (e) => {
            const row = $(e.currentTarget).closest('tr');
            $('#testimonial_id').val(row.data('id'));
            $('#testi_name').val(row.data('name'));
            $('#testi_role').val(row.data('role'));
            $('#testi_rating').val(row.data('rating'));
            $('#testi_message').val(row.data('message'));
            $('#testi_status').prop('checked', row.data('status') == '1');
            $('#testimonialFormCard').removeClass('d-none');
        });

        $(document).on('click', '.btn-delete-testi', (e) => {
            const row = $(e.currentTarget).closest('tr');
            const id = row.data('id');
            if (confirm('Delete this testimonial review?')) {
                let formData = new FormData();
                formData.append('_method', 'DELETE');

                Ajax.request({
                    url: this.formatUrl(URL_DELETE_TESTIMONIAL, id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        row.fadeOut(() => row.remove());
                    }
                });
            }
        });

        // ==========================================
        // QUICK LINKS
        // ==========================================
        $('#btnManageQuickLinks, #btnAddQuickLink, .btn-open-modal-quicklinks').on('click', () => {
            $('#quickLinkFormCard').addClass('d-none');
            $('#quickLinkForm')[0].reset();
            $('#quick_link_id').val('');
            this.quickLinksModal.show();
        });

        $('#btnOpenNewQuickLink').on('click', () => {
            $('#quickLinkForm')[0].reset();
            $('#quick_link_id').val('');
            $('#quickLinkFormCard').removeClass('d-none');
        });

        $('#btnCancelQuickLink').on('click', () => {
            $('#quickLinkFormCard').addClass('d-none');
        });

        $('#quickLinkForm').on('submit', (e) => {
            e.preventDefault();
            this.saveQuickLink();
        });

        $(document).on('click', '.btn-edit-ql', (e) => {
            const row = $(e.currentTarget).closest('tr');
            $('#quick_link_id').val(row.data('id'));
            $('#ql_title').val(row.data('title'));
            $('#ql_desc').val(row.data('desc'));
            $('#ql_icon').val(row.data('icon'));
            $('#ql_url').val(row.data('url'));
            $('#ql_color').val(row.data('color'));
            $('#quickLinkFormCard').removeClass('d-none');
        });

        $(document).on('click', '.btn-delete-ql', (e) => {
            const row = $(e.currentTarget).closest('tr');
            const id = row.data('id');
            if (confirm('Delete this quick link tile?')) {
                let formData = new FormData();
                formData.append('_method', 'DELETE');

                Ajax.request({
                    url: this.formatUrl(URL_DELETE_QUICK_LINK, id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        row.fadeOut(() => row.remove());
                    }
                });
            }
        });
    },

    openLayoutSelector(card) {
        const sectionId = card.data('id');
        const sectionType = card.data('type');
        const currentLayout = card.data('layout');
        const title = card.data('title') || 'Section';

        $('#layout_section_id').val(sectionId);
        $('#layout_section_type').val(sectionType);
        $('#layoutModalSectionTitle').text(title + ' (' + sectionType + ')');

        const container = $('#layoutOptionsContainer');
        container.empty();

        const typeInfo = BUILDER_REGISTRY[sectionType];
        if (!typeInfo || !typeInfo.layouts || Object.keys(typeInfo.layouts).length === 0) {
            container.html('<div class="col-12 text-center text-muted py-4">No additional layout variants registered for this section.</div>');
            this.layoutModal.show();
            return;
        }

        $.each(typeInfo.layouts, (key, layout) => {
            const isSelected = (key === currentLayout);
            const activeClass = isSelected ? 'border-primary bg-primary-subtle shadow' : 'border-light-subtle bg-white hover-shadow';
            const badgeBg = isSelected ? 'bg-primary text-white' : 'bg-secondary-subtle text-dark';

            const cardHtml = `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-3 rounded-4 cursor-pointer layout-option-card ${activeClass}" data-layout-key="${key}" style="border-width: 2px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge ${badgeBg}">${layout.preview_badge || 'Variant'}</span>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="selected_layout_radio" value="${key}" ${isSelected ? 'checked' : ''}>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-1">${layout.name}</h6>
                        <p class="text-muted small mb-0">${layout.description || ''}</p>
                    </div>
                </div>
            `;
            container.append(cardHtml);
        });

        // Click card to select radio
        container.find('.layout-option-card').on('click', function () {
            container.find('.layout-option-card').removeClass('border-primary bg-primary-subtle shadow').addClass('border-light-subtle bg-white');
            $(this).addClass('border-primary bg-primary-subtle shadow').removeClass('border-light-subtle bg-white');
            $(this).find('input[type="radio"]').prop('checked', true);
        });

        this.layoutModal.show();
    },

    applySelectedLayout() {
        const sectionId = $('#layout_section_id').val();
        const selectedLayout = $('input[name="selected_layout_radio"]:checked').val();

        if (!selectedLayout) {
            if (typeof Toast !== 'undefined' && Toast.error) {
                Toast.error('Please select a layout.');
            } else {
                alert('Please select a layout.');
            }
            return;
        }

        const btn = $('#btnApplyLayout');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Applying...');

        let formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('layout_key', selectedLayout);

        Ajax.request({
            url: this.formatUrl(URL_UPDATE_LAYOUT, sectionId),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Apply Layout');
                this.layoutModal.hide();

                // Update card UI
                const card = $(`#sortableSectionsList .section-item-card[data-id="${sectionId}"]`);
                card.data('layout', selectedLayout);
                card.find('.layout-badge').html(`<i class="bi bi-layout-split me-1"></i> ${selectedLayout}`);

                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success(response.message || 'Layout updated successfully.');
                }
            },
            error: () => {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Apply Layout');
            }
        });
    },

    openEditSection(card) {
        const sectionId = card.data('id');
        const sectionType = card.data('type');
        const title = card.data('title');
        const subtitle = card.data('subtitle');
        const customClass = card.data('custom-class');
        let settings = card.data('settings');

        if (typeof settings === 'string') {
            try {
                settings = JSON.parse(settings);
            } catch (e) {
                settings = {};
            }
        }
        settings = settings || {};

        $('#edit_section_id').val(sectionId);
        $('#edit_title').val(title || '');
        $('#edit_subtitle').val(subtitle || '');
        $('#edit_custom_class').val(customClass || '');

        if (sectionType === 'custom_content') {
            $('#customContentFields').removeClass('d-none');
            $('#edit_content').val(settings.content || '');
            $('#edit_button_text').val(settings.button_text || '');
            $('#edit_button_url').val(settings.button_url || '');
            $('#edit_bg_color').val(settings.bg_color || '#ffffff');
            $('#edit_text_color').val(settings.text_color || '#0f172a');
        } else {
            $('#customContentFields').addClass('d-none');
        }

        this.editSectionModal.show();
    },

    saveSectionDetails() {
        const sectionId = $('#edit_section_id').val();
        const form = document.getElementById('editSectionForm');
        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        const btn = $('#btnSaveSectionDetails');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        Ajax.request({
            url: this.formatUrl(URL_UPDATE_SECTION, sectionId),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save Changes');
                this.editSectionModal.hide();

                // Update card UI
                const card = $(`#sortableSectionsList .section-item-card[data-id="${sectionId}"]`);
                const newTitle = $('#edit_title').val();
                const newSubtitle = $('#edit_subtitle').val();

                card.data('title', newTitle);
                card.data('subtitle', newSubtitle);
                card.find('h6.fw-bold').text(newTitle);
                card.find('p.text-muted').text(newSubtitle || '');

                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success('Section properties updated successfully.');
                }
            },
            error: () => {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save Changes');
            }
        });
    },

    submitCustomSection() {
        const form = document.getElementById('customSectionForm');
        const formData = new FormData(form);
        const btn = $('#btnSubmitCustomSection');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Creating...');

        Ajax.request({
            url: URL_STORE_CUSTOM,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i> Create Section');
                this.customSectionModal.hide();

                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success('Custom section created successfully.');
                }

                setTimeout(() => window.location.reload(), 600);
            },
            error: () => {
                btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i> Create Section');
            }
        });
    },

    deleteCustomSection(id, card) {
        let formData = new FormData();
        formData.append('_method', 'DELETE');

        Ajax.request({
            url: this.formatUrl(URL_DELETE_CUSTOM, id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                card.fadeOut(400, () => {
                    card.remove();
                    this.updateOrderNumbers();
                    this.saveOrders(true);
                });
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success('Section deleted successfully.');
                }
            }
        });
    },

    saveAnnouncement() {
        const form = document.getElementById('announcementForm');
        const id = $('#announcement_id').val();
        const formData = new FormData(form);
        const isUpdate = Boolean(id);
        if (isUpdate) {
            formData.append('_method', 'PUT');
        }
        const url = isUpdate 
            ? this.formatUrl(URL_UPDATE_ANN, id)
            : URL_STORE_ANN;

        Ajax.request({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success(isUpdate ? 'Announcement updated.' : 'Announcement created.');
                }
                setTimeout(() => window.location.reload(), 600);
            }
        });
    },

    saveImportantMessage() {
        const form = document.getElementById('importantMessageForm');
        const id = $('#message_id').val();
        const formData = new FormData(form);
        const isUpdate = Boolean(id);
        if (isUpdate) {
            formData.append('_method', 'PUT');
        }
        const url = isUpdate 
            ? this.formatUrl(URL_UPDATE_MSG, id)
            : URL_STORE_MSG;

        Ajax.request({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success(isUpdate ? 'Notice updated.' : 'Notice created.');
                }
                setTimeout(() => window.location.reload(), 600);
            }
        });
    },

    saveTestimonial() {
        const form = document.getElementById('testimonialForm');
        const id = $('#testimonial_id').val();
        const formData = new FormData(form);
        const isUpdate = Boolean(id);
        if (isUpdate) {
            formData.append('_method', 'PUT');
        }
        const url = isUpdate 
            ? this.formatUrl(URL_UPDATE_TESTIMONIAL, id)
            : URL_STORE_TESTIMONIAL;

        Ajax.request({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success(isUpdate ? 'Testimonial updated.' : 'Testimonial created.');
                }
                setTimeout(() => window.location.reload(), 600);
            }
        });
    },

    saveQuickLink() {
        const form = document.getElementById('quickLinkForm');
        const id = $('#quick_link_id').val();
        const formData = new FormData(form);
        const isUpdate = Boolean(id);
        if (isUpdate) {
            formData.append('_method', 'PUT');
        }
        const url = isUpdate 
            ? this.formatUrl(URL_UPDATE_QUICK_LINK, id)
            : URL_STORE_QUICK_LINK;

        Ajax.request({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success(isUpdate ? 'Quick link updated.' : 'Quick link created.');
                }
                setTimeout(() => window.location.reload(), 600);
            }
        });
    }
};

$(function () {
    HomePageBuilder.init();
});
