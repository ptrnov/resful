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
use api\modules\login\models\AppModulPermission;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: PERMISSION PER-USER
  * Metode		: POST (Views)
  * URL			: http://production.kontrolgampang.com/login/user-permissions
  * Body Param	: ACCESS_ID
  * Alert		: {"result": "ACCESS_ID-Empty"} 	=> Field ACCESS_ID Param tidak ada.
  *				  {"result": "User-Not-Exist"}		=> user tidak di temukan.
 */
class UserPermissionController extends ActiveController
{
    public $modelClass = 'api\modules\login\models\AppModulPermission';
	
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
		  * Subject		: PERMISSION PER-USER
		  * Metode		: POST (Views)
		  * URL			: http://production.kontrolgampang.com/login/user-permissions
		  * Body Param	: ACCESS_ID
		  * Alert		: {"result": "ACCESS_ID-Empty"} 	=> Field ACCESS_ID Param tidak ada.
		  *				  {"result": "User-Not-Exist"}		=> user tidak di temukan.
		 */
		$paramsBody 			= Yii::$app->request->bodyParams;
		$accessId				= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		
		if($accessId){
			$cntUser= User::find()->where(['ACCESS_ID'=>$accessId])->count();
			if($cntUser){
				$modalPermission= AppModulPermission::find()->where(['ACCESS_ID'=>$accessId])->all();
				return array('USER_PERMISSIONS'=>$modalPermission);	
			}else{
				return array('result'=>'User-Not-Exist');
			}
		}else{
			return array('result'=>'ACCESS_ID-Empty');
		}
	}
	// per-user per-menu update permission
	public function actionUpdate()
    {
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: PERMISSION PER MODUL.
		  * Metode		: PUT (Update)
		  * URL			: http://production.kontrolgampang.com/login/user-permissions
		  * Body Param	: ACCESS_ID (key),MODUL_ID (key), BTN_VIEW/BTN_CREATE/BTN_UPDATE/BTN_DELETE/STATUS.
		  * Alert		: {"result": "ACCESS_ID-Empty"}=> Field ACCESS_ID Param tidak ada.
		  *				  {"result": "User-Not-Exist"}=> user tidak di temukan.
		 */
		$paramsBody 		= Yii::$app->request->bodyParams;
		$accessId			= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$modulId			= isset($paramsBody['MODUL_ID'])!=''?$paramsBody['MODUL_ID']:'';
		$btnView			= isset($paramsBody['BTN_VIEW'])!=''?$paramsBody['BTN_VIEW']:'';
		$btnCreate			= isset($paramsBody['BTN_CREATE'])!=''?$paramsBody['BTN_CREATE']:'';
		$btnUpdate			= isset($paramsBody['BTN_UPDATE'])!=''?$paramsBody['BTN_UPDATE']:'';
		$btnDelete			= isset($paramsBody['BTN_DELETE'])!=''?$paramsBody['BTN_DELETE']:'';
		$status				= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		if($accessId){
			$cntUser= User::find()->where(['ACCESS_ID'=>$accessId])->count();
			if($cntUser){
				$modalPermission = AppModulPermission::find()->where(['ACCESS_ID'=>$accessId,'MODUL_ID'=>$modulId])->one();
				if ($btnView!=''){$modalPermission->BTN_VIEW=$btnView;};
				if ($btnCreate!=''){$modalPermission->BTN_CREATE=$btnCreate;};
				if ($btnUpdate!=''){$modalPermission->BTN_UPDATE=$btnUpdate;};
				if ($btnDelete!=''){$modalPermission->BTN_DELETE=$btnDelete;};
				if ($status!=''){$modalPermission->STATUS=$status;};
				if($modalPermission->save()){
					$modalView = AppModulPermission::find()->where(['ACCESS_ID'=>$accessId,'MODUL_ID'=>$modulId])->one();
					return array('USER_PERMISSIONS'=>$modalView);	
				}else{
					return array('result'=>'permission-Not-Exist');
				}				
			}else{
				return array('result'=>'User-Not-Exist');
			}
		}else{
			return array('result'=>'ACCESS_ID-Empty');
		}
	}
}


