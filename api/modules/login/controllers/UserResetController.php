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
use api\modules\login\models\User;
use api\modules\login\models\UserLogin;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: RESET PASSWORD LOGIN MANUAL.
  * Metode		: POST (RESET PASSWORD)
  * URL			: http://production.kontrolgampang.com/login/user-resets
  * Body Param	: [manual] email
 */
class UserResetController extends ActiveController
{
    public $modelClass = 'api\modules\login\models\User';
	
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
		  * Subject		: RESET PASSWORD LOGIN MANUAL.
		  * Metode		: POST (RESET PASSWORD)
		  * URL			: http://production.kontrolgampang.com/login/user-resets
		  * Body Param	: [manual] email
		  * Keterangan  : SOSMED_PROVIDER[GOOGLE,FACEBOOK,TWITTER,LINKEDIN,YAHOO]
		  * Alert		: {"result": "Email-Empty"} 	=> Input email kosong.
		  *				  {"result": "Email-Not-Exist"}	=> Email tidak tidak ditemukan. 
		  *				  {"result": "Success-Check-Email-Code"} => Kode di kirim ke email, lakukan pergantian password. 
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$inEmail		= isset($paramsBody['email'])!=''?$paramsBody['email']:'';
		
		
		//FIELD MODEL.
		$modelCnt= User::find()->where(['email'=>$inEmail])->count();
		$model= User::find()->where(['email'=>$inEmail])->one();
		
		if($inEmail!=''){
			if($modelCnt){
				//Reset Code.
				$datetomecode=str_replace(':','',date('m:d H:i'));
				$codeReset = str_replace(' ','',$datetomecode);
				$model->setCodeReset($codeReset);
				if ($model->save()){
					/*
					 * SEND Email		: NOTIFY RESET CODE to Email 
					 * Activation URL	: "http://production.kontrolgampang.com/login/user-logins".
					 * Param			: ACTIVE_CODE
					*/
					$contentBody= $this->renderPartial('_postmanBody',[
						'model'=>$model,
						'codeReset'=>$codeReset
					]);	
					Yii::$app->mailer->compose()
					->setFrom(['kontrolgampang@gmail.com' => 'POSTMAN - KONTROL GAMPANG'])
					->setTo([$inEmail])
					//->setTo(['ptr.nov@gmail.com','piter@lukison.com'])
					->setSubject('PERMINTAAN PERUBAHAN PASSWORD, Reset Code is '.$codeReset)
					->setHtmlBody($contentBody)
					//->attach($filenameAll,[$filename,'xlsx'])
					->send(); 								
					return array('result'=>'Success-Check-Email-Code');
				}else{
					
				}
			}else{
				return array('result'=>'Email-Not-Exist');
			}
		}else{
			return array('result'=>'Email-Empty');
		}	
	}
	
	public function actionUpdate()
    {
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: VALIDASI INPUT PASSWORD BY ACTIVE_CODE
		  * Metode		: PUT (RESET PASSWORD)
		  * URL			: http://production.kontrolgampang.com/login/user-resets
		  * Body Param	: [manual] email,password,ACTIVE_CODE
		  * Keterangan  : SOSMED_PROVIDER[GOOGLE,FACEBOOK,TWITTER,LINKEDIN,YAHOO]
		  * Alert		: {"result": "Email-Empty"} 	=> Input email kosong.
		  *				  {"result": "Email-Not-Exist"}	=> Email tidak tidak ditemukan. 
		  *				  {"result": "wrong-code"} => Kode tidak cocok. 
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		
		$emailCheck		= isset($paramsBody['email'])!=''?$paramsBody['email']:'';
		$inPassword		= isset($paramsBody['password'])!=''?$paramsBody['password']:'';
		//$inEmail		= isset($paramsBody['password'])!=''?($paramsBody['password']!=''?$paramsBody['password']:'~'):'~';
		$inActiveCode	= isset($paramsBody['ACTIVE_CODE'])!=''?$paramsBody['ACTIVE_CODE']:'';
		//$inActiveCode	= isset($paramsBody['ACTIVE_CODE'])!=''?$paramsBody['ACTIVE_CODE']:'';
		
		
		
			//FIELD MODEL.
			$modelCnt= User::find()->where(['email'=>$emailCheck])->count();
			$model= User::find()->where(['email'=>$emailCheck])->one();
			//return $modelCnt;
		if($emailCheck<>''){		
			if($modelCnt<>0){
				if($inPassword<>''){
					if($inActiveCode<>''){
						if($model->validateCodeReset($inActiveCode)){
							//$model->status=10;
							//$model->save();
							$model->password_hash = Yii::$app->security->generatePasswordHash($inPassword);
							if ($model->save()){
								$modelView= UserLogin::find()->where(['email'=>$emailCheck])->one();
								return array('USER'=>$modelView);
							}					
						}else{
							return array('result'=>'wrong-code');
						}
					}else{
						return array('result'=>'ACTIVE_CODE-Empty');
					} 
				}else{
					return array('result'=>'Password-Cant-Empty');
				}
			}else{
				return array('result'=>'Email-Not-Exist');
			}
		}else{
			return array('result'=>'Email-Empty');
		}	
	}
}


