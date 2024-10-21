<?php

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
/** @var app\models\RichtextTab[] $richtextTabs */

$this->title = 'Settings';
?>

<div class="content-body mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#addTableTab">Thêm Mới Table</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#addRichtextTab">Thêm Mới Richtext</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userManagementTab">Quản Lý Người Dùng</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body pt-0">
                    <div class="tab-content">
                        <!-- Tab Thêm Mới Table -->
                        <div class="tab-pane fade show active" id="addTableTab">
                            <h5>Thêm Mới Table</h5>
                            <form id="addTableForm">
                                <div class="mb-3">
                                    <label for="tableName" class="form-label">Tên Bảng</label>
                                    <input type="text" class="form-control" id="tableName" required>
                                </div>
                                <div class="mb-3" id="columnsContainer">
                                    <label for="columns" class="form-label">Cột</label>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" placeholder="Nhập tên cột" required>
                                        <select class="form-select" required>
                                            <option value="">Chọn kiểu dữ liệu</option>
                                            <option value="VARCHAR">VARCHAR</option>
                                            <option value="INT">INT</option>
                                            <option value="TEXT">TEXT</option>
                                            <option value="DATE">DATE</option>
                                            <!-- Thêm các kiểu dữ liệu khác nếu cần -->
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="removeColumn(this)">Xóa</button>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary" type="button" onclick="addColumn()">+ Thêm
                                    Cột</button>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab Thêm Mới Richtext -->
                        <div class="tab-pane fade" id="addRichtextTab">
                            <h5>Thêm Mới Richtext</h5>
                            <form id="addRichtextForm">
                                <div class="mb-3">
                                    <label for="richtextTitle" class="form-label">Tiêu Đề Richtext</label>
                                    <input type="text" class="form-control" id="richtextTitle" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nội Dung Richtext</label>
                                    <div class="richtext-area" contenteditable="true"
                                        style="border: 1px solid #ced4da; padding: 10px; min-height: 150px;"></div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success" id="saveRichtextButton">Lưu Thay
                                        Đổi</button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab Quản Lý Người Dùng -->
                        <div class="tab-pane fade" id="userManagementTab">
                            <h5>Quản Lý Người Dùng</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Trạng Thái</th>
                                            <th scope="col">Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dữ liệu người dùng sẽ được hiển thị ở đây -->

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addColumn() {
        const columnsContainer = document.getElementById('columnsContainer');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group mb-2';
        inputGroup.innerHTML =
            `<input type="text" class="form-control" placeholder="Nhập tên cột" required>
                                <select class="form-select" required>
                                    <option value="">Chọn kiểu dữ liệu</option>
                                    <option value="VARCHAR">VARCHAR</option>
                                    <option value="INT">INT</option>
                                    <option value="TEXT">TEXT</option>
                                    <option value="DATE">DATE</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="removeColumn(this)">Xóa</button>`;
        columnsContainer.appendChild(inputGroup);
    }

    function removeColumn(button) {
        const inputGroup = button.parentElement;
        inputGroup.remove();
    }
</script>