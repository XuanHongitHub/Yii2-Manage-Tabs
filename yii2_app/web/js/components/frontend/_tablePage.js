$(document).ready(function () {
    let columnVisibility = {};

    function applyColumnVisibility() {
        $('.column-checkbox').each(function () {
            const column = $(this).data('column');
            const isChecked = columnVisibility[column] !== false;

            $(this).prop('checked', isChecked);

            if (isChecked) {
                $(`th[data-column="${column}"], td[data-column="${column}"]`).show();
            } else {
                $(`th[data-column="${column}"], td[data-column="${column}"]`).hide();
            }
        });
    }

    $(document).off('change', '.column-checkbox').on('change', '.column-checkbox', function () {
        const column = $(this).data('column');
        const isChecked = $(this).is(':checked');

        columnVisibility[column] = isChecked;

        if (isChecked) {
            $(`th[data-column="${column}"], td[data-column="${column}"]`).show();
        } else {
            $(`th[data-column="${column}"], td[data-column="${column}"]`).hide();
        }
    });

    $(document).off('pjax:send').on('pjax:send', function () {
        console.log('Pjax sending...');
        var loadingSpinner = $(`
            <div class="spinner-fixed">
                <i class="fa fa-spin fa-spinner me-2"></i>
            </div>
        `);
        $('body').append(loadingSpinner);
    });

    $(document).off('pjax:complete').on('pjax:complete', function () {
        console.log('Pjax completed');
        $('.spinner-fixed').remove();
        applyColumnVisibility();
    });

    applyColumnVisibility();

    $(document).off('click', '#add-row-btn').on('click', '#add-row-btn', function (e) {
        e.preventDefault();

        var formData = $('#add-data-form').serialize();

        $.ajax({
            url: add_data_url, // Đường dẫn xử lý thêm dữ liệu
            type: "POST",
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') // CSRF Token
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
                    var errorMessage = response.message;
                    if (response.errors) {
                        $.each(response.errors, function (field, messages) {
                            errorMessage += field + ": " + messages.join(", ") + "\n";
                        });
                    }
                    alert(errorMessage);
                }
            },
            error: function () {
                alert(
                    'Không thể thêm dữ liệu. Vui lòng thử lại.'
                );
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
                    $('#edit-form')[0].reset(); // Reset form
                    $('#editModal').modal('hide'); // Đóng modal
                    showToast('Cập nhật dữ liệu thành công!');
                    loadData();
                } else {
                    var errorMessage = response.message;
                    if (response.errors) {
                        $.each(response.errors, function (field, messages) {
                            errorMessage += field + ": " + messages.join(", ") + "\n";
                        });
                    }
                    alert(errorMessage);
                }
            },
            error: function () {
                alert(
                    'Không thể cập nhật dữ liệu. Vui lòng thử lại.'
                );
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
                    hidden_id: rowId,
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
$(document).off('click', 'import-data-btn').on('click', '#import-data-btn', function () {
    $('#importExelModal').modal('show');
});

// Handle Import Excel Form Submission
$(document).off('submit', '#importExcelForm').on('submit', '#importExcelForm', function (
    event) {

    event.preventDefault();
    var formData = new FormData(this);
    formData.append('pageId', pageId);

    var loadingSpinner = $(` 
        <div class="loading-overlay">
            <div class="loading-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <span class="ml-2">Đang nhập dữ liệu, vui lòng đợi...</span>                    
            </div>
        </div>
    `);
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
            } else if (response.duplicate) {
                $('#confirmMessage').html(
                    `Ghi đè các mục hiện có trong cột <strong>[Khóa chính]</strong>. Bạn có muốn tiếp tục nhập không?<br><br>
                    ${response.message}`
                );

                $('#confirmModal').modal('show');

                $('#confirmYesBtn').off('click').on('click',
                    function () {
                        var newLoadingSpinner = $(` 
                        <div class="loading-overlay">
                            <div class="loading-content">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span class="ml-2">Đang nhập dữ liệu, vui lòng đợi...</span>                    
                            </div>
                        </div>
                    `);
                        $('body').append(newLoadingSpinner);

                        formData.append('removeId', true);

                        $.ajax({
                            url: import_url,
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $(
                                    'meta[name="csrf-token"]'
                                ).attr(
                                    'content')
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                newLoadingSpinner
                                    .remove();

                                if (response.success) {
                                    loadData(pageId);

                                    showToast(
                                        'Tệp Excel được nhập và ghi đè [PK]s thành công!'
                                    );

                                    // $('#importExcelForm')[0].reset();
                                    $('#importExelModal')
                                        .modal('hide');

                                } else {
                                    newLoadingSpinner
                                        .remove();
                                    showModal('Error',
                                        'Không thể nhập tệp Excel: \n' +
                                        response
                                            .message);
                                }
                            }
                        });
                        $('#importStatusModal').modal('hide');
                        $('#confirmModal').modal('hide');
                    });
            } else {
                loadingSpinner.remove();
                showModal('Error', 'Không thể nhập tệp Excel: ' +
                    response.message);
            }
        },
        error: function (xhr, status, error) {
            loadingSpinner.remove();
            showModal('Error', 'Có lỗi xảy ra khi nhập tệp Excel:');
        }
    });
});

// Hàm hiển thị modal với thông điệp
function showModal(title, message) {
    $('#importStatusModalLabel').text(title);

    $('#importStatusMessage').html(message.replace(/\n/g, '<br>'));

    $('#importStatusModal').modal('show');

    $('#importExelModal').modal('hide');
}

$(document).on('click', '#export-excel-btn', function () {

    var search = '';
    var sort = $('th.sortable-column a.desc, th.sortable-column a.asc').data('sort');

    $.ajax({
        url: export_url,
        type: 'GET',
        data: {
            pageId: pageId,
            sort: sort,
            search: search,
        },
        success: function (response) {
            console.log("🚀 ~ pjaxUrl:", pjaxUrl);
            console.log("🚀 ~ search:", search);
            console.log("🚀 ~ sort:", sort);
        },
        error: function (xhr, status, error) {
            alert('Có lỗi xảy ra trong quá trình xuất dữ liệu.');
        }
    });
});



