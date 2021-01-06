<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\GodrollSearch */
$this->title = 'God rolls';
$this->registerJs("
    $('#show_godroll_form').on('click', function(e) {
        e.preventDefault();
        $('#update_godroll_form').toggle();
    });
");
$last_sync_godrolls = file_get_contents('last_sync_godrolls.txt');
if (Yii::$app->user->isGuest) {
    echo yii\authclient\widgets\AuthChoice::widget([
        'baseAuthUrl' => ['site/auth'],
        'popupMode' => false,
    ]);
} else { ?>
    <h1><?= $this->title; ?></h1>
    <br/>
    <a href="#" id="show_godroll_form">Updated: <?= $last_sync_godrolls; ?></a>
    <br/>
    <div id="update_godroll_form" style="display: none;">
        <?php $form = \yii\bootstrap\ActiveForm::begin(['action' => ['site/sync-godrolls'], 'options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'csv_file')->fileInput()->label(false); ?>
        <?= \yii\bootstrap\Html::button('Sync', ['type' => 'submit', 'class' => 'btn btn-danger']); ?>
        <?php \yii\bootstrap\ActiveForm::end() ?>
        <hr/>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'wtype',
                'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'wtype', $weapon_types, ['class' => 'form-control', 'prompt' => 'Choose']),
            ],
            'Name',
            'rpm',
            [
                'attribute' => 'Type',
                'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Type', [
                    'pve'=> 'pve',
                    'pvp'=> 'pvp',
                ], ['class' => 'form-control', 'prompt' => 'Choose']),
            ],
            'Sight_Barrel',
            'Mag_Perk',
            'Perk_1',
            'Perk_2',
            'Masterwork',
        ],
    ]); ?>
<?php } ?>


