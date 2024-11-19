<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'Create Group';

?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar-settings.php'); ?>


<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Msg</div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <!-- You can add page title or breadcrumbs here -->
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h4>Thêm Page</h4>
                        <p class="mt-1 f-m-light">Page chứa các tab con</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                                <label for="name" class="form-label">Tên Page</label>
                                <input type="text" id="name" class="form-control" value="">
                            </div>
                            <div class="col-12 col-md-6 col-lg-2 col-xl-2 mb-3">
                                <label for="tabs" class="form-label">Chọn Tab</label>
                                <select id="tabs" class="form-select" multiple size="5">
                                    <?php foreach ($tabs as $tab): ?>
                                    <?php if ($tab->menu_id === null): ?>
                                    <option value="<?= $tab->id ?>"><?= $tab->tab_name ?></option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>

                        <div class="mt-3">
                            <button type="button" id="saveTabPageChanges" class="btn btn-success">Tạo Page</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#saveTabPageChanges').on('click', function() {
        var pageName = $('#name').val();
        var selectedTabs = $('#tabs').val(); // Lấy danh sách tab đã chọn

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['create-or-update-page']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: pageName,
                pageTab: selectedTabs, // Gửi các tab đã chọn
            },
            success: function(response) {
                if (response.success) {
                    // Hiển thị thông báo thành công
                    $('#toast-body').text(response.message);
                    $('#liveToast').toast('show');
                    setTimeout(function() {
                        window.location.href =
                            '<?= \yii\helpers\Url::to(['index']) ?>';
                    }, 2000); // Sau 2 giây, chuyển hướng về trang index
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });
});
</script>