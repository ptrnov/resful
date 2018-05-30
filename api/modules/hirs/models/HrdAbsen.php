<?php

namespace api\modules\hirs\models;

use Yii;
use api\modules\hirs\models\HrdAbsenImg;

class HrdAbsen extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hrd_absen';
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
			//[['STORE_ID','KARYAWAN_ID','TGL','WAKTU','OFLINE_ID'], 'required','on'=>self::SCENARIO_CREATE],
			[['STORE_ID','KARYAWAN_ID','TGL','WAKTU'], 'required','on'=>self::SCENARIO_CREATE],
			[['ABSEN_ID','KARYAWAN_ID'], 'required','on'=>self::SCENARIO_UPDATE],
            [['TGL', 'WAKTU', 'CREATE_AT', 'UPDATE_AT','OFLINE_ID','LATITUDE','LONGITUDE'], 'safe'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 25],
            [['KARYAWAN_ID'], 'string', 'max' => 30],
            [['CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'ABSEN_ID' => 'Absen  ID',
            'OFLINE_ID' => 'Ofline  ID',
            'ACCESS_GROUP' => 'Access  Group',
            'STORE_ID' => 'Store  ID',
            'KARYAWAN_ID' => 'Karyawan  ID',
            'TGL' => 'Tgl',
            'WAKTU' => 'Waktu',
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
			// 'ID'=>function($model){
				// return $model->ID;
			// },
			'ABSEN_ID'=>function($model){
				return $model->ABSEN_ID;
			},
			'OFLINE_ID'=>function($model){
				return $model->OFLINE_ID;
			},
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'KARYAWAN_ID'=>function($model){
				return $model->KARYAWAN_ID;
			},
			'TGL'=>function($model){
				return $model->TGL;
			},
			'WAKTU'=>function($model){
				return $model->WAKTU;
			},
			'LATITUDE'=>function($model){
				return $model->LATITUDE;
			},					
			'LONGITUDE'=>function($model){
				return $model->LONGITUDE;
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
			},
			'ABSEN_IMAGE'=>function(){
				return $this->absenImageTbl->ABSEN_IMAGE;
			},			
		];
	}
	
	public function getAbsenImageTbl(){
		return $this->hasOne(HrdAbsenImg::className(), ['ABSEN_ID' => 'ABSEN_ID','KARYAWAN_ID'=>'KARYAWAN_ID']);
		// $modelImage=HrdAbsenImg::find()->where(['KARYAWAN_ID'=>$this->KARYAWAN_ID,'ABSEN_ID'=>$this->ABSEN_ID])->one();
		// return $modelImage;
	}	
}
