<?php
namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "product_harga".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property string $PRODUCT_ID
 * @property string $PERIODE_TGL1
 * @property string $PERIODE_TGL2
 * @property string $START_TIME
 * @property string $HARGA_JUAL
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class ProductHarga extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_harga';
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
            //[['PRODUCT_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['PRODUCT_ID', 'PERIODE_TGL1', 'PERIODE_TGL2','HARGA_JUAL'], 'required'],
            [['PERIODE_TGL1', 'PERIODE_TGL2', 'START_TIME', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['HARGA_JUAL','HPP','PPN'], 'safe'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['PRODUCT_ID'], 'string', 'max' => 35],
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
            'PRODUCT_ID' => 'Product  ID',
            'PERIODE_TGL1' => 'Periode  Tgl1',
            'PERIODE_TGL2' => 'Periode  Tgl2',
            'START_TIME' => 'Start  Time',
            'HARGA_JUAL' => 'Harga  Jual',
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
			'ID'=>function($model){
				return $model->ID;
			},
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'PRODUCT_ID'=>function($model){
				return $model->PRODUCT_ID;
			},
			'PERIODE_TGL1'=>function($model){
				return $model->PERIODE_TGL1;
			},
			'PERIODE_TGL2'=>function($model){
				return $model->PERIODE_TGL2;
			},
			'START_TIME'=>function($model){
				return $model->START_TIME;
				if($model->START_TIME){
					return $model->START_TIME;
				}else{
					return '00:00:00';
				}
			},
			'HPP'=>function($model){
				if($model->HPP){
					return $model->HPP;
				}else{
					return 0;
				}
			},
			'PPN'=>function($model){
				if($model->PPN){
					return $model->PPN;
				}else{
					return 0;
				}
			},
			'HARGA_JUAL'=>function($model){
				if($model->HARGA_JUAL){
					return $model->HARGA_JUAL;
				}else{
					return 0;
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
