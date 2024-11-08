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
<div class="d-flex justify-content-between mb-3">
    <a href="<?= \yii\helpers\Url::to(['tabs/download', 'tab_id' => $tabId]) ?>" class="btn btn-primary"
        target="_blank">
        Download .rtf
    </a>
    <button type="button" class="btn btn-success" id="save-button" data-tab-id="<?= $tabId ?>">Save</button>
</div>

<script>
function loadTabData(tabId, page, search, pageSize) {
    console.log("🚀 ~ loadTabData ~ tabId:", tabId);
    localStorage.clear();

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
            $('#table-data-current').html(data);
            // Cập nhật trạng thái của tab hiện tại
            $('.nav-link').removeClass('active');
            $('.nav-item').removeClass('active');
            $(`[data-id="${tabId}"]`).addClass('active');
            $(`[data-id="${tabId}"]`).closest('.nav-item').addClass('active');
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('An error occurred while loading data. Please try again later.');
        }
    });
}
$(document).ready(function() {
    var editor1 = new RichTextEditor("#div_editor1");
    //editor1.setHTMLCode("Use inline HTML or setHTMLCode to init the default content.");
    document.getElementById('save-button').addEventListener('click', function() {
        var content = editor1.getHTMLCode(); // Correctly retrieve content from editor
        const tabId = $(this).data('tab-id');

        $.ajax({
            url: "<?= \yii\helpers\Url::to(['tabs/save-richtext']) ?>",
            type: "POST",
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tabId: tabId,
                content: content
            },
            success: function(response) {
                var toastElementSuccess = document.getElementById('liveToastSuccess');
                var toastBodySuccess = toastElementSuccess.querySelector('.toast-body');
                toastBodySuccess.innerText = "The content was saved successfully!";

                var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                    delay: 3000
                });
                toastSuccess.show();
            },
            error: function(xhr, status, error) {
                var toastElementError = document.getElementById('liveToastError');
                var toastError = new bootstrap.Toast(toastElementError, {
                    delay: 3000
                });
                toastError.show();
            }
        });
    });
});
</script>