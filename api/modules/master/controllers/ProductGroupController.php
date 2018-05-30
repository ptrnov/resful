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

use api\modules\master\models\ProductGroup;
use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: GROUP PRODUCT PER-STORE.
  * URL			: http://production.kontrolgampang.com/master/product-groups
  * Body Param	: STORE_ID(key Master)
 */
class ProductGroupController extends ActiveController
{	

    public $modelClass = 'api\modules\login\models\ProductGroup';

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
		$groupID		= isset($paramsBody['GROUP_ID'])!=''?$paramsBody['GROUP_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		//PROPERTY
		$prdGrpNm		= isset($paramsBody['GROUP_NM'])!=''?$paramsBody['GROUP_NM']:'';
		$prdGrpNote		= isset($paramsBody['NOTE'])!=''?$paramsBody['NOTE']:'';
		
		//POLING SYNC nedded ACCESS_ID
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		= isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='GET'){
			/**
			  * @author 	: ptrnov  <piter@lukison.com>
			  * @since 		: 1.2
			  * Subject		: ALL GROUP PRODUCT PER-STORE.
			  * Metode		: POST (VIEW)
			  * URL			: http://production.kontrolgampang.com/master/product-groups
			  * Body Param	: METHODE=GET & STORE_ID(Key) or  GROUP_ID(key) 
			  *				: GROUP_ID='' maka semua prodak group pada STORE_ID di tampilkan.
			  *				: GROUP_ID<>'' maka yang di tampilkan satu product Group.
			 */
			if($groupID<>''){				
				//Model Per-Product
				$modelCnt= ProductGroup::find()->where(['GROUP_ID'=>$groupID])->count();									
				if($modelCnt){
					$dataHeader=explode('.',$groupID);
					if ($tblPooling=='TBL_PRODUCT_GROUP'){						
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_PRODUCT_GROUP',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$store_id,
							 'PRIMARIKEY_VAL'=>$groupID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_PRODUCT_GROUP',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$groupID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan AND $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}				
					}
					$model= ProductGroup::find()->where(['GROUP_ID'=>$groupID])->one();
					return array('LIST_PRODUCT_GROUP'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}else{
				//Model All Product per-STORE
				//Model
				$modelCnt= ProductGroup::find()->where(['STORE_ID'=>$store_id])->count();
				$model= ProductGroup::find()->where(['STORE_ID'=>$store_id])->all();		
				
				if($modelCnt){			
					return array('LIST_PRODUCT_GROUP'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}			
		}elseif($metode=='POST'){
			/**
			* @author 	: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: ALL GROUP PRODUCT PER-STORE.
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/master/product-groups
			* Body Param	: METHODE=POST & STORE_ID(Key) or  GROUP_ID(key) 
			* PROPERTY		: GROUP_NM,NOTE
			*/
			if($store_id<>''){
				 $modelNew = new ProductGroup();
				 //$modelNew->CREATE_AT=date('Y-m-d H:i:s'); //AUTO GENERATE
				 $modelNew->STORE_ID=$store_id;
				 if ($prdGrpNm<>''){$modelNew->GROUP_NM=$prdGrpNm;};
				 if ($prdGrpNote<>''){$modelNew->NOTE=$prdGrpNote;};
				 if($modelNew->save()){
					$rsltMax=ProductGroup::find()->where(['STORE_ID'=>$store_id])->max('GROUP_ID');
					$modelView=ProductGroup::find()->where(['GROUP_ID'=>$rsltMax])->one();
					return array('LIST_PRODUCT_GROUP'=>$modelView);
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
		* @author 		: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: ALL GROUP PRODUCT PER-STORE.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/master/product-groups
		* Body Param	: GROUP_ID(key) 
		* PROPERTY		: GROUP_NM,NOTE,STATUS
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		$groupID		= isset($paramsBody['GROUP_ID'])!=''?$paramsBody['GROUP_ID']:'';
		//PROPERTY
		$prdGrpNm		= isset($paramsBody['GROUP_NM'])!=''?$paramsBody['GROUP_NM']:'';
		$prdGrpNote		= isset($paramsBody['NOTE'])!=''?$paramsBody['NOTE']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		if($groupID<>''){
			$modelEdit = ProductGroup::find()->where(['GROUP_ID'=>$groupID])->one();
			if($modelEdit){
				 if ($prdGrpNm<>''){$modelEdit->GROUP_NM=$prdGrpNm;};
				 if ($prdGrpNote<>''){$modelEdit->NOTE=$prdGrpNote;};
				 if ($stt<>''){$modelEdit->STATUS=$stt;};
				 
				 if($modelEdit->save()){
					$modelView=ProductGroup::find()->where(['GROUP_ID'=>$groupID])->one();
					return array('LIST_PRODUCT_GROUP'=>$modelView);
				 }else{
					return array('result'=>$modelEdit->errors);
				 }
			 }else{
				 return array('result'=>'GROUP_ID-not-exist');
			 }
		}else{
			return array('result'=>'GROUP_ID-cannot-be-blank');
		}
	}
}


