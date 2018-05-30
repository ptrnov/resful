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

use api\modules\master\models\ProductPromo;
use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: PRODUCT PROMO.
  * URL			: http://production.kontrolgampang.com/master/product-promos
 */
class ProductPromoController extends ActiveController
{	

    public $modelClass = 'api\modules\login\models\ProductPromo';

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
		$prdtgl1		= isset($paramsBody['PERIODE_TGL1'])!=''?$paramsBody['PERIODE_TGL1']:'';
		$prdtgl2		= isset($paramsBody['PERIODE_TGL2'])!=''?$paramsBody['PERIODE_TGL2']:'';
		$prdjam			= isset($paramsBody['START_TIME'])!=''?$paramsBody['START_TIME']:'';
		$prdPromo		= isset($paramsBody['PROMO'])!=''?$paramsBody['PROMO']:'';
		$prdNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		
		//POLING SYNC nedded ACCESS_ID
		$accessID=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: PRODUCT PROMO.
			* Metode		: POST (VIEW) 
			* URL			: http://production.kontrolgampang.com/master/product-promos
			* Body Param	: METHODE=GET & STORE_ID(key Master),PRODUCT_ID(key Master)
			*				: STORE_ID='' maka semua prodak harga pada STORE_ID di tampilkan.
			*				: PRODUCT_ID<>'' maka yang di tampilkan satu product id.
			*				  In Product Controller Show Value PROMO by (PERIODE_TGL1 Between PERIODE_TGL2)
			*/
			if($store_id<>''){	
				if($productId<>''){			
					//Model Produck harga Per-Product
					$modelCnt= ProductPromo::find()->where(['STORE_ID'=>$store_id,'PRODUCT_ID'=>$productId])->count();
					//$model= ProductPromo::find()->where(['STORE_ID'=>$store_id,'PRODUCT_ID'=>$productId,])->all();				
					if($modelCnt){
						if ($id){
							$model= ProductPromo::find()->where(['STORE_ID'=>$store_id,'PRODUCT_ID'=>$productId,'ID'=>$id])->one();	
							if ($tblPooling=='TBL_PROMO'){						
								//==GET DATA POLLING
								$dataHeader=explode('.',$productId);
								$modelPoling=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_PROMO',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$productId,
									 'PRIMARIKEY_ID'=>$id
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
								//==UPDATE DATA POLLING UUID
								if($modelPoling){							
									foreach($modelPoling as $row => $val){
										$modelSimpan=SyncPoling::find()->where([
											 'NM_TABLE'=>'TBL_PROMO',
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
							return array('LIST_PRODUCT_DISCOUNT'=>$model);
						}else{						
							$model= ProductPromo::find()->where(['STORE_ID'=>$store_id,'PRODUCT_ID'=>$productId])->all();				
							return array('LIST_PRODUCT_PROMO'=>$model);						
						}
					}else{
						return array('result'=>'data-empty');
					}						
				}else{
					//Model Produck harga Per-Product
					$modelCnt= ProductPromo::find()->where(['STORE_ID'=>$store_id])->count();
					$model= ProductPromo::find()->where(['STORE_ID'=>$store_id])->all();				
					if($modelCnt){
						//return array('LIST_PRODUCT_PROMO'=>$model);
						return array('LIST_PRODUCT_PROMO'=>ArrayHelper::index($model, null, 'STORE_ID'));
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
			* Subject		: PRODUCT PROMO.
			* Metode		: POST (POST)
			* URL			: http://production.kontrolgampang.com/master/product-promos
			* Body Param	: METHODE=POST & STORE_ID(key Master),PRODUCT_ID(key Master)
			* PROPERTIES	: PERIODE_TGL1,PERIODE_TGL2,START_TIME,PROMO
			*/
			if($store_id<>''){
				if($productId<>''){
					$modelNew = new ProductPromo();
					$modelNew->scenario='create';
					$modelNew->PRODUCT_ID=$productId;
					if ($prdtgl1<>''){$modelNew->PERIODE_TGL1=date("Y-m-d", strtotime($prdtgl1));}; 
					if ($prdtgl2<>''){$modelNew->PERIODE_TGL2=date("Y-m-d", strtotime($prdtgl2));};
					if ($prdjam<>''){$modelNew->START_TIME=$prdjam;};
					if ($prdPromo<>''){$modelNew->PROMO=$prdPromo;};
					if ($prdNote<>''){$modelNew->DCRP_DETIL=$prdNote;};
					if($modelNew->save()){
						//$modelView=ProductPromo::find()->where(['PRODUCT_ID'=>$productId])->all();
						$rsltMax=ProductPromo::find()->where(['PRODUCT_ID'=>$productId])->max('ID');
						$modelView=ProductPromo::find()->where(['ID'=>$rsltMax])->one();
						return array('LIST_PRODUCT_PROMO'=>$modelView);
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
		* Subject		: PRODUCT PROMO.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/master/product-promos
		* Body Param	: PRODUCT_ID(key Master),ID(key Master)
		* PROPERTIES	: PERIODE_TGL1,PERIODE_TGL2,START_TIME,DISCOUNT
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		//KEY MASTER		
		$id				= isset($paramsBody['ID'])!=''?$paramsBody['ID']:'';
		$productId		= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		//PROPERTY 
		$prdtgl1		= isset($paramsBody['PERIODE_TGL1'])!=''?$paramsBody['PERIODE_TGL1']:'';
		$prdtgl2		= isset($paramsBody['PERIODE_TGL2'])!=''?$paramsBody['PERIODE_TGL2']:'';
		$prdjam			= isset($paramsBody['START_TIME'])!=''?$paramsBody['START_TIME']:'';
		$prdPromo		= isset($paramsBody['PROMO'])!=''?$paramsBody['PROMO']:'';
		$prdNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
	
		$modelEdit = ProductPromo::find()->where(['PRODUCT_ID'=>$productId,'ID'=>$id])->one();
		if($modelEdit){
			if ($prdtgl1<>''){$modelEdit->PERIODE_TGL1=date("Y-m-d", strtotime($prdtgl1));}; 
			if ($prdtgl2<>''){$modelEdit->PERIODE_TGL2=date("Y-m-d", strtotime($prdtgl2));};
			if ($prdjam<>''){$modelEdit->START_TIME=$prdjam;};
			if ($prdPromo<>''){$modelEdit->PROMO=$prdPromo;};
			if ($prdNote<>''){$modelEdit->DCRP_DETIL=$prdNote;};
			if ($stt<>''){$modelEdit->STATUS=$stt;};				 
			if($modelEdit->save()){
				$modelView=ProductPromo::find()->where(['PRODUCT_ID'=>$productId,'ID'=>$id])->one();
				return array('LIST_PRODUCT_PROMO'=>$modelView);
			}else{
				return array('result'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'PRODUCT_ID-not-exist');
		}
	}
}


