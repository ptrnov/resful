<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "item_stock".
 *
 * @property string $ID
 * @property string $CREATE_BY USER CREATED
 * @property string $CREATE_AT Tanggal dibuat
 * @property string $UPDATE_BY USER UPDATE
 * @property string $UPDATE_AT Tanggal di update
 * @property int $STATUS 0=Deactive 1=Active
 * @property string $ITEM_ID
 * @property string $OUTLET_CODE
 * @property double $STOCK
 * @property string $TGL_BELI
 * @property string $HARGA_BELI
 * @property string $MARGIN_KEUNTUNGAN
 * @property string $MAX_DISCOUNT
 * @property string $DCRIPT
 */
class ItemStock extends \yii\db\ActiveRecord
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
        return 'item_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ACCESS_UNIX','CREATE_AT', 'UPDATE_AT', 'TGL_BELI'], 'safe'],
            [['STATUS'], 'integer'],
            [['STOCK', 'HARGA_BELI', 'MARGIN_KEUNTUNGAN', 'MAX_DISCOUNT'], 'number'],
            [['DCRIPT'], 'string'],
            [['CREATE_BY', 'UPDATE_BY', 'ITEM_ID', 'OUTLET_CODE'], 'string', 'max' => 50],
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
            'ACCESS_UNIX' => Yii::t('app', 'Access Unix'),
            'ITEM_ID' => Yii::t('app', 'Item  ID'),
            'OUTLET_CODE' => Yii::t('app', 'Outlet  Code'),
            'STOCK' => Yii::t('app', 'Stock'),
            'TGL_BELI' => Yii::t('app', 'Tgl  Beli'),
            'HARGA_BELI' => Yii::t('app', 'Harga  Beli'),
            'MARGIN_KEUNTUNGAN' => Yii::t('app', 'Margin  Keuntungan'),
            'MAX_DISCOUNT' => Yii::t('app', 'Max  Discount'),
            'DCRIPT' => Yii::t('app', 'Dcript'),
        ];
    }
}
