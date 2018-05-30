<?php

namespace api\modules\login\controllers;

use yii;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use api\modules\login\models\UserLogin;
use api\modules\login\models\User;
use zyx\phpmailer\Mailer;
/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: ADD USER OWNER (SIGNUP AND PASS LOGIN ). On App "Profile Show for Update"
  * Metode		: POST (CREATE) 
  * URL			: http://production.kontrolgampang.com/login/user-signup-owners
  * Body Param	: [manual] email & username & password
  *				  [sosmed] email & username(email login) & SOSMED_PROVIDER,SOSMED_ID
  * Keterangan  : SOSMED_PROVIDER[GOOGLE,FACEBOOK,TWITTER,LINKEDIN,YAHOO];
  *				  Untuk SOSMED [input email=username];
  *				  email "tidak boleh kosong, wajib di isi". FURURE (validasi check email).
  * Alert		: {"result": "Email-Cant-Empty"} 		=> Email Tidak boleh kosong.
  *				  {"result": "Password-Cant-Empty"} 	=> password tidak boleh kosong.
  *				  {"result": "Email-Already-Exist"} 	=> Email sudah terdaftar, "go to lupa password". 
  *				  {"result": "Username-Already-Exist"}	=> Username sudah terdaftar, "go to lupa password". 
  *				  {"result": "Sosmed-Provider-Already-Exist"} => Auth SOSMED  sudah terdaftar, "go to lupa password". 			
 */
class UserSignupOwnerController extends ActiveController
{
    public $modelClass = 'api\modules\login\models\UserLogin';
	
	/**
     * Behaviors
	 * Mengunakan Auth HttpBasicAuth.
	 * Chacking logintest.
     */
    public function behaviors()    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => 
            [
                'class' => CompositeAuth::className(),
				'authMethods' => 
                [
                    #Hapus Tanda Komentar Untuk Autentifikasi Dengan Token               
                   // ['class' => HttpBearerAuth::className()],
                   // ['class' => QueryParamAuth::className(), 'tokenParam' => 'access-token'],
                ],
                'except' => ['options']
            ],
			'bootstrap'=> 
            [
				'class' => ContentNegotiator::className(),
				'formats' => 
                [
					'application/json' => Response::FORMAT_JSON,
				],
			],
			'corsFilter' => [
				'class' => \yii\filters\Cors::className(),
				'cors' => [
					// restrict access to
					//'Origin' => ['http://lukisongroup.com', 'http://lukisongroup.int','http://localhost','http://103.19.111.1','http://202.53.354.82'],
					'Origin' => ['*'],
					'Access-Control-Request-Method' => ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
					//'Access-Control-Request-Headers' => ['*'],
					'Access-Control-Request-Headers' => ['*'],
					// Allow only headers 'X-Wsse'
					'Access-Control-Allow-Credentials' => false,
					// Allow OPTIONS caching
					'Access-Control-Max-Age' => 3600,

					]		 
			],
			/* 'corsFilter' => [
				'class' => \yii\filters\Cors::className(),
				'cors' => [
					'Origin' => ['*'],
					'Access-Control-Allow-Headers' => ['X-Requested-With','Content-Type'],
					'Access-Control-Request-Method' => ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
					//'Access-Control-Request-Headers' => ['*'],					
					'Access-Control-Allow-Credentials' => true,
					'Access-Control-Max-Age' => 3600,
					'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page']
					]		 
			], */
        ]);		
    }
	
	public function actions()
	{
		$actions = parent::actions();
		unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
		return $actions;
	} 

	public function actionCreate()
    {
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: ADD USER OWNER (SIGNUP AND PASS LOGIN ). On App "Profile Show for Update"
		  * Metode		: POST (CREATE) 
		  * URL			: http://production.kontrolgampang.com/login/user-signup-owners
		  * Body Param	: [manual] email & username & password -> [default status=0, code activation=true then status=10]
		  *				  [sosmed] email & username(email login) & SOSMED_PROVIDER,SOSMED_ID
		  * Keterangan  : SOSMED_PROVIDER[GOOGLE,FACEBOOK,TWITTER,LINKEDIN,YAHOO];
		  *				  Untuk SOSMED [input email=username];
		  *				  email "tidak boleh kosong, wajib di isi". FURURE (validasi check email).
		  * Alert		: {"result": "Email-Cant-Empty"} 		=> Email Tidak boleh kosong.
		  *				  {"result": "Password-Cant-Empty"} 	=> password tidak boleh kosong.
		  *				  {"result": "Email-Already-Exist"} 	=> Email sudah terdaftar, "go to lupa password". 
		  *				  {"result": "Username-Already-Exist"}	=> Username sudah terdaftar, "go to lupa password". 
		  *				  {"result": "Sosmed-Provider-Already-Exist"} => Auth SOSMED  sudah terdaftar, "go to lupa password". 			
		 */
		$paramsBody 			= Yii::$app->request->bodyParams;		
		
		//ALL REQUIRED
		$email					= isset($paramsBody['email'])!=''?$paramsBody['email']:'';
		
		//MANUAL LOGIN
		$username				= isset($paramsBody['username'])!=''?$paramsBody['username']:'';
		$password_hash			= isset($paramsBody['password'])!=''?($paramsBody['password']!=''?$paramsBody['password']:'~'):'~';
			
		//SOSMED LOGIN
		$sosProvider			= isset($paramsBody['SOSMED_PROVIDER'])!=''?$paramsBody['SOSMED_PROVIDER']:'';
		$sosId					= isset($paramsBody['SOSMED_ID'])!=''?$paramsBody['SOSMED_ID']:'';
		$paramlUUID				= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		
		
		//FILTER DINAMIC FIELD AND VALUE
		if($sosProvider==''){
			//MANUAL LOGIN 
			$field		= 'username';
			$value		= $username;
		}else{
			$field	=$sosProvider=='GOOGLE'?'ID_GOOGLE':($sosProvider=='FACEBOOK'?'ID_FB':($sosProvider=='TWITTER'?'ID_TWITTER':($sosProvider=='LINKEDIN'?'ID_LINKEDIN':($sosProvider=='YAHOO'?'ID_YAHOO':'username'))));
			$value	=$sosId;
		};
		
		//MODEL CHECK
		$modelCntEmail= UserLogin::find()->where(['email'=>$email])->count();	//EMAIL check
		
		//DINAMIC FIELD MODEL.
		$modelCnt= UserLogin::find()->where([$field=>$value])->count();			//username check	
			
		
		//RESULT API
		if($modelCntEmail){
			return array('result'=>'Email-Already-Exist');	
		}else{
			if($email==''){
				return array('result'=>'Email-Cant-Empty');
			}else{				
				if($sosProvider==''){
					//MANUAL LOGIN 
					if($modelCnt){
						return array('result'=>'Username-Already-Exist');	
					}else{
						if($password_hash=='~'){
							return array('result'=>'Password-Cant-Empty');
						}else{
							//Reset Code.
							$datetomecode=str_replace(':','',date('m:d H:i'));
							$codeReset = str_replace(' ','',$datetomecode);
							//return array('result'=>'Saved MANUAL');
							$modelManual= new UserLogin();
							$modelManual->username=$username;
							$modelManual->email=$email;
							$modelManual->UUID=$paramlUUID;
							$modelManual->ACCESS_LEVEL='OWNER';
							$modelManual->create_at=date('Y-m-d H:i:s');
							$modelManual->password_hash = Yii::$app->security->generatePasswordHash($password_hash);
							$modelManual->auth_key = Yii::$app->security->generateRandomString();	
							$modelManual->status=0;							
							$modelManual->setCodeReset($codeReset);							
							if ($modelManual->save()){
								//return array('result'=>$modelManual->attributes);
								$modelView= User::find()->where(['username'=>$username])->one();
									/*
									 * SEND Email		: NOTIFY CODE to Email 
									 * Activation URL	: "http://production.kontrolgampang.com/login/user-logins".
									 * Param			: ACTIVE_CODE
									*/
									$contentBody= $this->renderPartial('_postmanBodyManual',[
										'modelView'=>$modelView,
										'codeReset'=>$codeReset
									]);	
									Yii::$app->mailer->compose()
									->setFrom(['postman@lukison.com' => 'POSTMAN - KONTROL GAMPANG'])
									->setTo([$email])
									//->setTo(['ptr.nov@gmail.com','piter@lukison.com'])
									->setSubject('SELAMAT DATANG, Activation Code is '.$codeReset)
									->setHtmlBody($contentBody)
									//->attach($filenameAll,[$filename,'xlsx'])
									->send(); 								
								return array('USER'=>$modelView);								
							}else{
								return array('result'=>$modelManual->errors);
							} 						
						}
					};		
				}else{
					//SOSMED LOGIN
					if($modelCnt){
						return array('result'=>'Sosmed-Provider-Already-Exist');
					}else{
						//return array('result'=>'Saved Sosmed');
						$modelSosmad= New UserLogin();
						$modelSosmad->username=$email;
						$modelSosmad->email=$email;
						$modelSosmad->UUID=$paramlUUID;
						$modelSosmad->ACCESS_LEVEL='OWNER';
						$modelSosmad->create_at=date('Y-m-d H:i:s');
						$modelSosmad->auth_key = Yii::$app->security->generateRandomString();
						if($sosProvider=='GOOGLE'){
							$modelSosmad->ID_GOOGLE=$value;
						}
						if($sosProvider=='FACEBOOK'){
							$modelSosmad->ID_FB=$value;
						}
						if($sosProvider=='GOOGLE'){
							$modelSosmad->ID_GOOGLE=$value;
						}
						if($sosProvider=='TWITTER'){
							$modelSosmad->ID_TWITTER=$value;
						}
						if($sosProvider=='LINKEDIN'){
							$modelSosmad->ID_LINKEDIN=$value;
						}
						if($sosProvider=='YAHOO'){
							$modelSosmad->ID_YAHOO=$value;
						}
						if ($modelSosmad->save()){
							//return array('result'=>$modelSosmad->attributes);
							$modelView= UserLogin::find()->where([$field=>$value])->one();
								//SEND EMAIL WLECOME
								$contentBody= $this->renderPartial('_postmanBodyAuth',[
									'modelView'=>$modelView
								]);	
								Yii::$app->mailer->compose()
								->setFrom(['postman@lukison.com' => 'POSTMAN - KONTROL GAMPANG'])
								->setTo([$email])
								//->setTo(['ptr.nov@gmail.com','piter@lukison.com'])
								->setSubject('SELAMAT DATANG')
								->setHtmlBody($contentBody)
								//->attach($filenameAll,[$filename,'xlsx'])
								->send(); 
								return array('USER'=>$modelView);							
						}else{
							return array('result'=>$modelSosmad->errors);
						} 					
					};	
				};
			}
		}		
	}
}


