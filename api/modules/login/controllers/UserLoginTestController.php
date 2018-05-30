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
use api\modules\login\models\StoreDefault;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: LOGIN MANUAL & SOSMED (On App: "Not Show Profile")
  * Metode		: POST (LOGIN CHECK)
  * URL			: http://production.kontrolgampang.com/login/user-logins
  * Body Param	: [manual] username & password
  *				  [sosmed] SOSMED_PROVIDER,SOSMED_ID
  * Keterangan  : SOSMED_PROVIDER[GOOGLE,FACEBOOK,TWITTER,LINKEDIN,YAHOO]
  * Alert		: {"result": "wrong-password"} 	=> Password salah.
  *				  {"result": "wrong-username"}	=> Username salah atau tidak ditemukan. 
  *				  {"result": "wrong-sosmed"} => Login sosmed salah atau tidak ditemukan. 
 */
class UserLoginTestController extends ActiveController
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
		  * Subject		: LOGIN MANUAL & SOSMED (On App: "Not Show Profile")
		  * Metode		: POST (LOGIN CHECK)
		  * URL			: http://production.kontrolgampang.com/login/user-logins
		  * Body Param	: [manual] username & password or ACTIVE_CODE->[status=0, then input ACTIVE_CODE]
		  *				  [sosmed] SOSMED_PROVIDER,SOSMED_ID
		  * Keterangan  : SOSMED_PROVIDER[GOOGLE,FACEBOOK,TWITTER,LINKEDIN,YAHOO]
		  * Alert		: {"result": "wrong-password"} 	=> Password salah.
		  *				  {"result": "wrong-username"}	=> Username salah atau tidak ditemukan. 
		  *				  {"result": "wrong-sosmed"} => Login sosmed salah atau tidak ditemukan. 
		  *				  {"result": "Active-Code"} => user belum melakukan authorize from email notify code. "status=0" 
		 */
		$paramsBody 			= Yii::$app->request->bodyParams;		
		
		//MANUAL LOGIN
		$username				= isset($paramsBody['username'])!=''?$paramsBody['username']:'';
		$password_hash			= isset($paramsBody['password'])!=''?($paramsBody['password']!=''?$paramsBody['password']:'~'):'~';
		
		//SOSMED LOGIN
		$sosProvider			= isset($paramsBody['SOSMED_PROVIDER'])!=''?$paramsBody['SOSMED_PROVIDER']:'';
		$sosId					= isset($paramsBody['SOSMED_ID'])!=''?$paramsBody['SOSMED_ID']:'';
		
		//ACTIVE Code
		$codeActived			= isset($paramsBody['ACTIVE_CODE'])!=''?$paramsBody['ACTIVE_CODE']:'';
		$paramlUUID				= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		
		//FILTER DINAMIC FIELD AND VALUE
		if($sosProvider==''){
			//MANUAL LOGIN 
			$field	= 'username';
			$value	= $username;
		}else{
			$field	=$sosProvider=='GOOGLE'?'ID_GOOGLE':($sosProvider=='FACEBOOK'?'ID_FB':($sosProvider=='TWITTER'?'ID_TWITTER':($sosProvider=='LINKEDIN'?'ID_LINKEDIN':($sosProvider=='YAHOO'?'ID_YAHOO':'username'))));
			$value	=$sosId;
		};
		
		//MODEL CHECK
		//$modelCnt= UserLogin::find()->where(['username'=>$username])->count();
		//$model= UserLogin::find()->where(['username'=>$username])->one();	
		
		//DINAMIC FIELD MODEL.
		$modelCnt= UserLogin::find()->where([$field=>$value])->count();
		$model= UserLogin::find()->where([$field=>$value])->one();	
		
		//RESULT API
		if($sosProvider==''){
			//MANUAL LOGIN 
			if($modelCnt){					
				if($model->validateLoginPassword($password_hash)){
					if ($model->status==10){
						$model->UUID=[$paramlUUID];
						$model->save();
						// return self::setUuid($model->ACCESS_GROUP,$model->ACCESS_ID,$paramlUUID);
						self::setUuid($model->ACCESS_GROUP,$model->ACCESS_ID,$paramlUUID);
						$modelView= UserLogin::find()->where([$field=>$value])->one();
						return array('USER'=>$modelView);
					}else{
						if ($codeActived){
							if($model->validateCodeReset($codeActived)){
								$model->status=10;
								$model->UUID=$paramlUUID;
								$model->save();
								$modelView= UserLogin::find()->where([$field=>$value])->one();
								return array('USER'=>$modelView);
							}else{
								return array('result'=>'wrong-code');
							}							
						}else{
							return array('result'=>'Active-Code');
						}
					}		
				}else{
					return array('result'=>'wrong-password');
				}
							
			}else{
				return array('result'=>'wrong-username');
			};		
		}else{
			//SOSMED LOGIN
			if($modelCnt){
				return array('USER'=>$model);
			}else{
				return array('result'=>'wrong-sosmed');
			};	
		};		
	}

    private function setUuid($accessGroup='',$accessId='',$uuid=''){
		//$modelCari=StoreDefault::find()->where(['ACCESS_GROUP'=>$accessGroup])->andWhere("FIND_IN_SET('".$accessId."',ACCESS_ID)")->all();
		$modelCari=StoreDefault::find()->where(['ACCESS_GROUP'=>$accessGroup])->andWhere("FIND_IN_SET('".$accessId."',ACCESS_ID)")->all();		
		if($modelCari){
			foreach($modelCari as $row => $val){
				// $rslt[]=$val->ID;
				// $rslt[]=$uuid;
				$modelUUID=StoreDefault::find()->where(['ID'=>$val->ID])->one();
				$aryUUID='';
				$aryUUID=explode(",",$modelUUID->UUID);					//FIELD STRING TO ARRAY
				if(!in_array($uuid,$aryUUID)){ 							//JIKA TIDAK DI TEMULAN UUID MAKAN DI TAMBAHKAN
					$rsltUUID=ArrayHelper::merge($aryUUID,[$uuid]);
					$modelUUID->UUID=implode(",",$rsltUUID);
					$modelUUID->save();									
				}
				
			}
		}
		// $modelView=StoreDefault::find()->where(['ACCESS_GROUP'=>$accessGroup])->andWhere("FIND_IN_SET('".$accessId."',ACCESS_ID)")->all();
		// return $modelView;
		// return 
	}	
}


