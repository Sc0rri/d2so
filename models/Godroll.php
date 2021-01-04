<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "godroll".
 *
 * @property string $Name
 * @property string $Sight_Barrel
 * @property string $Mag_Perk
 * @property string $Perk_1
 * @property string $Perk_2
 * @property string $Masterwork
 * @property string $Type
 * @property string $wtype
 * @property string $rpm
 */
class Godroll extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'godroll';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Sight_Barrel', 'Mag_Perk', 'Perk_1', 'Perk_2', 'Masterwork'], 'required'],
            [['Name'], 'string', 'max' => 100],
            [['Sight_Barrel', 'Mag_Perk', 'Perk_1', 'Perk_2', 'Masterwork', 'wtype', 'rpm'], 'string', 'max' => 255],
            [['Type'], 'string', 'max' => 5],
            [['Name', 'Type'], 'unique', 'targetAttribute' => ['Name', 'Type']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Name' => 'Название',
            'Sight/Barrel' => 'Sight/ Barrel',
            'Mag Perk' => 'Mag Perk',
            'Perk 1' => 'Perk 1',
            'Perk 2' => 'Perk 2',
            'Masterwork' => 'Masterwork',
            'Type' => 'Тип',
            'wtype' => 'Тип оружия',
            'rpm' => 'Скорострельность'
        ];
    }
}
