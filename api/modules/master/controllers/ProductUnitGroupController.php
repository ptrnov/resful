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

use api\modules\master\models\ProductUnitGroup;
use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: PRODUCT UNIT GROUP ALL APP.
  * URL			: http://production.kontrolgampang.com/master/product-unit-groups
  * Body Param	: UNIT_ID_GRP(key Master)
 */
class ProductUnitGroupController extends ActiveController
{	

    public $modelClass = 'api\modules\login\models\ProductUnitGroup';

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
		$unitIdGrp		= isset($paramsBody['UNIT_ID_GRP'])!=''?$paramsBody['UNIT_ID_GRP']:'';
		//PROPERTY
		$unitGrpNm		= isset($paramsBody['UNIT_NM_GRP'])!=''?$paramsBody['UNIT_NM_GRP']:'';
		$unitGrpNote	= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['NOTE']:'';
		
		//POLING SYNC nedded ACCESS_ID
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		= isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='GET'){
			/**
			  * @author 	: ptrnov  <piter@lukison.com>
			  * @since 		: 1.2
			  * Subject		: ALL UNIT GROUP PRODUCT.
			  * Metode		: POST (VIEW)
			  * URL			: http://production.kontrolgampang.com/master/product-unit-groups
			  * Body Param	: METHODE=GET & UNIT_ID_GRP(key Master)
			  *				  UNIT_ID_GRP=='' THEN SHOW ALL UNIT GROUP
			  *				  UNIT_ID_GRP<>'' THEN SHOW UNIT GROUP WHERE UNIT_ID_GRP
			 */
			if($unitIdGrp<>''){				
				//Model Per-UNIT GROUP
				$modelCnt= ProductUnitGroup::find()->where(['UNIT_ID_GRP'=>$unitIdGrp])->count();
				$model= ProductUnitGroup::find()->where(['UNIT_ID_GRP'=>$unitIdGrp])->one();				
				if($modelCnt){
					/*===========================
					 *=== POLLING UPDATE UUID ===
					 *===========================
					*/
					if ($tblPooling=='TBL_PRODUCT_UNITGROUP'){	
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_PRODUCT_UNITGROUP',
							 'ACCESS_GROUP'=>'',
							 'STORE_ID'=>'',
							 'PRIMARIKEY_VAL'=>$unitIdGrp
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_PRODUCT_UNITGROUP',
									 'ACCESS_GROUP'=>'',
									 'STORE_ID'=>'',
									 'PRIMARIKEY_VAL'=>$unitIdGrp,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan AND $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}
					}
					return array('LIST_PRODUCT_UNIT-GROUP'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}else{
				//Model All UNIT GROUP
				//Model
				$modelCnt= ProductUnitGroup::find()->count();
				$model= ProductUnitGroup::find()->all();		
				
				if($modelCnt){			
					return array('LIST_PRODUCT_UNIT-GROUP'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}
			
		}elseif($metode=='POST'){
			/**
			* @author 	: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: ALL UNIT GROUP PRODUCT.
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/master/product-unit-groups
			* Body Param	: METHODE=POST
			* PROPERTY		: UNIT_NM_GRP,DCRP_DETIL
			*/
			 $modelNew = new ProductUnitGroup();
			 if ($unitGrpNm<>''){$modelNew->UNIT_NM_GRP=strtoupper($unitGrpNm);};
			 if ($unitNote<>''){$modelNew->DCRP_DETIL=$unitNote;};
			  if($modelNew->save()){
				return array('LIST_PRODUCT_UNIT-GROUP'=>$modelNew);
			 }else{
				return array('result'=>$modelNew->errors);
			 }
		}else{
			return array('result'=>'Methode-Unknown');
		}		
	}
}


