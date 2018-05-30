<?php

namespace api\modules\hirs\models;

use Yii;
use api\modules\hirs\models\EmployeDataImage;

class EmployeData extends \yii\db\ActiveRecord
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
        return 'hrd_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['STATUS'], 'integer'],
            [['EMP_ALAMAT'], 'string'],
            [['CREATE_BY', 'UPDATE_BY', 'ACCESS_UNIX', 'OUTLET_CODE', 'EMP_ID', 'EMP_NM_DPN', 'EMP_NM_TGH', 'EMP_NM_BLK'], 'string', 'max' => 50],
            [['EMP_KTP', 'EMP_TLP', 'EMP_HP'], 'string', 'max' => 100],
            [['EMP_GENDER'], 'string', 'max' => 10],
            [['EMP_STS_NIKAH'], 'string', 'max' => 255],
            [['EMP_EMAIL'], 'string', 'max' => 150],
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
            'EMP_ID' => Yii::t('app', 'Emp  ID'),
            'EMP_NM_DPN' => Yii::t('app', 'Emp  Nm  Dpn'),
            'EMP_NM_TGH' => Yii::t('app', 'Emp  Nm  Tgh'),
            'EMP_NM_BLK' => Yii::t('app', 'Emp  Nm  Blk'),
            'EMP_KTP' => Yii::t('app', 'Emp  Ktp'),
            'EMP_ALAMAT' => Yii::t('app', 'Emp  Alamat'),
            'EMP_GENDER' => Yii::t('app', 'Emp  Gender'),
            'EMP_STS_NIKAH' => Yii::t('app', 'Emp  Sts  Nikah'),
            'EMP_TLP' => Yii::t('app', 'Emp  Tlp'),
            'EMP_HP' => Yii::t('app', 'Emp  Hp'),
            'EMP_EMAIL' => Yii::t('app', 'Emp  Email')
        ];
    }
	public function fields()
	{
		return [			
			'ACCESS_UNIX'=>function($model){
				return $model->ACCESS_UNIX;
			},
			'OUTLET_CODE'=>function($model){
				return $model->OUTLET_CODE;
			},
			'EMP_ID'=>function($model){
				return $model->EMP_ID;
			},					
			'NAME'=>function($model){
				return $model->EMP_NM_DPN .' '.$model->EMP_NM_TGH.' '.$model->EMP_NM_BLK;
			},	
			'EMP_KTP'=>function($model){
				return $model->EMP_KTP;
			},				
			'EMP_ALAMAT'=>function($model){
				return $model->EMP_ALAMAT;
			},	
			'EMP_GENDER'=>function($model){
				return $this->EMP_GENDER;
			},
			'EMP_STS_NIKAH'=>function($model){
				return $this->EMP_STS_NIKAH;
			},
			'EMP_TLP'=>function($model){
				return $this->EMP_TLP;
			},
			'EMP_HP'=>function($model){
				return $this->EMP_HP;
			},
			'EMP_EMAIL'=>function($model){
				return $this->EMP_EMAIL;
			}								
		];
	}
	
	//Join TABLE ITEM
	public function getImage(){
		return $this->hasOne(EmployeDataImage::className(), ['OUTLET_CODE' => 'OUTLET_CODE'])->andWhere('FIND_IN_SET( ACCESS_UNIX,"'.$this->ACCESS_UNIX.'")');
	}
	
	public function extraFields()
	{
		return ['image'];
	}
}
