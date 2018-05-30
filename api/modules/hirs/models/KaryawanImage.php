<?php

namespace api\modules\hirs\models;

use Yii;

/**
 * This is the model class for table "karyawan_image".
 *
 * @property string $ID
 * @property string $KARYAWAN_ID
 * @property string $KARYAWAN_IMAGE
 * @property string $KARYAWAN_FINGER
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class KaryawanImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'karyawan_image';
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
            [['KARYAWAN_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['KARYAWAN_IMAGE', 'KARYAWAN_FINGER', 'DCRP_DETIL'], 'string'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['KARYAWAN_ID'], 'string', 'max' => 30],
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
            'KARYAWAN_ID' => 'Karyawan  ID',
            'KARYAWAN_IMAGE' => 'Karyawan  Image',
            'KARYAWAN_FINGER' => 'Karyawan  Finger',
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
			'KARYAWAN_ID'=>function($model){
				return $model->KARYAWAN_ID;
			},
			'KARYAWAN_IMAGE'=>function($model){
				return $model->KARYAWAN_IMAGE;
			},
			'KARYAWAN_FINGER'=>function($model){
				return $model->KARYAWAN_FINGER;
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
