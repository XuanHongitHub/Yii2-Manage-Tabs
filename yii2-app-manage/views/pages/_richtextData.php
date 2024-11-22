<?php

use app\models\User;

$pageId = $_GET['pageId'];

?>

<div class="d-flex justify-content-end my-3">
    <button type="button" class="btn btn-secondary me-2" id="cancel-edit-button" style="display: none;">
        <i class="fa fa-times me-1"></i> Hủy
    </button>
    <button type="button" class="btn btn-warning me-4" id="save-button" data-page-id="<?= $pageId ?>">
        <i class="fa fa-edit me-1"></i> Sửa
    </button>
</div>

<div class="form-group my-3">
    <div name="content" id="div_editor1">
        <?= $content ?>
    </div>
</div>

<!-- Modal xác nhận -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmationModalLabel">Xác nhận lưu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn lưu các thay đổi?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmSave">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
    function loadTabData(pageId, page, search, pageSize) {
        localStorage.clear();

        var loadingSpinner = $(`
             <div class="spinner-fixed">
                <i class="fa fa-spin fa-spinner me-2"></i>
            </div>
        `);
        $('body').append(loadingSpinner);

        $.ajax({
            url: "<?= \yii\helpers\Url::to(['pages/load-page-data']) ?>",
            type: "GET",
            data: {
                pageId: pageId,
                page: page,
                search: search,
                pageSize: pageSize,
            },
            success: function(data) {
                loadingSpinner.remove();

                $('#table-data-current').html(data);
                // Cập nhật trạng thái của page hiện tại
                $('.nav-link').removeClass('active');
                $('.nav-item').removeClass('active');
                $(`[data-id="${pageId}"]`).addClass('active');
                $(`[data-id="${pageId}"]`).closest('.nav-item').addClass('active');
            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.');
            }
        });
    }
    $(document).ready(function() {
        var editor1 = new RichTextEditor("#div_editor1");
        editor1.setReadOnly(true);

        var initialContent = editor1.getHTMLCode();
        var isEditing = false;

        $(document).off('click', '#save-button').on('click', '#save-button', function() {
            var button = $(this);

            if (button.hasClass('btn-warning')) {
                isEditing = true;
                editor1.setReadOnly(false);
                $('#cancel-edit-button').show();
                button.removeClass('btn-warning').addClass('btn-success').html(
                    '<i class="fa fa-save me-1"></i> Lưu');

                initialContent = editor1.getHTMLCode();
            } else if (isEditing) {
                var currentContent = editor1.getHTMLCode();
                if (currentContent !== initialContent) {
                    $('#confirmationModal').modal('show');
                } else {
                    var toastElementError = document.getElementById('liveToastError');
                    var toastBodyError = toastElementError.querySelector('.toast-body');
                    toastBodyError.innerText = "Không có thay đổi nào để lưu.";
                    var toastError = new bootstrap.Toast(toastElementError, {
                        delay: 3000
                    });
                    toastError.show();
                }
            }
        });

        $(document).off('click', '#cancel-edit-button').on('click', '#cancel-edit-button', function() {
            editor1.setHTMLCode(initialContent);
            editor1.setReadOnly(true);
            $('#save-button').removeClass('btn-success').addClass('btn-warning').html(
                '<i class="fa fa-edit me-1"></i> Sửa');
            $('#cancel-edit-button').hide();
            isEditing = false;
        });

        $(document).off('click', '#confirmSave').on('click', '#confirmSave', function() {
            var content = editor1.getHTMLCode();
            const pageId = $('#save-button').data('page-id');

            $.ajax({
                url: "<?= \yii\helpers\Url::to(['pages/save-richtext']) ?>",
                type: "POST",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                    content: content
                },
                success: function(response) {
                    var toastElementSuccess = document.getElementById('liveToastSuccess');
                    var toastBodySuccess = toastElementSuccess.querySelector('.toast-body');
                    toastBodySuccess.innerText = "Lưu thành công!";

                    var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                        delay: 3000
                    });
                    toastSuccess.show();

                    initialContent = content;
                    editor1.setReadOnly(true);

                    $('#save-button').removeClass('btn-success').addClass('btn-warning').text(
                        'Sửa');
                    $('#cancel-edit-button').hide();
                    $('#confirmationModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    var toastElementError = document.getElementById('liveToastError');
                    var toastError = new bootstrap.Toast(toastElementError, {
                        delay: 3000
                    });
                    toastError.show();
                }
            });
        });
    });
</script>