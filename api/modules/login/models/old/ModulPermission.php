<?php

namespace api\modules\login\models;
use Yii;

class ModulPermission extends \yii\db\ActiveRecord
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
        return 'modul_permission';
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
			[['MODUL_ID'], 'integer'],
			[['USER_UNIX'],'string'],
			[['STATUS','BTN_VIEW','BTN_REVIEW', 'BTN_CREATE','BTN_EDIT', 'BTN_DELETE'], 'integer'],
			[['BTN_SIGN1', 'BTN_SIGN2', 'BTN_SIGN3','BTN_SIGN4','BTN_SIGN5'], 'integer'],
			[['CREATE_BY','UPDATE_BY'],'string'],
			[['CREATE_AT','UPDATE_AT'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'USER_UNIX' => 'USER_UNIX',
            'MODUL_ID' => 'Modul.ID',
            'STATUS' => 'Status',
            'BTN_VIEW' => 'view', 
			'BTN_REVIEW' => 'view',
			'BTN_CREATE' => 'Create',
            'BTN_EDIT' => 'Edit',
            'BTN_DELETE' => 'Delete',
            'BTN_SIGN1' => 'Sign1',
            'BTN_SIGN2' => 'Sign2',
            'BTN_SIGN3' => 'Sign3',
			'BTN_SIGN4' => 'Sign4',
			'BTN_SIGN5' => 'Sign5',
			'CREATE_BY' => 'Created.By',
			'CREATE_AT' => 'Created.At',
			'UPDATE_BY' => 'Update.By',
			'UPDATE_AT' => 'Update.At'
        ];
    }

    public function getModul()
	{
	  return $this->hasOne(ModulMenu::className(), ['MODUL_ID' => 'MODUL_ID']);
	}
	
	// public function getModulDcrp()
	// {
		// return $this->modul->MODUL_DCRP;
	// }
	
	// public function getUser()
	// {
	  // return $this->hasOne(Userlogin::className(), ['id' => 'USER_ID']);
	// }
	
	// public function getUserNm()
	// {
		// return $this->user->username;
	// }
	
}
