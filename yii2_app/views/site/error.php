<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>

<div class="card">
    <div class="error pt-5">
        <i class="fa-solid fa-triangle-exclamation fw-900 text-primary error-image"
            style="font-size: 6.5rem !important;"></i>
        <div class="error__title"><?= Html::encode($this->title) ?></div>
        <div class="error__subtitle">Hmmm...</div>
        <div class="error__description"><?= nl2br(Html::encode($message)) ?></div>
        <a href="<?= \yii\helpers\Url::to(['/']) ?>" class="error__button error__button--active">Home</a>
    </div>
</div>

<style>
@keyframes shimmer {
    from {
        opacity: 0;
    }

    to {
        opacity: 0.7;
    }
}

.error {
    font-family: 'Righteous', cursive !important;
    color: #363e49;
    text-align: center;
    height: 100vh;
}

.error-image {
    width: 150px;
    /* Kích thước hình ảnh */
    margin-bottom: 20px;
    /* Khoảng cách dưới hình ảnh */
    animation: shimmer 1.5s infinite alternate;
    /* Thêm hiệu ứng hoạt ảnh */
}

.error__title {
    font-size: 7em;
}

.error__subtitle {
    font-size: 2.5em;
}

.error__description {
    opacity: 0.5;
    margin-bottom: 1.75rem;
    font-size: 1.75em;
}

.error__button {
    min-width: 7em;
    margin-top: 3em;
    margin-right: 0.5em;
    padding: 0.6em 1.5em;
    outline: none;
    border: 2px solid #2f3640;
    background-color: transparent;
    border-radius: 8em;
    color: #576375;
    cursor: pointer;
    transition-duration: 0.2s;
    font-size: 1rem;
    font-family: 'Righteous', cursive !important;
    text-transform: uppercase !important;
    margin-bottom: 2rem;
}

.error__button:hover {
    color: #21252c;
}

.error__button--active {
    background-color: #4171cb;
    border: 2px solid #4171cb;
    color: white;
}

.error__button--active:hover {
    box-shadow: 0px 0px 8px 0px rgba(0, 0, 0, 0.5);
    color: white;
}
</style>