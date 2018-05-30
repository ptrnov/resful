<?php

namespace api\modules\login\models;

use Yii;

/**
 * This is the model class for table "app_modul_permission".
 *
 * @property integer $ID
 * @property string $ACCESS_ID
 * @property string $ACCESS_LEVEL
 * @property string $MODUL_ID
 * @property string $MODUL_NM
 * @property string $MODUL_GRP
 * @property integer $BTN_VIEW
 * @property integer $BTN_CREATE
 * @property integer $BTN_UPDATE
 * @property integer $BTN_DELETE
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class AppModulPermission extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_modul_permission';
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
            [['MODUL_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['BTN_VIEW', 'BTN_CREATE', 'BTN_UPDATE', 'BTN_DELETE', 'STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_ID', 'ACCESS_LEVEL', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['MODUL_ID', 'MODUL_GRP'], 'string', 'max' => 5],
            [['MODUL_NM'], 'string', 'max' => 100],
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
            'ACCESS_LEVEL' => 'Access  Level',
            'MODUL_ID' => 'Modul  ID',
            'MODUL_NM' => 'Modul  Nm',
            'MODUL_GRP' => 'Modul  Grp',
            'BTN_VIEW' => 'Btn  View',
            'BTN_CREATE' => 'Btn  Create',
            'BTN_UPDATE' => 'Btn  Update',
            'BTN_DELETE' => 'Btn  Delete',
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
		$rslt= [		
			'ACCESS_ID'=>function($model){
				return $model->ACCESS_ID;
			},	
			'MODUL_ID'=>function($model){
				return $model->MODUL_ID;
			},				
			'MODUL_NM'=>function($model){
				return $model->MODUL_NM;
			},
			'MODUL_GRP'=>function($model){
				return $model->MODUL_GRP;
			},
			'BTN_VIEW'=>function($model){
				return $model->BTN_VIEW;
			},
			'BTN_CREATE'=>function($model){
				return $model->BTN_CREATE;
			},
			'BTN_DELETE'=>function($model){
				return $model->BTN_DELETE;
			},
			'ACCESS_LEVEL'=>function($model){
				return $model->ACCESS_LEVEL;
			},
			'STATUS'=>function($model){
				$rslt=$model->STATUS;
				return $rslt==0?'disable':'enable';
			},				
		]; 
		return $rslt;
	} 
}
