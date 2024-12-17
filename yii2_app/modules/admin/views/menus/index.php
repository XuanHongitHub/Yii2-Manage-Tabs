<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use app\assets\Select2Asset;

/** @var yii\web\View $this */
Select2Asset::register($this);

$this->title = 'Danh Sách Menu';

$this->registerJsFile('js/components/admin/indexMenu.js', ['depends' => AppAsset::class]);
?>
<?php include Yii::getAlias('@app/views/layouts/_icon.php'); ?>
<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                <h4>Danh sách Menu</h4>
                <p class="mt-1 f-m-light">Menu Nhóm | Menu Đơn</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                <a class="btn btn-outline-warning me-2 mb-2" href="#" data-bs-toggle="modal"
                    data-bs-target="#hideModal">
                    <i class="fas fa-eye me-1"></i> Hiện/Ẩn
                </a>
                <a class="btn btn-outline-primary me-2 mb-2" href="#" data-bs-toggle="modal"
                    data-bs-target="#sortModal">
                    <i class="fas fa-sort-amount-down me-1"></i> Sắp Xếp
                </a>
                <a class="btn btn-danger me-2 mb-2" href="#" data-bs-toggle="modal" data-bs-target="#trashBinModal">
                    <i class="fas fa-trash me-1"></i> Thùng Rác
                </a>
                <a class="btn btn-success mb-2" href="<?= \yii\helpers\Url::to(['menus/create']) ?>">
                    <i class="fas fa-plus me-1"></i> Thêm Menu
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-responsive custom-scrollbar border table-bordered">
                <thead>
                    <tr>
                        <th class="toggle-all text-center" style="width: 2%">
                            <i class="fa-solid fa-circle-plus"></i>
                        </th>
                        <th colspan="2" style="width: 20%">Tên Menu</th>
                        <th style="width: 5%" class="text-center">Icon</th>
                        <th style="width: 25%">Page</th>
                        <th style="width: 6%" class="text-center text-nowrap">Trạng Thái</th>
                        <th style="width: 6%">Thao tác</th>
                    </tr>
                </thead>
                <?php
                $menuParents = array_filter($menus, fn($menu) => $menu->parent_id === null);
                $menuChildren = array_filter($menus, fn($menu) => $menu->parent_id !== null);
                ?>
                <tbody id="columnsContainer">
                    <?php foreach ($menuParents as $parentMenu): ?>
                        <?php if ($parentMenu->deleted != 1): ?>
                            <tr class="parent-row" data-parent-id="<?= Html::encode($parentMenu->id) ?>">
                                <td class="toggle-icon text-center">
                                    <?php
                                    $hasChildren = array_filter($menuChildren, fn($child) => $child->parent_id == $parentMenu->id);
                                    $hasParent = array_filter($menuChildren, fn($child) => $child->id == $parentMenu->parent_id);
                                    $hasPage = $parentMenu->menuPages;
                                    ?>
                                    <?php if (!empty($hasChildren)): ?>
                                        <i class="fa-solid fa-caret-right"></i>
                                    <?php endif; ?>
                                </td>
                                <td colspan="2"><?= Html::encode($parentMenu->name) ?></td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center" id="icon-display">
                                        <svg class="stroke-icon" width="24" height="24">
                                            <use
                                                href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $parentMenu->icon ?>">
                                            </use>
                                        </svg>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php foreach ($parentMenu->menuPages as $menuPage): ?>
                                            <span class="badge badge-primary"><?= Html::encode($menuPage->page->name) ?></span>
                                        <?php endforeach; ?>
                                    </div>

                                </td>
                                <td class="text-center">
                                    <?= $parentMenu->status == 1 ? '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>' ?>
                                </td>
                                <td class="text-nowrap text-center">
                                    <button class="btn btn-m btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-page-menu-id="<?= $parentMenu->id ?>"
                                        data-menu-name="<?= Html::encode($parentMenu->name) ?>"
                                        data-icon="<?= Html::encode($parentMenu->icon) ?>"
                                        data-status="<?= Html::encode($parentMenu->status) ?>"
                                        data-position="<?= Html::encode($parentMenu->position) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if (!empty($hasChildren)): ?>
                                        <button class="btn btn-m btn-sm btn-outline-primary edit-subpage-btn me-1 disabled">
                                            <i class="fa-solid fa-link"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-m btn-sm btn-outline-primary edit-subpage-btn me-1"
                                            data-menu-id="<?= $parentMenu->id ?>"
                                            data-menu-name="<?= Html::encode($parentMenu->name) ?>">
                                            <i class="fa-solid fa-link"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (!empty($hasPage)): ?>
                                        <button class="btn btn-m btn-sm btn-outline-warning me-1 disabled">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-m btn-sm btn-outline-warning me-1" id="submenu"
                                            data-menu-name="<?= Html::encode($parentMenu->name) ?>"
                                            data-menu-id="<?= $parentMenu->id ?>">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        class="btn btn-m btn-danger btn-sm delete-btn" data-menu-id="<?= $parentMenu->id ?>">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                <tbody id="children-<?= Html::encode($parentMenu->id) ?>" class="child-group">
                    <?php foreach ($menuChildren as $childMenu): ?>
                        <?php if ($childMenu->parent_id == $parentMenu->id): ?>
                            <tr class="child-row" data-parent-id="<?= Html::encode($parentMenu->id) ?>"
                                data-sort-id="<?= Html::encode($childMenu->id) ?>" style="display: none;">
                                <td colspan="2" rowspan=""></td>
                                <td style="width: 18%" class="text-nowrap">
                                    <?= Html::encode($childMenu->name) ?></td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center" id="icon-display">
                                        <svg class="stroke-icon" width="24" height="24">
                                            <use
                                                href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $childMenu->icon ?>">
                                            </use>
                                        </svg>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php foreach ($childMenu->menuPages as $menuPage): ?>
                                            <span class="badge badge-primary"><?= Html::encode($menuPage->page->name) ?></span>
                                        <?php endforeach; ?>
                                    </div>

                                </td>

                                <td class="text-center">
                                    <?= $childMenu->status == 1 ?
                                        '<span class="badge badge-warning">Ẩn</span>' :
                                        '<span class="badge badge-success">Hiện</span>' ?>
                                </td>
                                <td class="text-nowrap text-center">
                                    <button class="btn btn-m btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-page-menu-id="<?= $childMenu->id ?>"
                                        data-menu-name="<?= Html::encode($childMenu->name) ?>"
                                        data-icon="<?= Html::encode($childMenu->icon) ?>"
                                        data-status="<?= Html::encode($childMenu->status) ?>"
                                        data-position="<?= Html::encode($childMenu->position) ?>" data-bs-toggle="tooltip"
                                        title="Chỉnh sửa menu">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-m btn-sm btn-outline-primary edit-subpage-btn me-1"
                                        data-menu-id="<?= $childMenu->id ?>"
                                        data-menu-name="<?= Html::encode($childMenu->name) ?>" data-bs-toggle="tooltip"
                                        title="Chỉnh sửa trang con">
                                        <i class="fa-solid fa-link"></i>
                                    </button>

                                    <?php if (!empty($hasChildren)): ?>
                                        <button class="btn btn-m btn-sm btn-outline-warning me-1 disabled" data-bs-toggle="tooltip"
                                            title="Không thể thêm menu con (đã có mục con)">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-m btn-sm btn-outline-warning me-1" id="submenu"
                                            data-menu-name="<?= Html::encode($childMenu->name) ?>"
                                            data-menu-id="<?= $childMenu->id ?>" data-bs-toggle="tooltip" title="Sửa menu con">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    <?php endif; ?>

                                    <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        class="btn btn-m btn-danger btn-sm delete-btn" data-menu-id="<?= $childMenu->id ?>"
                                        data-bs-toggle="tooltip" title="Xóa menu">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>

                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
                <?php foreach ($menuChildren as $index => $childMenu): ?>
                    <?php if ($childMenu->parent_id == $parentMenu->id): ?>


                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <!-- Hiển thị menu con -->


        </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal sửa Menu  -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Sửa Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMenuForm">
                    <!-- Tên Menu -->
                    <div class="mb-3">
                        <label for="tabmenuName" class="form-label">Tên Menu</label>
                        <input type="text" class="form-control" id="tabmenuName" name="name" required>
                    </div>

                    <!-- Icon -->
                    <div class="mb-3">
                        <label for="icon-selected-value" class="form-label">Chọn icon</label>
                        <input type="hidden" id="icon-selected-value" value="">
                        <div class="row">
                            <div class="col-12">
                                <div id="icon-select-wrapper" class="d-flex align-items-center justify-content-between"
                                    style="cursor: pointer; border: 1px solid #ccc; padding: 8px; border-radius: 8px;">
                                    <span id="selected-icon-label">Chọn icon</span>
                                    <svg id="selected-icon" class="stroke-icon mx-2" width="24" height="24"></svg>
                                </div>

                                <!-- Danh sách icon -->
                                <div id="icon-list" class="d-flex flex-wrap mt-2"
                                    style="display: none; overflow-y: auto; max-height: 200px; border: 1px solid #ccc; border-radius: 8px;">
                                    <?php foreach ($iconOptions as $iconValue => $iconLabel): ?>
                                        <div class="icon-item col-2 col-md-2 col-lg-1 me-2 mb-2 text-center"
                                            data-icon="<?= Html::encode($iconValue) ?>"
                                            style="cursor: pointer; padding: 4px;">
                                            <svg class="stroke-icon" width="40" height="40">
                                                <use
                                                    href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= Html::encode($iconValue) ?>">
                                                </use>
                                            </svg>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label for="menustatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="menustatus" name="status" required>
                            <option value="0">Hiển thị</option>
                            <option value="1">Ẩn</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveTabMenuChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal SubMenu -->
<div class="modal fade" id="subMenuModal" tabindex="-1" aria-labelledby="subMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="subMenuModalLabel"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="sub-menus" class="form-label">Menu Con:</label>
                    <select id="sub-menus" class="form-select form-multi-select" multiple>
                    </select>
                </div>
                <div class="mt-3">
                    <label>Sắp xếp:</label>
                    <ul id="sortable-submenus" class="list-group">
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveSubMenuChanges" data-menu-id="">Lưu thay
                    đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal SubPage -->
<div class="modal fade" id="editSubPageModal" tabindex="-1" aria-labelledby="editSubPageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editSubPageModalLabel">Chỉnh sửa Page Con<< /h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="sub-pages">Page con:</label>
                    <select id="sub-pages" class="form-multi-select" multiple="multiple"></select>
                </div>
                <div class="mt-3">
                    <label>Sắp xếp:</label>
                    <ul id="sortable-subpages" class="list-group">
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveSubPageChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thùng Rác -->
<div class="modal fade" id="trashBinModal" tabindex="-1" aria-labelledby="trashBinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="trashBinModalLabel">Thùng Rác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chọn page bạn muốn khôi phục hoặc xóa hoàn toàn:</p>
                <table class="table table-bordered table-hover table-ui">
                    <thead>
                        <tr>
                            <th>Tên Menu</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedMenus = false; ?>
                        <?php foreach ($menus as $page): ?>
                            <?php if ($page->deleted == 1): ?>
                                <?php $hasDeletedMenus = true; ?>
                                <tr>
                                    <td><?= htmlspecialchars($page->name) ?></td>
                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-warning restore-page-btn" id="confirm-restore-btn"
                                            data-menu-id="<?= htmlspecialchars($page->id) ?>">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger delete-page-btn" id="delete-permanently-btn"
                                            data-page-name="<?= htmlspecialchars($page->name) ?>"
                                            data-menu-id="<?= htmlspecialchars($page->id) ?>">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$hasDeletedMenus): ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted">
                                    <em>There is nothing here.</em>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hide page -->
<div class="modal fade" id="hideModal" tabindex="-1" aria-labelledby="hideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="hideModalLabel">Hiện/Ẩn Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p class="pb-0 mb-0">Chọn page bạn muốn ẩn hoặc hiển thị:</p>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>Tên Menu</th>
                            <th class="text-center" style="width: 8%">Hiện</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-index">
                        <?php foreach ($menus as $menu): ?>
                            <?php if ($menu->deleted != 1): ?>
                                <tr>
                                    <td class="py-0">
                                        <?= htmlspecialchars($menu->name) ?>
                                    </td>
                                    <td class="py-0" class="text-center">
                                        <label class="switch mb-0 mt-1">
                                            <input class="form-check-input toggle-hide-btn" type="checkbox"
                                                data-menu-id="<?= htmlspecialchars($menu->id) ?>"
                                                <?php if ($menu->status == 0): ?> checked <?php endif; ?>>
                                            <span class="switch-state"></span>
                                        </label>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-hide-btn">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sắp Xếp -->
<div class="modal fade" id="sortModal" tabindex="-1" aria-labelledby="sortModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="sortModalLabel">Sắp Xếp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p>Kéo và thả để sắp xếp các menu.</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="toggleStatusMenus" checked>
                    <label class="form-check-label" for="toggleStatusMenus">Hiển thị Menu đã ẩn</label>
                </div>
                <ul class="list-group" id="sortable-pages">
                    <?php foreach ($menuParents as $index => $menu): ?>
                        <?php if ($menu->deleted != 1): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center page-item"
                                data-menu-id="<?= $menu->id ?>" data-status="<?= $menu->status ?>">
                                <span><?= htmlspecialchars($menu->name) ?></span>
                                <span class="badge bg-secondary"><?= $index + 1 ?></span>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-sort-btn">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteModalLabel">Xác nhận xóa page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa page này không? Không thể hoàn tác hành động này.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-menu-id="<?= htmlspecialchars($menuId) ?>">Xóa</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-page-name="<?= htmlspecialchars($menu->name) ?>"
                    data-menu-id="<?= htmlspecialchars($menuId) ?>">Xóa Vĩnh Viễn</button>
            </div>
        </div>
    </div>
</div>

<script>
    var save_sort_url = "<?= \yii\helpers\Url::to(['menus/save-sort']) ?>";
    var update_status_url = "<?= \yii\helpers\Url::to(['menus/update-hide-status']) ?>";
    var update_sortOrder_url = "<?= \yii\helpers\Url::to(['menus/update-sort-order']) ?>";
    var restore_menu_url = "<?= \yii\helpers\Url::to(['menus/restore-menu']) ?>";
    var delete_permanently_url = "<?= \yii\helpers\Url::to(['menus/delete-permanently-menu']) ?>";
    var delete_soft_url = "<?= \yii\helpers\Url::to(['menus/delete-menu']) ?>";
    var get_sub_page_url = "<?= \yii\helpers\Url::to(['menus/get-subpage']) ?>";
    var get_sub_menu_url = "<?= \yii\helpers\Url::to(['menus/get-submenu']) ?>";
    var save_sub_menu_url = "<?= \yii\helpers\Url::to(['menus/save-sub-menu']) ?>";
    var save_sub_page_url = "<?= \yii\helpers\Url::to(['menus/save-sub-page']) ?>";
    var yiiWebAlias = "<?= Yii::getAlias('@web') ?>";
    var update_menu_url = "<?= \yii\helpers\Url::to(['update-menu']) ?>";
</script>