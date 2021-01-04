<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "weapons".
 *
 * @property string $Id
 * @property string $Name
 * @property int $Hash
 * @property string $Type
 * @property string $Rpm
 * @property string $Dmg
 * @property int $Power_Limit
 * @property string $Masterwork_Type
 * @property int $Masterwork_Type_godroll
 * @property string $Masterwork_Tier
 * @property int $pve_godrolls
 * @property int $pvp_godrolls
 * @property int $user_id
 *
 * @property User $user
 */
class Weapons extends \yii\db\ActiveRecord
{
    public $_perks = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'weapons';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Name', 'Hash', 'Type', 'Rpm', 'user_id'], 'required'],
            [['Hash', 'pve_godrolls', 'pvp_godrolls', 'user_id'], 'integer'],
            [['Id', 'Name', 'Type', 'Rpm', 'Dmg', 'Masterwork_Type', 'Masterwork_Tier'], 'string', 'max' => 255],
            [['Id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Name',
            'Hash' => 'Hash',
            'Type' => 'Type',
            'Rpm' => 'Rpm',
            'Dmg' => 'Dmg',
            'Power_Limit' => 'Power limit',
            'Masterwork_Type' => 'Masterwork Type',
            'Masterwork_Type_godroll' => 'Masterwork Type Godroll',
            'Masterwork_Tier' => 'Masterwork Tier',
            'pve_godrolls' => 'Pve Godrolls',
            'pvp_godrolls' => 'Pvp Godrolls',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsage()
    {
        return $this->hasOne(Usage::className(), ['Hash' => 'Hash']);
    }

    public function getWeaponsPerks() {
        if (empty($this->_perks)) {
            $perks = WeaponPerks::find()->where(['weapon_id'=>$this->Id])->all();
            foreach ($perks as $one_perk) {
                $this->_perks[$one_perk->name] = $one_perk;
            }
        }

        return $this->_perks;
    }

    public function hasGodroll() {
        $godroll = Godroll::findOne(['Name' => $this->Name]);

        if ($godroll) {
            return true;
        } else {
            return false;
        }
    }
}
