<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "item_jual".
 *
 * @property string $ID
 * @property string $CREATE_BY USER CREATED
 * @property string $CREATE_AT Tanggal dibuat
 * @property string $UPDATE_BY USER UPDATE
 * @property string $UPDATE_AT Tanggal di update
 * @property int $STATUS 0=Deactive 1=Active(Makesure All status =0, just one status=1)
 * @property string $ITEM_ID
 * @property string $OUTLET_CODE
 * @property string $PERIODE_TGL1
 * @property string $PERIODE_TGL2
 * @property string $START_TIME
 * @property string $HARGA_JUAL balance forecast Penjulanan -> tabel Pembelian TABEL ITEM_BELI
 * @property string $DCRIPT
 */
class ItemJual extends \yii\db\ActiveRecord
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
        return 'item_jual';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ACCESS_UNIX','CREATE_AT', 'UPDATE_AT', 'PERIODE_TGL1', 'PERIODE_TGL2', 'START_TIME'], 'safe'],
            [['STATUS'], 'integer'],
            [['HARGA_JUAL'], 'number'],
            [['DCRIPT'], 'string'],
            [['CREATE_BY', 'UPDATE_BY', 'ITEM_ID', 'OUTLET_CODE'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => Yii::t('app', 'ID'),
            'CREATE_BY' => Yii::t('app', 'Create  By'),
            'CREATE_AT' => Yii::t('app', 'Create  At'),
            'UPDATE_BY' => Yii::t('app', 'Update  By'),
            'UPDATE_AT' => Yii::t('app', 'Update  At'),
            'STATUS' => Yii::t('app', 'Status'),
            'ACCESS_UNIX' => Yii::t('app', 'Access Unix'),
            'ITEM_ID' => Yii::t('app', 'Item  ID'),
            'OUTLET_CODE' => Yii::t('app', 'Outlet  Code'),
            'PERIODE_TGL1' => Yii::t('app', 'Periode  Tgl1'),
            'PERIODE_TGL2' => Yii::t('app', 'Periode  Tgl2'),
            'START_TIME' => Yii::t('app', 'Start  Time'),
            'HARGA_JUAL' => Yii::t('app', 'Harga  Jual'),
            'DCRIPT' => Yii::t('app', 'Dcript'),
        ];
    }
	public function fields()
	{
		return [			
			'PERIODE_TGL1'=>function($model){
				return $model->PERIODE_TGL1;
			},
			'PERIODE_TGL2'=>function($model){
				return $model->PERIODE_TGL2;
			},					
			'START_TIME'=>function($model){
				return $model->START_TIME;
			},	
			'HARGA_JUAL'=>function($model){
				return $model->HARGA_JUAL;
			},				
			'DCRIPT'=>function($model){
				return $model->DCRIPT;
			}			
		];
	}
}
