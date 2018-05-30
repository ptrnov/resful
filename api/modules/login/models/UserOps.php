<?php

namespace api\modules\login\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use api\modules\master\models\Store;
use api\modules\login\models\UserProfile;

class UserOps extends \yii\db\ActiveRecord
//class Userlogintest extends ActiveRecord implements IdentityInterface
{
	
	const SCENARIO_USER = 'createuser';
	public static function getDb()
	{
		/* Author -ptr.nov- : HRD | Dashboard I */
		return \Yii::$app->production_api;
	}
	public $new_pass;
    public $tmp='170726220936';
	public static function tableName()
    {
        return '{{user}}';
    }

    public function rules()
    {
        return [
			//[['username', 'ACCESS_ID','ACCESS_GROUP'], 'required'],
            [['auth_key'], 'string'],
            [['status', 'ACCESS_SITE', 'ONLINE', 'lft', 'rgt', 'lvl', 'icon_type', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['create_at', 'updated_at'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'icon'], 'string', 'max' => 255],
            [['ACCESS_ID', 'ACCESS_GROUP'], 'string', 'max' => 15],
            [['ACCESS_LEVEL'], 'string', 'max' => 50],
            [['ID_GOOGLE','ID_FB','ID_TWITTER','ID_LINKEDIN','ID_YAHOO'], 'string', 'max' => 255],
		];
    }

	public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'User Name'),
			'password_hash' => Yii::t('app', 'Password Hash'),
			'ACCESS_ID' => Yii::t('app', 'ACCESS_ID'),
			'ACCESS_LEVEL' => Yii::t('app', 'ACCESS_LEVEL')			
        ];
    }
	
	public function fields()
	{
		$rslt= [	
			'ACCESS_ID'=>function($model){
				return $model->ACCESS_ID;
			},
			'username'=>function($model){
				return $model->username;
			},
			'email'=>function($model){
				return $model->email;
			},
			'status'=>function($model){
				$rslt=$model->status;
				if($rslt==10){
					return 'Active';
				}else{
					return 'Deactive';
				};				
			},
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'ACCESS_LEVEL'=>function($model){
				return $model->ACCESS_LEVEL;
			},
			'PROFILE'=>function(){
				return $this->profileTbl;
			},
		]; 
		return $rslt;
	} 	
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Validasi Password.
	*/
	public function validateLoginPassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Set New password.
	*/
	public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
		
	/*
	 * Author by : ptr.nov@gmail.com
	 * Create new code for Reset Password.
	*/	
	public function setCodeReset($password)
    {
        $this->password_reset_token = Yii::$app->security->generatePasswordHash($password);
    }
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Validate Reset Password.
	*/
	public function validateCodeReset($password)
    {
		  // if($password==''){
			    // return false;
		  // }else{
			   return Yii::$app->security->validatePassword($password, $this->password_reset_token);
		  // }       
    }
	
	/*
	 * Author by : ptr.nov@gmail.com
	 * Join Table user (one to one) Table Profile.
	 * Ingat: jangan di join di model Search lagi.
	*/
	public function getProfileTbl(){
		 return $this->hasOne(UserProfile::className(), ['ACCESS_ID' => 'ACCESS_ID']);
		//return  $rslt;
	}	
}
?>
