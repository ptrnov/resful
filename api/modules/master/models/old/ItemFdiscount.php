<?php

namespace api\modules\master\models;
use Yii;

/**
 * This is the model class for table "item_formula_discount".
 *
 * @property string $ID
 * @property string $CREATE_BY USER CREATED
 * @property string $CREATE_AT Tanggal dibuat
 * @property string $UPDATE_BY USER UPDATE
 * @property string $UPDATE_AT Tanggal di update
 * @property int $STATUS 0 = NO DISCOUNT 1= FIX DISCOUNT - EVENT (PERIODE TGL1&2). 2= FIX DISCOUNT - HARI & TIME. 3= CONTITION DISCOUNT.(Tawar menawar).      Condition MAX_DISCOUNT.  
 * @property string $ITEM_ID
 * @property string $OUTLET_CODE
 * @property int $HARI DISCOUNT BY HARI & PERIODE_TIME1&2
 * @property string $PERIODE_TGL1
 * @property string $PERIODE_TGL2
 * @property string $PERIODE_TIME1
 * @property string $PERIODE_TIME2
 * @property string $DISCOUNT_PERCENT
 * @property string $DCRIPT
 */
class ItemFdiscount extends \yii\db\ActiveRecord
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
        return 'item_formula_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ACCESS_UNIX','CREATE_AT', 'UPDATE_AT', 'PERIODE_TGL1', 'PERIODE_TGL2', 'PERIODE_TIME1', 'PERIODE_TIME2'], 'safe'],
            [['STATUS', 'HARI'], 'integer'],
            [['DISCOUNT_PERCENT','MAX_DISCOUNT'], 'number'],
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
            'STATUS' => Yii::t('app', 'Status'), //1Discount FIX;1=DISCOUNT CONDITIONAL
            'ACCESS_UNIX' => Yii::t('app', 'Access Unix'),
            'ITEM_ID' => Yii::t('app', 'Item  ID'),
            'OUTLET_CODE' => Yii::t('app', 'Outlet  Code'),
            'HARI' => Yii::t('app', 'Hari'),
            'PERIODE_TGL1' => Yii::t('app', 'Periode  Tgl1'),
            'PERIODE_TGL2' => Yii::t('app', 'Periode  Tgl2'),
            'PERIODE_TIME1' => Yii::t('app', 'Periode  Time1'),
            'PERIODE_TIME2' => Yii::t('app', 'Periode  Time2'),
            'DISCOUNT_PERCENT' => Yii::t('app', 'Discount  Percent'),
            'MAX_DISCOUNT' => Yii::t('app', 'Discount  Percent'),
            'DCRIPT' => Yii::t('app', 'Dcript'),
        ];
    }
	
	public function fields()
	{
		return [			
			'STATUS'=>function($model){
				return $model->STATUS;
			},
			'PERIODE_TGL1'=>function($model){
				return $model->PERIODE_TGL1;
			},
			'PERIODE_TGL2'=>function($model){
				return $model->PERIODE_TGL2;
			},					
			'PERIODE_TIME1'=>function($model){
				return $model->PERIODE_TIME1;
			},	
			'PERIODE_TIME2'=>function($model){
				return $model->PERIODE_TIME2;
			},	
			'DISCOUNT_PERCENT'=>function($model){
				return $model->DISCOUNT_PERCENT;
			},
			'MAX_DISCOUNT'=>function($model){
				return $model->MAX_DISCOUNT;
			},				
			'DCRIPT'=>function($model){
				return $model->DCRIPT;
			}			
		];
	}
}
