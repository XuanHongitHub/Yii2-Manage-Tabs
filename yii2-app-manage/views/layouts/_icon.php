<?php

$iconOptions = [
    'stroke-widget' => 'Tiện ích',
    'stroke-home' => 'Trang chủ',
    'stroke-layout' => 'Bố cục',
    'stroke-project' => 'Dự án',
    'stroke-file' => 'Tệp',
    'stroke-board' => 'Bảng',
    'stroke-ecommerce' => 'Thương mại điện tử',
    'stroke-email' => 'Email',
    'stroke-chat' => 'Trò chuyện',
    'stroke-user' => 'Người dùng',
    'stroke-bookmark' => 'Dấu trang',
    'stroke-contact' => 'Liên hệ',
    'stroke-task' => 'Nhiệm vụ',
    'stroke-calendar' => 'Lịch',
    'stroke-social' => 'Xã hội',
    'stroke-to-do' => 'Việc cần làm',
    'stroke-search' => 'Tìm kiếm',
    'stroke-form' => 'Biểu mẫu',
    'stroke-table' => 'Tab',
    'stroke-ui-kits' => 'UI Bộ công cụ',
    'stroke-bonus-kit' => 'Bộ công cụ thưởng',
    'stroke-animation' => 'Hoạt ảnh',
    'stroke-icons' => 'Biểu tượng',
    'stroke-button' => 'Nút',
    'stroke-charts' => 'Biểu đồ',
    'stroke-landing-page' => 'Trang đích',
    'stroke-sample-page' => 'Trang mẫu',
    'stroke-internationalization' => 'Quốc tế hóa',
    'stroke-starter-kit' => 'Bộ công cụ khởi động',
    'stroke-others' => 'Khác',
    'stroke-gallery' => 'Thư viện',
    'stroke-blog' => 'Blog',
    'stroke-faq' => 'Câu hỏi thường gặp',
    'stroke-job-search' => 'Tìm kiếm việc làm',
    'stroke-learning' => 'Học tập',
    'stroke-maps' => 'Bản đồ',
    'stroke-editors' => 'Biên tập viên',
    'stroke-knowledgebase' => 'Cơ sở tri thức',
    'stroke-support-tickets' => 'Vé hỗ trợ'
];

?>

<script>
$(document).ready(function() {
    $('#icon-select-wrapper').on('click', function() {
        $('#icon-list').toggleClass('hide-important');
    });

    $('#icon-list .icon-item').on('click', function() {
        var selectedIcon = $(this).data('icon');

        $('#selected-icon-label').text('Icon: ' + selectedIcon);
        $('#selected-icon use').attr('href', '<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#' +
            selectedIcon);

        $('#icon-list .icon-item').removeClass('selected');
        $(this).addClass('selected');

        $('#icon-selected-value').val(selectedIcon);

        // Ẩn lại icon list sau khi chọn
        $('#icon-list').removeClass('hide-important');
    });

    $('#icon-list .icon-item').css({
        'border': '1px solid transparent',
        'border-radius': '8px',
        'padding': '4px 4px 0px 4px !important'
    });

    $('#icon-list .icon-item.selected').css({
        'border': '2px solid #4171cb'
    });
});
</script>