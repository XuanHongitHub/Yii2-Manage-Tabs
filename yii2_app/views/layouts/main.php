<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\helpers\Json;

$errorMessage = Yii::$app->session->getFlash('error');
$successMessage = Yii::$app->session->getFlash('success');

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php
    $this->head();
    $cssFile = [
        'css/style.css',
        'css/bootstrap.css',
    ];

    foreach ($cssFile as $css) {
        $this->registerCssFile($css, ['depends' => [\yii\web\YiiAsset::class]]);
    }

    ?>

</head>


<body>
    <?php $this->beginBody(); ?>

    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <div
            class="nav-right col-xxl-7 col-xl-6 col-auto box-col-6 pull-right right-header p-0 ms-auto d-flex align-items-center me-3">
            <ul class="nav-menus">

                <li class="profile-nav onhover-dropdown p-0">
                    <div class="d-flex align-items-center profile-media">
                        <?php if (!Yii::$app->user->isGuest): ?>
                        <svg style="margin-bottom: -5px; width: 30px !important; height: 30px !important;">
                            <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-user"></use>
                        </svg>
                        <div class="flex-grow-1">
                            <span><?= Html::encode(Yii::$app->user->identity->username) ?></span>
                            <p class="mb-0">
                                <?php if (Yii::$app->user->identity->role == 10): ?>
                                User
                                <?php elseif (Yii::$app->user->identity->role == 20): ?>
                                Admin
                                <?php else: ?>
                                <?= Html::encode(Yii::$app->user->identity->role) ?>
                                <?php endif; ?>
                                <i class="middle fa fa-angle-down"></i>
                            </p>

                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="<?= Yii::$app->urlManager->createUrl(['admin/pages/index']) ?>"><span><i
                                        class="fa-solid fa-gear me-2"></i>Cài đặt</span></a></li>
                        <li><a href="<?= Yii::$app->urlManager->createUrl(['site/change-password']) ?>"><span><i
                                        class="fa-solid fa-key me-2"></i></i>Đổi mật khẩu</span></a></li>
                        <li>
                            <form action="<?= Html::encode(Yii::$app->urlManager->createUrl(['site/logout'])) ?>"
                                method="post">
                                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
                                <a class="d-inline" href="#" onclick="this.closest('form').submit(); return false;">
                                    <span><i class="fa-solid fa-right-to-bracket me-2"></i> Đăng xuất</span>
                                </a>
                            </form>
                        </li>
                    </ul>
                    <?php else: ?>
                    <div class="auth-buttons">
                        <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="btn btn-primary me-1">
                            <i class="fa-solid fa-right-to-bracket"></i> Login
                        </a>
                        <a href="<?= Yii::$app->urlManager->createUrl(['site/signup']) ?>"
                            class="btn btn-outline-success">
                            <i class="fa-solid fa-user-plus"></i> Sign Up
                        </a>
                    </div>
                    <?php endif; ?>
                </li>
        </div>
        <!-- Page Header Ends-->
        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            <div class="sidebar-wrapper" data-layout="stroke-svg">
                <div>
                    <div class="logo-wrapper">
                        <a href="<?= \yii\helpers\Url::to(['/']) ?>">
                            <img class="img-fluid for-light" src="<?= Yii::getAlias('@web') ?>/images/logo-1.png"
                                style="width: 141px !important; padding-top: 7px;" alt="">
                        </a>
                        <div class="toggle-sidebar">
                            <i class="fa-solid fa-bars-staggered font-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="logo-icon-wrapper"><a href="<?= \yii\helpers\Url::to(['/']) ?>"><img class="img-fluid"
                                src="<?= Yii::getAlias('@web') ?>/images/logo-icon.png" style="width: 29px !important;"
                                alt=""></a></div>
                    <nav class="sidebar-main">
                        <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                        <div id="sidebar-menu">
                            <ul class="sidebar-links" id="simple-bar">
                                <li class="back-btn">
                                    <a href="<?= \yii\helpers\Url::to(['/']) ?>"><img class="img-fluid"
                                            src="<?= Yii::getAlias('@web') ?>/images/logo-icon.png" alt=""></a>
                                    <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                            aria-hidden="true"></i></div>
                                </li>
                                <li class="sidebar-main-title pt-4">
                                    <div>
                                        <h6 class="lan-1">Menu</h6>
                                    </div>
                                </li>
                                <?php if (!empty($tabMenus)): ?>
                                <?php foreach ($tabMenus as $menu): ?>
                                <?php if ($menu->parent_id === null): ?>
                                <li class="sidebar-list">
                                    <?php
                                                // Kiểm tra menu có con và có page con không
                                                $hasChildren = $menu->getChildMenus()->exists();
                                                ?>
                                    <?php if ($hasChildren): ?>
                                    <!-- Nếu có menu con hoặc page con -->
                                    <a class="sidebar-link sidebar-title" href="#">
                                        <svg class="stroke-icon">
                                            <use
                                                href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $menu->icon ?>">
                                            </use>
                                        </svg>
                                        <svg class="fill-icon">
                                            <use
                                                href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-editors">
                                            </use>
                                        </svg>
                                        <span><?= Html::encode($menu->name) ?></span>
                                        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                    </a>
                                    <ul class="sidebar-submenu" style="display: none;">
                                        <?php if ($hasChildren): ?>
                                        <?php foreach ($menu->getChildMenus()->all() as $childMenu): ?>
                                        <li class="sidebar-list">
                                            <a href="<?= \yii\helpers\Url::to(['/pages', 'menuId' => $childMenu->id]) ?>"
                                                data-menu-id="<?= $childMenu->id ?>"
                                                class="<?= Yii::$app->request->get('pageId') === $childMenu->id ? 'active' : '' ?>">
                                                <svg class="svg-menu">
                                                    <use
                                                        href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $childMenu->icon ?>">
                                                    </use>
                                                </svg>
                                                <?= Html::encode($childMenu->name) ?>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                    <?php else: ?>
                                    <!-- Xử lý trường hợp mặc định cho menu không có con và không có page -->
                                    <a class="sidebar-link sidebar-title link-nav"
                                        href="<?= \yii\helpers\Url::to(['/pages', 'menuId' => $menu->id]) ?>"
                                        data-menu-id="<?= $menu->id ?>">
                                        <svg class="stroke-icon">
                                            <use
                                                href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $menu->icon ?>">
                                            </use>
                                        </svg>
                                        <span><?= Html::encode($menu->name) ?></span>
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>


                        </div>
                        <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
                    </nav>
                </div>
            </div>
            <!-- Page Sidebar Ends-->
            <div class="page-body">
                <!-- Container-fluid starts-->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $content ?>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid Ends-->
            </div>
            <!-- footer start-->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 p-0 footer-copyright">
                            <p class="mb-0">Copyright 2024 © Cominit.</p>
                        </div>
                        <div class="col-md-6 p-0">

                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <?php $this->endBody() ?>

</body>
<!-- Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Thông Báo</div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const errorMessage = <?= Json::encode($errorMessage) ?>;

    const successMessage = <?= Json::encode($successMessage) ?>;
    if (successMessage) {
        document.getElementById('toast-body').textContent = successMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
    if (errorMessage) {
        document.getElementById('toast-body').textContent = errorMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
});
</script>

</html>
<?php $this->endPage() ?>