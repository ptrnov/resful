<?php

namespace api\modules\master\models;

use Yii;

/**
 * This is the model class for table "c0001g1".
 *
 * @property integer $PROVINCE_ID
 * @property string $PROVINCE
 */
class Provinsi extends \yii\db\ActiveRecord
{
	public static function getDb()
    {
       return Yii::$app->get('production_api');
    }
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'locate_province';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PROVINCE'], 'required'],
            [['PROVINCE_ID'], 'integer'],
            [['PROVINCE'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'PROVINCE_ID' => 'Province  ID',
            'PROVINCE' => 'Province',
        ];
    }
}
