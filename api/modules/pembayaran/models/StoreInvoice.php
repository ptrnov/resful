<?php

namespace api\modules\pembayaran\models;

use Yii;
use api\modules\pembayaran\models\StorePerangkatKasir;
use api\modules\master\models\Store;
use api\modules\pembayaran\models\StoreInvoicePaket;

class StoreInvoice extends \yii\db\ActiveRecord
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
        return 'store_membership';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['STORE_STT', 'FAKTURE_TEMPO', 'PAYMENT_STT', 'PAYMENT_METHODE', 'DOMPET_AUTODEBET', 'PAKET_ID', 'PAKET_DURATION', 'PAKET_DURATION_BONUS'], 'integer'],
            [['STORE_DATE_END_LATES', 'STORE_DATE_START', 'STORE_DATE_END', 'FAKTURE_DATE_START', 'FAKTURE_DATE_END', 'PAYMENT_DATE', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['FAKTURE_NO'], 'required'],
            [['HARGA_BULAN', 'HARGA_HARI', 'HARGA_PAKET', 'HARGA_PAKET_HARI'], 'number'],
            [['ACCESS_GROUP', 'STORE_ID', 'STORE_STT_NM', 'FAKTURE_NO', 'PAYMENT_STT_NM', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['KASIR_ID', 'PAYMENT_METHODE_NM', 'PAKET_GROUP', 'PAKET_NM'], 'string', 'max' => 100],
            [['KASIR_ID', 'FAKTURE_DATE_START', 'FAKTURE_DATE_END'], 'unique', 'targetAttribute' => ['KASIR_ID', 'FAKTURE_DATE_START', 'FAKTURE_DATE_END']],
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
            'STORE_STT' => 'Store  Stt',
            'STORE_STT_NM' => 'Store  Stt  Nm',
            'STORE_DATE_END_LATES' => 'Store  Date  End  Lates',
            'STORE_DATE_START' => 'Store  Date  Start',
            'STORE_DATE_END' => 'Store  Date  End',
            'FAKTURE_NO' => 'Fakture  No',
            'FAKTURE_DATE_START' => 'Fakture  Date  Start',
            'FAKTURE_TEMPO' => 'Fakture  Tempo',
            'FAKTURE_DATE_END' => 'Fakture  Date  End',
            'PAYMENT_STT' => 'Payment  Stt',
            'PAYMENT_STT_NM' => 'Payment  Stt  Nm',
            'PAYMENT_DATE' => 'Payment  Date',
            'PAYMENT_METHODE' => 'Payment  Methode',
            'PAYMENT_METHODE_NM' => 'Payment  Methode  Nm',
            'DOMPET_AUTODEBET' => 'Dompet  Autodebet',
            'PAKET_ID' => 'Paket  ID',
            'PAKET_GROUP' => 'Paket  Group',
            'PAKET_NM' => 'Paket  Nm',
            'PAKET_DURATION' => 'Paket  Duration',
            'PAKET_DURATION_BONUS' => 'Paket  Duration  Bonus',
            'HARGA_BULAN' => 'Harga  Bulan',
            'HARGA_HARI' => 'Harga  Hari',
            'HARGA_PAKET' => 'Harga  Paket',
            'HARGA_PAKET_HARI' => 'Harga  Paket  Hari',
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
			'PAYMENT_METHODE'=>function($model){
				return $model->PAYMENT_METHODE;
			},					
			'PAYMENT_METHODE_NM'=>function($model){
				return $model->PAYMENT_METHODE_NM;
			},
			'PAKET_GROUP'=>function($model){
				return $model->PAKET_GROUP;
			},
			'PAKET_ID'=>function($model){
				return $model->PAKET_ID;
			},
			'PAKET_NM'=>function($model){
				return $model->PAKET_NM;
			},
			'PAKET_DURATION'=>function($model){
				return $model->PAKET_DURATION;
			},
			'PAKET_DURATION_BONUS'=>function($model){
				return $model->PAKET_DURATION_BONUS;
			},
			'HARGA_BULAN'=>function($model){
				return $model->HARGA_BULAN;
			},
			'HARGA_HARI'=>function($model){
				return $model->HARGA_HARI;
			},
			'HARGA_PAKET'=>function($model){
				return $model->HARGA_PAKET;
			},
			'HARGA_PAKET_HARI'=>function($model){
				return $model->HARGA_PAKET_HARI;
			},			
			'PAKET_PROPERTIES'=>function(){
				return $this->storeInvoicePaketTbl;
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
	
	public function getStoreInvoicePaketTbl(){
		return $this->hasOne(StoreInvoicePaket::className(), ['PAKET_ID' => 'PAKET_ID']);
	}	
	
}
