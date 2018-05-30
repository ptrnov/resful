<?php

namespace api\modules\ppob\models;

use Yii;

/**
 * This is the model class for table "ppob_list_nominal".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property string $DETAIL_ID
 * @property string $KODE
 * @property string $KETERANGAN
 * @property string $NOMINAL
 * @property string $HARGA_KG
 * @property string $HARGA_JUAL
 * @property integer $STATUS
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class PpobListNominal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ppob_list_nominal';
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
            [['ACCESS_GROUP', 'STORE_ID', 'DETAIL_ID', 'KODE', 'KETERANGAN'], 'required'],
            [['HARGA_KG', 'HARGA_JUAL'], 'number'],
            [['STATUS'], 'integer'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 25],
            [['DETAIL_ID'], 'string', 'max' => 8],
            [['KODE', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['KETERANGAN', 'NOMINAL'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'ACCESS_GROUP' => 'Access  Group',
            'STORE_ID' => 'Store  ID',
            'DETAIL_ID' => 'Detail  ID',
            'KODE' => 'Kode',
            'KETERANGAN' => 'Keterangan',
            'NOMINAL' => 'Nominal',
            'HARGA_KG' => 'Harga  Kg',
            'HARGA_JUAL' => 'Harga  Jual',
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
			'ID'=>function($model){
				return $model->ID;
			},
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'DETAIL_ID'=>function($model){
				return $model->DETAIL_ID;
			},
			'KODE'=>function($model){
				return $model->KODE;
			},
			'KETERANGAN'=>function($model){
				return $model->KETERANGAN;
			},
			'NOMINAL'=>function($model){
				return $model->NOMINAL;
			},
			'HARGA_KG'=>function($model){
				return $model->HARGA_KG;
			},
			'HARGA_JUAL'=>function($model){
				return $model->HARGA_JUAL;
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			}
		];
	}
}
