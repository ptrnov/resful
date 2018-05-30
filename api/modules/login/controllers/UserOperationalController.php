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

use api\modules\login\models\UserOps;
use api\modules\master\models\Store;
use api\modules\login\models\User;
use api\modules\master\models\SyncPoling;
use api\modules\login\models\UserLogin;


/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: List User OPRATIONAL PER-STORE.
  * Metode		: POST (Views)
  * URL			: http://production.kontrolgampang.com/login/user-operationals
  * Body Param	: STORE_ID
  * Alert		: {"result": "Store-Empty"} 	=> Store kosong tidak di isi.
  *				  {"result": "Store-Not-Exist"}	=> Store tidak ada atau tidak ditemukan.
 */
class UserOperationalController extends ActiveController
{
    public $modelClass = 'api\modules\login\models\UserOps';
	
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
		  * Subject		: User OPRATIONAL PER-STORE.
		  * Metode		: POST (Views)
		  * URL			: http://production.kontrolgampang.com/login/user-operationals
		  * Body Param	: STORE_ID
		  * Alert		: {"result": "Store-Empty"} 	=> Store kosong tidak di isi.
		  *				  {"result": "Store-Not-Exist"}	=> Store tidak ada atau tidak ditemukan.
		 */
		$paramsBody 			= Yii::$app->request->bodyParams;
		$storeId				= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$accessGroup			= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';
		
		//==POLING SYNC ===
		$metode					= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$accessID				= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$in_username			= isset($paramsBody['username'])!=''?$paramsBody['username']:'';
		$in_password			= isset($paramsBody['password'])!=''?$paramsBody['password']:'';
		$tblPooling				= isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID				= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';		

		if($metode=='GET'){
			if($accessID<>''){				
				$modelCnt= User::find()->where(['ACCESS_ID'=>$accessID])->count();
				$model= User::find()->where(['ACCESS_ID'=>$accessID])->one();			
				if($modelCnt){
					/*===========================
					 *=== POLLING UPDATE UUID ===
					 *===========================
					*/
					if ($tblPooling=='TBL_USER_OPERATIONAL'){
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_USER_OPERATIONAL',
							 'ACCESS_GROUP'=>'',
							 'STORE_ID'=>'',
							 'PRIMARIKEY_VAL'=>$accessID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_USER_OPERATIONAL',
									 'ACCESS_GROUP'=>'',
									 'STORE_ID'=>'',
									 'PRIMARIKEY_VAL'=>$accessID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan AND $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}
					}
					return array('PROFILE'=>$model);
				}else{
					return array('result'=>'data-empty');
				}	
			}else{
				$model= User::find()->where(['ACCESS_GROUP'=>$accessGroup])->all();
				return array('LIST_User'=>$model); 
			}
		}if($metode=='POST'){
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
		}else{
			if($storeId){
				$cntStore= Store::find()->where(['STORE_ID'=>$storeId])->count();
				if($cntStore){
					//return array('result'=>'Show UserOps store');	
					$modalStore= Store::find()->where(['STORE_ID'=>$storeId])->one();
					$modalUserOps= UserOps::find()->where("FIND_IN_SET(ACCESS_ID,'".$modalStore->ACCESS_ID."') AND ACCESS_LEVEL!='OWNER'")->all();				
					return array('LIST_User'=>$modalUserOps); 
				}else{
					return array('result'=>'Store-Not-Exist');
				}
			}else{
				return array('result'=>'Store-Empty');
			}			
		}
		
	}
	
	public function actionUpdate()
    {
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: CHANGE PASSWORD OPRATIONAL.
		  * Metode		: PUT (Update)
		  * URL			: http://production.kontrolgampang.com/login/user-operationals
		  * Body Param	: ACCESS_ID (key),username(key) & password.
		  * Alert		: {"result": "ACCESS_ID-Empty"}=> Field ACCESS_ID Param tidak ada.
		  *				  {"result": "User-Not-Exist"}=> user tidak di temukan.
		  *				  {"result": "failed-save"}=> Error Saved.
		 */
		$paramsBody 		= Yii::$app->request->bodyParams;
		$accessId			= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$in_username		= isset($paramsBody['username'])!=''?$paramsBody['username']:'';
		$in_password		= isset($paramsBody['password'])!=''?$paramsBody['password']:'';
		
		if($accessId){
			$cntUser= User::find()->where(['ACCESS_ID'=>$accessId,'username'=>$in_username])->count();
			if($cntUser){
				if($in_password<>''){				
					$modalUser = User::find()->where(['ACCESS_ID'=>$accessId])->one();				
					$modalUser->password_hash = Yii::$app->security->generatePasswordHash($in_password);
					if($modalUser->save()){
						$modalView = User::find()->where(['ACCESS_ID'=>$accessId])->one();
						return array('LIST_USER'=>$modalView);	
					}else{
						return array('result'=>'failed-save');
					}	
				}else{
					return array('result'=>'password-required');
				}	
			}else{
				return array('result'=>'Not Exist Store');
			}
		}else{
				return array('result'=>'ACCESS_ID-Empty');
		}
	}
	//PR [update (password,staus) ]
	//
}


