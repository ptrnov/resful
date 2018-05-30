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

use api\modules\master\models\ProductImage;


/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: IMAGE PRODUCT
  * URL			: http://production.kontrolgampang.com/master/product-images
 */
class ProductImageController extends ActiveController
{	

    public $modelClass = 'api\modules\login\models\ProductImage';

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
		$productId		= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		//PROPERTY
		$prdImage		= isset($paramsBody['PRODUCT_IMAGE'])!=''?$paramsBody['PRODUCT_IMAGE']:'';
		$prdStt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$prdNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: IMAGE PRODUCT.
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/master/product-images
			* Body Param	: METHODE=GET & STORE_ID(Key) or  PRODUCT_ID(key) 
			*				: GROUP_ID='' maka semua prodak Image pada STORE_ID di tampilkan.
			*				: GROUP_ID<>'' maka yang di tampilkan satu PRODUCT_ID.
			*/
			if($store_id<>''){				
				if($productId<>''){				
					//Model Per-Product
					$modelCnt= ProductImage::find()->where(['PRODUCT_ID'=>$productId])->count();
					$model= ProductImage::find()->where(['PRODUCT_ID'=>$productId])->one();					
					if($modelCnt){
						return array('LIST_PRODUCT_IMAGE'=>$model);
					}else{
						return array('result'=>'data-empty');
					}
				}else{
					$modelCnt= ProductImage::find()->where(['STORE_ID'=>$store_id])->count();
					$model= ProductImage::find()->where(['STORE_ID'=>$store_id])->all();						
					if($modelCnt){			
						return array('LIST_PRODUCT_IMAGE'=>$model);
					}else{
						return array('result'=>'data-empty');
					}	
				}
			}else{
				if($productId<>''){				
					//Model Per-Product
					$modelCnt= ProductImage::find()->where(['PRODUCT_ID'=>$productId])->count();
					$model= ProductImage::find()->where(['PRODUCT_ID'=>$productId])->one();					
					if($modelCnt){
						return array('LIST_PRODUCT_IMAGE'=>$model);
					}else{
						return array('result'=>'data-empty');
					}
				}else{
					return array('result'=>'STORE_ID-PRODUCT_ID-not-exist');	
				}
			}			
		}elseif($metode=='POST'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: IMAGE PRODUCT.
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/master/product-images
			* Body Param	: METHODE=POST & PRODUCT_ID(Key) or  PRODUCT_IMAGE(key) 
			* PROPERTY		: STATUS,DCRP_DETIL
			*/
			$modelCnt=ProductImage::find()->where(['PRODUCT_ID'=>$productId])->count();
			if(!$modelCnt){
				$modelNew = new ProductImage();
				$modelNew->scenario='create';
				if ($productId<>''){$modelNew->PRODUCT_ID=$productId;};
				if ($prdImage<>''){$modelNew->PRODUCT_IMAGE=$prdImage;};
				if ($prdStt<>''){$modelNew->STATUS=$prdStt;};
				if ($prdNote<>''){$modelNew->DCRP_DETIL=$prdNote;};
				if($modelNew->save()){
					$modelView=ProductImage::find()->where(['PRODUCT_ID'=>$productId])->one();
					return array('LIST_PRODUCT_GROUP'=>$modelView);
				}else{
					return array('error'=>$modelNew->errors);
				}
			}else{
				return array('result'=>'PRODUCT_ID-already-exist');
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
		* Subject		: IMAGE PRODUCT.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/master/product-images
		* Body Param	: PRODUCT_ID(Key)
		* PROPERTY		: PRODUCT_IMAGE,STATUS,DCRP_DETIL
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		//KEY
		$productId		= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		//PROPERTY
		$prdImage		= isset($paramsBody['PRODUCT_IMAGE'])!=''?$paramsBody['PRODUCT_IMAGE']:'';
		$prdStt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$prdNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		
		$modelEdit=ProductImage::find()->where(['PRODUCT_ID'=>$productId])->one();
		if($modelEdit){			
			if ($prdImage<>''){$modelEdit->PRODUCT_IMAGE=$prdImage;};
			if ($prdStt<>''){$modelEdit->STATUS=$prdStt;};
			if ($prdNote<>''){$modelEdit->DCRP_DETIL=$prdNote;};
			$modelEdit->scenario='update';
			if($modelEdit->save()){
				$modelView=ProductImage::find()->where(['PRODUCT_ID'=>$productId])->one();
				return array('LIST_PRODUCT_GROUP'=>$modelView);
			}else{
				return array('error'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'PRODUCT_ID-not-exist');
		}
	}
}


