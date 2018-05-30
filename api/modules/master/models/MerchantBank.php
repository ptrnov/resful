<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "merchant_bank".
 *
 * @property integer $BANK_ID
 * @property string $BANK_NM
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class MerchantBank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'merchant_bank';
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
            [['STATUS'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['BANK_NM'], 'string', 'max' => 150],
            [['CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'BANK_ID' => 'Bank  ID',
            'BANK_NM' => 'Bank  Nm',
            'STATUS' => 'Status',
            'DCRP_DETIL' => 'Dcrp  Detil',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
        ];
    }
	
	public function fields()
	{
		return [			
			'BANK_ID'=>function($model){
				return $model->BANK_ID;
			},
			'BANK_NM'=>function($model){
				return $model->BANK_NM;
			},
			'DCRP_DETIL'=>function($model){
				return $model->DCRP_DETIL;
			},					
			'STATUS'=>function($model){
				$rslt=$model->STATUS;
				if($rslt==0){
					return 'Disable'; 
				}elseif($rslt==1){
					return 'Enable';
				};					
			},
		];
	}
}
