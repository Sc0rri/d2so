<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "armors".
 *
 * @property string $Id
 * @property string $Name
 * @property int $Hash
 * @property string $Type
 * @property string $Equippable
 * @property string $Masterwork_Type
 * @property int $Mobility
 * @property int $Recovery
 * @property int $Resilience
 * @property int $Intellect
 * @property int $Discipline
 * @property int $Strength
 * @property int $Total
 * @property int $Power_Limit
 * @property int $user_id
 * @property int $Mobility_Recovery
 * @property int $Mobility_Resilience
 * @property int $Mobility_Intellect
 * @property int $Mobility_Discipline
 * @property int $Mobility_Strength
 * @property int $Recovery_Resilience
 * @property int $Recovery_Intellect
 * @property int $Recovery_Discipline
 * @property int $Recovery_Strength
 * @property int $Resilience_Intellect
 * @property int $Resilience_Discipline
 * @property int $Resilience_Strength
 * @property int $Intellect_Discipline
 * @property int $Intellect_Strength
 * @property int $Discipline_Strength
 * @property int $Season_mod
 *
 * @property User $user
 */
class Armors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'armors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Name', 'Hash', 'Type', 'Equippable', 'Masterwork_Type', 'Mobility', 'Recovery', 'Resilience', 'Intellect', 'Discipline', 'Strength', 'Total', 'user_id', 'Mobility_Recovery', 'Mobility_Resilience', 'Mobility_Intellect', 'Mobility_Discipline', 'Mobility_Strength', 'Recovery_Resilience', 'Recovery_Intellect', 'Recovery_Discipline', 'Recovery_Strength', 'Resilience_Intellect', 'Resilience_Discipline', 'Resilience_Strength', 'Intellect_Discipline', 'Intellect_Strength', 'Discipline_Strength'], 'required'],
            [['Hash', 'Mobility', 'Recovery', 'Resilience', 'Intellect', 'Discipline', 'Strength', 'Total', 'user_id', 'Mobility_Recovery', 'Mobility_Resilience', 'Mobility_Intellect', 'Mobility_Discipline', 'Mobility_Strength', 'Recovery_Resilience', 'Recovery_Intellect', 'Recovery_Discipline', 'Recovery_Strength', 'Resilience_Intellect', 'Resilience_Discipline', 'Resilience_Strength', 'Intellect_Discipline', 'Intellect_Strength', 'Discipline_Strength', 'Season_mod'], 'integer'],
            [['Id', 'Name'], 'string', 'max' => 255],
            [['Type', 'Equippable', 'Masterwork_Type'], 'string', 'max' => 45],
            [['Id'], 'unique'],
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
            'Equippable' => 'Equippable',
            'Masterwork_Type' => 'Masterwork Type',
            'Mobility' => 'Mobility',
            'Recovery' => 'Recovery',
            'Resilience' => 'Resilience',
            'Intellect' => 'Intellect',
            'Discipline' => 'Discipline',
            'Strength' => 'Strength',
            'Total' => 'Total',
            'Power_Limit' => 'Power limit','Power' => 'Power',
            'user_id' => 'User ID',
            'Mobility_Recovery' => 'Mobility Recovery',
            'Mobility_Resilience' => 'Mobility Resilience',
            'Mobility_Intellect' => 'Mobility Intellect',
            'Mobility_Discipline' => 'Mobility Discipline',
            'Mobility_Strength' => 'Mobility Strength',
            'Recovery_Resilience' => 'Recovery Resilience',
            'Recovery_Intellect' => 'Recovery Intellect',
            'Recovery_Discipline' => 'Recovery Discipline',
            'Recovery_Strength' => 'Recovery Strength',
            'Resilience_Intellect' => 'Resilience Intellect',
            'Resilience_Discipline' => 'Resilience Discipline',
            'Resilience_Strength' => 'Resilience Strength',
            'Intellect_Discipline' => 'Intellect Discipline',
            'Intellect_Strength' => 'Intellect Strength',
            'Discipline_Strength' => 'Discipline Strength',
            'Season_mod' => 'Season mod'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
