<?php

namespace api\modules\transaksi\models;

use Yii;

/**
 * This is the model class for table "penjualan_header".
 *
 * @property string $ID RECEVED & RELEASE: ID UNIX, POSTING URL DAN AJAX
 * @property string $CREATE_BY CREATE_BY
 * @property string $CREATE_AT CREATE_AT
 * @property string $UPDATE_BY UPDATE_BY
 * @property string $UPDATE_AT UPDATE_AT
 * @property int $STATUS
 * @property string $TRANS_ID
 * @property string $ACCESS_UNIX
 * @property string $TRANS_DATE
 * @property string $OUTLET_ID
 * @property string $CONSUMER_NM
 * @property string $CONSUMER_EMAIL
 * @property string $CONSUMER_PHONE
 */
class PenjualanHeader extends \yii\db\ActiveRecord
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
        return 'penjualan_header';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT', 'TRANS_DATE'], 'safe'],
            [['TOTAL_ITEM','TOTAL_HARGA','TYPE_PAY','BANK_NM','BANK_NO'], 'safe'],
            [['STATUS'], 'integer'],
            [['CREATE_BY', 'UPDATE_BY', 'ACCESS_UNIX', 'OUTLET_ID'], 'string', 'max' => 50],
            [['TRANS_ID', 'CONSUMER_NM'], 'string', 'max' => 100],
            [['CONSUMER_EMAIL'], 'string', 'max' => 200],
            [['CONSUMER_PHONE'], 'string', 'max' => 150],
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
            'TRANS_ID' => Yii::t('app', 'Trans  ID'),
            'ACCESS_UNIX' => Yii::t('app', 'Access  Unix'),
            'TRANS_DATE' => Yii::t('app', 'Trans  Date'),
            'OUTLET_ID' => Yii::t('app', 'Outlet  ID'),
			'TOTAL_ITEM' => Yii::t('app', 'Total Item'),
            'TOTAL_HARGA' => Yii::t('app', 'Total Harga'),
			'TYPE_PAY' => Yii::t('app', 'TYPE_PAY'),
            'BANK_NM' => Yii::t('app', 'BANK_NM'),
            'BANK_NO' => Yii::t('app', 'BANK_NO'),
            'CONSUMER_NM' => Yii::t('app', 'Consumer  Nm'),
            'CONSUMER_EMAIL' => Yii::t('app', 'Consumer  Email'),
            'CONSUMER_PHONE' => Yii::t('app', 'Consumer  Phone'),
        ];
    }
}
