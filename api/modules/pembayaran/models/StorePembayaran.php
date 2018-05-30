<?php

namespace api\modules\pembayaran\models;

use Yii;
use api\modules\master\models\Store;
use api\modules\pembayaran\models\StorePerangkatKasir;

class StorePembayaran extends \yii\db\ActiveRecord
{
	public static function getDb()
    {
        return Yii::$app->get('production_api');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_membership_pembayaran';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ACCESS_GROUP', 'STORE_ID', 'FAKTURE_NO'], 'required'],
            [['PAKET_ID', 'PAYMENT_STT', 'DOMPET_AUTODEBET', 'PAYMENT_METHODE'], 'integer'],
            [['FAKTURE_DATE_START', 'FAKTURE_DATE_END', 'PAYMENT_DATE', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['HARGA_BULAN', 'HARGA_PAKET', 'PAYMENT_TOTAL'], 'number'],
            [['ACCESS_GROUP', 'STORE_ID', 'PAKET_GROUP', 'FAKTURE_NO', 'PAYMENT_STT_NM', 'KONTRAK_DURASI', 'KONTRAK_BERJALAN', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['KASIR_ID', 'PAYMENT_METHODE_NM'], 'string', 'max' => 100],
            [['SALES_CODE'], 'string', 'max' => 150],
            [['ACCESS_GROUP', 'STORE_ID', 'PAKET_ID', 'FAKTURE_NO'], 'unique', 'targetAttribute' => ['ACCESS_GROUP', 'STORE_ID', 'PAKET_ID', 'FAKTURE_NO']],
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
            'KASIR_ID' => 'Kasir  ID',
            'PAKET_ID' => 'Paket  ID',
            'PAKET_GROUP' => 'Paket  Group',
            'SALES_CODE' => 'Sales  Code',
            'FAKTURE_NO' => 'Fakture  No',
            'FAKTURE_DATE_START' => 'Fakture  Date  Start',
            'FAKTURE_DATE_END' => 'Fakture  Date  End',
            'PAYMENT_STT' => 'Payment  Stt',
            'PAYMENT_STT_NM' => 'Payment  Stt  Nm',
            'DOMPET_AUTODEBET' => 'Dompet  Autodebet',
            'PAYMENT_DATE' => 'Payment  Date',
            'PAYMENT_METHODE' => 'Payment  Methode',
            'PAYMENT_METHODE_NM' => 'Payment  Methode  Nm',
            'KONTRAK_DURASI' => 'Kontrak  Durasi',
            'KONTRAK_BERJALAN' => 'Kontrak  Berjalan',
            'HARGA_BULAN' => 'Harga  Bulan',
            'HARGA_PAKET' => 'Harga  Paket',
            'PAYMENT_TOTAL' => 'Payment  Total',
            'CREATE_BY' => 'Create  By',
            'UPDATE_BY' => 'Update  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_AT' => 'Update  At',
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
			'STORE_NM'=>function(){
				return $this->storeNm;
			},
			'KASIR_ID'=>function($model){
				return $model->KASIR_ID;
			},
			'KASIR_NM'=>function(){
				return $this->kasirNm;
			},					
			'PAKET_GROUP'=>function($model){
				return $model->PAKET_GROUP;
			},
			'PAKET_ID'=>function($model){
				return $model->PAKET_ID;
			},
			'SALES_CODE'=>function($model){
				return $model->SALES_CODE;
			},
			'FAKTURE_NO'=>function($model){
				return $model->FAKTURE_NO;
			},			
			'FAKTURE_DATE_START'=>function($model){
				return $model->FAKTURE_DATE_START;
			},					
			'FAKTURE_DATE_END'=>function($model){
				return $model->FAKTURE_DATE_END;
			},					
			'PAYMENT_STT'=>function($model){
				return $model->PAYMENT_STT;
			},					
			'PAYMENT_STT_NM'=>function($model){
				return $model->PAYMENT_STT_NM;
			},
			'DOMPET_AUTODEBET'=>function($model){
				return $model->DOMPET_AUTODEBET;
			},
			'PAYMENT_DATE'=>function($model){
				return $model->PAYMENT_DATE;
			},
			'PAYMENT_DATE'=>function($model){
				return $model->PAYMENT_DATE;
			},
			'PAYMENT_DATE'=>function($model){
				return $model->PAYMENT_DATE;
			},           	
			'PAYMENT_METHODE'=>function($model){
				return $model->PAYMENT_METHODE;
			},					
			'PAYMENT_METHODE_NM'=>function($model){
				return $model->PAYMENT_METHODE_NM;
			},
			'KONTRAK_DURASI'=>function($model){
				return $model->KONTRAK_DURASI;
			},
			'KONTRAK_BERJALAN'=>function($model){
				return $model->KONTRAK_BERJALAN;
			},
			'HARGA_BULAN'=>function($model){
				return $model->HARGA_BULAN;
			},
			'HARGA_PAKET'=>function($model){
				return $model->HARGA_PAKET;
			},
			'PAYMENT_TOTAL'=>function($model){
				return $model->PAYMENT_TOTAL;
			}
		];
	}
	
	
	public function getStorePerangkatKasirTbl(){
		return $this->hasOne(StorePerangkatKasir::className(), ['KASIR_ID' => 'KASIR_ID']);
	}	
	public function getKasirNm(){
		$rslt = $this->storePerangkatKasirTbl['KASIR_NM'];
		if ($rslt){
			return $rslt;
		}else{
			return "none";
		}; 
	}
	
	public function getStoreTbl(){
		return $this->hasOne(Store::className(), ['STORE_ID' => 'STORE_ID']);
	}	
	public function getStoreNm(){
		$rslt = $this->storeTbl['STORE_NM'];
		if ($rslt){
			return $rslt;
		}else{
			return "none";
		}; 
	}
}
