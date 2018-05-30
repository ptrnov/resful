<?php

namespace api\modules\pembayaran\controllers;

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

use api\modules\pembayaran\models\StorePembayaran;
use api\modules\master\models\SyncPoling;

class StorePaymentController extends ActiveController
{

	public $modelClass = 'api\modules\pembayaran\models\StorePembayaran';

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
	
	public function actionCreate()
    {        
		/**
		  * @author 		: ptrnov  <piter@lukison.com>
		  * @since 			: 1.2
		  * Subject			: PEMBAYARAN STORE-KASIR
		  * Metode			: POST 
		  * URL				: http://production.kontrolgampang.com/pembayaran/store-pembayarans
		  * param Metode	: POST & GET
		  * Param View		:  
		  * Param Create	:  
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';		
		$kasirId		= isset($paramsBody['KASIR_ID'])!=''?$paramsBody['KASIR_ID']:'';		
		$invoice		= isset($paramsBody['FAKTURE_NO'])!=''?$paramsBody['FAKTURE_NO']:'';		
		
		//POLING SYNC nedded ACCESS_ID
		$accessID=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$perangkatUuid=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		//VALIDATION STORE
		$cntStorePembayaran= StorePembayaran::find()->where(['STORE_ID'=>$storeId])->count();
		
		if($metode=='GET'){
			if($cntStorePembayaran And $kasirId){				
				if ($tblPooling=='TBL_STORE_PAYMENT'){						
					//==GET DATA POLLING
					$dataHeader=explode('.',$storeId);
					$modelPoling=SyncPoling::find()->where([
						 'NM_TABLE'=>'TBL_STORE_PAYMENT',
						 'ACCESS_GROUP'=>$dataHeader[0],
						 'STORE_ID'=>$storeId,
						 'PRIMARIKEY_VAL'=>$kasirId,
					])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
					//==UPDATE DATA POLLING UUID
					if($modelPoling){							
						foreach($modelPoling as $row => $val){
							$modelSimpan=SyncPoling::find()->where([
								 'NM_TABLE'=>'TBL_STORE_PAYMENT',
								 'ACCESS_GROUP'=>$dataHeader[0],
								 'STORE_ID'=>$storeId,
								 'PRIMARIKEY_VAL'=>$kasirId,
								 'TYPE_ACTION'=>$val->TYPE_ACTION
							])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
							if($modelSimpan AND $paramlUUID){
								$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
								$modelSimpan->save();
							}
						}							
					}
				}
				$modelView=StorePembayaran::find()->Where(['KASIR_ID'=>$kasirId])->one();
			}elseif($cntStorePembayaran){
				$modelView=StorePembayaran::find()->where(['STORE_ID'=>$storeId])->all();
			}else{
				return array('result'=>'Store-Invoice-Not-Exist');
			}
			return array('STORE_PAYMENT'=>$modelView);
		}else{
			return array('result'=>'POST-or-GET');
		}
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
		/* $paramsBody 	= Yii::$app->request->bodyParams;		
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';		
		$customerId		= isset($paramsBody['CUSTOMER_ID'])!=''?$paramsBody['CUSTOMER_ID']:'';		
		$nama			= isset($paramsBody['NAME'])!=''?$paramsBody['NAME']:'';
		$email			= isset($paramsBody['EMAIL'])!=''?$paramsBody['EMAIL']:'';
		$phone			= isset($paramsBody['PHONE'])!=''?$paramsBody['PHONE']:'';
		$dcript			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		
		//==STATUS== [0=Disable;1=Enable;3=Disable]
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		//if ($stt!=''){$modelCustomer->STATUS=$stt;};
		$modelCustomer= Customer::find()->where(['CUSTOMER_ID'=>$customerId])->one();
		if($modelCustomer){
			//$modelMerchant->BANK_NM='ok zone1';
			if ($nama!=''){$modelCustomer->NAME=$nama;};
			if ($email!=''){$modelCustomer->EMAIL=$email;};
			if ($phone!=''){$modelCustomer->PHONE=$phone;};
		    if ($stt!=''){$modelCustomer->STATUS=$stt;};
			if ($dcript!=''){$modelCustomer->DCRP_DETIL=$dcript;};
			if($modelCustomer->save()){
				$modelView=Customer::find()->where(['CUSTOMER_ID'=>$customerId])->one();				
				return array('CUSTOMER'=>$modelView);
			}else{
				return array('result'=>$modelCustomer->errors);
			}
		}else{
			return array('result'=>'Customer-Not-Exist');
		};	 */		
		return true;
	}
	
	
	public function actionDelete()
    {  
		/**
		  * @author 		: ptrnov  <piter@lukison.com>
		  * @since 			: 1.2
		  * Subject			: CUSTOMER PER-STORE
		  * Metode			: POST (DELETE)
		  * URL				: http://production.kontrolgampang.com/master/customers
		  * Body Param		: CUSTOMER_ID(Key)
		 */
		/* $paramsBody 	= Yii::$app->request->bodyParams;		
		$customerId		= isset($paramsBody['CUSTOMER_ID'])!=''?$paramsBody['CUSTOMER_ID']:'';
		
		$modelCustomer= Customer::find()->where(['CUSTOMER_ID'=>$customerId])->one();
		if($modelCustomer){
			$modelCustomer->STATUS=3;
			if($modelCustomer->save()){			
				$modelView=Customer::find()->where(['CUSTOMER_ID'=>$customerId])->one();
				return array('CUSTOMER'=>$modelView);
			}else{
				return array('result'=>$modelCustomer->errors);
			}
		}else{
			return array('result'=>'Cistomer-Not-Exist');
		}; */
		return true;
	}
	
}
    
	
	
	
	
