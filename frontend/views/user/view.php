<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = $model->_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?php if (Yii::$app->user->identity->isAdmin()): ?>
            <?= Html::a('Delete', ['delete', 'id' => (string)$model->_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            '_id',
            'title',
            'bio',
            [
                'label' => 'Avatar',
                'attribute' => 'avatar_url',
                'format' => 'raw',
                'value' => Html::img($model->avatar_url, ['width' => '200']),
            ],

            'email',
            'password_hash',
            'auth_token',
        ],
    ]) ?>

</div>
