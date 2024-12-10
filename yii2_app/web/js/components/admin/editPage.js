$(document).ready(function () {
    var rteInitialized = false;
    var initialContent = $('#richtext-editor').val();

    $('#content-display').hide();
    $('#edit-content').show();
    $('#save-button').show();

    if (!rteInitialized) {
        editor1 = new RichTextEditor("#richtext-editor");
        rteInitialized = true;
    }

    if (editor1 && typeof editor1.getHTMLCode === 'function') {
        $('#save-button').on('click', function () {
            var updatedContent = editor1.getHTMLCode();

            if (updatedContent !== initialContent) {
                $('#confirmationModal').modal('show');

                $('#confirmSave').on('click', function () {
                    $.ajax({
                        url: save_richtext_url,
                        type: 'POST',
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                                'content'),
                        },
                        data: {
                            id,
                            content: updatedContent
                        },
                        success: function (response) {
                            initialContent = updatedContent;
                            swal({
                                title: "Thành công!",
                                text: "Dữ liệu đã được lưu thành công.",
                                icon: "success",
                            });
                            $('#confirmationModal').modal('hide');
                            window.location.href = list_page_url;
                        },
                        error: function () {
                            swal({
                                title: "Lỗi hệ thống!",
                                text: "Không thể thực hiện yêu cầu, vui lòng thử lại.",
                                icon: "error",
                            });
                            $('#confirmationModal').modal('hide');
                        }
                    });
                });
            } else {
                showToast('Không có thay đổi nào!');
            }
        });
    } else {
        console.error('Editor chưa được khởi tạo đúng cách');
    }
});