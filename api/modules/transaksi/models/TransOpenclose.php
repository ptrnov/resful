<?php

namespace api\modules\transaksi\models;

use Yii;
use api\modules\transaksi\models\TransStoran;

class TransOpenclose extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_openclose';
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
            [['STORE_ID','ACCESS_ID','TGL_OPEN','CASHINDRAWER'], 'required','on'=>self::SCENARIO_CREATE],
            [['OPENCLOSE_ID','TGL_CLOSE','SELLCASH','TOTALCASH_ACTUAL','STATUS'], 'required','on'=>self::SCENARIO_UPDATE],
            [['TGL_OPEN','TGL_CLOSE', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['CASHINDRAWER', 'ADDCASH', 'SELLCASH', 'TOTALCASH', 'TOTALCASH_ACTUAL','TOTALREFUND','TOTALDONASI'], 'safe'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP', 'ACCESS_ID'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['OPENCLOSE_ID', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 255],
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
            'TGL_OPEN' => 'TGL_OPEN',
            'TGL_CLOSE' => 'TGL_CLOSE',
            'CASHINDRAWER' => 'Cashindrawer',
            'ADDCASH' => 'Addcash',
            'SELLCASH' => 'Sellcash',
            'TOTALCASH' => 'Totalcash',
            'TOTALCASH_ACTUAL' => 'Totalcash  Actual',
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
			'TGL_OPEN'=>function($model){
				return $model->TGL_OPEN;
			},					
			'TGL_CLOSE'=>function($model){
				return $model->TGL_CLOSE;
			},					
			'CASHINDRAWER'=>function($model){
				return $model->CASHINDRAWER;
			},
			'ADDCASH'=>function($model){
				return $model->ADDCASH;
			},
			'SELLCASH'=>function($model){
				return $model->SELLCASH;
			},
			'TOTALCASH'=>function($model){
				return $model->TOTALCASH;
			},			
			'TOTALREFUND'=>function($model){
				return $model->TOTALREFUND;
			},
			'TOTALDONASI'=>function($model){
				return $model->TOTALDONASI;
			},
			'TOTALCASH_ACTUAL'=>function($model){
				return $model->TOTALCASH_ACTUAL;
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
			// 'STORAN'=>function(){
				// return $this->storanTbl;
			// }
		];
	}
	
	// public function getStoranTbl(){
		// return $this->hasOne(TransStoran::className(), ['OPENCLOSE_ID' => 'OPENCLOSE_ID']);
	// }	
}
