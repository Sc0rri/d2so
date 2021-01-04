<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usage".
 *
 * @property int $Hash
 * @property string $Name
 * @property double $pve_usage
 * @property double $pvp_usage
 */
class Usage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Hash', 'Name'], 'required'],
            [['Hash'], 'integer'],
            [['pve_usage', 'pvp_usage'], 'number'],
            [['Name'], 'string', 'max' => 255],
            [['Hash'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Hash' => 'Hash',
            'Name' => 'Name',
            'pve_usage' => 'Pve Usage',
            'pvp_usage' => 'Pvp Usage',
        ];
    }
}
