<?php

namespace api\modules\hirs\models;

use Yii;

class EmployeDataImage extends \yii\db\ActiveRecord
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
        return 'hrd_data_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['STATUS'], 'integer'],
            [['CREATE_BY', 'UPDATE_BY', 'ACCESS_UNIX', 'OUTLET_CODE', 'EMP_ID'], 'string', 'max' => 50],
            [['IMG64'], 'safe']
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
            'ACCESS_UNIX' => Yii::t('app', 'Access  Unix'),
            'OUTLET_CODE' => Yii::t('app', 'Outlet  Code'),
            'EMP_ID' => Yii::t('app', 'Emp  ID'),
            'IMG64' => Yii::t('app', 'Image')
        ];
    }
	
	public function fields()
	{
		return [			
			'ACCESS_UNIX'=>function($model){
				return $model->ACCESS_UNIX;
			},
			'OUTLET_CODE'=>function($model){
				return $model->OUTLET_CODE;
			},
			'EMP_ID'=>function($model){
				return $model->EMP_ID;
			},					
			'IMG64'=>function($model){
				return $model->IMG64;
			}								
		];
	}
}
