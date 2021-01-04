<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\WeaponsSearch */

$this->title = 'Оружие';
$this->registerJs("
    $('#show_weapon_form').on('click', function(e) {
        e.preventDefault();
        $('#update_weapon_form').toggle();
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
    <a href="#" id="show_weapon_form">Обновлено: <?= Yii::$app->user->identity->last_sync_weapon; ?></a>
    <br/>
    <hr/>
    <div id="update_weapon_form" style="display: none;">
        <?php $form = \yii\bootstrap\ActiveForm::begin(['action' => ['site/sync-weapons'], 'options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= $form->field($model, 'csv_file')->fileInput()->label(false); ?>
        <?= \yii\bootstrap\Html::button('Синхронизироваться', ['type' => 'submit', 'class' => 'btn btn-danger']); ?>

        <?php \yii\bootstrap\ActiveForm::end() ?>
        <hr/>
    </div>
    <?= \yii\bootstrap\Html::a('Экспортировать', ['site/export-weapons']); ?>
    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        'Id',
        'Name',
        [
            'attribute' => 'Type',
            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Type', array_filter(ArrayHelper::map(\app\models\Weapons::find()->distinct('Type')->all(), 'Type', 'Type')), ['class' => 'form-control', 'prompt' => 'Выберите']),
        ],
        [
            'attribute' => 'Dmg',
            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Dmg', array_filter(ArrayHelper::map(\app\models\Weapons::find()->distinct('Dmg')->all(), 'Dmg', 'Dmg')), ['class' => 'form-control', 'prompt' => 'Выберите']),
        ],
        'Rpm',
        [
            'attribute' => 'Masterwork_Type',
            'format' => 'raw',
            'contentOptions' => function ($data) {
                if ($data->Masterwork_Type_godroll) {
                    return ['style' => 'background-color:lightgreen; font-weight: bold;'];
                } else {
                    return [];
                }
            },
            'value' => function ($data) {
                return $data->Masterwork_Type.($data->Masterwork_Type_godroll?' <span class="badge">'.$data->Masterwork_Type_godroll.'</span>':'');
            },
            'filter' => \yii\helpers\Html::activeDropDownList($searchModel, 'Masterwork_Type', array_filter(ArrayHelper::map(\app\models\Weapons::find()->distinct('Masterwork_Type')->all(), 'Masterwork_Type', 'Masterwork_Type')), ['class' => 'form-control', 'prompt' => 'Выберите']),
        ],
        'Power_Limit',
    ];

    for ($i=0; $i<$perks_count; $i++) {
        $columns = array_merge($columns, [
                [
                    'label' => 'Perk '.$i,
                    'format' => 'raw',
                    'contentOptions' => function ($data) use ($i) {
                        $name = 'Perks '.$i;
                        if (isset($data->weaponsPerks[$name]) && $data->weaponsPerks[$name]->godroll) {
                            return ['style' => 'background-color:lightgreen; font-weight: bold;'];
                        } else {
                            return [];
                        }
                    },
                    'value' => function ($data) use ($i) {
                        $name = 'Perks '.$i;
                        return (isset($data->weaponsPerks[$name]))?($data->weaponsPerks[$name]->value.($data->weaponsPerks[$name]->godroll?' <span class="badge">'.$data->weaponsPerks[$name]->godroll.'</span>':'')):'';
                    },
                ]
        ]);
    }

    $columns = array_merge($columns, [
        'pve_godrolls',
        'pvp_godrolls',
        [
            'attribute' => 'pve_usage',
            'label' => 'PVE usage',
            'format' => 'raw',
            'value' => function ($data) {
                return ($data->usage) ? $data->usage->pve_usage : '';
            },
        ],
        [
            'attribute' => 'pvp_usage',
            'label' => 'PVP usage',
            'format' => 'raw',
            'value' => function ($data) {
                return ($data->usage) ? $data->usage->pvp_usage : '';
            },
        ]
    ]);
    ?>

    <div class="weapons-index">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $columns,
            'rowOptions'=>function($model){
                if(!$model->hasGodroll()){
                    return ['class' => 'danger'];
                }
            },
        ]); ?>
    </div>

<?php } ?>