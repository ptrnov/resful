<?php

namespace api\modules\master\models;


use Yii;

/**
 * This is the model class for table "c0001g2".
 *
 * @property integer $CITY_ID
 * @property string $PROVINCE_ID
 * @property string $PROVINCE
 * @property string $TYPE
 * @property string $CITY_NAME
 * @property integer $POSTAL_CODE
 */
class Kota extends \yii\db\ActiveRecord
{
	
	public static function getDb()
    {
       return Yii::$app->get('production_api');
    }
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'locate_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PROVINCE_ID'], 'required'],
            [['CITY_ID', 'POSTAL_CODE'], 'integer'],
            [['PROVINCE_ID', 'PROVINCE', 'TYPE', 'CITY_NAME'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CITY_ID' => 'City  ID',
            'PROVINCE_ID' => ' Nama Province ',
            'PROVINCE' => 'Province',
            'TYPE' => 'Type',
            'CITY_NAME' => 'City  Name',
            'POSTAL_CODE' => 'Kode Pos',
        ];
    }
	
	public function fields()
	{
		return [			
			'PROVINCE_ID'=>function($model){
				return $model->PROVINCE_ID;
			},
			'PROVINCE'=>function($model){
				return $model->PROVINCE;
			},
			'CITY_ID'=>function($model){
				return $model->CITY_ID;
			},
			'CITY_NAME'=>function($model){
				return $model->CITY_NAME;
			},
			'TYPE'=>function($model){
				return $model->TYPE;
			},					
			'POSTAL_CODE'=>function($model){
				return $model->POSTAL_CODE;
			}
		];
	}
}
