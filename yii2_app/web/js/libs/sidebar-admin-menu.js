(function ($) {
    $(document).ready(function () {
        var currentUrl = window.location.pathname;

        // Duyệt qua tất cả các liên kết trong sidebar
        $(".sidebar-wrapper nav ul li a").each(function () {
            var linkHref = $(this).attr('href'); // Lấy href của link

            // Kiểm tra nếu đường dẫn hiện tại có chứa phần đường dẫn của link (không cần phải trùng khớp chính xác toàn bộ URL)
            if (currentUrl.indexOf(linkHref) !== -1) {
                $(this).addClass("active"); // Thêm class active nếu URL trùng khớp
                $(this).parents("ul.sidebar-submenu").slideDown("normal"); // Mở submenu của tab
                $(this).parents("li").addClass("active"); // Thêm class active vào <li> chứa tab
                $(this).parents("ul.sidebar-submenu").prev("a").addClass("active"); // Thêm active vào link cha
            } else {
                $(this).removeClass("active"); // Loại bỏ class active nếu không khớp
            }
        });
    });
})(jQuery);
