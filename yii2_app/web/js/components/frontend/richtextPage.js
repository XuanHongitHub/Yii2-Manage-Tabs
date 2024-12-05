$(document).ready(function () {
    var editor1;
    var initialContent = $('#richtext-editor').val();
    var isEditing = false;

    $(document).off('click', '#edit-button').on('click', '#edit-button', function () {
        $('#content-display').hide();
        $('#edit-content').show();
        $('#edit-button').hide();
        $('#save-button').show();
        $('#cancel-edit-button').show();

        if (!editor1) {
            editor1 = new RichTextEditor("#richtext-editor");
        }

        isEditing = true;
    });

    $(document).off('click', '#cancel-edit-button').on('click', '#cancel-edit-button', function () {
        $('#richtext-editor').val(initialContent);
        $('#content-display').show();
        $('#edit-content').hide();
        $('#edit-button').show();
        $('#save-button').hide();
        $('#cancel-edit-button').hide();
        isEditing = false;
    });

    $(document).off('click', '#save-button').on('click', '#save-button', function () {
        var updatedContent = editor1.getHTMLCode();

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
                        updatedContent;
                    $('#content-display').html(
                        updatedContent);
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