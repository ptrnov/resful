<?php

namespace api\modules\laporan\models;

use Yii;
use yii\helpers\ArrayHelper;

class Store extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store';
    }

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
			[['ACCESS_GROUP', 'STORE_ID', 'STORE_NM'], 'string'],
			[['STATUS'], 'integer']   
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'ACCESS_GROUP' => Yii::t('app', 'ACCESS_GROUP'),                     
			'STORE_ID' => Yii::t('app', 'STORE CODE'),
			'STORE_NM' => Yii::t('app', 'OUTLET NAME'),
            'STATUS' => Yii::t('app', 'STATUS'),  
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
			'STATUS'=>function($model){
				return $model->STATUS;
			},	
		];
	}	
}
