<?php

use app\models\User;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabId = $_GET['tabId'];

?>
<div class="toast-container position-fixed top-0 end-0 mt-5 p-3">
    <div id="liveToastSuccess" class="toast bg-success text-white" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Successfully!
        </div>
    </div>

    <div id="liveToastError" class="toast bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Error!
        </div>
    </div>
</div>

<div class="form-group my-3">
    <div name="content" id="div_editor1">
        <?= $content ?>
    </div>
</div>

<script>
$(document).ready(function() {
    var editor1 = new RichTextEditor("#div_editor1");
    //editor1.setHTMLCode("Use inline HTML or setHTMLCode to init the default content.");
});

function loadTabData(tabId, page, search, pageSize) {
    localStorage.clear();

    var loadingSpinner = $(`
             <div class="spinner-fixed">
                <i class="fa fa-spin fa-spinner me-2"></i>
            </div>
        `);
    $('body').append(loadingSpinner);

    $.ajax({
        url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
        type: "GET",
        data: {
            tabId: tabId,
            page: page,
            search: search,
            pageSize: pageSize,
        },
        success: function(data) {
            loadingSpinner.remove();

            $('#table-data-current').html(data);
            // Cập nhật trạng thái của tab hiện tại
            $('.nav-link').removeClass('active');
            $('.nav-item').removeClass('active');
            $(`[data-id="${tabId}"]`).addClass('active');
            $(`[data-id="${tabId}"]`).closest('.nav-item').addClass('active');
        },
        error: function(xhr, status, error) {
            loadingSpinner.remove();
            console.error('Error:', error);
            alert('An error occurred while loading data. Please try again later.');
        }
    });
}
</script>