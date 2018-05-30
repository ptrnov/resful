<?php

namespace api\modules\login\models;

use Yii;

/**
 * This is the model class for table "user_image".
 *
 * @property string $ID
 * @property string $ACCESS_ID
 * @property string $ACCESS_IMAGE
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class UserImage extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_image';
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
			[['ACCESS_ID','ACCESS_IMAGE'], 'required','on'=>self::SCENARIO_CREATE],
			[['ACCESS_ID','ACCESS_IMAGE'], 'required','on'=>self::SCENARIO_UPDATE],
            [['ACCESS_IMAGE', 'DCRP_DETIL'], 'string'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['ACCESS_ID', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'ACCESS_ID' => 'Access  ID',
            'ACCESS_IMAGE' => 'Access  Image',
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
			'ACCESS_ID'=>function($model){
				return $model->ACCESS_ID;
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
			'ACCESS_IMAGE'=>function($model){
				return $model->ACCESS_IMAGE;
			},
		];
	}
}
