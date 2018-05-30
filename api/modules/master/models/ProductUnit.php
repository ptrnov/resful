<?php

namespace api\modules\master\models;

use Yii;
use api\modules\master\models\ProductUnitGroup;
/**
 * This is the model class for table "product_unit".
 *
 * @property string $UNIT_ID
 * @property string $UNIT_NM
 * @property integer $UNIT_ID_GRP
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class ProductUnit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_unit';
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
            //[['UNIT_ID'], 'required'],
            [['UNIT_ID_GRP', 'STATUS'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['CREATE_AT', 'UPDATE_AT','CREATE_UUID','UPDATE_UUID'], 'safe'],
            [['UNIT_ID'], 'string', 'max' => 6],
            [['UNIT_NM', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UNIT_ID' => 'Unit  ID',
            'UNIT_NM' => 'Unit  Nm',
            'UNIT_ID_GRP' => 'Unit  Id  Grp',
            'STATUS' => 'STATUS',
            'DCRP_DETIL' => 'DISCRIPTIONS',
            'CREATE_BY' => 'CREATE.BY',
            'CREATE_AT' => 'CREATE.AT',
            'UPDATE_BY' => 'UPDATE.BY',
            'UPDATE_AT' => 'UPDATE.AT',
            'CREATE_UUID' => 'CREATE.UUID',
            'UPDATE_UUID' => 'UPDATE.UUID',
        ];
    }
	
	public function fields()
	{
		return [			
			'UNIT_ID'=>function($model){
				return $model->UNIT_ID;
			},
			'UNIT_NM'=>function($model){
				if($model->UNIT_NM){
					return $model->UNIT_NM;
				}else{
					return 'none';
				}
			},
			'UNIT_ID_GRP'=>function($model){
				return $model->UNIT_ID_GRP;
			},
			'UNIT_NM_GRP'=>function($model){
				return $this->unitGroupTbl->UNIT_NM_GRP;
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			},					
			'DCRP_DETIL'=>function($model){
				if($model->DCRP_DETIL){
					return $model->DCRP_DETIL;
				}else{
					return 'none';
				}
			},
			'CREATE_UUID'=>function($model){
				return $model->CREATE_UUID;
			},	
			'UPDATE_UUID'=>function($model){
				return $model->UPDATE_UUID;
			},	
		];
	}
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table Product Unit (one to one)  Product Unit Group.
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getUnitGroupTbl(){
		return $this->hasOne(ProductUnitGroup::className(), ['UNIT_ID_GRP' => 'UNIT_ID_GRP']);
	}
}
