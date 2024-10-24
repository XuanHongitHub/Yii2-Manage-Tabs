<?php

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
/** @var app\models\RichtextTab[] $richtextTabs */
/** @var app\models\Tab[] $tabs */

$this->title = 'Manage Tabs';
?>

<div class="content-body mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex">
                <div class="ms-auto">
                    <div class="dropdown dropstart my-2">
                        <a class="btn btn-secondary" href="<?= \yii\helpers\Url::to(['tabs/settings']) ?>"
                            style="color: white; text-decoration: none;">
                            <i class="fa-solid fa-gear"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs">
                        <?php foreach ($tabs as $index => $tab): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="loadTabData(<?= $tab->id ?>)">
                                <?php
                                    $tableTab = app\models\TableTab::find()->where(['tab_id' => $tab->id])->one();
                                    echo $tableTab ? htmlspecialchars($tableTab->table_name) : 'Không tìm thấy bảng';
                                    ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
                <div class="card-body pt-0">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-data-current">
                            <div class="table-tabs" id="table-data-current">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<script>
function loadTabData(tabId) {
    $.ajax({
        url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
        type: "GET",
        data: {
            tab_id: tabId
        },
        success: function(data) {
            $('#table-data-current').html(data);
            $('.tab-pane').removeClass('show active');
            $('#tab-data-current').addClass('show active');
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.');
        }
    });
}
</script>