<?php

use app\models\User;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabId = $_GET['tab_id'];

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
<div class="d-flex mb-3">
    <div class="btn-group-ellipsis me-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-ellipsis"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                    data-bs-target="#hideModal">
                    <i class="fas fa-eye me-1"></i> Show/Hidden Tab
                </a>
            </li>
            <li>
                <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                    data-bs-target="#sortModal">
                    <i class="fas fa-sort-amount-down me-1"></i> Sort Order Tab
                </a>
            </li>
            <li>
                <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                    data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt me-1"></i> Delete Tab
                </a>
            </li>
            <li>
                <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                    data-bs-target="#trashBinModal">
                    <i class="fas fa-trash me-1"></i> Trash Bin
                </a>
            </li>
        </ul>
    </div>
    <div class="ms-auto">
        <div class="btn btn-outline-secondary">
            <span class="fw-medium"><?= $richtextTab->tab_name ?></span> | <span class="fw-bold">Richtext</span>
        </div>
    </div>
</div>

<div class="form-group">
    <textarea id="editor" name="content" class="form-control" rows="10"><?= $content ?></textarea>
</div>
<div class="d-flex justify-content-between mb-3">
    <a href="<?= \yii\helpers\Url::to(['tabs/download', 'tab_id' => $tabId]) ?>" class="btn btn-primary"
        target="_blank">
        Download .txt
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
    document.getElementById('save-button').addEventListener('click', function () {
        var content = document.getElementById('editor').value;
        const tabId = $(this).data('tab-id');

        $.ajax({
            url: "<?= \yii\helpers\Url::to(['tabs/save-richtext']) ?>",
            type: "POST",
            data: {
                tabId: tabId,
                content: content
            },

            success: function (response) {
                var toastElementSuccess = document.getElementById('liveToastSuccess');
                var toastBodySuccess = toastElementSuccess.querySelector('.toast-body');
                toastBodySuccess.innerText = "The content was saved successfully!";

                var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                    delay: 3000
                });
                toastSuccess.show();
            },
            error: function (xhr, status, error) {
                alert('An error occurred while saving the content. Please try again later.');
            }
        });
    });
    $(document).ready(function () {
        $('#confirm-delete-btn').on('click', function () {
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
                success: function (response) {
                    if (response.success) {
                        location.reload();
                        $('#deleteModal').modal('hide');
                    } else {
                        alert(response.message || "Deleting table failed.");
                    }
                },
                error: function (error) {
                    alert("An error occurred while deleting table.");
                }
            });
        });

        $('#confirm-delete-permanently-btn').on('click', function () {
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
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                            $('#deleteModal').modal('hide');
                        } else {
                            alert(response.message || "Deleting table failed.");
                        }
                    },
                    error: function (error) {
                        alert("An error occurred while deleting table.");
                    }
                });
            }
        });
    });
</script>