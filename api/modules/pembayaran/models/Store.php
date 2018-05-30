<?php

namespace api\modules\pembayaran\models;

use Yii;
use api\modules\pembayaran\models\StorePerangkatKasir;
/**
 * This is the model class for table "store".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property string $STORE_NM
 * @property string $ACCESS_ID
 * @property string $UUID
 * @property resource $PLAYER_ID
 * @property string $DATE_START
 * @property string $DATE_END
 * @property integer $PROVINCE_ID
 * @property string $PROVINCE_NM
 * @property integer $CITY_ID
 * @property string $CITY_NAME
 * @property string $ALAMAT
 * @property string $PIC
 * @property string $TLP
 * @property string $FAX
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
 

 
class Store extends \yii\db\ActiveRecord
{
	public function attributes()
	{
		return array_merge(parent::attributes(), ['PERANGKAT_UUID']);
	}
	
	public static function getDb()
    {
        return Yii::$app->get('production_api');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    // public static function getDb()
    // {
        // return Yii::$app->get(['production_api','storeUuid']);
    // }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['STORE_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['ACCESS_ID', 'UUID', 'PLAYER_ID', 'ALAMAT', 'DCRP_DETIL'], 'string'],
            [['INDUSTRY_ID','INDUSTRY_NM','INDUSTRY_GRP_ID','INDUSTRY_GRP_NM','PERANGKAT_UUID','storeUuid'], 'safe'],
            [['DATE_START', 'DATE_END', 'CREATE_AT', 'UPDATE_AT','PPN'], 'safe'],
            [['PROVINCE_ID', 'CITY_ID', 'STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 25],
            [['STORE_NM', 'PIC'], 'string', 'max' => 100],
            [['PROVINCE_NM', 'CITY_NAME', 'TLP', 'FAX', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
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
            'STORE_NM' => 'Store  Nm',
            'ACCESS_ID' => 'Access  ID',
            'UUID' => 'Uuid',
            'PLAYER_ID' => 'Player  ID',
            'DATE_START' => 'Date  Start',
            'DATE_END' => 'Date  End',
            'PROVINCE_ID' => 'Province  ID',
            'PROVINCE_NM' => 'Province  Nm',
            'CITY_ID' => 'City  ID',
            'CITY_NAME' => 'City  Name',
            'ALAMAT' => 'Alamat',
            'PIC' => 'Pic',
            'TLP' => 'Tlp',
            'FAX' => 'Fax',
            'PPN' => 'Pajak',
            'INDUSTRY_ID' => 'INDUSTRY_ID',
            'INDUSTRY_NM' => 'INDUSTRY_NM',
            'INDUSTRY_GRP_ID' => 'INDUSTRY_GRP_ID',
            'INDUSTRY_GRP_NM' => 'INDUSTRY_GRP_NM',
            'CREATE_BY' => 'Create  By',
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
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},	
			'STORE_ID'=>function($model){
					return $model->STORE_ID;
			},	
			'STORE_NM'=>function($model){
					return $model->STORE_NM;
			},	
			'ACCESS_ID'=>function($model){
					return $model->ACCESS_ID;
			},	
			'UUID'=>function($model){
					return $model->UUID;
			},	
			'PERANGKAT_UUID'=>function($model){
					return $this->storeUuid;
			}			
		];
	}
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table user (one to one) Table Store Merchant.
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getStoreUuid(){
		$explodeUUid = explode(',',$this->UUID);
		foreach ($explodeUUid as $row){
			if ($row <> 'undefined' AND $row <> ''){				
				$storeKasir=StorePerangkatKasir::find()->where(['PERANGKAT_UUID'=>$row])->one();
				if($storeKasir){
					$kasirId=$storeKasir->KASIR_ID;
					$kasirNm=$storeKasir->KASIR_NM;
				}else{
					$kasirId='0';
					$kasirNm='NONE';
				}
				$aryUuid[]=[
					'PERANGKAT_UUID'=>$row,'KASIR_ID'=>$kasirId,'KASIR_NM'=>$kasirNm
				];
			}				
		};
		return $aryUuid;
	}
	

}
