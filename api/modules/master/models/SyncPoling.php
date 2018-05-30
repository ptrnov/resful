<?php

namespace api\modules\master\models;

use Yii;
/**
 * == TYPE_ACTION ====
 * 1. CEATE
 * 2. UPDATE
 * 3. DELETE
 * 
 * == STT_OPS ====
 * 1. SINKRON
 * 2. TIDAK_SINKRON
 * 
 *  == STT_OWNER ====
 * 1. SINKRON
 * 2. TIDAK_SINKRON
 *
 */
class SyncPoling extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_pooling';
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
            [['ID', 'TYPE_ACTION'], 'integer'],
            [['CREATE_AT', 'UPDATE_AT','ARY_UUID','ARY_PLAYERID'], 'safe'],
            [['NM_TABLE', 'PRIMARIKEY_NM','PRIMARIKEY_VAL','PRIMARIKEY_ID'], 'string', 'max' => 255],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
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
            'NM_TABLE' => 'NM_TABLE',
            'PRIMARIKEY_NM' => 'PRIMARIKEY_NM',
            'PRIMARIKEY_VAL' => 'PRIMARIKEY_VAL',
            'PRIMARIKEY_ID' => 'PRIMARIKEY_ID',
            'TYPE_ACTION' => 'TYPE_ACTION',
            'ACCESS_GROUP' => 'ACCESS_GROUP',
            'STORE_ID' => 'STORE_ID',
            'ARY_UUID' => 'ARY_UUID',
            'ARY_PLAYERID' => 'ARY_PLAYERID',
            'CREATE_BY' => 'CREATE_BY',
            'CREATE_AT' => 'CREATE_AT',
            'UPDATE_BY' => 'UPDATE_BY',
            'UPDATE_AT' => 'UPDATE_AT',
        ];
    }
	public function fields()
	{
		return [		
			'NM_TABLE'=>function($model){
				return $model->NM_TABLE;
			},	
			'PRIMARIKEY_NM'=>function($model){
				return $model->PRIMARIKEY_NM;
			},	
			'PRIMARIKEY_VAL'=>function($model){
				return $model->PRIMARIKEY_VAL;
			},	
			'PRIMARIKEY_ID'=>function($model){
				return $model->PRIMARIKEY_ID;
			},	
			'TYPE_ACTION'=>function($model){
				return $model->TYPE_ACTION;
			},	
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},	
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},	
			'ARY_UUID'=>function($model){
				return $model->ARY_UUID;
			},	
			'ARY_PLAYERID'=>function($model){
				return $model->ARY_PLAYERID;
			}	
		];
	}
}
