<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "product_group".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property integer $GROUP_ID
 * @property string $GROUP_NM
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $NOTE
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class ProductGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_group';
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
           // [['ACCESS_GROUP', 'STORE_ID', 'GROUP_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['NOTE'], 'string'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 25],
            [['GROUP_NM'], 'string', 'max' => 255],
            [['CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
			[['GROUP_ID'], 'string', 'max' => 100],
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
            'GROUP_ID' => 'Group  ID',
            'GROUP_NM' => 'Group  Nm',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
            'STATUS' => 'Status',
            'NOTE' => 'Note',
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
			'GROUP_ID'=>function($model){
				return $model->GROUP_ID;
			},
			'GROUP_NM'=>function($model){				
				if($model->GROUP_NM){
					return $model->GROUP_NM;
				}else{
					return 'none';
				}
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			},					
			'NOTE'=>function($model){
				if($model->NOTE){
					return $model->NOTE;
				}else{
					return 'none';
				}
			}
		];
	}
}
