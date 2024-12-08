$(document).ready(function () {

    if (firstpageId !== null) {
        loadPageData(firstpageId);
    } else {
        console.log("No pages available to load data.");
    }

});

function loadPageData(pageId) {
    $.ajax({
        url: loadPageUrl,
        type: "GET",
        data: {
            pageId: pageId,
            menuId: menuId,
        },
        success: function (data) {
            $('#table-data-current').html(data);
            // Cập nhật trạng thái của page hiện tại
            $('.nav-link').removeClass('active');
            $('.nav-item').removeClass('active');
            $(`[data-id="${pageId}"]`).addClass('active');
            $(`[data-id="${pageId}"]`).closest('.nav-item').addClass('active');

        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.');
        }
    });
}