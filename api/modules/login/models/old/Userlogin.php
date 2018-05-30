<?php

namespace api\modules\login\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

use common\models\User;
use api\modules\login\models\UserImage;
use api\modules\login\models\UserProfil;
use api\modules\login\models\Corp;

class Userlogin extends \yii\db\ActiveRecord
//class Userlogin extends ActiveRecord implements IdentityInterface
{
	
	const SCENARIO_USER = 'createuser';
	
	public $new_pass;
    
	public static function getDb()
    {
        return Yii::$app->get('api_dbkg');
    }
	
	public static function tableName()
    {
        return '{{user}}';
    }

    public function rules()
    {
        return [
			[['username','auth_key','password_hash','POSITION_ACCESS'], 'required','on' => self::SCENARIO_USER],
			[['new_pass','username','status'], 'required','on' =>'updateuser'],
			[['username','auth_key','password_hash','password_reset_token'], 'string'],
			[['email'], 'string'],
			[['id','status','create_at','update_at'],'safe'],
			[['ACCESS_UNIX','ACCESS_GROUP','ACCESS_LEVEL','ACCESS_SITE','ONLINE','UUID'], 'safe'],
		];
    }

	public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'User.ID'),
            'username' => Yii::t('app', 'User Name'),
			'auth_key' => Yii::t('app', 'Access Token'),
			'password_hash' => Yii::t('app', 'Password Hash'),
			'password_reset_token' => Yii::t('app', 'Reset Password'),
			'email' => Yii::t('app', 'Email'),
			'create_at' => Yii::t('app', 'create_at'),
			'update_at' => Yii::t('app', 'update_at'),
			'ACCESS_UNIX' => Yii::t('app', 'ACCESS_UNIX'),
			'ACCESS_GROUP' => Yii::t('app', 'ACCESS_GROUP'),
			'ACCESS_LEVEL' => Yii::t('app', 'ACCESS_LEVEL'),
			'ACCESS_SITE' => Yii::t('app', 'ACCESS_SITE'),
			'ONLINE' => Yii::t('app', 'ONLINE'),
			'UUID' => Yii::t('app', 'UUID')			
        ];
    }
	
	public function getUserImageTbl()
	{
		return $this->hasOne(UserImage::className(), ['ACCESS_UNIX' => 'ACCESS_UNIX']);
	}	
		
	public function getUserProfilTbl()
	{
		return $this->hasOne(UserProfil::className(), ['ACCESS_UNIX' => 'ACCESS_UNIX']);
	}
	
	public function getCorpTbl()
	{
		return Corp::find()->Where('FIND_IN_SET("'.$this->ACCESS_UNIX.'", ACCESS_UNIX)');
	}
	
	
	public function fields()
	{
		return [
			'id'=>function($model){
				return $model->id;
			},
			'username'=>function($model){
				return $model->username;
			},
			'auth_key'=>function($model){
				return $model->auth_key;
			},		
			'password_hash'=>function($model){
				return $model->password_hash;
			},		
			'password_reset_token'=>function($model){
				return $model->password_reset_token;
			},		
			'email'=>function($model){
				return $model->email;
			},		
			'ACCESS_UNIX'=>function($model){
				return $model->ACCESS_UNIX;
			},	
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'ACCESS_SITE'=>function($model){
				return $model->ACCESS_SITE;
			},
			'ONLINE'=>function($model){
				return $model->ONLINE;
			},
			'UUID'=>function($model){
				return $model->UUID;
			},					
			'PROFILE_NM'=>function(){
				return $this->userProfilTbl!=''?$this->userProfilTbl->NM_DEPAN:$this->username;
			},
			'CORP_NM'=>function(){
				//return $this->corpTbl!=''?$this->corpTbl->CORP_NM:'Nama Perusahaan';
				return $this->corpTbl!=''?$this->corpTbl->CORP_NM:'Nama Perusahaan';
			},	
			// 'IMG64'=>function(){
				// return $this->userImageTbl!=''?$this->userImageTbl->IMG_64:$this->noimage;
			// },			
			
			// 'CORP_IMG64'=>function(){
				//return $this->corpTbl!=''?$this->corpTbl->CORP_NM:'Nama Perusahaan';
				// return $this->corpTbl!=''?$this->corpTbl->CORP_IMG64:$this->noimage;
			// }	 	
		];
	} 	
  
	
	/**
     * Generates password hash from password signature
     *
     * @param string $SIGPASSWORD
	 * @author ptrnov  <piter@lukison.com>
	 * @since 1.1
     */
    public function setPassword_login($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

	/**
     * return Password Signature
     *
     * @param string $SIGPASSWORD
	 * @author ptrnov  <piter@lukison.com>
	 * @since 1.1
     */
	public function validateOldPassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);

    }

	 /**
     * @inheritdoc
     */
   /*  public static function findIdentityByAccessToken($token, $type = null)
    {
        //throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
		 return static::findOne(['auth_key' => $token]);
    } */
	public function getNoimage(){
		$gambarkosong="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";
		return $gambarkosong;			
	}
}
?>
