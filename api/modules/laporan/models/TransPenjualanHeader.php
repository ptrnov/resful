<?php

namespace api\modules\laporan\models;

use Yii;

class TransPenjualanHeader extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create'; //STATUS=0
	const SCENARIO_UPDATE = 'update'; //CHECK COUNT 'TOTAL_PRODUC' jika sama send 'Email/sms' => STATUS=1 (complete)
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_penjualan_header';
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
            [['STORE_ID','ACCESS_ID','TRANS_DATE','OFLINE_ID'], 'required','on'=>self::SCENARIO_CREATE],
			[['TRANS_ID','TOTAL_PRODUCT'], 'required','on'=>self::SCENARIO_UPDATE],
			[['TRANS_DATE', 'CREATE_AT', 'UPDATE_AT','MERCHANT_ID','TRANS_ID','CONSUMER_ID','OPENCLOSE_ID'], 'safe'],
            [['TOTAL_PRODUCT', 'SUB_TOTAL_HARGA', 'PPN', 'TOTAL_HARGA'], 'number'],
            [['TYPE_PAY_ID', 'BANK_ID', 'STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP', 'ACCESS_ID'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['UPDATE_BY'], 'string', 'max' => 50],
            [['TYPE_PAY_NM', 'BANK_NM', 'CONSUMER_PHONE'], 'string', 'max' => 150],
            [['MERCHANT_NM', 'MERCHANT_NO','OFLINE_ID'], 'string', 'max' => 255],
            [['CONSUMER_NM'], 'string', 'max' => 100],
            [['CONSUMER_EMAIL'], 'string', 'max' => 200],
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
            'TRANS_ID' => 'Trans  ID',
            'OFLINE_ID' => 'Ofline ID',
            'OPENCLOSE_ID' => 'Openclose ID',
            'TRANS_DATE' => 'Trans  Date',
            'TOTAL_PRODUCT' => 'Total  Product',
            'SUB_TOTAL_HARGA' => 'Sub  Total  Harga',
            'PPN' => 'Ppn',
            'TOTAL_HARGA' => 'Total  Harga',
            'TYPE_PAY_ID' => 'Type  Pay  ID',
            'TYPE_PAY_NM' => 'Type  Pay  Nm',
            'BANK_ID' => 'Bank  ID',
            'BANK_NM' => 'Bank  Nm',
            'MERCHANT_ID' => 'Store Merchant',
            'MERCHANT_NM' => 'Merchant  Nm',
            'MERCHANT_NO' => 'Merchant  No',
            'CONSUMER_ID' => 'Consumer  Id',
            'CONSUMER_NM' => 'Consumer  Nm',
            'CONSUMER_EMAIL' => 'Consumer  Email',
            'CONSUMER_PHONE' => 'Consumer  Phone',
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
			// 'ID'=>function($model){
				// return $model->ID;
			// },
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'ACCESS_ID'=>function($model){
				return $model->ACCESS_ID;
			},
			'TRANS_ID'=>function($model){
				return $model->TRANS_ID;
			},
			'OFLINE_ID'=>function($model){
				return $model->OFLINE_ID;
			},
			'OPENCLOSE_ID'=>function($model){
				return $model->OPENCLOSE_ID;
			},
			'TRANS_DATE'=>function($model){
				return $model->TRANS_DATE;
			},					
			'TOTAL_PRODUCT'=>function($model){
				return $model->TOTAL_PRODUCT;
			},
			'SUB_TOTAL_HARGA'=>function($model){
				return $model->SUB_TOTAL_HARGA;
			},
			'PPN'=>function($model){
				return $model->PPN;
			},
			'TOTAL_HARGA'=>function($model){
				return $model->TOTAL_HARGA;
			},
			'MERCHANT_ID'=>function($model){
				return $model->MERCHANT_ID;
			},
			'TYPE_PAY_ID'=>function($model){
				return $model->TYPE_PAY_ID;
			},
			'TYPE_PAY_NM'=>function($model){
				return $model->TYPE_PAY_NM;
			},
			'BANK_ID'=>function($model){
				return $model->BANK_ID;
			},
			'BANK_NM'=>function($model){
				return $model->BANK_NM;
			},
			'MERCHANT_NM'=>function($model){
				return $model->MERCHANT_NM;
			},
			'MERCHANT_NO'=>function($model){
				return $model->MERCHANT_NO;
			},
			'CONSUMER_ID'=>function($model){
				return $model->CONSUMER_ID;
			},
			'CONSUMER_NM'=>function($model){
				return $model->CONSUMER_NM;
			},
			'CONSUMER_EMAIL'=>function($model){
				return $model->CONSUMER_EMAIL;
			},
			'CONSUMER_PHONE'=>function($model){
				return $model->CONSUMER_PHONE;
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
