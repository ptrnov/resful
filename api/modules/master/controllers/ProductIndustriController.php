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

use api\modules\master\models\Industri;
use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: PRODUCT INDUSTRI ALL APP.
  * URL			: http://production.kontrolgampang.com/master/product-industris
  * Body Param	: INDUSTRY_ID(key Master)
 */
class ProductIndustriController extends ActiveController
{	

    public $modelClass = 'api\modules\login\models\Industri';

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
		$industriId		= isset($paramsBody['INDUSTRY_ID'])!=''?$paramsBody['INDUSTRY_ID']:'';
		$industriGrpId	= isset($paramsBody['INDUSTRY_GRP_ID'])!=''?$paramsBody['INDUSTRY_GRP_ID']:'';
		//PROPERTY
		$industriNm		= isset($paramsBody['INDUSTRY_NM'])!=''?$paramsBody['INDUSTRY_NM']:'';
		
		//==POLING SYNC ===
		$accessID		=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='GET'){
			/**
			  * @author 	: ptrnov  <piter@lukison.com>
			  * @since 		: 1.2
			  * Subject		: ALL INDUSTRI.
			  * Metode		: POST (VIEW)
			  * URL			: http://production.kontrolgampang.com/master/product-industris
			  * Body Param	: METHODE=GET & INDUSTRY_ID(key Master)
			  *				  INDUSTRY_ID=='' THEN SHOW ALL INDUSTRY
			  *				  INDUSTRY_ID<>'' THEN SHOW INDUSTRI  GROUP WHERE INDUSTRY_ID
			  * SUPPORT 	: http://production.kontrolgampang.com/master/product-industri-groups
			 */
			if($industriGrpId<>''){
				if($industriId<>''){				
					//Model Per-INDUSTRI
					$modelCnt= Industri::find()->where(['INDUSTRY_GRP_ID'=>$industriGrpId,'INDUSTRY_ID'=>$industriId])->count();
					$model= Industri::find()->where(['INDUSTRY_GRP_ID'=>$industriGrpId,'INDUSTRY_ID'=>$industriId])->one();				
					if($modelCnt){
						/*===========================
						 *=== POLLING UPDATE UUID ===
						 *===========================
						*/
						if ($tblPooling=='TBL_PRODUCT_INDUSTRI'){
							$modelPoling=SyncPoling::find()->where([
								 'NM_TABLE'=>'TBL_PRODUCT_INDUSTRI',
								 'ACCESS_GROUP'=>'',
								 'STORE_ID'=>'',
								 'PRIMARIKEY_VAL'=>$industriId
							])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
							//==UPDATE DATA POLLING UUID
							if($modelPoling){							
								foreach($modelPoling as $row => $val){
									$modelSimpan=SyncPoling::find()->where([
										 'NM_TABLE'=>'TBL_PRODUCT_INDUSTRI',
										 'ACCESS_GROUP'=>'',
										 'STORE_ID'=>'',
										 'PRIMARIKEY_VAL'=>$industriId,
										 'TYPE_ACTION'=>$val->TYPE_ACTION
									])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
									if($modelSimpan AND $paramlUUID){
										$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
										$modelSimpan->save();
									}
								}							
							}
						}
						return array('LIST_INDUSTRI'=>$model);
					}else{
						return array('result'=>'data-empty');
					}		
				}else{
					//Model All INDUSTRI
					//Model
					$modelCnt= Industri::find()->where(['INDUSTRY_GRP_ID'=>$industriGrpId])->count();
					$model= Industri::find()->where(['INDUSTRY_GRP_ID'=>$industriGrpId])->all();		
					
					if($modelCnt){			
						return array('LIST_INDUSTRI'=>$model);
					}else{
						return array('result'=>'data-empty');
					}		
				}
			}else{
				if($industriId<>''){				
					//Model Per-INDUSTRI
					$modelCnt= Industri::find()->where(['INDUSTRY_ID'=>$industriId])->count();
					$model= Industri::find()->where(['INDUSTRY_ID'=>$industriId])->one();				
					if($modelCnt){
						/*===========================
						 *=== POLLING UPDATE UUID ===
						 *===========================
						*/
						if ($tblPooling=='TBL_PRODUCT_INDUSTRI'){
							$modelPoling=SyncPoling::find()->where([
								 'NM_TABLE'=>'TBL_PRODUCT_INDUSTRI',
								 'ACCESS_GROUP'=>'',
								 'STORE_ID'=>'',
								 'PRIMARIKEY_VAL'=>$industriId
							])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
							//==UPDATE DATA POLLING UUID
							if($modelPoling){							
								foreach($modelPoling as $row => $val){
									$modelSimpan=SyncPoling::find()->where([
										 'NM_TABLE'=>'TBL_PRODUCT_INDUSTRI',
										 'ACCESS_GROUP'=>'',
										 'STORE_ID'=>'',
										 'PRIMARIKEY_VAL'=>$industriId,
										 'TYPE_ACTION'=>$val->TYPE_ACTION
									])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
									if($modelSimpan AND $paramlUUID){
										$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
										$modelSimpan->save();
									}
								}							
							}
						}
						return array('LIST_INDUSTRI'=>$model);
					}else{
						return array('result'=>'data-empty');
					}		
				}else{
					//Model All INDUSTRI
					//Model
					$modelCnt= Industri::find()->count();
					$model= Industri::find()->all();		
					
					if($modelCnt){			
						return array('LIST_INDUSTRI'=>$model);
					}else{
						return array('result'=>'data-empty');
					}		
				}			
			}
		}elseif($metode=='POST'){
			/**
			* @author 	: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: ALL INDUSTRI PRODUCT.
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/master/product-industris
			* Body Param	: METHODE=POST & NDUSTRY_ID(key Master) OR INDUSTRY_GRP_ID(key)
			* PROPERTY		: INDUSTRY_NM
			* SUPPORT 		: http://production.kontrolgampang.com/master/product-industri-groups
			*/
			$modelNew = new Industri();
			$modelNew->CREATE_AT=date('Y-m-d H:i:s');
			if ($industriNm<>''){$modelNew->INDUSTRY_NM=strtoupper($industriNm);};
			if ($industriGrpId<>''){$modelNew->INDUSTRY_GRP_ID=strtoupper($industriGrpId);};
			if($modelNew->save()){
				return array('LIST_INDUSTRI'=>$modelNew);
			}else{
				return array('result'=>$modelNew->errors);
			}
		}else{
			return array('result'=>'Methode-Unknown');
		}		
	}
}


