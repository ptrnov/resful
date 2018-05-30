<?php

namespace api\modules\transaksi\models;

use Yii;
use api\modules\transaksi\models\TransStoranImage;
class TransStoran extends \yii\db\ActiveRecord
{
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_CREATE = 'create';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_storan';
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
            [['OPENCLOSE_ID','TGL_STORAN','NOMINAL_STORAN','BANK_NM','BANK_NO'], 'required','on'=>self::SCENARIO_UPDATE],
            [['OPENCLOSE_ID','TGL_STORAN','NOMINAL_STORAN','TOTALCASH'], 'required','on'=>self::SCENARIO_CREATE],
            [['TGL_STORAN', 'CREATE_AT', 'UPDATE_AT','BANK_NM','BANK_NO'], 'safe'],
            [['TOTALCASH', 'NOMINAL_STORAN', 'SISA_STORAN'], 'number'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP', 'ACCESS_ID'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['OPENCLOSE_ID', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
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
            'OPENCLOSE_ID' => 'Openclose  ID',
            'TGL_STORAN' => 'TGL_STORAN',
            'TOTALCASH' => 'Totalcash',
            'NOMINAL_STORAN' => 'Nominal  Storan',
            'SISA_STORAN' => 'Sisa  Storan',
            'BANK_NM' => 'Bank',
            'BANK_NO' => 'Bank.No',
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
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'ACCESS_ID'=>function($model){
				return $model->ACCESS_ID;
			},
			'OPENCLOSE_ID'=>function($model){
				return $model->OPENCLOSE_ID;
			},
			'TGL_STORAN'=>function($model){
				return $model->TGL_STORAN;
			},					
			'TOTALCASH'=>function($model){
				return $model->TOTALCASH;
			},
			'NOMINAL_STORAN'=>function($model){
				return $model->NOMINAL_STORAN;
			},
			'SISA_STORAN'=>function($model){
				return $model->SISA_STORAN;
			},
			'BANK_NM'=>function($model){
				return $model->BANK_NM;
			},
			'BANK_NO'=>function($model){
				return $model->BANK_NO;
			},
			'CREATE_AT'=>function($model){
				return $model->CREATE_AT;
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
			'STORAN_IMAGE'=>function(){
				return $this->storanImageTbl->STORAN_IMAGE;
			},
		];
	}
	
	public function getStoranImageTbl(){
		return $this->hasOne(TransStoranImage::className(), ['OPENCLOSE_ID' => 'OPENCLOSE_ID']);
	}
}
