<?php

namespace api\modules\pembayaran\models;

use Yii;

/**
 * This is the model class for table "dompet_saldo".
 *
 * @property string $ACCESS_GROUP user ACCESS_LEVEL=OWNER (ACCESS_ID=ACCESS_GROUP)
 * @property string $VA_ID VIRTUAL ACCOUT ID
 * @property string $SALDO_DOMPET total semua saldo
 * @property string $SALDO_MENEGNDAP saldo mengendap di tahan
 * @property string $SALDO_JUALAN
 * @property string $CURRENT_TGL diambil dari Tbl ptr_ppob_lpts4->current_date
 * @property string $TGL
 * @property string $WAKTU diambil dari Tbl ptr_ppob_lpts4->current_date
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class DompetSaldo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dompet_saldo';
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
            [['ACCESS_GROUP'], 'required'],
            [['SALDO_DOMPET', 'SALDO_MENEGNDAP', 'SALDO_JUALAN'], 'number'],
            [['CURRENT_TGL', 'TGL', 'WAKTU', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['VA_ID'], 'string', 'max' => 100],
            [['CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            // [['ACCESS_GROUP', 'VA_ID'], 'unique', 'targetAttribute' => ['ACCESS_GROUP', 'VA_ID']],
            // [['ACCESS_GROUP'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ACCESS_GROUP' => 'ACCESS_GROUP',
            'VA_ID' => 'VA_ID',
            'SALDO_DOMPET' => 'SALDO_DOMPET',
            'SALDO_MENEGNDAP' => 'SALDO_MENEGNDAP',
            'SALDO_JUALAN' => 'SALDO_JUALAN',
            'CURRENT_TGL' => 'CURRENT_TGL',
            'TGL' => 'TGL',
            'WAKTU' => 'WAKTU',
            'CREATE_BY' => 'CREATE_BY',
            'CREATE_AT' => 'CREATE_AT',
            'UPDATE_BY' => 'UPDATE_BY',
            'UPDATE_AT' => 'UPDATE_AT',
        ];
    }
	
	public function fields()
	{
		return [			
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'VA_ID'=>function($model){
				return $model->VA_ID;
			},
			'SALDO_DOMPET'=>function($model){
				return $this->SALDO_DOMPET;
			},
			'SALDO_MENEGNDAP'=>function($model){
				return $model->SALDO_MENEGNDAP;
			},
			'SALDO_JUALAN'=>function($model){
				return $model->SALDO_JUALAN;
			},			
			'CURRENT_TGL'=>function($model){
				return $model->CURRENT_TGL;
			},	
			'TGL'=>function($model){
				return $model->TGL;
			},					
			'WAKTU'=>function($model){
				return $model->WAKTU;
			},					
			'WAKTU'=>function($model){
				return $model->WAKTU;
			}		
		];
	}
}
