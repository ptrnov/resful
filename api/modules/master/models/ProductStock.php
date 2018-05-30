<?php

namespace api\modules\master\models;

use Yii;

class ProductStock extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_stock';
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
			[['PRODUCT_ID','INPUT_DATE','INPUT_TIME','INPUT_STOCK'], 'required','on'=>self::SCENARIO_CREATE],
            [['LAST_STOCK', 'INPUT_STOCK', 'CURRENT_STOCK', 'SISA_STOCK'], 'number'],
            [['INPUT_DATE', 'INPUT_TIME', 'CURRENT_DATE', 'CURRENT_TIME', 'CREATE_AT', 'UPDATE_AT','CREATE_UUID','UPDATE_UUID'], 'safe'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['PRODUCT_ID'], 'string', 'max' => 35],
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
            'ACCESS_GROUP' => 'Access  Group',
            'STORE_ID' => 'Store  ID',
            'PRODUCT_ID' => 'Product  ID',
            'LAST_STOCK' => 'Last  Stock',
            'INPUT_DATE' => 'Input  Date',
            'INPUT_TIME' => 'Input  Time',
            'INPUT_STOCK' => 'Input  Stock',
            'CURRENT_DATE' => 'Current  Date',
            'CURRENT_TIME' => 'Current  Time',
            'CURRENT_STOCK' => 'Current  Stock',
            'SISA_STOCK' => 'Sisa  Stock',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
            'STATUS' => 'Status',
            'DCRP_DETIL' => 'Dcrp  Detil',
            'YEAR_AT' => 'Year  At',
            'MONTH_AT' => 'Month  At',
			'CREATE_UUID' => 'CREATE_UUID',
            'UPDATE_UUID' => 'UPDATE_UUID',
        ];
    }
	
	public function fields()
	{
		return [			
			'ID'=>function($model){
				return $model->ID;
			},'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'PRODUCT_ID'=>function($model){
				return $model->PRODUCT_ID;
			},			
			'LAST_STOCK'=>function($model){
				return $model->LAST_STOCK;
			},
			'INPUT_DATE'=>function($model){
				return $model->INPUT_DATE;
			},
			'INPUT_TIME'=>function($model){
				return $model->INPUT_TIME;
			},
			'INPUT_STOCK'=>function($model){
				return $model->INPUT_STOCK;
			},
			'CURRENT_DATE'=>function($model){
				return $model->CURRENT_DATE;
			},
			'CURRENT_TIME'=>function($model){
				return $model->CURRENT_TIME;
			},
			'CURRENT_STOCK'=>function($model){
				return $model->CURRENT_STOCK;
			},
			'SISA_STOCK'=>function($model){
				return $model->SISA_STOCK;
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
