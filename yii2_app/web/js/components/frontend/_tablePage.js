$(document).ready(function () {

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    function initResizableColumns() {
        const table = $('#table-data');
        const cols = table.find('th.rs-col');
        let startX, startWidth, startTableWidth, nextCol, nextStartWidth;

        // Cập nhật chiều cao của resizer để nó bằng chiều cao của bảng
        function updateResizerHeight() {
            const tableHeight = table[0].getBoundingClientRect().height;
            table.find('.resizer').css('height', tableHeight); // Cập nhật chiều cao của tất cả các resizer
        }

        // Cập nhật chiều cao của resizer khi cửa sổ thay đổi kích thước
        $(window).on('resize', function () {
            updateResizerHeight();
        });

        cols.each(function () {
            const col = $(this);
            const resizer = $('<div class="resizer"></div>').appendTo(col);

            updateResizerHeight();

            // Sự kiện kéo chuột để thay đổi độ rộng cột
            resizer.on('mousedown', function (e) {
                startX = e.clientX;
                startWidth = col[0].getBoundingClientRect().width;
                startTableWidth = table[0].getBoundingClientRect().width;

                nextCol = col.next('th.rs-col');
                nextStartWidth = nextCol.length ? nextCol[0].getBoundingClientRect().width : 0;

                $(document).on('mousemove', resizeColumn);
                $(document).on('mouseup', stopResizing);

                e.preventDefault();
            });

            function resizeColumn(e) {
                let newWidth = startWidth + (e.clientX - startX);
                if (newWidth > 100) {
                    let widthDelta = newWidth - startWidth;

                    // Cập nhật độ rộng cột hiện tại
                    col.css('width', newWidth);

                    // Kiểm tra xem bảng có bị tràn không
                    const isOverflowing = table[0].scrollWidth > table.parent()[0].clientWidth;

                    if (isOverflowing) {
                        // Điều chỉnh độ rộng toàn bộ bảng
                        let newTableWidth = startTableWidth + widthDelta;
                        table.css('width', newTableWidth);
                    } else {
                        // Chỉ thay đổi độ rộng của cột bên phải nếu có và nếu cột bên phải có thể thu nhỏ
                        if (nextCol.length && nextStartWidth - widthDelta > 0) {
                            nextCol.css('width', nextStartWidth - widthDelta);
                        } else if (!nextCol.length) {
                            // Không có cột bên phải, đảm bảo bảng không bị thu hẹp hơn width cố định ban đầu
                            let minTableWidth = startTableWidth > table.parent()[0].clientWidth ? table.parent()[0].clientWidth : startTableWidth;
                            if (table.css('width') < minTableWidth) {
                                table.css('width', minTableWidth);
                            }
                        }
                    }
                }
            }

            function stopResizing() {
                $(document).off('mousemove', resizeColumn);
                $(document).off('mouseup', stopResizing);
                debounceSaveColumnWidth();
            }

            col.on('dblclick', function () {
                col.css('width', '100px');
                debounceSaveColumnWidth();
            });
        });
    }

    initResizableColumns();

    const debounceSaveColumnWidth = debounce(function () {
        let columnWidths = [];

        $('#table-data').find('th.rs-col').each(function (index) {
            const columnName = $(this).data('column-name');
            if (!columnName) {
                console.error('Không tìm thấy cột.');
                return;
            }
            const columnPosition = index;
            columnWidths.push({
                column_name: columnName,
                column_width: $(this).outerWidth(),
                column_position: columnPosition
            });
        });

        $.ajax({
            url: save_column_width_url,
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                menuId,
                pageId,
                columns_config: columnWidths
            },
            success: function (response) {
                if (!response.success) {
                    showToast('Lỗi: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Lỗi AJAX:', error);
                showToast('Không thể cập nhật độ rộng cột.');
            }
        });
    }, 500);


    $(document).off('click', '#btn-config').on('click', '#btn-config', function () {
        $('#sortable-config').sortable({
            handle: '.drag-handle',
            update: function (event, ui) {
                $('#sortable-config tr').each(function (index) {
                    $(this).attr('data-position', index);
                });
            }
        });

        $('#columnsModal').modal('show');
    });


    $(document).off('click', '#save-columns-config').on('click', '#save-columns-config', function () {
        let columnsConfig = [];

        $('#sortable-config .column-switch').each(function () {
            const columnName = $(this).data('column');
            const isChecked = $(this).prop('checked');
            const columnPosition = $(this).closest('tr').index();

            columnsConfig.push({
                column_name: columnName,
                is_visible: isChecked,
                column_position: columnPosition
            });
        });

        $.ajax({
            url: save_column_config_url,
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                menuId,
                pageId,
                columns_config: columnsConfig
            },
            success: function (response) {
                if (response.success) {
                    showToast(response.message);
                    $('#columnsModal').modal('hide');
                    loadData();
                } else {
                    showToast('Lỗi cập nhật: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log('Lỗi AJAX:', error);
                showToast('Không thể cập nhật cột.');
            }
        });
    });


    $(document).off('pjax:send').on('pjax:send', function () {
        var loadingSpinner = $(`
            <div class="spinner-fixed">
                <i class="fa fa-spin fa-spinner me-2"></i>
            </div>
        `);
        $('body').append(loadingSpinner);
    });

    $(document).off('pjax:complete').on('pjax:complete', function () {
        $('.spinner-fixed').remove();
        // initResizableColumns();

    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        initResizableColumns();
    });

    $(document).off('click', '#add-row-btn').on('click', '#add-row-btn', function (e) {
        e.preventDefault();

        var formData = $('#add-data-form').serialize();

        $.ajax({
            url: add_data_url,
            type: "POST",
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                data: formData,
                pageId,
            },
            success: function (response) {
                if (response.success) {
                    $('#add-data-form')[0].reset();
                    $('#addDataModal').modal('hide');
                    showToast('Thêm dữ liệu thành công!');
                    loadData();
                } else {
                    $('.error-message').remove();

                    var errorMessage = response.message;
                    if (response.errors) {
                        $.each(response.errors, function (field, messages) {
                            errorMessage += field + ": " + messages.join(", ") + "\n";

                            var errorHtml = '<div class="error-message text-danger">' + messages.join(", ") + '</div>';
                            $('#' + field).closest('.form-group').append(errorHtml);
                        });
                    }
                }
            },
            error: function () {
                alert('Không thể thêm dữ liệu. Vui lòng thử lại.');
            }
        });
    });



    $(document).off('click', '.btn-edit').on('click', '.btn-edit', function () {
        var rowData = $(this).data('row');

        $.each(rowData, function (key, value) {
            var inputField = $('#edit-' + key);
            if (inputField.length) {
                inputField.val(value);
            }
        });

        $('#editModal').modal('show');
    });

    $(document).off('click', '#save-row-btn').on('click', '#save-row-btn', function (e) {
        e.preventDefault();

        var formData = $('#edit-form').serialize();

        $.ajax({
            url: update_data_url,
            type: "POST",
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                data: formData,
                pageId,
            },
            success: function (response) {
                if (response.success) {
                    $('#edit-form')[0].reset();
                    $('#editModal').modal('hide');
                    showToast('Cập nhật dữ liệu thành công!');
                    loadData();
                } else {
                    $('.error-message').remove();

                    var errorMessage = response.message;
                    if (response.errors) {
                        $.each(response.errors, function (field, messages) {
                            errorMessage += field + ": " + messages.join(", ") + "\n";

                            var errorHtml = '<div class="error-message text-danger">' + messages.join(", ") + '</div>';
                            $('#edit-' + field).closest('.form-group').append(errorHtml);
                        });
                    }
                }
            },
            error: function () {
                alert('Không thể cập nhật dữ liệu. Vui lòng thử lại.');
            }
        });
    });
    $(document).off('click', '.btn-delete').on('click', '.btn-delete', function (e) {
        e.preventDefault();

        var rowId = $(this).data('hidden_id');
        if (confirm('Bạn có chắc chắn muốn xóa dòng này?')) {
            $.ajax({
                url: delete_data_url,
                type: "POST",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: rowId,
                    pageId,
                },
                success: function (response) {
                    if (response.success) {
                        showToast('Xóa dữ liệu thành công!');
                        loadData();
                    } else {
                        alert('Có lỗi xảy ra: ' + response.message);
                    }
                },
                error: function () {
                    alert(
                        'Không thể xóa dữ liệu. Vui lòng thử lại.'
                    );
                }
            });
        }
    })

    $(document).off('click', '#delete-selected-btn').on('click',
        '#delete-selected-btn',
        function (e) {
            e.preventDefault();

            var selectedIds = [];
            $('.checkbox-row:checked').each(function () {
                selectedIds.push($(this).data(
                    'hidden_id'));
            });
            var selectedIds = [];
            $('.checkbox-row:checked').each(function () {
                selectedIds.push($(this).data(
                    'hidden_id'));
            });

            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một dòng để xóa.');
                return;
            }
            if (confirm('Bạn có chắc chắn muốn xóa các dòng đã chọn?')) {
                $.ajax({
                    url: delete_all_data_url,
                    type: "POST",
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds,
                        pageId,
                    },
                    success: function (response) {
                        if (response.success) {
                            showToast('Xóa dữ liệu thành công!');
                            loadData();

                        } else {
                            alert('Có lỗi xảy ra: ' + response
                                .message); // Thông báo lỗi
                        }
                    },
                    error: function () {
                        alert(
                            'Không thể xóa dữ liệu. Vui lòng thử lại.'
                        ); // Thông báo lỗi nếu có sự cố
                    }
                });
            }
        });

    $('#search-form input[name="search"]').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            loadData();
        }
    });
});


// Import Excel Button Click
$(document).off('click', '#import-data-btn').on('click', '#import-data-btn', function () {
    $('#importExelModal').modal('show');
});

// Handle Import Excel Form Submission
$(document).off('submit', '#importExcelForm').on('submit', '#importExcelForm', function (event) {
    event.preventDefault();

    // Hiển thị hộp thoại xác nhận
    showModal('Xác nhận', 'Bạn có chắc chắn muốn nhập dữ liệu từ tệp Excel này không?');

    $('#importStatusModal').off('shown.bs.modal').on('shown.bs.modal', function () {
        $(this).find('.modal-footer').html(`
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-primary" id="confirmImport">Tiếp tục</button>
        `);

        $('#confirmImport').off('click').on('click', function () {
            $('#importStatusModal').modal('hide');
            var formData = new FormData($('#importExcelForm')[0]);
            formData.append('pageId', pageId);

            var loadingSpinner = $(
                `<div class="loading-overlay">
                    <div class="loading-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="ml-2">Đang nhập dữ liệu, vui lòng đợi...</span>                    
                    </div>
                </div>`
            );
            $('body').append(loadingSpinner);

            $.ajax({
                url: import_url,
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    loadingSpinner.remove();

                    if (response.success) {
                        loadData(pageId);
                        showToast('Nhập dữ liệu từ Excel thành công!');

                        $('#importExcelForm')[0].reset();
                        $('#importExelModal').modal('hide');
                    } else {
                        showModal('Lỗi', '' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    loadingSpinner.remove();
                    showModal('Lỗi', 'Có lỗi xảy ra khi nhập tệp Excel: ' + error);
                }
            });
        });
    });
});


let lastAjaxUrl = '';

$(document).ajaxSend(function (event, jqXHR, settings) {
    lastAjaxUrl = settings.url;
});
$(document).on('click', '#export-excel-btn', function () {
    var loadingSpinner = $(`
        <div class="loading-overlay">
            <div class="loading-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <span class="ml-2">Đang xuất dữ liệu, vui lòng đợi...</span>                    
            </div>
        </div>
    `);
    $('body').append(loadingSpinner);

    var search = '';
    var sort = $('th a.desc, th a.asc').data('sort');

    if (!sort) {
        sort = '-id';
    }

    if (lastAjaxUrl.includes('search=')) {
        const urlParams = new URLSearchParams(lastAjaxUrl.split('?')[1]);
        search = urlParams.get('search');
    }

    const exportUrl = export_url + '?pageId=' + pageId + '&sort=' + sort + '&search=' + search;

    window.location.href = exportUrl;
    loadingSpinner.remove();
});





