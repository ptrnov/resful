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

use api\modules\pembayaran\models\StorePerangkatKasir;
use api\modules\master\models\SyncPoling;
use api\modules\pembayaran\models\StoreInvoicePaket;
use api\modules\pembayaran\models\Store;

class StoreKasirController extends ActiveController
{

	public $modelClass = 'api\modules\pembayaran\models\StorePerangkatKasir';

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
		  * URL				: http://production.kontrolgampang.com/pembayaran/store-kasirs
		  * param Metode	: POST & GET
		  * Key				: METHODE,STORE_ID,KASIR_ID
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';		
		$kasirId		= isset($paramsBody['KASIR_ID'])!=''?$paramsBody['KASIR_ID']:'';		
		
		//POLING SYNC nedded ACCESS_ID
		$accessID=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$perangkatUuid=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		//VALIDATION STORE
		$cntStorePerangkatKasir= StorePerangkatKasir::find()->where(['STORE_ID'=>$storeId])->count();
		
		if($metode=='GET'){
			if($cntStorePerangkatKasir And $kasirId){				
				if ($tblPooling=='TBL_STORE_KASIR'){						
					//==GET DATA POLLING
					$dataHeader=explode('.',$storeId);
					$modelPoling=SyncPoling::find()->where([
						 'NM_TABLE'=>'TBL_STORE_KASIR',
						 'ACCESS_GROUP'=>$dataHeader[0],
						 'STORE_ID'=>$storeId,
						 'PRIMARIKEY_VAL'=>$kasirId,
					])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
					//==UPDATE DATA POLLING UUID
					if($modelPoling){							
						foreach($modelPoling as $row => $val){
							$modelSimpan=SyncPoling::find()->where([
								 'NM_TABLE'=>'TBL_STORE_KASIR',
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
				$modelView=StorePerangkatKasir::find()->Where(['KASIR_ID'=>$kasirId])->one();
			}elseif($cntStorePerangkatKasir){
				$modelView=StorePerangkatKasir::find()->where(['STORE_ID'=>$storeId])->all();
			}else{
				return array('result'=>'Store-Kasir-Not-Exist');
			}
			return array('STORE_KASIR'=>$modelView);
		}else{
			return array('result'=>'POST-or-GET');
		}
	}
	
    /* ==================
	 * === LIST PAKET ===
	 * ==================
	 * URL 		: http://production.kontrolgampang.com/pembayaran/store-kasirs/list-paket
	 * TMETHODE	: POST
	*/
	public function actionListPaket()
	{
		$model= StoreInvoicePaket::find()->all();
		return $model;
	}
	
	/* ============================
	 * === LIST METOTHE PAYMENT ===
	 * ============================
	 * URL 		: http://production.kontrolgampang.com/pembayaran/store-kasirs/list-payment-methode
	 * TMETHODE	: POST
	*/
	public function actionListPaymentMethode()
	{
		$ary=[
		 ['PAYMENT_METHODE' =>1,'PAYMENT_METHODE_NM'=>'Dompet KG'],
		 ['PAYMENT_METHODE' =>2,'PAYMENT_METHODE_NM'=>'Kartu Kredit'],
		 ['PAYMENT_METHODE' =>3,'PAYMENT_METHODE_NM'=>'Transfer'],	
		];		
		//$valAry = ArrayHelper::map($ary, 'PAYMENT_METHODE', 'PAYMENT_METHODE_NM');
		return $ary;
	}
	
	/* =======================
	 * === LIST AUTODEBET  ===
	 * =======================
	 * URL 		: http://production.kontrolgampang.com/pembayaran/store-kasirs/list-auto-debet
	 * TMETHODE	: POST
	*/
	public function actionListAutoDebet()
	{
		$ary=[
		 ['DOMPET_AUTODEBET' =>0,'DOMPET_AUTODEBET_NM'=>'Tidak'],
		 ['DOMPET_AUTODEBET' =>1,'DOMPET_AUTODEBET_NM'=>'Autodebet']
		];		
		//$valAry = ArrayHelper::map($ary, 'PAYMENT_METHODE', 'PAYMENT_METHODE_NM');
		return $ary;
	}
	
	
	/* ================================
	 * === ADD NEW PERANGKAT KASIR ===
	 * ================================
	 * URL 		: http://production.kontrolgampang.com/pembayaran/store-kasirs/tambah-perangkat
	 * METHODE	: POST
	 * Key		: 
	 * Param	: STORE_ID,PERANGKAT_UUID,ACCESS_ID
	**/
    public function actionTambahPerangkat(){
		
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';			
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$perangkatUuid	= isset($paramsBody['PERANGKAT_UUID'])!=''?$paramsBody['PERANGKAT_UUID']:'';
		$dataHeader=explode('.',$storeId);
		$modelStoreKasir= new StorePerangkatKasir();
		if ($accessID!=''){$modelStoreKasir->UPDATE_BY=$accessID;};
		if ($perangkatUuid!=''){$modelStoreKasir->PERANGKAT_UUID=$perangkatUuid;};
		if ($storeId!=''){$modelStoreKasir->STORE_ID=$storeId;};
		if ($modelStoreKasir->save()){
			$rsltMax=StorePerangkatKasir::find()->where([
				'STORE_ID'=>$storeId,
				'ACCESS_GROUP'=>$dataHeader[0],
			])->max('KASIR_ID');
			$modelView=StorePerangkatKasir::find()->where([
					'STORE_ID'=>$storeId,'ACCESS_GROUP'=>$dataHeader[0],'KASIR_ID'=>$rsltMax
			])->one();				
			return array('STORE_KASIR'=>$modelView);
		}else{
			return array('result'=>$modelStoreKasir->errors);
		}
			
	}
	
	/* ========================
	 * === LIST UUID STORE  ===
	 * ========================
	 * 	URL 		: http://production.kontrolgampang.com/pembayaran/store-kasirs/list-uuid
	 * 	METHODE		: POST
	 *	PARAM KRY	: STORE_ID
	*/
	public function actionListUuid()
	{
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$modelStore		= Store::find()->where(['STORE_ID'=>$storeId])->one();
		if($modelStore){
			$modelStoreUUID=$modelStore['UUID'];
			$modelStorId=$modelStore['STORE_ID'];
			$modelStorNm=$modelStore['STORE_NM'];
			$modelPerangkatUuid=$modelStore['storeUuid'];
				$explodeUUid = explode(',',$modelStoreUUID);
				foreach ($explodeUUid as $row){
					if ($row <> 'undefined' AND $row <> ''){
					$aryUuid[]=$row;
					}				
				};			
			$dataUuid=[
				'STORE_ID'=>$modelStorId,
				'STORE_NM'=>$modelStorNm,
				'UUID'=>$aryUuid,
				'PERANGKAT_UUID'=>$modelPerangkatUuid
			];
			return array('LIST_UUID'=>$dataUuid);
		}else{
			return array('result'=>'Store-uuid-Not-Exist');
		};			
		
	}
	/* ================================
	 * === Update Setting Perangkat ===
	 * ================================
	 * URL 		: http://production.kontrolgampang.com/pembayaran/store-kasirs/ganti-perangkat
	 * METHODE	: POST
	 * Key		: STORE_ID, KASIR_ID
	 * Param	: PERANGKAT_UUID,ACCESS_ID
	**/
    public function actionGantiPerangkat(){
		
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';		
		$kasirId		= isset($paramsBody['KASIR_ID'])!=''?$paramsBody['KASIR_ID']:'';	
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$perangkatUuid	= isset($paramsBody['PERANGKAT_UUID'])!=''?$paramsBody['PERANGKAT_UUID']:'';
		$dataHeader=explode('.',$storeId);
		$modelStoreKasir= StorePerangkatKasir::find()->where([
			'KASIR_ID'=>$kasirId,
			'STORE_ID'=>$storeId,
			'ACCESS_GROUP'=>$dataHeader[0],
		])->one();
		if($modelStoreKasir){
			//$modelMerchant->BANK_NM='ok zone1';
			if ($accessID!=''){$modelStoreKasir->UPDATE_BY=$accessID;};
			if ($perangkatUuid!=''){$modelStoreKasir->PERANGKAT_UUID=$perangkatUuid;};
			if ($modelStoreKasir->save()){
				$modelView=StorePerangkatKasir::find()->where([
					'KASIR_ID'=>$kasirId,
					'STORE_ID'=>$storeId,
					'ACCESS_GROUP'=>$dataHeader[0],
				])->one();			
				return array('STORE_KASIR'=>$modelView);
			}else{
				return array('result'=>$modelStoreKasir->errors);
			}
		}else{
			return array('result'=>'KasirId-Not-Exist');
		};			
	}
	
	/* ===============================================
	 * === Update Paket & Metode Bayar & Autodebet ===
	 * ===============================================
	 * URL 		: http://production.kontrolgampang.com/pembayaran/store-kasirs/setting-pembayaran
	 * METHODE	: POST
	 * Key		: STORE_ID, KASIR_ID
	 * Param	: DOMPET_AUTODEBET, PAYMENT_METHODE,PAKET_ID,ACCESS_ID
	**/
    public function actionSettingPembayaran(){		
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$storeId		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';		
		$kasirId		= isset($paramsBody['KASIR_ID'])!=''?$paramsBody['KASIR_ID']:'';	
		$sutodebet		= isset($paramsBody['DOMPET_AUTODEBET'])!=''?$paramsBody['DOMPET_AUTODEBET']:'';
		$payMethode		= isset($paramsBody['PAYMENT_METHODE'])!=''?$paramsBody['PAYMENT_METHODE']:'';
		$paketId		= isset($paramsBody['PAKET_ID'])!=''?$paramsBody['PAKET_ID']:'';
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		
		$dataHeader=explode('.',$storeId);
		$modelStoreKasir= StorePerangkatKasir::find()->where([
			'KASIR_ID'=>$kasirId,
			'STORE_ID'=>$storeId,
			'ACCESS_GROUP'=>$dataHeader[0],
		])->one();
		if($modelStoreKasir){
			//$modelMerchant->BANK_NM='ok zone1';
			if ($accessID!=''){$modelStoreKasir->UPDATE_BY=$accessID;};
			if ($sutodebet!=''){$modelStoreKasir->DOMPET_AUTODEBET=$sutodebet;};
			if ($payMethode!=''){$modelStoreKasir->PAYMENT_METHODE=$payMethode;};
			if ($paketId!=''){$modelStoreKasir->PAKET_ID=$paketId;};
			if ($modelStoreKasir->save()){
				$modelView=StorePerangkatKasir::find()->where([
					'KASIR_ID'=>$kasirId,
					'STORE_ID'=>$storeId,
					'ACCESS_GROUP'=>$dataHeader[0],
				])->one();			
				return array('STORE_KASIR'=>$modelView);
			}else{
				return array('result'=>$modelStoreKasir->errors);
			}
		}else{
			return array('result'=>'KasirId-Not-Exist');
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
    
	
	
	
	
