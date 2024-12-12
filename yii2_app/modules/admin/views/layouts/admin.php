<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\models\User;
use yii\bootstrap5\Html;
use yii\web\JqueryAsset;
use yii\web\View;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$errorMessage = Yii::$app->session->getFlash('error');
$successMessage = Yii::$app->session->getFlash('success');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <?php
    $this->registerAssetBundle(JqueryAsset::class, View::POS_HEAD);
    $this->registerJsFile('js/libs/sidebar-admin-menu.js', ['depends' => AppAsset::class]);

    ?>

</head>


<body>
    <?php $this->beginBody(); ?>
    <!-- loader starts-->
    <div class="loader-wrapper">
        <div class="theme-loader">
            <div class="loader-p"></div>
        </div>
    </div>
    <!-- loader ends-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <div class="page-header">
            <div class="header-wrapper row m-0">
                <div class="header-logo-wrapper col-auto p-0">
                    <div class="logo-wrapper"><a href="<?= \yii\helpers\Url::to(['/']) ?>"><img
                                class="img-fluid logo-cs for-light" src="<?= Yii::getAlias('@web') ?>/images/logo-1.png"
                                alt=""></a>
                    </div>
                    <div class="toggle-sidebar">
                        <svg class="sidebar-toggle">
                            <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-animation"></use>
                        </svg>
                    </div>
                </div>
                <div class="left-header col-xxl-5 col-xl-6 col-auto box-col-4 horizontal-wrapper p-0">

                </div>
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
                                <li><a href="<?= Yii::$app->urlManager->createUrl(['admin/pages/']) ?>"><span><i
                                                class="fa-solid fa-gear me-2"></i>Cài đặt</span></a></li>
                                <li><a href="<?= Yii::$app->urlManager->createUrl(['site/change-password']) ?>"><span><i
                                                class="fa-solid fa-key me-2"></i></i>Đổi mật khẩu</span></a></li>
                                <li>
                                    <form
                                        action="<?= Html::encode(Yii::$app->urlManager->createUrl(['site/logout'])) ?>"
                                        method="post">
                                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
                                        <a class="d-inline" href="#"
                                            onclick="this.closest('form').submit(); return false;">
                                            <span><i class="fa-solid fa-right-to-bracket me-2"></i> Đăng xuất</span>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                            <?php else: ?>
                            <div class="auth-buttons">
                                <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>"
                                    class="btn btn-primary me-1">
                                    <i class="fa-solid fa-right-to-bracket"></i> Login
                                </a>
                                <a href="<?= Yii::$app->urlManager->createUrl(['site/signup']) ?>"
                                    class="btn btn-outline-success">
                                    <i class="fa-solid fa-user-plus"></i> Sign Up
                                </a>
                            </div>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
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
                            <li class="back-btn"><a href="<?= \yii\helpers\Url::to(['/']) ?>"><img class="img-fluid"
                                        src="<?= Yii::getAlias('@web') ?>/images/logo-icon.png" alt=""></a>
                                <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                        aria-hidden="true"></i></div>
                            </li>
                            <?php echo \yii\widgets\Menu::widget([
                                'items' => [
                                    [
                                        'label' => '',
                                        'url' => ['/']
                                    ],
                                    ['label' => 'Quản lý Page', 'url' => ['pages/'], 'template' => '<a class="sidebar-link sidebar-title link-nav" href="{url}"><i class="fa-solid fa-table"></i> <span>{label}</span></a>'],
                                    ['label' => 'Quản lý Menu', 'url' => ['menus/'], 'template' => '<a class="sidebar-link sidebar-title link-nav" href="{url}"><i class="fa-solid fa-folder-tree"></i> <span>{label}</span></a>'],
                                    ['label' => 'Quản lý File', 'url' => ['files/'], 'template' => '<a class="sidebar-link sidebar-title link-nav" href="{url}"><i class="fa-solid fa-file"></i> <span>{label}</span></a>'],
                                    ['label' => 'Quản lý Người dùng', 'url' => ['users/'], 'template' => '<a class="sidebar-link sidebar-title link-nav" href="{url}"><i class="fa-solid fa-user"></i> <span>{label}</span></a>', 'visible' => Yii::$app->user->identity->role == User::ROLE_ADMIN],
                                ],
                                'encodeLabels' => false,
                                'options' => ['class' => 'sidebar-links', 'id' => 'simple-bar'],
                                'itemOptions' => ['class' => 'sidebar-list'],
                                'submenuTemplate' => "\n<ul class='sidebar-submenu'>\n{items}\n</ul>\n",
                                'linkTemplate' => '<a class="sidebar-link sidebar-title" href="{url}"><span>{label}</span><div class="according-menu"><i class="fa fa-angle-right"></i></div></a>',

                            ]); ?>

                        </div>
                        <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
                    </nav>
                </div>
            </div>
            <!-- Page Sidebar Ends-->
            <div class="page-body">
                <!-- Container-fluid starts-->
                <div class="container-fluid pt-2">
                    <div class="row">
                        <div class="col-sm-12">

                            <?= $content ?>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid Ends-->
            </div>
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
var successMessage = <?= json_encode($successMessage) ?>;
var errorMessage = <?= json_encode($errorMessage) ?>;
</script>

</html>
<?php $this->endPage() ?>