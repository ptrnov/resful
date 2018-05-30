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

use api\modules\master\models\MerchantType;
use api\modules\master\models\SyncPoling;
use api\modules\login\models\User;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: MERCHANT TYPE
  * Metode		: POST (Views)
  * URL			: http://production.kontrolgampang.com/master/merchant-types
  * Body Param	: No Field
 */
class MerchantTypeController extends ActiveController
{	
    //public $modelClass = 'common\models\User';
    public $modelClass = 'api\modules\login\models\MerchantType';

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
		* @author 	: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: MERCHANT TYPE
		* Metode		: POST (GET)
		* URL			: http://production.kontrolgampang.com/master/merchant-types
		* Body Param	: METHODE[GET/POST],TYPE_PAY_ID,TYPE_PAY_NM,DCRP_DETIL,STATUS,ACCESS_ID,UUID
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$typeId			= isset($paramsBody['TYPE_PAY_ID'])!=''?$paramsBody['TYPE_PAY_ID']:'';		
		$typeNm			= isset($paramsBody['TYPE_PAY_NM'])!=''?$paramsBody['TYPE_PAY_NM']:'';		
		$dcrip			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';		
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';		
		
		
		//==POLING SYNC ===
		$accessID		=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='GET'){
			//==GET DATA POLLING
			$modelPoling=SyncPoling::find()->where([
				'NM_TABLE'=>'TBL_MERCHANT_TYPE',
				 'ACCESS_GROUP'=>'',
				 'STORE_ID'=>'',
				 'PRIMARIKEY_VAL'=>$typeId
			])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
			//==UPDATE DATA POLLING UUID
			if($modelPoling){							
				foreach($modelPoling as $row => $val){
					$modelSimpan=SyncPoling::find()->where([
						 'NM_TABLE'=>'TBL_MERCHANT_TYPE',
						 'ACCESS_GROUP'=>'',
						 'STORE_ID'=>'',
						 'PRIMARIKEY_VAL'=>$typeId,
						 'TYPE_ACTION'=>$val->TYPE_ACTION
					])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
					if($modelSimpan AND $paramlUUID){
						$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
						$modelSimpan->save();
					}
				}							
			}
			IF ($typeId<>''){
				$modelView=MerchantType::find()->where(['TYPE_PAY_ID'=>$typeId])->one();
			}else{
				$modelView=MerchantType::find()->all();
			}			
			return array('MERCHANT_TYPE'=>$modelView);
			
		}elseif($metode=='POST'){
			 $modelNew = new MerchantType();
			 $modelNew->CREATE_AT=date('Y-m-d H:i:s');			 
			 if ($typeNm<>''){$modelNew->TYPE_PAY_NM=$typeNm;};
			 if ($dcrip<>''){$modelNew->DCRP_DETIL=$dcrip;};
			 if ($stt<>''){$modelNew->STATUS=$stt;};
			 if ($accessID<>''){$modelNew->CREATE_BY=$accessID;};
			 if ($paramlUUID<>''){$modelNew->CREATE_UUID=$paramlUUID;};
			 if($modelNew->save()){
				$rsltMax=MerchantType::find()->max('TYPE_PAY_ID');
				$modelView=MerchantType::find()->where(['TYPE_PAY_ID'=>$rsltMax])->one();
				return array('LIST_TYPE_PAY'=>$modelView);
			 }else{
				return array('result'=>$modelNew->errors);
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
		* Subject		: MERCHANT TYPE
		* Metode		: PUT
		* URL			: http://production.kontrolgampang.com/master/merchant-types
		* Body Param	: METHODE[GET/POST],TYPE_PAY_ID,TYPE_PAY_NM,DCRP_DETIL,STATUS,ACCESS_ID,UUID
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$typeId			= isset($paramsBody['TYPE_PAY_ID'])!=''?$paramsBody['TYPE_PAY_ID']:'';		
		$typeNm			= isset($paramsBody['TYPE_PAY_NM'])!=''?$paramsBody['TYPE_PAY_NM']:'';		
		$dcrip			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';		
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';		
		
		
		//==POLING SYNC ===
		$accessID		=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($typeId<>''){
			$modelEdit = MerchantType::find()->where(['TYPE_PAY_ID'=>$typeId])->one();
			if($modelEdit){
				 if ($typeNm<>''){$modelEdit->TYPE_PAY_NM=$typeNm;};
				 if ($dcrip<>''){$modelEdit->DCRP_DETIL=$dcrip;};
				 if ($stt<>''){$modelEdit->STATUS=$stt;};
				 if ($accessID<>''){$modelEdit->UPDATE_BY=$accessID;};
				 if ($paramlUUID<>''){$modelEdit->CREATE_UUID=$paramlUUID;};
				 if ($paramlUUID<>''){$modelEdit->UPDATE_UUID=$paramlUUID;};
				 if($modelEdit->save()){
					$modelView=MerchantType::find()->where(['TYPE_PAY_ID'=>$typeId])->one();
					return array('LIST_TYPE_PAY'=>$modelView);
				 }else{
					return array('result'=>$modelEdit->errors);
				 }
			 }else{
				 return array('result'=>'TYPE_PAY_ID-not-exist');
			 }
		}else{
			return array('result'=>'TYPE_PAY_ID-cannot-be-blank');
		}
	}
}


