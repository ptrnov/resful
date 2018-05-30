<?php

namespace api\modules\transaksi\models;

use Yii;

/**
 * This is the model class for table "penjualan_detail".
 *
 * @property string $ID RECEVED & RELEASE: ID UNIX, POSTING URL DAN AJAX
 * @property string $CREATE_BY CREATE_BY
 * @property string $CREATE_AT CREATE_AT
 * @property string $UPDATE_BY UPDATE_BY
 * @property string $UPDATE_AT UPDATE_AT
 * @property int $STATUS
 * @property string $TRANS_ID TRANS_ID
 * @property string $ACCESS_UNIX
 * @property string $TRANS_DATE
 * @property string $OUTLET_ID
 * @property string $OUTLET_NM
 * @property string $ITEM_ID
 * @property string $ITEM_NM
 * @property double $ITEM_QTY
 * @property string $SATUAN
 * @property string $HARGA
 * @property string $DISCOUNT
 * @property int $DISCOUNT_STT
 */
class PenjualanDetail extends \yii\db\ActiveRecord
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
        return 'penjualan_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT', 'TRANS_DATE'], 'safe'],
            [['STATUS', 'DISCOUNT_STT'], 'integer'],
            [['ITEM_QTY', 'HARGA', 'DISCOUNT'], 'number'],
            [['CREATE_BY', 'UPDATE_BY', 'TRANS_ID', 'ACCESS_UNIX', 'OUTLET_ID', 'ITEM_ID', 'SATUAN'], 'string', 'max' => 50],
            [['OUTLET_NM', 'ITEM_NM'], 'string', 'max' => 100],
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
            'OUTLET_NM' => Yii::t('app', 'Outlet  Nm'),
            'ITEM_ID' => Yii::t('app', 'Item  ID'),
            'ITEM_NM' => Yii::t('app', 'Item  Nm'),
            'ITEM_QTY' => Yii::t('app', 'Item  Qty'),
            'SATUAN' => Yii::t('app', 'Satuan'),
            'HARGA' => Yii::t('app', 'Harga'),
            'DISCOUNT' => Yii::t('app', 'Discount'),
            'DISCOUNT_STT' => Yii::t('app', 'Discount  Stt'),
        ];
    }
}
