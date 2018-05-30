<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "penjualan_metode".
 *
 * @property string $ID RECEVED & RELEASE:
 ID UNIX, POSTING URL DAN AJAX
 * @property string $CREATE_BY CREATE_BY
 * @property string $CREATE_AT CREATE_AT
 * @property string $UPDATE_BY UPDATE_BY
 * @property string $UPDATE_AT UPDATE_AT
 * @property int $STATUS
 * @property string $ACCESS_UNIX
 * @property string $OUTLET_CODE
 * @property int $TYPE_PAY
 * @property string $BANK_NM
 * @property string $DCRIPT
 */
class PayMetode extends \yii\db\ActiveRecord
{
	 /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('api_dbkg');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_metode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['STATUS', 'TYPE_PAY'], 'integer'],
            [['DCRIPT'], 'string'],
            [['CREATE_BY', 'UPDATE_BY', 'ACCESS_UNIX', 'OUTLET_CODE'], 'string', 'max' => 50],
            [['BANK_NM'], 'string', 'max' => 255],
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
            'ACCESS_UNIX' => Yii::t('app', 'Access  Unix'),
            'OUTLET_CODE' => Yii::t('app', 'Outlet  Code'),
            'TYPE_PAY' => Yii::t('app', 'Type  Pay'),
            'BANK_NM' => Yii::t('app', 'Bank  Nm'),
            'DCRIPT' => Yii::t('app', 'Dcript'),
        ];
    }
	
	public function fields()
	{
		return [			
			'TYPE_PAY'=>function($model){
				return $model->TYPE_PAY;
			},
			'BANK_NM'=>function($model){
				return $model->BANK_NM;
			},					
			'DCRIPT'=>function($model){
				return $model->DCRIPT;
			}
		];
	}
}
