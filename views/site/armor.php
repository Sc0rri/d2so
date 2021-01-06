<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app/models\ArmorsSearch */

$this->title = 'Armor';
$this->registerJs("
    $('#show_armor_form').on('click', function(e) {
        e.preventDefault();
        $('#update_armor_form').toggle();
    });
    
      $('#show_export_form').on('click', function(e) {
        e.preventDefault();
        $('#export_armor_form').toggle();
    });
");
?>
<?php
if (Yii::$app->user->isGuest) {
    echo yii\authclient\widgets\AuthChoice::widget([
        'baseAuthUrl' => ['site/auth'],
        'popupMode' => false,
    ]);
} else { ?>
    <h1><?= $this->title; ?></h1>
    <br/>
    <a href="#" id="show_armor_form">Updated: <?= Yii::$app->user->identity->last_sync_armor; ?></a>
    <br/>
    <hr/>
    <div id="update_armor_form" style="display: none;">
        <?php $form = \yii\bootstrap\ActiveForm::begin(['action' => ['site/sync-armor'], 'options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'csv_file')->fileInput()->label(false); ?>
        <?= \yii\bootstrap\Html::button('Import', ['type' => 'submit', 'class' => 'btn btn-danger']); ?>

        <?php \yii\bootstrap\ActiveForm::end() ?>
        <hr/>
    </div>
    <a href="#" id="show_export_form">Export</a>

    <div id="export_armor_form" style="display: none;">
        <?php $form = \yii\bootstrap\ActiveForm::begin(['action' => ['site/export-armor']]) ?>

        <?= $form->field($export_model, 'equippable')->dropDownList([
            'Warlock'=> 'Warlock',
            'Hunter'=> 'Hunter',
            'Titan'=> 'Titan'
        ])->label(false); ?>
        <?= $form->field($export_model, 'sum')->dropDownList([
            'Mobility_Recovery' => 'Mobility+Recovery',
            'Mobility_Resilience' => 'Mobility+Resilience',
            'Mobility_Intellect' => 'Mobility+Intellect',
            'Mobility_Discipline' => 'Mobility+Discipline',
            'Mobility_Strength' => 'Mobility+Strength',
            'Recovery_Resilience' => 'Recovery+Resilience',
            'Recovery_Intellect' => 'Recovery+Intellect',
            'Recovery_Discipline' => 'Recovery+Discipline',
            'Recovery_Strength' => 'Recovery+Strength',
            'Resilience_Intellect' => 'Resilience+Intellect',
            'Resilience_Discipline' => 'Resilience+Discipline',
            'Resilience_Strength' => 'Resilience+Strength',
            'Intellect_Discipline' => 'Intellect+Discipline',
            'Intellect_Strength' => 'Intellect+Strength',
            'Discipline_Strength' => 'Discipline+Strength',
        ])->label(false); ?>

        <?= $form->field($export_model, 'export_other')->checkbox(); ?>
        <?= \yii\bootstrap\Html::button('Export', ['type' => 'submit', 'class' => 'btn btn-danger']); ?>

        <?php \yii\bootstrap\ActiveForm::end() ?>
        <hr/>
    </div>
    <div class="armors-index">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'Id',
                'Name',
                [
                    'attribute' => 'Season_mod',
                    'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Season_mod', array_filter(\yii\helpers\ArrayHelper::map(\app\models\Armors::find()->distinct('Season_mod')->orderBy(['Season_mod'=>SORT_DESC])->all(), 'Season_mod', 'Season_mod')), ['class' => 'form-control', 'prompt' => 'Choose']),
                ],
                [
                    'attribute' => 'Equippable',
                    'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Equippable', [
                        'Warlock'=> 'Warlock',
                        'Hunter'=> 'Hunter',
                        'Titan'=> 'Titan'
                    ], ['class' => 'form-control', 'prompt' => 'Choose']),
                ],
                [
                    'attribute' => 'Type',
                    'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Type', [
                            'Helmet'=>'Helmet',
                            'Gauntlets'=>'Gauntlets',
                            'Chest Armor'=>'Chest Armor',
                            'Leg Armor' => 'Leg Armor'
                    ], ['class' => 'form-control', 'prompt' => 'Choose']),
                ],
                [
                    'attribute' => 'Masterwork_Type',
                    'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Masterwork_Type', [
                        'Void Energy Capacity' => 'Void',
                        'Solar Energy Capacity' => 'Solar',
                        'Arc Energy Capacity' => 'Arc'
                    ], ['class' => 'form-control', 'prompt' => 'Choose']),
                ],
                'Mobility',
                'Recovery',
                'Resilience',
                'Intellect',
                'Discipline',
                'Strength',
                'Total',
                'Power_Limit',
                [
                    'attribute' => 'sum',
                    'label' => 'Sum',
                    'format' => 'raw',
                    'value' => function ($data) use ($searchModel) {
                        return ($searchModel->sum) ? $data->{$searchModel->sum} : '';
                    },
                    'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'sum', [
                        'Mobility_Recovery' => 'Mobility+Recovery',
                        'Mobility_Resilience' => 'Mobility+Resilience',
                        'Mobility_Intellect' => 'Mobility+Intellect',
                        'Mobility_Discipline' => 'Mobility+Discipline',
                        'Mobility_Strength' => 'Mobility+Strength',
                        'Recovery_Resilience' => 'Recovery+Resilience',
                        'Recovery_Intellect' => 'Recovery+Intellect',
                        'Recovery_Discipline' => 'Recovery+Discipline',
                        'Recovery_Strength' => 'Recovery+Strength',
                        'Resilience_Intellect' => 'Resilience+Intellect',
                        'Resilience_Discipline' => 'Resilience+Discipline',
                        'Resilience_Strength' => 'Resilience+Strength',
                        'Intellect_Discipline' => 'Intellect+Discipline',
                        'Intellect_Strength' => 'Intellect+Strength',
                        'Discipline_Strength' => 'Discipline+Strength',
                    ], ['class' => 'form-control', 'prompt' => 'Choose']),
                ],
            ],
        ]); ?>
    </div>

<?php } ?>