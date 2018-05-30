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
use api\modules\master\models\Store;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: ADD USER OPRATIONAL
  * Metode		: POST
  * URL			: http://production.kontrolgampang.com/login/user-signup-oprs
  * Body Param	: username & password & ACCESS_GROUP & STORE_ID
  * Alert		: {"result": "User Already Exist"} 	=> user sudah terdaftar, "cari username yang lain". 
  *				  {"result": "Not Exist Store"}	=> Store tidak ditemukan. 
  *				  {"result": "Not Exist Owner"} => Parent Owner tidak titemukan. 
  *				  {"result": "Not Exist access_group"} => ACCESS_GROUP tidak ditemukan.  
 */
class UserSignupOprController extends ActiveController
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
		  * Subject		: ADD USER OPRATIONAL
		  * Metode		: POST
		  * URL			: http://production.kontrolgampang.com/login/user-signup-oprs
		  * Body Param	: username & password & ACCESS_GROUP & STORE_ID
		  * Alert		: {"result": "User Already Exist"} 	=> user sudah terdaftar, "cari username yang lain". 
		  *				  {"result": "Not Exist Store"}	=> Store tidak ditemukan. 
		  *				  {"result": "Not Exist Owner"} => Parent Owner tidak titemukan. 
		  *				  {"result": "Not Exist access_group"} => ACCESS_GROUP tidak ditemukan.  
		 */
		$paramsBody 			= Yii::$app->request->bodyParams;
		$accessGroup			= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';
		$storeId				= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$in_username			= isset($paramsBody['username'])!=''?$paramsBody['username']:'';
		$in_password			= isset($paramsBody['password'])!=''?$paramsBody['password']:'';
		
		if($accessGroup){
			$cntOwner= UserLogin::find()->where(['ACCESS_GROUP'=>$accessGroup])->count();
			if($cntOwner){
				$cntStore= Store::find()->where(['STORE_ID'=>$storeId])->count();
				if($cntStore){
					$cntUser= UserLogin::find()->where(['username'=>$in_username])->count();
					if(!$cntUser){
						if($in_password<>''){
							$model= new UserLogin();
							//$model->scenario = 'createuser_oprs';
							$model->username=$in_username;
							$model->ACCESS_LEVEL='OPS';
							$model->ACCESS_GROUP=$accessGroup;
							$model->create_at=date('Y-m-d H:i:s');
							$model->password_hash = Yii::$app->security->generatePasswordHash($in_password);
							$model->auth_key = Yii::$app->security->generateRandomString();						
							if ($model->save()){
								$modelUser= UserLogin::find()->where(['username'=>$in_username])->one();
								$modelStore= Store::find()->where(['STORE_ID'=>$storeId])->one();
								//penambahan array pada store->ACCESS_ID [user penguna store];
								$modelStore->ACCESS_ID=$modelStore->ACCESS_ID.','.$modelUser->ACCESS_ID;
								// $modelStore->save();
								// return array('result'=>$modelStore->errors);
								if($modelStore->save()){
									return array('result'=>$modelUser->attributes);								
								}else{
									return array('result'=>'Unregister-User-Store');
									//return array('result'=>$modelStore->attributes);
								}	 		
							}else{
								return array('result'=>$model->errors);
							} 	
						}else{
							return array('result'=>'password-required');
						}
					}else{
						return array('result'=>'User Already Exist');
					}
				}else{
					return array('result'=>'Not Exist Store');
				}			
			}else{
				return array('result'=>'Not Exist Owner');
			}
		}else{
			return array('result'=>'Not Exist access_group');
		}
	}
}


