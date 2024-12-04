$(document).ready(function () {
    var editor1;
    var initialContent = $('#richtext-editor').val();
    var isEditing = false;

    // Chuyển đổi sang chế độ chỉnh sửa
    $('#edit-button').on('click', function () {
        $('#content-display').hide();
        $('#edit-content').show();
        $('#edit-button').hide();
        $('#save-button').show();
        $('#cancel-edit-button').show();

        // Khởi tạo editor khi cần chỉnh sửa
        if (!editor1) {
            editor1 = new RichTextEditor("#richtext-editor");
        }

        isEditing = true;
    });

    // Hủy thay đổi
    $('#cancel-edit-button').on('click', function () {
        // Quay lại nội dung ban đầu
        $('#richtext-editor').val(initialContent);
        $('#content-display').show();
        $('#edit-content').hide();
        $('#edit-button').show();
        $('#save-button').hide();
        $('#cancel-edit-button').hide();
        isEditing = false;
    });

    // Lưu nội dung sau khi chỉnh sửa
    $('#save-button').on('click', function () {
        var updatedContent = editor1.getHTMLCode();

        // Nếu nội dung có thay đổi, thực hiện lưu
        if (updatedContent !== initialContent) {
            $.ajax({
                url: save_richtext_url,
                type: "POST",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                    content: updatedContent
                },
                success: function (response) {
                    initialContent =
                        updatedContent; // Cập nhật nội dung ban đầu sau khi lưu
                    $('#content-display').html(
                        updatedContent); // Cập nhật nội dung hiển thị
                    $('#content-display').show();
                    $('#edit-content').hide();
                    $('#edit-button').show();
                    $('#save-button').hide();
                    $('#cancel-edit-button').hide();
                    showToast('Lưu thành công!');
                },
                error: function (xhr, status, error) {
                    showToast('Có lỗi xảy ra khi lưu!');
                }
            });
        } else {
            showToast('Không có thay đổi nào!');
        }
    });
});