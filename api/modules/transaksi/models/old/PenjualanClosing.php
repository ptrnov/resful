<?php

namespace api\modules\transaksi\models;

use Yii;

/**
 * This is the model class for table "penjualan_closing".
 *
 * @property string $ID RECEVED & RELEASE: ID UNIX, POSTING URL DAN AJAX
 * @property string $CREATE_BY CREATE_BY
 * @property string $CREATE_AT CREATE_AT
 * @property string $UPDATE_BY UPDATE_BY
 * @property string $UPDATE_AT UPDATE_AT
 * @property int $STATUS
 * @property string $CLOSING_ID
 * @property string $ACCESS_UNIX
 * @property string $CLOSING_DATE
 * @property string $OUTLET_ID
 * @property string $TTL_MODAL
 * @property string $TTL_UANG
 * @property double $TTL_QTY
 * @property double $TTL_STORAN
 * @property double $TTL_SISA
 */
class PenjualanClosing extends \yii\db\ActiveRecord
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
        return 'penjualan_closing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT', 'CLOSING_DATE'], 'safe'],
            [['STATUS'], 'integer'],
            [['TTL_MODAL', 'TTL_UANG', 'TTL_QTY', 'TTL_STORAN', 'TTL_SISA'], 'number'],
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
            'CLOSING_DATE' => Yii::t('app', 'Closing  Date'),
            'OUTLET_ID' => Yii::t('app', 'Outlet  ID'),
            'TTL_MODAL' => Yii::t('app', 'Ttl  Modal'),
            'TTL_UANG' => Yii::t('app', 'Ttl  Uang'),
            'TTL_QTY' => Yii::t('app', 'Ttl  Qty'),
            'TTL_STORAN' => Yii::t('app', 'Ttl  Storan'),
            'TTL_SISA' => Yii::t('app', 'Ttl  Sisa'),
        ];
    }
}
