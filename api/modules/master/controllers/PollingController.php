<?php

namespace api\modules\master\controllers;

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

use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: SYNCRONIZE POLLING
  * Metode		: POST (CRUD)
  * URL			: http://production.kontrolgampang.com/master/polling
  * Body Param	: ACCESS_GROUP,STORE_ID
 */
class PollingController extends ActiveController
{	
    public $modelClass = 'api\modules\login\models\SyncPoling';

	/**
     * Behaviors
	 * Mengunakan Auth HttpBasicAuth.
	 * Chacking kontrolgampang\login.
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
					'application/json' => Response::FORMAT_JSON,"JSON_PRETTY_PRINT"
				],
				
			],
			'corsFilter' => [
				'class' => \yii\filters\Cors::className(),
				'cors' => [
					// restrict access to
					'Origin' => ['*','http://localhost:810'],
					'Access-Control-Request-Method' => ['POST', 'PUT','GET'],
					// Allow only POST and PUT methods
					'Access-Control-Request-Headers' => ['X-Wsse'],
					// Allow only headers 'X-Wsse'
					'Access-Control-Allow-Credentials' => true,
					// Allow OPTIONS caching
					'Access-Control-Max-Age' => 3600,
					// Allow the X-Pagination-Current-Page header to be exposed to the browser.
					'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
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
		unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view'],$actions['store']);
		//unset($actions['update'], $actions['create'], $actions['delete'], $actions['view']);
		return $actions;
	}
	
	public function actionIndex()
    {        
		/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: SYNCRONIZE POLLING
			* Metode		: POST (CRUD)
			* URL			: http://production.kontrolgampang.com/master/polling
			* Headers Param	: STORE_ID, UUID
			* URl PARAM		: ?STORE_ID170726220936.0001&UUID=uuid-test-123
			* ==== TYPE_ACTION ====	| == STT_OPS ======	| == STT_OWNER ====
			* 1. CEATE			    | 1. SINKRON		|	1. SINKRON
			* 2. UPDATE				| 2. TIDAK_SINKRON	|	2. TIDAK_SINKRON
			* 3. DELETE				|
			* KETERANGAN	: ARY_UUID ada, maka tidak di tampilkan, jika ARY_UUID tidak ada ditampilkan.
		*/
		$params     	= $_REQUEST;
		$paramsHeader	= Yii::$app->request->headers;
		$accessGroup	= isset($params['ACCESS_GROUP'])!=''?$params['ACCESS_GROUP']:$paramsHeader['ACCESS_GROUP'];
		$storeId		= isset($params['STORE_ID'])!=''?$params['STORE_ID']:$paramsHeader['STORE_ID'];
		$paramlUUID		= isset($params['UUID'])!=''?$params['UUID']:$paramsHeader['UUID'];
		$tblNm			= isset($params['NM_TABLE'])!=''?$params['NM_TABLE']:$paramsHeader['NM_TABLE'];
		// $aryStoreId		= explode(".",$storeId);		
		// $accessGroup	= $aryStoreId[0];	
		//if ($tblNm=='TBL_MERCHANT_TYPE'){
			$modelViewAll= SyncPoling::find()->where(['ACCESS_GROUP'=>'','STORE_ID'=>''])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();			
		//}else{
			$modelView= SyncPoling::find()->where(['ACCESS_GROUP'=>$accessGroup,'STORE_ID'=>$storeId])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();			
		//}
		return ArrayHelper::merge($modelView,$modelViewAll);
		
	}	
}


