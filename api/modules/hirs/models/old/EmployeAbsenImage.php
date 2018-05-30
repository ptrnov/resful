<?php

namespace api\modules\hirs\models;

use Yii;

/**
 * This is the model class for table "hrd_absen_img".
 *
 * @property string $ID
 * @property string $CREATE_BY USER CREATED
 * @property string $CREATE_AT Tanggal dibuat
 * @property string $UPDATE_BY USER UPDATE
 * @property string $UPDATE_AT Tanggal di update
 * @property int $STATUS 0=disable 1=Normal (one table items).      (Android No Stock) 2=Detail (Join Itm Hpp)     (Android USED Stock)
 * @property string $EMP_ID
 * @property string $TGL
 * @property string $IMG_MASUK
 * @property string $IMG_KELUAR
 */
class EmployeAbsenImage extends \yii\db\ActiveRecord
{
	public static function getDb()
    {
        return Yii::$app->get('api_dbkg');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hrd_absen_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT', 'TGL'], 'safe'],
            [['STATUS'], 'integer'],
            [['IMG_MASUK', 'IMG_KELUAR'], 'string'],
            [['CREATE_BY', 'UPDATE_BY', 'EMP_ID'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => Yii::t('app', 'ID'),
            'CREATE_BY' => Yii::t('app', 'Create  By'),
            'CREATE_AT' => Yii::t('app', 'Create  At'),
            'UPDATE_BY' => Yii::t('app', 'Update  By'),
            'UPDATE_AT' => Yii::t('app', 'Update  At'),
            'STATUS' => Yii::t('app', 'Status'),
            'EMP_ID' => Yii::t('app', 'Emp  ID'),
            'TGL' => Yii::t('app', 'Tgl'),
            'IMG_MASUK' => Yii::t('app', 'Img  Masuk'),
            'IMG_KELUAR' => Yii::t('app', 'Img  Keluar'),
        ];
    }
}
