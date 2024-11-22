<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\bootstrap5\Html;

use app\models\User;
use app\models\Page;
use app\models\Menu;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabMenus = Menu::find()
    ->where(['deleted' => 0])
    ->orderBy(['position' => SORT_ASC])
    ->all();


?>

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

        <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">                        
            <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
            <div class="ProfileCard-details">
            <div class="ProfileCard-realName">{{name}}</div>
            </div>
            </div>
          </script>
        <script class="empty-template" type="text/x-handlebars-template">
            <div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div>
                </script>
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
                                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $menu->icon ?>">
                                    </use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-editors"></use>
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
                                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $menu->icon ?>">
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