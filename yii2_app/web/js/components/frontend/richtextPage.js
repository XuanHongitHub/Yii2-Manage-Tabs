$(document).ready(function () {
    var initialContent = $('#richtext-editor').val();
    var editor1;
    var isEditing = false;

    // Nút mở trình soạn thảo
    $(document).on('click', '#edit-button', function () {
        $('#content-display').hide();
        $('#edit-content').show();
        $('#edit-button').hide();

        initialContent = $('#richtext-editor').val() || $('#content-display').html();

        editor1 = new RichTextEditor("#richtext-editor", {
            language: "vi",
        });

        editor1.setHTMLCode(initialContent);

        isEditing = true;
    });

    // Xử lý khi nhấn Cancel (mở modal xác nhận)
    $(document).on('click', '.rte_command_cancelrt', function () {
        $('#cancelModal').modal('show');
    });

    // Xác nhận Cancel và khôi phục nội dung
    $(document).on('click', '#confirmCancel', function () {
        if (editor1) {
            editor1.setHTMLCode(initialContent); // Khôi phục nội dung ban đầu
        }

        $('#cancelModal').modal('hide'); // Đóng modal
        $('#edit-content').hide();
        $('#content-display').show();
        $('#edit-button').show();

        isEditing = false;
    });

    // Lưu nội dung
    $(document).on('click', '.rte_command_savert', function () {
        if (editor1) {
            var updatedContent = editor1.getHTMLCode();

            if (updatedContent === initialContent) {
                showToast('Không có thay đổi nào!');
                return;
            }

            if (isEditing) {
                $('#confirmationModal').modal('show');
            }
        }
    });

    // Xác nhận lưu
    $(document).on('click', '#confirmSave', function () {
        if (editor1) {
            var updatedContent = editor1.getHTMLCode();

            $.ajax({
                url: save_richtext_url,
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    pageId,
                    content: updatedContent,
                },
                success: function (data) {
                    initialContent = updatedContent;
                    $('#content-display').html(updatedContent);
                    swal({
                        title: "Thành công!",
                        text: "Dữ liệu đã được lưu thành công.",
                        icon: "success",
                    });
                    $('#confirmationModal').modal('hide');
                    $('#edit-content').hide();
                    $('#content-display').show();
                    $('#edit-button').show();

                    isEditing = false;
                },
                error: function () {
                    swal({
                        title: "Lỗi hệ thống!",
                        text: "Không thể thực hiện yêu cầu, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            });
        }
    });
});
