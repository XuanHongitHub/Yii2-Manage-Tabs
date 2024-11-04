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


<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm remove tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this tab? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete permanently</button>
            </div>
        </div>
    </div>
</div>


<script>
function loadTabData(tabId, page) {
    console.log("🚀 ~ rrrrr loadTabData ~ tabId:", tabId);

    $.ajax({
        url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
        type: "GET",
        data: {
            tabId: tabId,
            page: page
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
    $('#confirm-delete-btn').on('click', function() {
        const tabId = $(this).data('tab-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/delete-tab']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tabId: tabId,
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                    $('#deleteModal').modal('hide');
                } else {
                    alert(response.message || "Deleting table failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while deleting table.");
            }
        });
    });

    $('#confirm-delete-permanently-btn').on('click', function() {
        const tabId = $(this).data('tab-id');
        var tableName = '<?= $tableName ?>';

        if (confirm("Are you sure you want to delete permanenttly?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['tabs/delete-permanently-tab']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    tabId: tabId,
                    tableName: tableName,
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                        $('#deleteModal').modal('hide');
                    } else {
                        alert(response.message || "Deleting table failed.");
                    }
                },
                error: function(error) {
                    alert("An error occurred while deleting table.");
                }
            });
        }
    });
});
</script>