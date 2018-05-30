<?php

namespace api\modules\login\models;


use Yii;

/**
 * This is the model class for table "c0001g2".
 *
 * @property integer $CITY_ID
 * @property string $PROVINCE_ID
 * @property string $PROVINCE
 * @property string $TYPE
 * @property string $CITY_NAME
 * @property integer $POSTAL_CODE
 */
class LocateKota extends \yii\db\ActiveRecord
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
        return 'locate_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PROVINCE_ID'], 'required'],
            [['CITY_ID', 'POSTAL_CODE'], 'integer'],
            [['PROVINCE_ID', 'PROVINCE', 'TYPE', 'CITY_NAME'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CITY_ID' => 'City  ID',
            'PROVINCE_ID' => ' Nama Province ',
            'PROVINCE' => 'Province',
            'TYPE' => 'Type',
            'CITY_NAME' => 'City  Name',
            'POSTAL_CODE' => 'Kode Pos',
        ];
    }
}
