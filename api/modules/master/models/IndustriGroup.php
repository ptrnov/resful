<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "industri_group".
 *
 * @property integer $INDUSTRY_GRP_ID
 * @property string $INDUSTRY_GRP_NM
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 */
class IndustriGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'industri_group';
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
            [['STATUS'], 'integer'],
            [['INDUSTRY_GRP_NM'], 'string', 'max' => 255],
            [['CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'INDUSTRY_GRP_ID' => 'Industry  Grp',
            'INDUSTRY_GRP_NM' => 'Industry  Grp  Nm',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
            'STATUS' => 'Status',
        ];
    }
	
	public function fields()
	{
		return [			
			'INDUSTRY_GRP_ID'=>function($model){
				return $model->INDUSTRY_GRP_ID;
			},
			'INDUSTRY_GRP_NM'=>function($model){
				if($model->INDUSTRY_GRP_NM){
					return $model->INDUSTRY_GRP_NM;
				}else{
					return 'none';
				}
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			}
		];
	}
}
