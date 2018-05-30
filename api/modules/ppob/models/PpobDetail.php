<?php

namespace api\modules\ppob\models;


use Yii;
use api\modules\ppob\models\PpobHeader;
/**
 * This is the model class for table "ppob_detail".
 *
 * @property string $ID
 * @property string $DETAIL_ID
 * @property string $HEADER_ID
 * @property string $PROVIDER_ID
 * @property string $DETAIL_NM
 * @property string $PROVIDER_NM
 * @property string $DETAIL_DCRP
 * @property integer $STATUS
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class PpobDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ppob_detail';
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
            [['DETAIL_ID', 'HEADER_ID', 'PROVIDER_ID'], 'required'],
            [['PROVIDER_ID', 'STATUS'], 'integer'],
            [['DETAIL_DCRP'], 'string'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['DETAIL_ID'], 'string', 'max' => 8],
            [['HEADER_ID'], 'string', 'max' => 3],
            [['DETAIL_NM', 'PROVIDER_NM'], 'string', 'max' => 100],
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
            'DETAIL_ID' => 'Detail  ID',
            'HEADER_ID' => 'Header  ID',
            'PROVIDER_ID' => 'Provider  ID',
            'DETAIL_NM' => 'Detail  Nm',
            'PROVIDER_NM' => 'Provider  Nm',
            'DETAIL_DCRP' => 'Detail  Dcrp',
            'STATUS' => 'Status',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
        ];
    }
	
	public function fields()
	{
		return [			
			'DETAIL_ID'=>function($model){
				return $model->DETAIL_ID;
			},
			'DETAIL_NM'=>function($model){
				return $model->DETAIL_NM;
			},
			'HEADER_ID'=>function($model){
				return $model->HEADER_ID;
			},
			'HEADER_NM'=>function(){
				return $this->headerNm;
			},
			'PROVIDER_ID'=>function($model){
				return $model->PROVIDER_ID;
			},
			'PROVIDER_NM'=>function($model){
				return $model->PROVIDER_NM;
			},
			'DETAIL_DCRP'=>function($model){
				return $model->DETAIL_DCRP;
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			}
		];
	}
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table Header (one to one)
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getMerchanTbl(){
		return $this->hasOne(PpobHeader::className(), ['HEADER_ID' => 'HEADER_ID']);
	}
	//HEADER NM
	public function getHeaderNm(){
		return $this->merchanTbl->HEADER_NM;
	}
}
