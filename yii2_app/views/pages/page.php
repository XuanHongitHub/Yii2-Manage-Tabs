<?php

/** @var yii\web\View $this */

$this->title = 'Pages Data';
?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>

<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">

            </div>
        </div>
    </div>
    <!-- Container-fluid starts -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h4>
                            <?= $page_item ? $page_item->name : 'Page này không tồn tại hoặc đã bị ẩn/xóa.'; ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="page-data-current">
                                <div class="table-responsive" id="table-data-current">
                                    <!-- Data Loading -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
<?php


?>
<script async>
    $(document).ready(function() {

        var table_pageId = <?= !empty($page_item) ? $page_item->id : 'null' ?>;
        if (table_pageId !== null) {
            loadPageData(table_pageId);
        } else {
            console.log("No pages available to load data.");
        }

        function loadPageData(pageId, page, search, pageSize) {
            localStorage.clear();

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
                    $('#table-data-current').html(data);
                    // Cập nhật trạng thái của page hiện tại
                    $('.nav-link').removeClass('active');
                    $('.nav-item').removeClass('active');
                    $(`[data-id="${pageId}"]`).addClass('active');
                    $(`[data-id="${pageId}"]`).closest('.nav-item').addClass('active');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.');
                }
            });
        }


        $(document).off('keydown', '#goToPageInput').on('keydown', '#goToPageInput',
            function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#goToPageButton').click();
                }
            });
        $(document).off('click', '.pagination .paginate_button').on('click', '.pagination .paginate_button',
            function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                var pageId = table_pageId;
                var search = $('input[name="search"]').val();
                var pageSize = $('#pageSize').val();

                if (search && typeof search === 'string') {
                    search = search.trim();
                }

                loadData(pageId, page, search, pageSize);
            });

        $(document).off('click', '#goToPageButton').on('click', '#goToPageButton', function() {
            var page = $('#goToPageInput').val();
            var pageId = table_pageId;
            var search = $('input[name="search"]').val();
            var pageSize = $('#pageSize').val();

            if (search && typeof search === 'string') {
                search = search.trim();
            }

            if (page && !isNaN(page)) {
                page = parseInt(page) - 1;
                loadData(pageId, page, search, pageSize);
            } else {
                console.log('Invalid page number.');
            }
        });


        $(document).off('click', '#lastPageButton').on('click', '#lastPageButton', function(e) {
            e.preventDefault();

            var page = $(this).data('page');
            var pageId = table_pageId;
            var search = $('input[name="search"]').val();
            var pageSize = $('#pageSize').val();
            var totalCount = $('#totalCount').val();

            if (search && typeof search === 'string') {
                search = search.trim();
            }

            lastPage = Math.ceil(totalCount / pageSize) - 1;

            loadData(pageId, lastPage, search, pageSize);
        });


        $(document).off('change', '#pageSize').on('change', '#pageSize', function() {
            var pageSize = $(this).val();

            var pageId = table_pageId;
            var search = $('input[name="search"]').val();

            if (search && typeof search === 'string') {
                search = search.trim();
            }

            if (pageSize && (pageSize === 'all' || !isNaN(pageSize))) {
                loadData(pageId, 0, search, pageSize);
            } else {
                console.log('Invalid page size.');
            }
        });


    });
</script>