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


use api\modules\laporan\models\Store;
use api\modules\laporan\models\ChartProdukTopMonth;
use api\modules\laporan\models\ChartProdukTopWeek;
use api\modules\laporan\models\ChartProdukTopDay;
use api\modules\laporan\models\ChartProdukLevelBuffer;

class ProdukChartController extends ActiveController
{

	public $modelClass = 'api\modules\laporan\models\Store';

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
	
		
	/* ==========================================
	 * ==== 	PRODUK TOP  			  	  ===
	 * ==== Create By ptr.nov@gmail.com  	  ===
	 * ==== TYPE 	: ACCESS_GROUP/STORE_ID	  ===
	 * ==== PERIODE	: Day/Week/Month	  	  ===
	 * ==========================================
	*/
	
	//=== MONTHLY ===
	public function actionBulananTopProduk()
	{	
		$paramsBody		= Yii::$app->request->bodyParams;
		$accessGrp		= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';	
		$uuid			= isset($paramsBody['PERANGKAT'])!=''?$paramsBody['PERANGKAT']:'';				
		$tgl			= isset($paramsBody['TGL'])!=''?$paramsBody['TGL']:'';	
		$pilih			= isset($paramsBody['PILIH'])!=''?$paramsBody['PILIH']:'';	
		$param=[
			'ACCESS_GROUP'=>$accessGrp,
			'STORE_ID'=>$storeId,
			'PERANGKAT'=>$uuid,			
			'TGL'=>$tgl,
			'PILIH'=>$pilih
		];		
		$modelChartProdukTopMonth= new ChartProdukTopMonth($param);
		return $modelChartProdukTopMonth;
	}	
	
	//=== WEEKLY ===
	public function actionMingguanTopProduk()
	{	
		$paramsBody		= Yii::$app->request->bodyParams;
		$accessGrp		= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';	
		$uuid			= isset($paramsBody['PERANGKAT'])!=''?$paramsBody['PERANGKAT']:'';				
		$tgl			= isset($paramsBody['TGL'])!=''?$paramsBody['TGL']:'';	
		$pilih			= isset($paramsBody['PILIH'])!=''?$paramsBody['PILIH']:'';	
		$param=[
			'ACCESS_GROUP'=>$accessGrp,
			'STORE_ID'=>$storeId,
			'PERANGKAT'=>$uuid,			
			'TGL'=>$tgl,
			'PILIH'=>$pilih
		];		
		$modelChartProdukTopWeek= new ChartProdukTopWeek($param);
		return $modelChartProdukTopWeek;
	}	
	
	//=== DAILY ===
	public function actionHarianTopProduk()
	{	
		$paramsBody		= Yii::$app->request->bodyParams;
		$accessGrp		= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';	
		$uuid			= isset($paramsBody['PERANGKAT'])!=''?$paramsBody['PERANGKAT']:'';				
		$tgl			= isset($paramsBody['TGL'])!=''?$paramsBody['TGL']:'';	
		$pilih			= isset($paramsBody['PILIH'])!=''?$paramsBody['PILIH']:'';	
		$param=[
			'ACCESS_GROUP'=>$accessGrp,
			'STORE_ID'=>$storeId,
			'PERANGKAT'=>$uuid,			
			'TGL'=>$tgl,
			'PILIH'=>$pilih
		];		
		$modelChartProdukTopDay= new ChartProdukTopDay($param);
		return $modelChartProdukTopDay;
	}
	
	//=== LEVEL BUFFER PRODUK ===
	public function actionLevelBufferProduk()
	{	
		$paramsBody		= Yii::$app->request->bodyParams;
		$accessGrp		= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';	
		$uuid			= isset($paramsBody['PERANGKAT'])!=''?$paramsBody['PERANGKAT']:'';				
		$tgl			= isset($paramsBody['TGL'])!=''?$paramsBody['TGL']:'';	
		$pilih			= isset($paramsBody['PILIH'])!=''?$paramsBody['PILIH']:'';	
		$param=[
			'ACCESS_GROUP'=>$accessGrp,
			'STORE_ID'=>$storeId,
			'PERANGKAT'=>$uuid,			
			'TGL'=>$tgl,
			'PILIH'=>$pilih
		];		
		$modelChartProdukLevelBuffer= new ChartProdukLevelBuffer($param);
		return $modelChartProdukLevelBuffer;
	}	
	
}
    
	
	
	
	
