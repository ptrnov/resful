<?php

namespace api\modules\transaksi\models;

use Yii;

/**
 * This is the model class for table "penjualan_closing_bukti".
 *
 * @property string $ID RECEVED & RELEASE: ID UNIX, POSTING URL DAN AJAX
 * @property string $CREATE_BY CREATE_BY
 * @property string $CREATE_AT CREATE_AT
 * @property string $UPDATE_BY UPDATE_BY
 * @property string $UPDATE_AT UPDATE_AT
 * @property int $STATUS
 * @property string $CLOSING_ID
 * @property string $ACCESS_UNIX
 * @property string $STORAN_DATE
 * @property string $OUTLET_ID
 * @property double $TTL_STORAN
 * @property string $IMG
 */
class PenjualanClosingBukti extends \yii\db\ActiveRecord
{
	public static function getDb()
    {
        return Yii::$app->get('dbkg');
    }
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjualan_closing_bukti';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT', 'STORAN_DATE'], 'safe'],
            [['STATUS'], 'integer'],
            [['TTL_STORAN'], 'number'],
            [['IMG'], 'string'],
            [['CREATE_BY', 'UPDATE_BY', 'ACCESS_UNIX', 'OUTLET_ID'], 'string', 'max' => 50],
            [['CLOSING_ID'], 'string', 'max' => 100],
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
            'CLOSING_ID' => Yii::t('app', 'Closing  ID'),
            'ACCESS_UNIX' => Yii::t('app', 'Access  Unix'),
            'STORAN_DATE' => Yii::t('app', 'Storan  Date'),
            'OUTLET_ID' => Yii::t('app', 'Outlet  ID'),
            'TTL_STORAN' => Yii::t('app', 'Ttl  Storan'),
            'IMG' => Yii::t('app', 'Img'),
        ];
    }
}
