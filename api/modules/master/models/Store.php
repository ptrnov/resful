<?php

namespace api\modules\master\models;

use Yii;
use api\modules\master\models\StoreMerchant;
use api\modules\hirs\models\Karyawan;
use api\modules\login\models\User;
/**
 * This is the model class for table "store".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property string $STORE_NM
 * @property string $ACCESS_ID
 * @property string $UUID
 * @property resource $PLAYER_ID
 * @property string $DATE_START
 * @property string $DATE_END
 * @property integer $PROVINCE_ID
 * @property string $PROVINCE_NM
 * @property integer $CITY_ID
 * @property string $CITY_NAME
 * @property string $ALAMAT
 * @property string $PIC
 * @property string $TLP
 * @property string $FAX
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
 

 
class Store extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store';
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
            //[['STORE_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['ACCESS_ID', 'UUID', 'PLAYER_ID', 'ALAMAT', 'DCRP_DETIL'], 'string'],
            [['INDUSTRY_ID','INDUSTRY_NM','INDUSTRY_GRP_ID','INDUSTRY_GRP_NM'], 'safe'],
            [['DATE_START', 'DATE_END', 'CREATE_AT', 'UPDATE_AT','PPN'], 'safe'],
            [['PROVINCE_ID', 'CITY_ID', 'STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 25],
            [['STORE_NM', 'PIC'], 'string', 'max' => 100],
            [['PROVINCE_NM', 'CITY_NAME', 'TLP', 'FAX', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
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
            'STORE_NM' => 'Store  Nm',
            'ACCESS_ID' => 'Access  ID',
            'UUID' => 'Uuid',
            'PLAYER_ID' => 'Player  ID',
            'DATE_START' => 'Date  Start',
            'DATE_END' => 'Date  End',
            'PROVINCE_ID' => 'Province  ID',
            'PROVINCE_NM' => 'Province  Nm',
            'CITY_ID' => 'City  ID',
            'CITY_NAME' => 'City  Name',
            'ALAMAT' => 'Alamat',
            'PIC' => 'Pic',
            'TLP' => 'Tlp',
            'FAX' => 'Fax',
            'PPN' => 'Pajak',
            'INDUSTRY_ID' => 'INDUSTRY_ID',
            'INDUSTRY_NM' => 'INDUSTRY_NM',
            'INDUSTRY_GRP_ID' => 'INDUSTRY_GRP_ID',
            'INDUSTRY_GRP_NM' => 'INDUSTRY_GRP_NM',
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
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},	
			'STORE_ID'=>function($model){
					return $model->STORE_ID;
				},	
			'STORE_NM'=>function($model){
					return $model->STORE_NM;
				},	
			'ACCESS_ID'=>function($model){
					return $model->ACCESS_ID;
				},	
			'UUID'=>function($model){
					return $model->UUID;
				},	
			'PLAYER_ID'=>function($model){
					return $model->PLAYER_ID;
				},	
			'PROVINCE_ID'=>function($model){
					return $model->PROVINCE_ID;
				},	
			'PROVINCE_NM'=>function($model){
					return $model->PROVINCE_NM;
				},	
			'CITY_ID'=>function($model){
					return $model->CITY_ID;
				},	
			'CITY_NAME'=>function($model){
					return $model->CITY_NAME;
				},	
			'ALAMAT'=>function($model){
					return $model->ALAMAT;
				},	
			'LATITUDE'=>function($model){
					return $model->LATITUDE;
				},	
			'LONGITUDE'=>function($model){
					return $model->LONGITUDE;
				},	
			'PIC'=>function($model){
					return $model->PIC;
				},	
			'TLP'=>function($model){
					return $model->TLP;
				},	
			'FAX'=>function($model){
					return $model->FAX;
				},	
			'PPN'=>function($model){
					return $model->PPN;
				},	
			'INDUSTRY_ID'=>function($model){
					return $model->INDUSTRY_ID;
				},	
			'INDUSTRY_NM'=>function($model){
					return $model->INDUSTRY_NM;
				},	
			'INDUSTRY_GRP_ID'=>function($model){
					return $model->INDUSTRY_GRP_ID;
				},	
			'INDUSTRY_GRP_NM'=>function($model){
					return $model->INDUSTRY_GRP_NM;
				},	
			'STATUS'=>function($model){
					$rslt=$model->STATUS;
					if($rslt==0){
						return 'Trial'; //trial 30 hari, store pertama
					}elseif($rslt==1){
						return 'Active';
					}elseif($rslt==2){
						return 'Deactive';
					}elseif($rslt==3){
						return 'Deleted';
					}elseif($rslt==4){
						return 'Tenggang'; //15 Hari sebelum berakhir
					}else{
						return 'UNKOWN';
					};					
				},
			'START'=>function($model){
					return $model->DATE_START;
				},
            'END'=>function($model){
					return $model->DATE_END;
				},				
			'DCRP_DETIL'=>function($model){
					return $model->DCRP_DETIL;
				},	
			'LIST_MERCHANTS'=>function(){
					return  $this->merchanTbl;
				},	
			'LIST_KARYAWANS'=>function(){
					return  $this->karyawanTbl;
				},
			'LIST_USERS'=>function(){
					return  $this->userOpsTbl;
				}	
		];
	}
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table user (one to one) Table Store Merchant.
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getMerchanTbl(){
		 //return $this->hasMany(StoreMerchant::className(), ['ACCESS_GROUP' => 'ACCESS_GROUP'])->Where('STORE_ID=STORE_ID');
		return $this->hasMany(StoreMerchant::className(), ['STORE_ID' => 'STORE_ID']);
		//return  $rslt;
	}
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table user (one to one) Table Store Merchant.
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getKaryawanTbl(){
		 //return $this->hasMany(StoreMerchant::className(), ['ACCESS_GROUP' => 'ACCESS_GROUP'])->Where('STORE_ID=STORE_ID');
		return $this->hasMany(Karyawan::className(), ['STORE_ID' => 'STORE_ID']);
		//return  $rslt;
	}
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table user (one to many).
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getUserOpsTbl(){
		$modalUserOps= User::find()->where("FIND_IN_SET(ACCESS_ID,'".$this->ACCESS_ID."') AND ACCESS_LEVEL!='OWNER'")->all();
		return  $modalUserOps;
	}
}
