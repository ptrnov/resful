<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property integer $CUSTOMER_ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property string $NAME
 * @property string $EMAIL
 * @property string $PHONE
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
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
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['CUSTOMER_ID','STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            //[['YEAR_AT', 'MONTH_AT'], 'required'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 25],
            [['NAME', 'EMAIL'], 'string', 'max' => 255],
            [['PHONE'], 'string', 'max' => 100],
            [['CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CUSTOMER_ID' => 'CUSTOMER_ID',
            'ACCESS_GROUP' => 'Access  Group',
            'STORE_ID' => 'Store  ID',
            'NAME' => 'Name',
            'EMAIL' => 'Email',
            'PHONE' => 'Phone',
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
			'CUSTOMER_ID'=>function($model){
				return $model->CUSTOMER_ID;
			},
			'NAME'=>function($model){
				return $model->NAME;
			},
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'EMAIL'=>function($model){
				return $model->EMAIL;
			},
			'PHONE'=>function($model){
				return $model->PHONE;
			},					
			'STATUS'=>function($model){
				// $rslt=$model->STATUS;
				// if($rslt==0){
					// return 'Disable'; 
				// }elseif($rslt==1){
					// return 'Enable';
				// }elseif($rslt==3){
					// return 'Delete';
				// };				 
				return $model->STATUS;
			},
			'DCRP_DETIL'=>function($model){
				return $model->DCRP_DETIL;
			}
		];
	}
}
