<?php

namespace api\modules\pembayaran\models;

use Yii;

class StoreKasir extends \yii\db\ActiveRecord
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
        return 'store_kasir';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['KASIR_ID'], 'required'],
            [['KASIR_STT', 'DOMPET_AUTODEBET', 'PAKET_ID', 'STATUS'], 'integer'],
            [['DATE_START', 'DATE_END', 'KONTRAK_DATE', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['KASIR_ID', 'PERANGKAT_UUID'], 'string', 'max' => 100],
            [['KASIR_NM', 'ACCESS_GROUP', 'STORE_ID', 'KASIR_STT_NM', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['KONTRAK_DURASI'], 'string', 'max' => 255],
            [['KASIR_ID', 'ACCESS_GROUP', 'STORE_ID'], 'unique', 'targetAttribute' => ['KASIR_ID', 'ACCESS_GROUP', 'STORE_ID']],
            [['KASIR_ID'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'KASIR_ID' => 'KASIR_ID',
            'KASIR_NM' => 'KASIR_NM',
            'ACCESS_GROUP' => 'ACCESS_GROUP',
            'STORE_ID' => 'STORE_ID',
            'PERANGKAT_UUID' => 'PERANGKAT_UUID',
            'KASIR_STT' => 'KASIR_STT',
            'KASIR_STT_NM' => 'KASIR_STT_NM',
            'DOMPET_AUTODEBET' => 'DOMPET_AUTODEBET',
            'PAKET_ID' => 'PAKET_ID',
            'DATE_START' => 'DATE_START',
            'DATE_END' => 'DATE_END',
            'KONTRAK_DURASI' => 'KONTRAK_DURASI',
            'KONTRAK_DATE' => 'KONTRAK_DATE',
            'STATUS' => 'STATUS',
            'CREATE_BY' => 'CREATE_BY',
            'UPDATE_BY' => 'UPDATE_BY',
            'CREATE_AT' => 'CREATE_AT',
            'UPDATE_AT' => 'UPDATE_AT',
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
			'KASIR_ID'=>function($model){
				return $model->KASIR_ID;
			},
			'KASIR_NM'=>function($model){
				return $model->KASIR_NM;
			},					
			'PERANGKAT_UUID'=>function($model){
				return $model->PERANGKAT_UUID;
			},					
			'KASIR_STT'=>function($model){
				return $model->KASIR_STT;
			},					
			'KASIR_STT_NM'=>function($model){
				return $model->KASIR_STT_NM;
			},					
			'DOMPET_AUTODEBET'=>function($model){
				return $model->DOMPET_AUTODEBET;
			},					
			'PAKET_ID'=>function($model){
				return $model->PAKET_ID;
			},					
			'DATE_START'=>function($model){
				return $model->DATE_START;
			},					
			'DATE_END'=>function($model){
				return $model->DATE_END;
			},					
			'KONTRAK_DURASI'=>function($model){
				return $model->KONTRAK_DURASI;
			},					
			'KONTRAK_DATE'=>function($model){
				return $model->KONTRAK_DATE;
			},					
			'STATUS'=>function($model){
				return $model->STATUS;
			},					
			'STATUS_NM'=>function($model){
				$sttNm=$model->STATUS==0?'disable':($model->STATUS==1?'Enable':'Delete');
			},					
			
		];
	}
}
