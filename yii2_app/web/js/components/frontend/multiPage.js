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
            $('#expand-option').html('<i class="fa-solid fa-square-minus"></i>Thu gọn');
        } else {
            pageList.addClass('expand');
            checkOverflow();
            $('#expand-option').html('<i class="fa-solid fa-chevron-down me-2"></i>Mở Rộng');
        }
    });


    function checkOverflow() {
        var pageList = $('#page-list');
        var pageItems = pageList.find('.nav-item');
        var containerWidth = pageList.parent().width();
        var totalWidth = 0;
        var visibleCount = 0;

        var dropdownMenu = $('#dropdown-menu-list');
        dropdownMenu.find('.nav-item').appendTo(pageList);

        pageItems = pageList.find('.nav-item');
        pageItems.show();

        pageItems.each(function (index) {
            totalWidth += $(this).outerWidth(true);
            if (totalWidth <= containerWidth - 50) {
                visibleCount++;
            } else {
                return false;
            }
        });

        dropdownMenu.empty();
        pageItems.each(function (index) {
            if (index >= visibleCount) {
                var item = $(this).clone();
                dropdownMenu.append(item);
                $(this).hide();
            }
        });
    }

    checkOverflow();
    $(window).resize(function () {
        checkOverflow();
    });

    let ascending = true;

    // Sắp xếp danh sách khi nhấn nút
    $('#sort-toggle').click(function () {
        ascending = !ascending;
        sortPageList(ascending);
        $(this).html(
            `<i class="fa-solid ${ascending ? 'fa-arrow-up-a-z' : 'fa-arrow-down-a-z'}"></i> Sắp xếp ${ascending ? 'A-Z' : 'Z-A'}`
        );
    });

    // Tìm kiếm theo từ khóa
    $(document).off('input', '#search-page').on('input', '#search-page', function () {
        var searchQuery = $(this).val().toLowerCase();
        $('#page-list-modal .list-group-item').each(function () {
            var itemText = $(this).text().toLowerCase();
            if (itemText.indexOf(searchQuery) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Hàm sắp xếp danh sách
    function sortPageList(ascending) {
        const items = $('#page-list-modal li').get();
        items.sort((a, b) => {
            const textA = $(a).find('a').text().toLowerCase();
            const textB = $(b).find('a').text().toLowerCase();
            return ascending ? textA.localeCompare(textB) : textB.localeCompare(textA);
        });
        $('#page-list-modal').append(items);
    }

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
            $('#page-data-current').html(data);
            $('.nav-link').removeClass('active');
            $('.nav-item').removeClass('active');
            $(`[data-id="${pageId}"]`).addClass('active');
            $(`[data-id="${pageId}"]`).closest('.nav-item').addClass('active');
            if ($(`[data-id="${pageId}"]`).closest('.nav-item').is(':hidden')) {
                var expandOption = $('#expand-option');
                if ($('#page-list').hasClass('expand')) {
                    expandOption.trigger('click');
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.');
        }
    });
}
