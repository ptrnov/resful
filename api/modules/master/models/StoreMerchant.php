<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "store_merchant".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property integer $TYPE_PAY
 * @property string $BANK_NM
 * @property string $MERCHANT_NM
 * @property string $MERCHANT_NO
 * @property string $MERCHANT_TOKEN
 * @property string $MERCHANT_URL
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class StoreMerchant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_merchant';
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
            //[['STORE_ID','MERCHANT_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['TYPE_PAY_ID', 'STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['MERCHANT_URL', 'DCRP_DETIL'], 'string'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['TYPE_PAY_NM','BANK_NM'], 'string', 'max' => 150],
            [['STORE_ID'], 'string', 'max' => 25],
            [['BANK_NM', 'MERCHANT_NM', 'MERCHANT_NO', 'MERCHANT_TOKEN'], 'string', 'max' => 255],
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
            'ACCESS_GROUP' => 'Access  Group',
            'STORE_ID' => 'Store  ID',
            'MERCHANT_ID' => 'MERCHANT_ID',
            'TYPE_PAY_ID' => 'TypePayID',
            'TYPE_PAY_NM' => 'TypePayNM',
            'BANK_ID' => 'BankId',
            'BANK_NM' => 'BankNm',
            'MERCHANT_NM' => 'Merchant  Nm',
            'MERCHANT_NO' => 'Merchant  No',
            'MERCHANT_TOKEN' => 'Merchant  Token',
            'MERCHANT_URL' => 'Merchant  Url',
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
		return [			
			'STATUS'=>function($model){
				$rslt=$model->STATUS;
				if($rslt==0){
					return 'Disable'; 
				}elseif($rslt==1){
					return 'Enable';
				}elseif($rslt==3){
					return 'Deleted';
				};					
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},'MERCHANT_ID'=>function($model){
				return $model->MERCHANT_ID;
			},
			'TYPE_PAY_ID'=>function($model){
				return $model->TYPE_PAY_ID;
			},
			'TYPE_PAY_NM'=>function($model){
				return $model->TYPE_PAY_NM;
			},
			'BANK_ID'=>function($model){
				return $model->BANK_ID;
			},					
			'BANK_NM'=>function($model){
				return $model->BANK_NM;
			},					
			'MERCHANT_NM'=>function($model){
				return $model->MERCHANT_NM;
			},					
			'MERCHANT_NO'=>function($model){
				return $model->MERCHANT_NO;
			},					
			'MERCHANT_TOKEN'=>function($model){
				return $model->MERCHANT_TOKEN;
			},					
			'MERCHANT_URL'=>function($model){
				return $model->MERCHANT_URL;
			},
			'DCRP_DETIL'=>function($model){
				return $model->DCRP_DETIL;
			}						
		];
	}
}
