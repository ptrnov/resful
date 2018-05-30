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


use api\modules\laporan\models\PollingChart;
use api\modules\laporan\models\TransPenjualanHeader;

class PollingChartController extends ActiveController
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
	
	
	/* ======================================
	 * ====      Polling Status Group     ===
	 * ==== Create By ptr.nov@gmail.com   ===
	 * ======================================
	*/
	public function actionPollingGroup()
	{
		$paramsBody		= Yii::$app->request->bodyParams;
		$sccessGroup	= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';	
		$perangkat		= isset($paramsBody['PERANGKAT'])!=''?$paramsBody['PERANGKAT']:'';	
		$param=[
			'ACCESS_GROUP'=>$sccessGroup,
			'STORE_ID'=>'',
			'PERANGKAT'=>$perangkat,
		];	
		$modelPollingChart= new PollingChart($param);
		return $modelPollingChart;
	}	
	
	/* ======================================
	 * ====   Polling Status Per-store    ===
	 * ==== Create By ptr.nov@gmail.com   ===
	 * ======================================
	*/
	public function actionPollingPerstore()
	{
		$paramsBody		= Yii::$app->request->bodyParams;
		$sccessGroup	= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';	
		$perangkat		= isset($paramsBody['PERANGKAT'])!=''?$paramsBody['PERANGKAT']:'';
		$param=[
			'ACCESS_GROUP'=>$sccessGroup,
			'STORE_ID'=>$storeId,
			'PERANGKAT'=>$perangkat,
		];		
		$modelPollingChart= new PollingChart($param);
		return $modelPollingChart;
	}		
	
}
    
	
	
	
	
