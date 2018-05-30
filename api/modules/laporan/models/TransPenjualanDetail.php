<?php

namespace api\modules\transaksi\models;

use Yii;

class TransPenjualanDetail extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create'; //add Details Product BY TRANS_ID,OFLINE_ID
	const SCENARIO_UPDATE = 'update'; 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_penjualan_detail';
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
            //[['STORE_ID', 'ACCESS_ID', 'TRANS_ID', 'TRANS_DATE', 'PRODUCT_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
			[['STORE_ID','TRANS_ID','OFLINE_ID'], 'required','on'=>self::SCENARIO_CREATE],
            [['TRANS_DATE', 'CREATE_AT', 'UPDATE_AT','OFLINE_ID','GOLONGAN'], 'safe'],
            [['PRODUCT_QTY', 'HARGA_JUAL', 'DISCOUNT'], 'number'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP', 'ACCESS_ID'], 'string', 'max' => 15],
            [['STORE_ID','UNIT_ID'], 'string', 'max' => 20],
            [['TRANS_ID', 'UNIT_NM', 'PROMO', 'UPDATE_BY'], 'string', 'max' => 50],
            [['PRODUCT_ID'], 'string', 'max' => 35],
            [['PRODUCT_PROVIDER','PRODUCT_PROVIDER_NO','PRODUCT_PROVIDER_NM','OFLINE_ID'], 'string', 'max' => 255],
            [['PRODUCT_NM'], 'string', 'max' => 100],
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
            'ACCESS_ID' => 'Access  ID',
            'GOLONGAN' => 'Golongan ID',
            'TRANS_ID' => 'Trans  ID',
            'OFLINE_ID' => 'Ofline ID',
            'TRANS_DATE' => 'Trans  Date',
            'PRODUCT_ID' => 'Product  ID',
            'PRODUCT_NM' => 'Product  Nm',
            'PRODUCT_PROVIDER' => 'Provider',
            'PRODUCT_PROVIDER_NO' => 'Provider No',
            'PRODUCT_PROVIDER_NM' => 'Provicer Nm',
            'PRODUCT_QTY' => 'Product  Qty',
            'UNIT_ID' => 'Unit  ID',
            'UNIT_NM' => 'Unit  Nm',
            'HARGA_JUAL' => 'Harga  Jual',
            'DISCOUNT' => 'Discount',
            'PROMO' => 'Promo',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
            'STATUS' => 'Status',
            'DCRP_DETIL' => 'Dcrp  Detil',
            'YEAR_AT' => 'Year  At',
            'MONTH_AT' => 'Month  At',
        ];
    }
	
	public function fields()
	{
		return [			
			'ID'=>function($model){
				return $model->ID;
			},
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'ACCESS_ID'=>function($model){
				return $model->ACCESS_ID;
			},
			'GOLONGAN'=>function($model){
				return $model->GOLONGAN;
			},
			'TRANS_ID'=>function($model){
				return $model->TRANS_ID;
			},
			'OFLINE_ID'=>function($model){
				return $model->OFLINE_ID;
			},
			'TRANS_DATE'=>function($model){
				return $model->TRANS_DATE;
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
			'UNIT_ID'=>function($model){
				return $model->UNIT_ID;
			},
			'UNIT_NM'=>function($model){
				return $model->UNIT_NM;
			},
			'HARGA_JUAL'=>function($model){
				return $model->HARGA_JUAL;
			},
			'DISCOUNT'=>function($model){
				return $model->DISCOUNT;
			},
			'PROMO'=>function($model){
				return $model->PROMO;
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			},					
			'DCRP_DETIL'=>function($model){
				if($model->DCRP_DETIL){
					return $model->DCRP_DETIL;
				}else{
					return 'none';
				}
			}
		];
	}
}
