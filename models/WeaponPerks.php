<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "weapon_perks".
 *
 * @property string $weapon_id
 * @property string $name
 * @property string $value
 * @property string $godroll
 *
 * @property Weapons $weapon
 */
class WeaponPerks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'weapon_perks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['weapon_id', 'name'], 'required'],
            [['weapon_id', 'name', 'value', 'godroll'], 'string', 'max' => 255],
            [['weapon_id', 'name'], 'unique', 'targetAttribute' => ['weapon_id', 'name']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapons::className(), 'targetAttribute' => ['weapon_id' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'weapon_id' => 'Weapon ID',
            'name' => 'Name',
            'value' => 'Value',
            'godroll' => 'Godroll',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapons::className(), ['Id' => 'weapon_id']);
    }
}
