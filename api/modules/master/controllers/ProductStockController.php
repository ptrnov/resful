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

use api\modules\master\models\ProductStock;
use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: PRODUCT STOCK.
  * URL			: http://production.kontrolgampang.com/master/product-stocks
 */
class ProductStockController extends ActiveController
{	

    public $modelClass = 'api\modules\login\models\ProductStock';

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
		$paramsBody 	= Yii::$app->request->bodyParams;
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';		
		//KEY MASTER		
		$id				= isset($paramsBody['ID'])!=''?$paramsBody['ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$productId		= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		//PROPERTY 
		$lastStock		= isset($paramsBody['LAST_STOCK'])!=''?$paramsBody['LAST_STOCK']:'';
		$tglInput		= isset($paramsBody['INPUT_DATE'])!=''?$paramsBody['INPUT_DATE']:'';
		$JamInput		= isset($paramsBody['INPUT_TIME'])!=''?$paramsBody['INPUT_TIME']:'';
		$stockInput		= isset($paramsBody['INPUT_STOCK'])!=''?$paramsBody['INPUT_STOCK']:'';
		$tglBerjalan	= isset($paramsBody['CURRENT_DATE'])!=''?$paramsBody['CURRENT_DATE']:'';
		$jamBerjalan	= isset($paramsBody['CURRENT_TIME'])!=''?$paramsBody['CURRENT_TIME']:'';
		$stockBerjalan	= isset($paramsBody['CURRENT_STOCK'])!=''?$paramsBody['CURRENT_STOCK']:'';
		$sisaStock		= isset($paramsBody['SISA_STOCK'])!=''?$paramsBody['SISA_STOCK']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$note			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$paramlUUID		= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		//POLING SYNC nedded ACCESS_ID
		$accessID=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: PRODUCT STOCK.
			* Metode		: POST (VIEW) 
			* URL			: http://production.kontrolgampang.com/master/product-stocks
			* Body Param	: METHODE=GET & STORE_ID(key Master),PRODUCT_ID(key Master)
			*				: STORE_ID='' maka semua prodak stock pada STORE_ID di tampilkan.
			*				: PRODUCT_ID<>'' maka yang di tampilkan satu product id.
			*/
			if($store_id<>''){	
				if($productId<>''){	
					$modelCnt= ProductStock::find()->where(['STORE_ID'=>$store_id,'PRODUCT_ID'=>$productId])->count();
					if($modelCnt){
						if ($id){
							$model= ProductStock::find()->where(['STORE_ID'=>$store_id,'PRODUCT_ID'=>$productId,'ID'=>$id])->one();		
								if ($tblPooling=='TBL_STOCK'){						
									//==GET DATA POLLING
									$dataHeader=explode('.',$productId);
									$modelPoling=SyncPoling::find()->where([
										 'NM_TABLE'=>'TBL_STOCK',
										 'ACCESS_GROUP'=>$dataHeader[0],
										 'STORE_ID'=>$store_id,
										 'PRIMARIKEY_VAL'=>$productId,
										 'PRIMARIKEY_ID'=>$id
									])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
									//==UPDATE DATA POLLING UUID
									if($modelPoling){							
										foreach($modelPoling as $row => $val){
											$modelSimpan=SyncPoling::find()->where([
												 'NM_TABLE'=>'TBL_STOCK',
												 'ACCESS_GROUP'=>$dataHeader[0],
												 'STORE_ID'=>$store_id,
												 'PRIMARIKEY_VAL'=>$productId,
												 'PRIMARIKEY_ID'=>$id,
												 'TYPE_ACTION'=>$val->TYPE_ACTION
											])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
											if($modelSimpan AND $paramlUUID){
												$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
												$modelSimpan->save();
											}
										}							
									}
								}
							return array('LIST_PRODUCT_STOCK'=>$model);							
						}else{						
							$model= ProductStock::find()->where(['STORE_ID'=>$store_id,'PRODUCT_ID'=>$productId,])->all();				
							return array('LIST_PRODUCT_STOCK'=>$model);
						
						}
					}else{
						return array('result'=>'data-empty');
					}	
											
				}else{
					//Model Produck harga Per-Product
					$modelCnt= ProductStock::find()->where(['STORE_ID'=>$store_id])->count();
					$model= ProductStock::find()->where(['STORE_ID'=>$store_id])->all();				
					if($modelCnt){
						return array('LIST_PRODUCT_STOCK'=>$model);
					}else{
						return array('result'=>'data-empty');
					}
				}
			}else{
				return array('result'=>'STORE_ID-cannot-be-blank');
			}			
		}elseif($metode=='POST'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: PRODUCT STOCK.
			* Metode		: POST (POST)
			* URL			: http://production.kontrolgampang.com/master/product-stocks
			* Body Param	: METHODE=POST & STORE_ID(key Master),PRODUCT_ID(key Master)
			* PROPERTIES	: LAST_STOCK,INPUT_DATE,INPUT_TIME,INPUT_STOCK,URRENT_DATE,CURRENT_TIME,CURRENT_STOCK,SISA_STOCK,STATUS,DCRP_DETIL
			*				  LAST_STOCK -> (ACUMULATE <INPUT_DATE & INPUT_TIME)
			*				  (CREATE) INPUT_DATE,INPUT_TIME,
			*				  INPUT_STOCK > (ACUMULATE =INPUT_DATE & INPUT_TIME)
			*				  (UPDATE) CURRENT_DATE,CURRENT_TIME,
			*				  CURRENT_STOCK,SISA_STOCK ->(stok berjalan,sisa stok berjalan)
			*				  STATUS,DCRP_DETIL
			*/
			if($store_id<>''){
				if($productId<>''){
					$modelNew = new ProductStock();
					$modelNew->scenario='create';
					$modelNew->PRODUCT_ID=$productId;
					//STOCK LALU
					if ($lastStock<>''){$modelNew->LAST_STOCK=$lastStock;};
					//STOCK SEKARANG
					if ($tglInput<>''){$modelNew->INPUT_DATE=date("Y-m-d", strtotime($tglInput));}; 
					if ($JamInput<>''){$modelNew->INPUT_TIME=date("H:i:s", strtotime($JamInput));};
					if ($stockInput<>''){$modelNew->INPUT_STOCK=$stockInput;};
					//STOCK BERJALAN
					if ($tglBerjalan<>''){$modelNew->CURRENT_DATE=date("Y-m-d", strtotime($tglBerjalan));}; 
					if ($jamBerjalan<>''){$modelNew->CURRENT_TIME=date("H:i:s", strtotime($jamBerjalan));};					
					if ($stockBerjalan<>''){$modelNew->CURRENT_STOCK=$stockBerjalan;};
					if ($sisaStock<>''){$modelNew->SISA_STOCK=$sisaStock;};
					//GENERAL
					if ($stt<>''){$modelNew->STATUS=$stt;};
					if ($note<>''){$modelNew->DCRP_DETIL=$note;};
					if ($paramlUUID<>''){$modelNew->CREATE_UUID=$paramlUUID;};
				    if ($accessID<>''){$modelNew->CREATE_BY=$accessID;};
				 
					if($modelNew->save()){
						$rsltMax=ProductStock::find()->where(['PRODUCT_ID'=>$productId])->max('ID');
						$modelView=ProductStock::find()->where(['ID'=>$rsltMax])->one();
						return array('LIST_PRODUCT_STOCK'=>$modelView);
					}else{
						return array('result'=>$modelNew->errors);
					}
				}else{
					return array('result'=>'PRODUCT_ID-cannot-be-blank');
				}
			}else{
				return array('result'=>'STORE_ID-cannot-be-blank');
			}
		}else{
			return array('result'=>'Methode-Unknown');
		}		
	}
	
	public function actionUpdate()
    {  	
		/**
		* @author 		: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: PRODUCT STOCK.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/master/product-stocks
		* PROPERTIES	: LAST_STOCK,INPUT_DATE,INPUT_TIME,INPUT_STOCK,URRENT_DATE,CURRENT_TIME,CURRENT_STOCK,SISA_STOCK,STATUS,DCRP_DETIL
		*				  LAST_STOCK -> (ACUMULATE <INPUT_DATE & INPUT_TIME)
		*				  (CREATE) INPUT_DATE,INPUT_TIME,
		*				  INPUT_STOCK > (ACUMULATE =INPUT_DATE & INPUT_TIME)
		*				  (UPDATE) CURRENT_DATE,CURRENT_TIME,
		*				  CURRENT_STOCK,SISA_STOCK ->(stok berjalan,sisa stok berjalan)
		*				  STATUS,DCRP_DETIL
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		//KEY MASTER		
		$id				= isset($paramsBody['ID'])!=''?$paramsBody['ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$productId		= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		//PROPERTY 
		$lastStock		= isset($paramsBody['LAST_STOCK'])!=''?$paramsBody['LAST_STOCK']:'';
		$tglInput		= isset($paramsBody['INPUT_DATE'])!=''?$paramsBody['INPUT_DATE']:'';
		$JamInput		= isset($paramsBody['INPUT_TIME'])!=''?$paramsBody['INPUT_TIME']:'';
		$stockInput		= isset($paramsBody['INPUT_STOCK'])!=''?$paramsBody['INPUT_STOCK']:'';
		$tglBerjalan	= isset($paramsBody['CURRENT_DATE'])!=''?$paramsBody['CURRENT_DATE']:'';
		$jamBerjalan	= isset($paramsBody['CURRENT_TIME'])!=''?$paramsBody['CURRENT_TIME']:'';
		$stockBerjalan	= isset($paramsBody['CURRENT_STOCK'])!=''?$paramsBody['CURRENT_STOCK']:'';
		$sisaStock		= isset($paramsBody['SISA_STOCK'])!=''?$paramsBody['SISA_STOCK']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$note			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$paramlUUID		= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		$modelEdit = ProductStock::find()->where(['PRODUCT_ID'=>$productId,'ID'=>$id])->one();
		if($modelEdit){
			//STOCK LALU
			if ($lastStock<>''){$modelEdit->LAST_STOCK=$lastStock;};
			//STOCK SEKARANG
			if ($tglInput<>''){$modelEdit->INPUT_DATE=date("Y-m-d", strtotime($tglInput));}; 
			if ($JamInput<>''){$modelEdit->INPUT_TIME=date("H:i:s", strtotime($JamInput));};
			if ($stockInput<>''){$modelEdit->INPUT_STOCK=$stockInput;};
			//STOCK BERJALAN
			if ($tglBerjalan<>''){$modelEdit->CURRENT_DATE=date("Y-m-d", strtotime($tglBerjalan));}; 
			if ($jamBerjalan<>''){$modelEdit->CURRENT_TIME=date("H:i:s", strtotime($jamBerjalan));};					
			if ($stockBerjalan<>''){$modelEdit->CURRENT_STOCK=$stockBerjalan;};
			if ($sisaStock<>''){$modelEdit->SISA_STOCK=$sisaStock;};
			//GENERAL
			if ($stt<>''){$modelEdit->STATUS=$stt;};
			if ($note<>''){$modelEdit->DCRP_DETIL=$note;};	
			if ($paramlUUID<>''){$modelEdit->UPDATE_UUID=$paramlUUID;};
			if ($accessID<>''){$modelEdit->UPDATE_BY=$accessID;};			
			if($modelEdit->save()){
				$modelView=ProductStock::find()->where(['PRODUCT_ID'=>$productId,'ID'=>$id])->one();
				return array('LIST_PRODUCT_STOCK'=>$modelView);
			}else{
				return array('result'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'PRODUCT_ID-not-exist');
		}
	}
}


