<?php

namespace api\modules\laporan\models;
use Yii;

/**
 * This is the model class for table "trans_penjualan_detail_summary_daily".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property string $TAHUN
 * @property integer $BULAN
 * @property string $TGL
 * @property string $PRODUCT_ID
 * @property string $PRODUCT_NM
 * @property string $PRODUCT_PROVIDER
 * @property string $PRODUCT_PROVIDER_NO
 * @property string $PRODUCT_PROVIDER_NM
 * @property string $PRODUCT_QTY
 * @property string $HPP
 * @property string $HARGA_JUAL
 * @property string $SUB_TOTAL
 * @property string $CREATE_AT
 * @property string $UPDATE_AT
 * @property string $KETERANGAN
 */
class TransPenjualanDetailSummaryDaily extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_penjualan_detail_summary_daily';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('production_api');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['BULAN'], 'integer'],
            [['TGL', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['PRODUCT_QTY', 'HPP', 'HARGA_JUAL', 'SUB_TOTAL'], 'number'],
            [['KETERANGAN'], 'string'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['TAHUN'], 'string', 'max' => 5],
            [['PRODUCT_ID'], 'string', 'max' => 50],
            [['PRODUCT_NM'], 'string', 'max' => 100],
            [['PRODUCT_PROVIDER', 'PRODUCT_PROVIDER_NO', 'PRODUCT_PROVIDER_NM'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'ACCESS_GROUP' => 'Access  Group',
            'STORE_ID' => 'Store  ID',
            'TAHUN' => 'Tahun',
            'BULAN' => 'Bulan',
            'TGL' => 'Tgl',
            'PRODUCT_ID' => 'Product  ID',
            'PRODUCT_NM' => 'Product  Nm',
            'PRODUCT_PROVIDER' => 'Product  Provider',
            'PRODUCT_PROVIDER_NO' => 'Product  Provider  No',
            'PRODUCT_PROVIDER_NM' => 'Product  Provider  Nm',
            'PRODUCT_QTY' => 'Product  Qty',
            'HPP' => 'Hpp',
            'HARGA_JUAL' => 'Harga  Jual',
            'SUB_TOTAL' => 'Sub  Total',
            'CREATE_AT' => 'Create  At',
            'UPDATE_AT' => 'Update  At',
            'KETERANGAN' => 'Keterangan',
        ];
    }
	
	public function fields()
	{
		return [		
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},	
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},	
			'TAHUN'=>function($model){
				return $model->TAHUN;
			},	
			'BULAN'=>function($model){
				return $model->BULAN;
			},	
			'TGL'=>function($model){
				return $model->TGL;
			},	
			'PRODUCT_ID'=>function($model){
				return $model->PRODUCT_ID;
			},	
			'PRODUCT_NM'=>function($model){
				return $model->PRODUCT_NM;
			},	
			'PRODUCT_PROVIDER'=>function($model){
				return $model->PRODUCT_PROVIDER;
			},	
			'PRODUCT_PROVIDER_NO'=>function($model){
				return $model->PRODUCT_PROVIDER_NO;
			},	
			'PRODUCT_PROVIDER_NM'=>function($model){
				return $model->PRODUCT_PROVIDER_NM;
			},	
			'PRODUCT_QTY'=>function($model){
				return $model->PRODUCT_QTY;
			},	
			'HPP'=>function($model){
				return $model->HPP;
			},	
			'HARGA_JUAL'=>function($model){
				return $model->HARGA_JUAL;
			},	
			'SUB_TOTAL'=>function($model){
				return $model->SUB_TOTAL;
			}			
		];
	}
}
