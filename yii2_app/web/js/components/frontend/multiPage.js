$(document).ready(function () {

    if (firstpageId !== null) {
        loadPageData(firstpageId);
    } else {
        console.log("No pages available to load data.");
    }

    $('#expand-option').on('click', function () {
        var pageList = $('#page-list');
        var pageItems = pageList.find('.nav-item');

        if (pageList.hasClass('expand')) {
            pageList.removeClass('expand');
            pageItems.show();
            $('#expand-option').text('Thu gọn');

        } else {
            pageList.addClass('expand');
            checkOverflow();
            $('#expand-option').text('Mở Rộng');

        }
    });


    function checkOverflow() {
        var pageList = $('#page-list');
        var pageItems = pageList.find('.nav-item');
        var containerWidth = pageList.width();
        var totalWidth = 0;
        var visibleCount = 0;

        pageItems.each(function (index) {
            totalWidth += $(this).outerWidth(true);
            if (totalWidth <= containerWidth) {
                visibleCount++;
            } else {
                return false;
            }
        });

        pageItems.each(function (index) {
            if (index >= visibleCount) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    }

    checkOverflow();
    $(window).resize(function () {
        checkOverflow();
    });

});

function loadPageData(pageId) {
    if ($('#listPageModal').hasClass('show')) {
        $('#listPageModal').modal('hide');
    }
    $.ajax({
        url: loadPageUrl,
        type: "GET",
        data: {
            pageId: pageId,
            menuId: menuId,
        },
        success: function (data) {
            $('#table-data-current').html(data);
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
