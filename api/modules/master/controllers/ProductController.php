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

use api\modules\master\models\Product;
use api\modules\master\models\SyncPoling;
use api\modules\login\models\User;
/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: ONE PRODUCT PER-STORE.
  * Metode		: POST (update)
  * URL			: http://production.kontrolgampang.com/master/products
  * Body Param	: PRODUCT_ID(key)
 */
class ProductController extends ActiveController
{	

    public $modelClass = 'api\modules\login\models\Product';

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
		//KEY
		$productID		= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		//PROPERTY
		$prdNm			= isset($paramsBody['PRODUCT_NM'])!=''?$paramsBody['PRODUCT_NM']:'';
		$prdQr			= isset($paramsBody['PRODUCT_QR'])!=''?$paramsBody['PRODUCT_QR']:'';
		$prdGrp			= isset($paramsBody['GROUP_ID'])!=''?$paramsBody['GROUP_ID']:'';
		$prdWarna		= isset($paramsBody['PRODUCT_WARNA'])!=''?$paramsBody['PRODUCT_WARNA']:'';
		//--ukuran dalam desimal
		$prdUkuran		= isset($paramsBody['PRODUCT_SIZE'])!=''?$paramsBody['PRODUCT_SIZE']:'';
		$prdUkuranUnit	= isset($paramsBody['PRODUCT_SIZE_UNIT'])!=''?$paramsBody['PRODUCT_SIZE_UNIT']:'';		
		//--ukuran dalam desimal
		$prdUnitJual	= isset($paramsBody['UNIT_ID'])!=''?$paramsBody['UNIT_ID']:'';
		$prdHeadline	= isset($paramsBody['PRODUCT_HEADLINE'])!=''?$paramsBody['PRODUCT_HEADLINE']:'';
		$prdLevelStock	= isset($paramsBody['STOCK_LEVEL'])!=''?$paramsBody['STOCK_LEVEL']:'';
		//Industri
		$prdIndustriId	= isset($paramsBody['INDUSTRY_ID'])!=''?$paramsBody['INDUSTRY_ID']:'';
		$crnStock		= isset($paramsBody['CURRENT_STOCK'])!=''?$paramsBody['CURRENT_STOCK']:'';
		$crnHpp			= isset($paramsBody['CURRENT_HPP'])!=''?$paramsBody['CURRENT_HPP']:'';
		$crnPpn			= isset($paramsBody['CURRENT_PPN'])!=''?$paramsBody['CURRENT_PPN']:'';
		$crnPrice		= isset($paramsBody['CURRENT_PRICE'])!=''?$paramsBody['CURRENT_PRICE']:'';
		//Releatiship (check by date)
        //-harga;-Discount;-stock;-Promo
		
		//POLING SYNC nedded ACCESS_ID
		$accessID=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		$paramId=isset($paramsBody['ID'])!=''?$paramsBody['ID']:'';
		
		if($metode=='GET'){
			/**
			  * @author 	: ptrnov  <piter@lukison.com>
			  * @since 		: 1.2
			  * Subject		: ALL PRODUCT PER-STORE ATAU ONE PRODUCT (Select Key).
			  * Metode		: POST (VIEW)
			  * URL			: http://production.kontrolgampang.com/master/products
			  * Body Param	: METHODE=GET & PRODUCT_ID(key) & STORE_ID(Key)
			  *				: PRODUCT_ID='' maka semua prodak pada STORE_ID di tampilkan.
			  *				: PRODUCT_ID<>'' maka yang di tampilkan satu product.
			 */
			$dataHeader=explode('.',$productID);//0=ACCESS_GROUP;1=STORE_ID;2=PRODUCT_ID;
			if($productID<>''){		
				if($productID<>''){								//==== TBL_PRODUCT FOR UPDATE UUID ====
					if ($tblPooling=='TBL_PRODUCT'){						
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_PRODUCT',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$store_id,
							 'PRIMARIKEY_VAL'=>$productID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_PRODUCT',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$productID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan AND $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}
						//==VIEW DATA POLLING
						// $modelPolingView=SyncPoling::find()->where([
							 // NM_TABLE=>'TBL_PRODUCT',
							 // ACCESS_GROUP=>$dataHeader[0],
							 // STORE_ID=>$store_id,
							 // PRIMARIKEY_VAL=>$productID
						// ])->one();						
					}elseif($tblPooling=='TBL_STOCK'){					//===TBL_STOCK  FOR UPDATE UUID ===
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_STOCK',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$store_id,
							 'PRIMARIKEY_VAL'=>$productID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_STOCK',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$productID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan && $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}						
					}elseif($tblPooling=='TBL_SYNC_QTY'){					//===TBL_STOCK  FOR UPDATE UUID ===
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_SYNC_QTY',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$store_id,
							 'PRIMARIKEY_VAL'=>$productID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();					
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_SYNC_QTY',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$productID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan && $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}						
					}elseif($tblPooling=='TBL_SYNC_HARGA'){					//===TBL_STOCK  FOR UPDATE UUID ===
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_SYNC_HARGA',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$store_id,
							 'PRIMARIKEY_VAL'=>$productID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();					
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_SYNC_HARGA',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$productID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan && $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}						
					}else{						
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_PRODUCT',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$store_id,
							 'PRIMARIKEY_VAL'=>$productID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_PRODUCT',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$productID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan && $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}						
					} 
					$modelView= Product::find()->where(['PRODUCT_ID'=>$productID])->one();
					return array('LIST_PRODUCT'=>$modelView);
				}else{
					//Model Per-Product
					$modelCnt= Product::find()->where(['PRODUCT_ID'=>$productID])->count();
					$model= Product::find()->where(['PRODUCT_ID'=>$productID])->one();		
					
					if($modelCnt){
						return array('LIST_PRODUCT'=>$model);
					}else{
						return array('result'=>'data-empty');
					}
				}
			}else{
				//Model All Product per-STORE
				//Model
				$modelCnt= Product::find()->where(['STORE_ID'=>$store_id])->count();
				$model= Product::find()->where(['STORE_ID'=>$store_id])->all();		
				
				if($modelCnt){			
					return array('LIST_PRODUCT'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}			
		}elseif($metode=='POST'){
			/**
			* @author 	: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: ADD PRODUCT PER-STORE.
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/master/products
			* Body Param	: METHODE=POST & STORE_ID(Primary Key), GROUP_ID(key),UNIT_ID(Key),INDUSTRY_ID(key)
			* PROPERTY		: PRODUCT_NM,PRODUCT_QR,PRODUCT_WARNA,PRODUCT_SIZE,PRODUCT_SIZE_UNIT,PRODUCT_HEADLINE,STOCK_LEVEL
			* SUPPORT		: http://production.kontrolgampang.com/master/product-groups (GROUP_ID)
			*				  http://production.kontrolgampang.com/master/product-units	 (UNIT_ID)
			*				  http://production.kontrolgampang.com/master/product-industris (INDUSTRY_ID)
			*				  http://production.kontrolgampang.com/master/product-stocks 	(releationship by date)
			*				  http://production.kontrolgampang.com/master/product-discounts (releationship by date)
			*				  http://production.kontrolgampang.com/master/product-hargas    (releationship by date)
			*				  http://production.kontrolgampang.com/master/product-promos    (releationship by date)
			*/
			if($store_id<>''){
				 $modelNew = new Product();
				 $modelNew->CREATE_AT=date('Y-m-d H:i:s');
				 $modelNew->STORE_ID=$store_id;
				 if ($prdNm<>''){$modelNew->PRODUCT_NM=$prdNm;};
				 if ($prdQr<>''){$modelNew->PRODUCT_QR=$prdQr;};
				 if ($prdWarna<>''){$modelNew->PRODUCT_WARNA=$prdWarna;};
				 if ($prdUkuran<>''){$modelNew->PRODUCT_SIZE=$prdUkuran;};
				 if ($prdUkuranUnit<>''){$modelNew->PRODUCT_SIZE_UNIT=$prdUkuranUnit;};
				 if ($prdHeadline<>''){$modelNew->PRODUCT_HEADLINE=$prdHeadline;};
				 if ($prdLevelStock<>''){$modelNew->STOCK_LEVEL=$prdLevelStock;};
				 if ($prdGrp<>''){$modelNew->GROUP_ID=$prdGrp;};
				 if ($prdUnitJual<>''){$modelNew->UNIT_ID=$prdUnitJual;};
				 if ($prdIndustriId<>''){$modelNew->INDUSTRY_ID=$prdIndustriId;};
				 if ($crnStock<>''){$modelNew->CURRENT_STOCK=$crnStock;};
				 if ($crnHpp<>''){$modelNew->CURRENT_HPP=$crnHpp;};
				 if ($crnPpn<>''){$modelNew->CURRENT_PPN=$crnPpn;};
				 if ($crnPrice<>''){$modelNew->CURRENT_PRICE=$crnPrice;};
				 if ($paramlUUID<>''){$modelNew->CREATE_UUID=$paramlUUID;};
				 if ($accessID<>''){$modelNew->CREATE_BY=$accessID;};
				 if($modelNew->save()){
					$rsltMax=Product::find()->where(['STORE_ID'=>$store_id])->max('PRODUCT_ID');
					$modelView=Product::find()->where(['PRODUCT_ID'=>$rsltMax])->one();
					return array('LIST_PRODUCT'=>$modelView);
				 }else{
					return array('result'=>$modelNew->errors);
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
		* @author 	: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: UPDATE PRODUCT.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/master/products
		* Body Param	: PRODUCT_ID(Primary Key), GROUP_ID(key),UNIT_ID(Key),INDUSTRY_ID(key)
		* PROPERTY		: PRODUCT_NM,PRODUCT_QR,PRODUCT_WARNA,PRODUCT_SIZE,PRODUCT_SIZE_UNIT,PRODUCT_HEADLINE,STOCK_LEVEL,STATUS
		* SUPPORT		: http://production.kontrolgampang.com/master/product-groups (GROUP_ID)
		*				  http://production.kontrolgampang.com/master/product-units	 (UNIT_ID)
		*				  http://production.kontrolgampang.com/master/product-industris (INDUSTRY_ID)
		*				  http://production.kontrolgampang.com/master/product-stocks 	(releationship by date)
		*				  http://production.kontrolgampang.com/master/product-discounts (releationship by date)
		*				  http://production.kontrolgampang.com/master/product-hargas    (releationship by date)
		*				  http://production.kontrolgampang.com/master/product-promos    (releationship by date)
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		//KEY
		$productID		= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		//PROPERTY
		$prdNm			= isset($paramsBody['PRODUCT_NM'])!=''?$paramsBody['PRODUCT_NM']:'';
		$prdQr			= isset($paramsBody['PRODUCT_QR'])!=''?$paramsBody['PRODUCT_QR']:'';
		$prdGrp			= isset($paramsBody['GROUP_ID'])!=''?$paramsBody['GROUP_ID']:'';
		$prdWarna		= isset($paramsBody['PRODUCT_WARNA'])!=''?$paramsBody['PRODUCT_WARNA']:'';
		//--ukuran dalam desimal
		$prdUkuran		= isset($paramsBody['PRODUCT_SIZE'])!=''?$paramsBody['PRODUCT_SIZE']:'';
		$prdUkuranUnit	= isset($paramsBody['PRODUCT_SIZE_UNIT'])!=''?$paramsBody['PRODUCT_SIZE_UNIT']:'';		
		//--ukuran dalam desimal
		$prdUnitJual	= isset($paramsBody['UNIT_ID'])!=''?$paramsBody['UNIT_ID']:'';
		$prdHeadline	= isset($paramsBody['PRODUCT_HEADLINE'])!=''?$paramsBody['PRODUCT_HEADLINE']:'';
		$prdLevelStock	= isset($paramsBody['STOCK_LEVEL'])!=''?$paramsBody['STOCK_LEVEL']:'';
		//Industri
		$prdIndustriId	= isset($paramsBody['INDUSTRY_ID'])!=''?$paramsBody['INDUSTRY_ID']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$crnStock		= isset($paramsBody['CURRENT_STOCK'])!=''?$paramsBody['CURRENT_STOCK']:'';
		$crnHpp			= isset($paramsBody['CURRENT_HPP'])!=''?$paramsBody['CURRENT_HPP']:'';
		$crnPpn			= isset($paramsBody['CURRENT_PPN'])!=''?$paramsBody['CURRENT_PPN']:'';		
		$crnPrice		= isset($paramsBody['CURRENT_PRICE'])!=''?$paramsBody['CURRENT_PRICE']:'';
		
		
		//POLLING & LOG SYSTEM
		$paramlUUID=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		$accessID=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		//Releatiship (check by date)
        //-harga;-Discount;-stock;-Promo
		if($productID<>''){
			$modelEdit = Product::find()->where(['PRODUCT_ID'=>$productID])->one();
			if($modelEdit){
				 if ($prdNm<>''){$modelEdit->PRODUCT_NM=$prdNm;};
				 if ($prdQr<>''){$modelEdit->PRODUCT_QR=$prdQr;};
				 if ($prdWarna<>''){$modelEdit->PRODUCT_WARNA=$prdWarna;};
				 if ($prdUkuran<>''){$modelEdit->PRODUCT_SIZE=$prdUkuran;};
				 if ($prdUkuranUnit<>''){$modelEdit->PRODUCT_SIZE_UNIT=$prdUkuranUnit;};
				 if ($prdHeadline<>''){$modelEdit->PRODUCT_HEADLINE=$prdHeadline;};
				 if ($prdLevelStock<>''){$modelEdit->STOCK_LEVEL=$prdLevelStock;};
				 if ($prdGrp<>''){$modelEdit->GROUP_ID=$prdGrp;};
				 if ($prdUnitJual<>''){$modelEdit->UNIT_ID=$prdUnitJual;};
				 if ($prdIndustriId<>''){$modelEdit->INDUSTRY_ID=$prdIndustriId;};
				 if ($stt<>''){$modelEdit->STATUS=$stt;};
				 if ($crnStock<>''){$modelEdit->CURRENT_STOCK=$crnStock;};
				 if ($crnHpp<>''){$modelEdit->CURRENT_HPP=$crnHpp;};
				 if ($crnPpn<>''){$modelEdit->CURRENT_PPN=$crnPpn;};
				 if ($crnPrice<>''){$modelEdit->CURRENT_PRICE=$crnPrice;};
				 if ($paramlUUID<>''){$modelEdit->UPDATE_UUID=$paramlUUID;};
				 if ($accessID<>''){$modelEdit->UPDATE_BY=$accessID;};
				 if($modelEdit->save()){
					$modelView=Product::find()->where(['PRODUCT_ID'=>$productID])->one();
					return array('LIST_PRODUCT'=>$modelView);
				 }else{
					return array('result'=>$modelEdit->errors);
				 }
			 }else{
				 return array('result'=>'product-not-exist');
			 }
		}else{
			return array('result'=>'productID-cannot-be-blank');
		}
	}
}


