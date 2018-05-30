<?php

namespace api\modules\login\models;
use Yii;

class ModulMenu extends \yii\db\ActiveRecord
{
	public $unixUser;
    /**
     * @inheritdoc
     */
	 
	public static function getDb()
    {
        return Yii::$app->get('api_dbkg');
    }
	
    public static function tableName()
    {
        return 'modul';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['MODUL_DCRP','UserUnix'], 'string'],
            [['MODUL_STS', 'SORT','MODUL_ID','MODUL_GRP'], 'integer'],
            [['MODUL_NM','BTN_NM',], 'string', 'max' => 100],
            [['BTN_URL','BTN_ICON',], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'MODUL_ID' => 'Modul  ID',
            'MODUL_GRP' => 'Group',
			'SORT' => 'Sort',
            'MODUL_NM' => 'Modul  Nm',           
            'MODUL_STS' => 'Modul  Sts',            
            'BTN_NM' => 'Button Name',
            'BTN_URL' => 'Url',
            'BTN_ICON' => 'Icol',
			'MODUL_DCRP' => 'Modul  Dcrp',
			
        ];
    }
	
	public function getModulMenuTbl()
	{
	  return $this->hasOne(ModulPermission::className(), ['MODUL_ID' => 'MODUL_ID']);
	}
	public function getUserUnix()
	{
		return $this->modulMenuTbl->USER_UNIX;
	}
	/* public function fields()
	{
		return [
			'mODUL_ID'=>function($MODUL_ID){
				return $model->MODUL_ID;
			},
			'mODUL_GRP'=>function($model){
				return $model->MODUL_GRP;
			},
			'MODUL_NM'=>function($model){
				return $model->MODUL_NM;
			},		
			'SORT'=>function($model){
				return $model->SORT;
			},		
			'MODUL_STS'=>function($model){
				return $model->MODUL_STS;
			},		
			'BTN_NM'=>function($model){
				return $model->BTN_NM;
			},		
			'BTN_URL'=>function($model){
				return $model->BTN_URL;
			},	
			'BTN_ICON'=>function($model){
				return $model->BTN_ICON;
			},
			'MODUL_DCRP'=>function($model){
				return $model->MODUL_DCRP;
			},
			'UserUnix'=>function(){
				return $this->userUnix;
			}			
		
		];
	} */
}
