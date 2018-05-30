<?php

namespace api\modules\master\models;

use Yii;
use api\modules\master\models\IndustriGroup;
/**
 * This is the model class for table "industri".
 *
 * @property integer $INDUSTRY_ID
 * @property integer $INDUSTRY_GRP_ID
 * @property string $INDUSTRY_NM
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 */
class Industri extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'industri';
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
            [['INDUSTRY_GRP_ID', 'STATUS'], 'integer'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['INDUSTRY_NM'], 'string', 'max' => 255],
            [['CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'INDUSTRY_ID' => 'Industry  ID',
            'INDUSTRY_GRP_ID' => 'Industry  Grp',
            'INDUSTRY_NM' => 'Industry  Nm',
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
			'INDUSTRY_ID'=>function($model){
				return $model->INDUSTRY_ID;
			},
			'INDUSTRY_NM'=>function($model){
				if($model->INDUSTRY_NM){
					return $model->INDUSTRY_NM;
				}else{
					return 'none';
				}
			},
			'INDUSTRY_GRP_ID'=>function($model){
				return $model->INDUSTRY_GRP_ID;
			},
			'INDUSTRY_GRP_NM'=>function($model){
				return $model->industriGroupTbl->INDUSTRY_GRP_NM;
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			}
		];
	}
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table industri (one to one)  Industri Group.
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getIndustriGroupTbl(){
		return $this->hasOne(IndustriGroup::className(), ['INDUSTRY_GRP_ID' => 'INDUSTRY_GRP_ID']);
	}
}
