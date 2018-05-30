<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "product_unit_group".
 *
 * @property integer $UNIT_ID_GRP
 * @property string $UNIT_NM_GRP
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class ProductUnitGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_unit_group';
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
            [['UNIT_NM_GRP', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UNIT_ID_GRP' => 'Unit  Id  Grp',
            'UNIT_NM_GRP' => 'Unit  Nm  Grp',
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
			'UNIT_ID_GRP'=>function($model){
				return $model->UNIT_ID_GRP;
			},
			'UNIT_NM_GRP'=>function($model){
				if($model->UNIT_NM_GRP){
					return $model->UNIT_NM_GRP;
				}else{
					return 'none';
				}
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
			}
		];
	}
}
