<?php

namespace api\modules\login\models;


use Yii;

/**
 * This is the model class for table "corp_64".
 *
 * @property string $ID
 * @property string $CORP_NM
 * @property string $CORP_64
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class CorpImage extends \yii\db\ActiveRecord
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
        return 'corp_64';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CORP_64'], 'string'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['CORP_NM'], 'string', 'max' => 255],
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
            'CORP_NM' => 'Corp  Nm',
            'CORP_64' => 'Corp 64',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
        ];
    }
}
