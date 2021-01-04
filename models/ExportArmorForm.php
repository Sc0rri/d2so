<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ExportArmorForm extends Model
{
    public $equippable;
    public $sum;
    public $export_other;

    public function rules()
    {
        return [
            [['equippable', 'sum'], 'required'],
            ['export_other', 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'equippable' => 'Equippable',
            'Sum' => 'Sum',
            'export_other' => 'Export other top sum',
        ];
    }
}