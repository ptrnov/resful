<?php

namespace api\modules\laporan\controllers;

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


use api\modules\laporan\models\CounterSearch;
use api\modules\laporan\models\CounterGroup;
use api\modules\laporan\models\TransPenjualanHeader;

class CounterController extends ActiveController
{

	public $modelClass = 'api\modules\laporan\models\TransPenjualanHeader';

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
	
	/* public function actions()
    {		
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => function () {					
					$param=["PenjualanHeaderSearch"=>Yii::$app->request->queryParams];
					//return $param;
                    $searchModel = new PenjualanHeaderSearch();
					return $searchModel->search($param);
                },
            ],
        ];
    } */
	
	public function actionPerStore()
	{
		//?STORE_ID=170726220936.0001&TRANS_DATE=2017-09-29
		//$params     	= $_REQUEST;
		//$paramsHeader	= Yii::$app->request->headers;	
		// $param=["PenjualanHeaderSearch"=>Yii::$app->request->queryParams];
		$param=["CounterSearch"=>Yii::$app->request->bodyParams];
		$searchModel = new CounterSearch();
		return $searchModel->searchPerStore($param);
	}
	
	/* 
	public function actionPerAccessGroup()
	{
		//?STORE_ID=170726220936.0001&TRANS_DATE=2017-09-29
		//$params     	= $_REQUEST;
		//$paramsHeader	= Yii::$app->request->headers;	
		// $param=["PenjualanHeaderSearch"=>Yii::$app->request->queryParams];
		$param=["CounterSearch"=>Yii::$app->request->bodyParams];
		$searchModel = new CounterSearch();
		return $searchModel->searchPerAccessGroup($param);
	}	 */
	
	public function actionPerAccessGroup()
	{
		//?STORE_ID=170726220936.0001&TRANS_DATE=2017-09-29
		//$params     	= $_REQUEST;
		//$paramsHeader	= Yii::$app->request->headers;	
		// $param=["PenjualanHeaderSearch"=>Yii::$app->request->queryParams];
		// $param=["CounterSearch"=>Yii::$app->request->bodyParams];
		// $searchModel = new CounterSearch();
		// return $searchModel->searchPerAccessGroup($param);
		
		
		$paramsBody		= Yii::$app->request->bodyParams;
		$ACCESS_GROUP	= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		//$STORE_ID		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';	
		$thn			= isset($paramsBody['THN'])!=''?$paramsBody['THN']:'';	
		$param=[
			'ACCESS_GROUP'=>$ACCESS_GROUP,
			//'STORE_ID'=>$STORE_ID,
			'THN'=>$thn,
		];		
		$modelCounterGroup= new CounterGroup($param);
		return $modelCounterGroup;
	}	
}
    
	
	
	
	
