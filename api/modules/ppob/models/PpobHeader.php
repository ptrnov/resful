<?php

namespace api\modules\ppob\models;


use Yii;

/**
 * This is the model class for table "ppob_header".
 *
 * @property string $ID
 * @property string $HEADER_ID
 * @property string $HEADER_NM
 * @property string $HEADER_DCRP
 * @property integer $STATUS
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class PpobHeader extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ppob_header';
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
            [['HEADER_DCRP'], 'string'],
            [['STATUS'], 'integer'],
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['HEADER_ID'], 'string', 'max' => 3],
            [['HEADER_NM'], 'string', 'max' => 100],
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
            'HEADER_ID' => 'Header  ID',
            'HEADER_NM' => 'Header  Nm',
            'HEADER_DCRP' => 'Header  Dcrp',
            'STATUS' => 'Status',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
        ];
    }
	
	public function fields()
	{
		return [			
			'HEADER_ID'=>function($model){
				return $model->HEADER_ID;
			},
			'HEADER_NM'=>function($model){
				return $model->HEADER_NM;
			},
			'HEADER_DCRP'=>function($model){
				return $model->HEADER_DCRP;
			},
			'STATUS'=>function($model){
				return $model->STATUS;
			}
		];
	}
}
