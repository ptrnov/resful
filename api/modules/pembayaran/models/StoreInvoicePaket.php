<?php

namespace api\modules\pembayaran\models;


use Yii;

class StoreInvoicePaket extends \yii\db\ActiveRecord
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
        return 'store_membership_paket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PAKET_DURATION', 'PAKET_DURATION_BONUS', 'PAKET_STT'], 'integer'],
            [['HARGA_BULAN', 'HARGA_HARI', 'HARGA_PAKET', 'HARGA_PAKET_HARI'], 'number'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['PAKET_GROUP', 'PAKET_STT_NM', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['PAKET_NM'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PAKET_ID' => 'Paket  ID',
            'PAKET_GROUP' => 'Paket  Group',
            'PAKET_NM' => 'Paket  Nm',
            'PAKET_DURATION' => 'Paket  Duration',
            'PAKET_DURATION_BONUS' => 'Paket  Duration  Bonus',
            'HARGA_BULAN' => 'Harga  Bulan',
            'HARGA_HARI' => 'Harga  Hari',
            'HARGA_PAKET' => 'Harga  Paket',
            'HARGA_PAKET_HARI' => 'Harga  Paket  Hari',
            'PAKET_STT' => 'Paket  Stt',
            'PAKET_STT_NM' => 'Paket  Stt  Nm',
            'CREATE_BY' => 'Create  By',
            'UPDATE_BY' => 'Update  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_AT' => 'Update  At',
        ];
    }
	
	public function fields()
	{
		return [			
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
			'PAKET_STT'=>function($model){
				return $model->PAKET_STT;
			},	
			'PAKET_STT_NM'=>function($model){
				return $model->PAKET_STT_NM;
			},		
		];
	}
}
