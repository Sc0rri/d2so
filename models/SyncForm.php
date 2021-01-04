<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class SyncForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $csv_file;

    public function rules()
    {
        return [
            [['csv_file'], 'required'],
            [['csv_file'], 'file'],
        ];
    }
    public function attributeLabels()
    {
        return ['csv_file'=>'Файл'];
    }
}