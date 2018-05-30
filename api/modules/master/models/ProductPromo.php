<?php

namespace api\modules\master\models;

use Yii;

class ProductPromo extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_promo';
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
            [['PRODUCT_ID','PERIODE_TGL1','PERIODE_TGL2','START_TIME','PROMO'], 'required','on'=>self::SCENARIO_CREATE],
            [['PERIODE_TGL1', 'PERIODE_TGL2', 'START_TIME', 'CREATE_AT', 'UPDATE_AT','END_TIME'], 'safe'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['PRODUCT_ID'], 'string', 'max' => 35],
            [['PROMO', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
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
            'PRODUCT_ID' => 'Product  ID',
            'PERIODE_TGL1' => 'Periode  Tgl1',
            'PERIODE_TGL2' => 'Periode  Tgl2',
            'START_TIME' => 'Start  Time',
            'END_TIME' => 'END_TIME',
            'PROMO' => 'Promo',
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
			'ID'=>function($model){
				return $model->ID;
			},
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'PRODUCT_ID'=>function($model){
				return $model->PRODUCT_ID;
			},			
			'PERIODE_TGL1'=>function($model){
				return $model->PERIODE_TGL1;
			},
			'PERIODE_TGL2'=>function($model){
				return $model->PERIODE_TGL2;
			},
			'START_TIME'=>function($model){
				return $model->START_TIME;
			},
			'END_TIME'=>function($model){
				return $model->END_TIME;
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
