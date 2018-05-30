<?php

namespace api\modules\ppob\controllers;

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

use api\modules\ppob\models\ApiTableTest;

class SibisnisController extends ActiveController
{

	public $modelClass = 'api\modules\ppob\models\ApiTableTest';

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
		 unset($actions['index']);//, $actions['update']);
		 //, $actions['create'], $actions['delete'], $actions['view']);
		 //unset($actions['update'], $actions['create'], $actions['delete'], $actions['view']);
		 return $actions;
	 }
	
	public function actionIndex()
	{
		
		
		// $model= ApiTableTest::find()->all();
		// return $model;
		//return ['a'=>1];
		$allDataKelpmpok=Yii::$app->apippob->ArrayKelompokAllType();
		return $allDataKelpmpok;
	}
	
	public function actionMasterData()
	{
		$paramsBody 		= Yii::$app->request->bodyParams;		
		$function			= isset($paramsBody['function'])!=''?$paramsBody['function']:'';
		$memberid			= isset($paramsBody['memberid'])!=''?$paramsBody['memberid']:'';
		$tipe				= isset($paramsBody['tipe'])!=''?$paramsBody['tipe']:'';
		$kategori_id		= isset($paramsBody['kategori_id'])!=''?$paramsBody['kategori_id']:'';
		$produk				= isset($paramsBody['produk'])!=''?$paramsBody['produk']:'';
		$id_pelanggan		= isset($paramsBody['id_pelanggan'])!=''?$paramsBody['id_pelanggan']:'';
		$reff_id			= isset($paramsBody['reff_id'])!=''?$paramsBody['reff_id']:'';
		$msisdn				= isset($paramsBody['msisdn'])!=''?$paramsBody['msisdn']:'';
		
		if (strtoupper($function)==strtoupper("get-info-kelompok")){			
			$rsltRespon=Yii::$app->apippob->LabtestSibisnis($function,$memberid,$tipe);
			return $rsltRespon;
		}elseif(strtoupper($function)==strtoupper("get-info-produk")){			
			$rsltRespon=Yii::$app->apippob->LabtestSibisnis($function,$memberid,'',$kategori_id);
			return $rsltRespon;
		}elseif(strtoupper($function)==strtoupper("h2h-inquiry")){
			$rsltRespon=Yii::$app->apippob->LabtestSibisnis($function,$memberid,'','',$produk,$id_pelanggan);
			return $rsltRespon;
		}elseif(strtoupper($function)==strtoupper("h2h-bayar")){
			$rsltRespon=Yii::$app->apippob->LabtestSibisnis($function,$memberid,'','',$produk,'',$reff_id,$msisdn);
			return $rsltRespon;
		}else{
			return ['error'=>'function not found'];
		};
	}
	
	public function actionUpdate()
    {  
		/**
		  * @author 		: ptrnov  <piter@lukison.com>
		  * @since 			: 1.2
		  * Subject			: CUSTOMER PER-STORE
		  * Metode			: PUT (Update)
		  * URL				: http://production.kontrolgampang.com/master/customers
		  * Body Param		: CUSTOMER_ID(Key)
		 */
		// $paramsBody 	= Yii::$app->request->bodyParams;		
		// $id				= isset($paramsBody['ID'])!=''?$paramsBody['ID']:'';		
		// $nama			= isset($paramsBody['NAMA'])!=''?$paramsBody['NAMA']:'';
		$params     	= $_REQUEST;
		$paramsHeader	= Yii::$app->request->headers;
		$paramsBody 	= Yii::$app->request->bodyParams;		
		
		$id			= $paramsHeader['ID']!=''?$paramsHeader['ID']:$paramsBody['ID'];
		$nama		= $paramsHeader['NAMA']!=''?$paramsHeader['NAMA']:$paramsBody['NAMA'];
		
		$modelCustomer= ApiTableTest::find()->where(['ID'=>$id])->one();
		if($modelCustomer){
			//$modelMerchant->BANK_NM='ok zone1';
			if ($nama!=''){$modelCustomer->NAMA=$nama;}
			if($modelCustomer->save()){
				$modelView=ApiTableTest::find()->where(['ID'=>$id])->one();				
				return $modelView;
			}else{
				return array('result'=>$modelCustomer->errors);
			}
		}else{
			return array('result'=>'Customer-Not-Exist');
		};			
	}
	
	private function errorCode(){
		$codes = [

            100 => 'Continue',

            101 => 'Switching Protocols',

            200 => 'OK',

            201 => 'Created',

            202 => 'Accepted',

            203 => 'Non-Authoritative Information',

            204 => 'No Content',

            205 => 'Reset Content',

            206 => 'Partial Content',

            300 => 'Multiple Choices',

            301 => 'Moved Permanently',

            302 => 'Found',

            303 => 'See Other',

            304 => 'Not Modified',

            305 => 'Use Proxy',

            306 => '(Unused)',

            307 => 'Temporary Redirect',

            400 => 'Bad Request',

            401 => 'Unauthorized',

            402 => 'Payment Required',

            403 => 'Forbidden',

            404 => 'Not Found',

            405 => 'Method Not Allowed',

            406 => 'Not Acceptable',

            407 => 'Proxy Authentication Required',

            408 => 'Request Timeout',

            409 => 'Conflict',

            410 => 'Gone',

            411 => 'Length Required',

            412 => 'Precondition Failed',

            413 => 'Request Entity Too Large',

            414 => 'Request-URI Too Long',

            415 => 'Unsupported Media Type',

            416 => 'Requested Range Not Satisfiable',

            417 => 'Expectation Failed',

            500 => 'Internal Server Error',

            501 => 'Not Implemented',

            502 => 'Bad Gateway',

            503 => 'Service Unavailable',

            504 => 'Gateway Timeout',

            505 => 'HTTP Version Not Supported'

       ];
	}
}
    
	
	
	
	
