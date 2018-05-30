<?php

namespace api\modules\ppob\models;

use Yii;

/**
 * This is the model class for table "ppob_master_ktg".
 *
 * @property string $ID
 * @property string $KTG_ID
 * @property string $KTG_NM
 * @property int $STATUS
 * @property string $KETERANGAN
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class PpobMasterKtg extends \yii\db\ActiveRecord
{
	
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
    public static function tableName()
    {
        return 'ppob_master_ktg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['KTG_NM'], 'required'],
            [['STATUS'], 'integer'],
            [['KETERANGAN'], 'string'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['KTG_ID', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['KTG_NM'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'KTG_ID' => 'Ktg  ID',
            'KTG_NM' => 'Ktg  Nm',
            'STATUS' => 'Status',
            'KETERANGAN' => 'Keterangan',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
        ];
    }
	

	public function fields()
	{
		return [			
			'KELOMPOK'=>function($model){
				return $model->KELOMPOK;
			},
			'KTG_ID'=>function($model){
				return $model->KTG_ID;
			},
			'KTG_NM'=>function($model){
				return $model->KTG_NM;
			},
			'KETERANGAN'=>function($model){
				return $model->KETERANGAN;
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			}
		];
	}
}
