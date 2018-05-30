<?php

namespace api\modules\master\models;

use Yii;


class Customer extends \yii\db\ActiveRecord
{
	public static function getDb()
    {
        return Yii::$app->get('api_dbkg');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['SATUAN_NM'], 'required','on'=>'create'],
            [['ACCESS_UNIX','CREATE_AT', 'UPDATE_AT', 'NAME','EMAIL','PHONE'], 'safe'],
            [['STATUS'], 'integer'],
            [['CREATE_BY', 'UPDATE_BY','OUTLET_CODE'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => Yii::t('app', 'ID'),
            'CREATE_BY' => Yii::t('app', 'Create  By'),
            'CREATE_AT' => Yii::t('app', 'Create  At'),
            'UPDATE_BY' => Yii::t('app', 'Update  By'),
            'UPDATE_AT' => Yii::t('app', 'Update  At'),
            'STATUS' => Yii::t('app', 'Status'),
            'ACCESS_UNIX' => Yii::t('app', 'ACCESS_UNIX'),
            'OUTLET_CODE' => Yii::t('app', 'OUTLET_CODE'),
            'NAME' => Yii::t('app', 'NAME'),
            'EMAIL' => Yii::t('app', 'EMAIL'),
            'PHONE' => Yii::t('app', 'PHONE'),
        ];
    }
	public function fields()
	{
		return [			
			'CREATE_AT'=>function($model){
				return $model->CREATE_AT;
			},
			'UPDATE_AT'=>function($model){
				return $model->UPDATE_AT;
			},					
			'ACCESS_UNIX'=>function($model){
				return $model->ACCESS_UNIX;
			},					
			'OUTLET_CODE'=>function($model){
				return $model->OUTLET_CODE;
			},
			'NAME'=>function($model){
				return $model->NAME;
			},
			'EMAIL'=>function($model){
				return $model->EMAIL;
			},
			'PHONE'=>function($model){
				return $model->PHONE;
			}	
		];
	}
}

