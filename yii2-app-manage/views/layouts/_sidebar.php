<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
?>

<!-- Page Header Start-->
<div class="page-header">
    <div class="header-wrapper row m-0">
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"><a href="<?= \yii\helpers\Url::to(['/']) ?>"><img class="img-fluid for-light"
                        src="<?= Yii::getAlias('@web') ?>/images/logo-1.png" alt=""><img class="img-fluid for-dark"
                        src="<?= Yii::getAlias('@web') ?>/images/logo.png" alt=""></a>
            </div>
            <div class="toggle-sidebar">
                <svg class="sidebar-toggle">
                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-animation"></use>
                </svg>
            </div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-auto box-col-4 horizontal-wrapper p-0">
            <div class="left-menu-header">
                <ul class="header-left">
                    <li>
                        <div class="form-group w-100">
                            <div class="Typeahead Typeahead--twitterUsers">
                                <div class="u-posRelative d-flex">
                                    <svg class="search-bg svg-color me-2">
                                        <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-search">
                                        </use>
                                    </svg>
                                    <input class="demo-input py-0 Typeahead-input form-control-plaintext w-100"
                                        type="text" placeholder="Search .." name="q" title="">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-auto box-col-6 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <li class="serchinput">
                    <div class="serchbox">
                        <svg>
                            <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-search"></use>
                        </svg>
                    </div>
                    <div class="form-group search-form">
                        <input type="text" placeholder="Search here...">
                    </div>
                </li>

                <li>
                    <div class="mode">
                        <svg>
                            <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#moon"></use>
                        </svg>
                    </div>
                </li>

                <li class="profile-nav onhover-dropdown p-0">
                    <div class="d-flex align-items-center profile-media">
                        <img class="b-r-10 img-40" src="<?= Yii::getAlias('@web') ?>/images/profile.png" alt="">
                        <div class="flex-grow-1">
                            <?php if (!Yii::$app->user->isGuest): ?>
                            <span><?= Html::encode(Yii::$app->user->identity->username) ?></span>
                            <p class="mb-0"><?= Html::encode(Yii::$app->user->identity->role) ?> <i
                                    class="middle fa fa-angle-down"></i></p>
                            <?php else: ?>
                            <span>Guest</span>
                            <p class="mb-0">Not logged in <i class="middle fa fa-angle-down"></i></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <?php if (!Yii::$app->user->isGuest): ?>
                        <li>
                            <form action="<?= Html::encode(Yii::$app->urlManager->createUrl(['site/logout'])) ?>"
                                method="post" style="display:inline;">
                                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
                                <a href="#" class="" onclick="this.closest('form').submit(); return false;">
                                    <i data-feather="log-out" class="me-2"></i> <span>Logout</span>
                                </a>
                            </form>
                        </li>
                        <?php else: ?>
                        <li>
                            <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>" class="nav-link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12H9m0 0h6m-6 0l-3 3m3-3l3-3" />
                                </svg> Login
                            </a>
                        </li>
                        <li>
                            <a href="<?= Yii::$app->urlManager->createUrl(['site/signup']) ?>" class="nav-link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 12h14m-7-7l7 7-7 7" />
                                </svg> Sign Up
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
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
            <div class="logo-wrapper"><a href="<?= \yii\helpers\Url::to(['/']) ?>"><img class="img-fluid for-light"
                        src="<?= Yii::getAlias('@web') ?>/images/logo-1.png" alt=""><img class="img-fluid for-dark"
                        src="<?= Yii::getAlias('@web') ?>/images/logo.png" alt=""></a>
                <div class="toggle-sidebar">
                    <svg class="sidebar-toggle">
                        <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#toggle-icon"></use>
                    </svg>
                </div>
            </div>
            <div class="logo-icon-wrapper"><a href="<?= \yii\helpers\Url::to(['/']) ?>"><img class="img-fluid"
                        src="<?= Yii::getAlias('@web') ?>/images/logo-icon.png" alt=""></a></div>
            <nav class="sidebar-main">
                <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                <div id="sidebar-menu">
                    <ul class="sidebar-links" id="simple-bar">
                        <li class="back-btn"><a href="<?= \yii\helpers\Url::to(['/']) ?>"><img class="img-fluid"
                                    src="<?= Yii::getAlias('@web') ?>/images/logo-icon.png" alt=""></a>
                            <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                    aria-hidden="true"></i></div>
                        </li>
                        <li class="pin-title sidebar-main-title">
                            <div>
                                <h6>Pinned</h6>
                            </div>
                        </li>
                        <li class="sidebar-main-title">
                            <div>
                                <h6 class="lan-1">General</h6>
                            </div>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav"
                                href="<?= \yii\helpers\Url::to(['table-tabs/index']) ?>">
                                <svg class="stroke-icon">
                                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#stroke-file"></use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-home"></use>

                                </svg><span>Manage Tabs</span>
                                <div class="according-menu"><i class="fa fa-angle-right"></i>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-list"><a class="sidebar-link sidebar-title link-nav"
                                href="landing-page.html">
                                <svg class="stroke-icon">
                                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#stroke-landing-page">
                                    </use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#fill-landing-page">
                                    </use>
                                </svg><span>Landing page</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </nav>
        </div>
    </div>
    <!-- Page Sidebar Ends-->