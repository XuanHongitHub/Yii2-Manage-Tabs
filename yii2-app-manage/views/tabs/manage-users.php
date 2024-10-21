<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs">
            <?php foreach ($tabs as $index => $tab): ?>
            <li class="nav-item">
                <a class="nav-link <?= $index === 0 ? 'active' : '' ?>" data-toggle="tab" href="#tab<?= $tab->id ?>">
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="card-body pt-0">
        <div class="tab-content">
            <?php foreach ($tabs as $index => $tab): ?>
            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="tab-<?= $tab->id ?>">
                <?php if ($tab->tab_type == 'table'): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button class="btn btn-light me-2" title="Menu">
                            <i class="fas fa-bars"></i>
                        </button>
                        <button class="btn btn-light" title="Trash">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Tìm kiếm..." aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Tìm</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="border-bottom-primary">
                                <th scope="col">Id</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Username</th>
                                <th scope="col">Designation</th>
                                <th scope="col">Company</th>
                                <th scope="col">Language</th>
                                <th scope="col">Country</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Thêm dữ liệu vào đây -->
                        </tbody>
                    </table>
                </div>
                <?php elseif ($tab->tab_type == 'richtext'): ?>
                <!-- Nội dung cho tab kiểu richtext -->
                <h5>Richtext Editor</h5>
                <div class="richtext-area" contenteditable="true"
                    style="border: 1px solid #ced4da; padding: 10px; min-height: 150px;">
                    <p>Bắt đầu nhập nội dung ở đây...</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>