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

use api\modules\master\models\Store;
use api\modules\master\models\StoreMerchant;
use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: MERCHANT PER-STORE
  * Metode		: POST (CRUD)
  * URL			: http://production.kontrolgampang.com/master/merchants
  * Body Param	: MERCHANT_ID(Key)
  * Suport Api	: http://production.kontrolgampang.com/master//master/merchant-types [TYPE_PAY_ID]
  *				  http://production.kontrolgampang.com/master//master/merchant-banks  [BANK_ID]
  * Inquery Api	: 1
 */
class MerchantController extends ActiveController
{	
    //public $modelClass = 'common\models\User';
    public $modelClass = 'api\modules\login\models\StoreMerchant';

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
		  * Subject		: MERCHANT PER-STORE
		  * Metode		: POST (CREATE)
		  * URL			: http://production.kontrolgampang.com/master/merchants
		  * param Metode: POST (create) & GET (views)
		  * Param View	: MERCHANT_ID(Key) & STORE_ID(Key)
		  * Param Create: STORE_ID(Key) and NAME/EMAIL/PHONE/STATUS/DCRP_DETIL
		  * Body Param	: STORE_ID(Key) & BANK_NM & TYPE_PAY_ID(key) & BANK_ID(key) & MERCHANT_NM/MERCHANT_NO/MERCHANT_TOKEN/MERCHANT_URL;
		  * 			  local switch param (METHODE)['POST'=create; 'GET'=views].
		  * Suport Api	: http://production.kontrolgampang.com/master//master/merchant-types  [TYPE_PAY_ID]
		  *				  http://production.kontrolgampang.com/master//master/merchant-banks  [BANK_ID]
		  * Keterangan	: Status [0=Disable;1=Enable;3=Delete; 0=default];
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$merchantId		= isset($paramsBody['MERCHANT_ID'])!=''?$paramsBody['MERCHANT_ID']:'';
		$typePayId		= isset($paramsBody['TYPE_PAY_ID'])!=''?$paramsBody['TYPE_PAY_ID']:'';
		$bankId			= isset($paramsBody['BANK_ID'])!=''?$paramsBody['BANK_ID']:'';
		$merchantNm		= isset($paramsBody['MERCHANT_NM'])!=''?$paramsBody['MERCHANT_NM']:'';
		$merchantNo		= isset($paramsBody['MERCHANT_NO'])!=''?$paramsBody['MERCHANT_NO']:'';
		$merchantToken	= isset($paramsBody['MERCHANT_TOKEN'])!=''?$paramsBody['MERCHANT_TOKEN']:'';
		$merchantUrl	= isset($paramsBody['MERCHANT_URL'])!=''?$paramsBody['MERCHANT_URL']:'';
		//VALIDATION STORE
		$cntStore= Store::find()->where(['STORE_ID'=>$store_id])->count();
		
		//POLING SYNC nedded ACCESS_ID
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		= isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='POST'){
			if($cntStore){
				//Model
				$modelMerchant= new StoreMerchant();
				$modelMerchant->STORE_ID=$store_id;
				$modelMerchant->CREATE_AT=date('Y-m-d H:i:s');
				if ($typePayId!=''){$modelMerchant->TYPE_PAY_ID=$typePayId;};
				if ($bankId!=''){$modelMerchant->BANK_ID=$bankId;};
				if ($merchantNm!=''){$modelMerchant->MERCHANT_NM=$merchantNm;};
				if ($merchantNo!=''){$modelMerchant->MERCHANT_NO=$merchantNo;};
				if ($merchantToken!=''){$modelMerchant->MERCHANT_TOKEN=$merchantToken;};
				if ($merchantUrl!=''){$modelMerchant->MERCHANT_URL=$merchantUrl;};
				if($modelMerchant->save()){			
					$rsltMax=StoreMerchant::find()->where(['STORE_ID'=>$store_id])->max('MERCHANT_ID');
					$modelView=StoreMerchant::find()->where(['MERCHANT_ID'=>$rsltMax])->one();
					return array('MERCHANT'=>$modelView);
				}else{
					return array('result'=>$modelMerchant->errors);
				}
			}else{
				return array('result'=>'Store-Not-Exist');
			}	
		}elseif($metode=='GET'){
			if($cntStore And $merchantId){
					/*===========================
					 *=== POLLING UPDATE UUID ===
					 *===========================
					*/
					$dataHeader=explode('.',$store_id);
					if ($tblPooling=='TBL_STORE_MERCHANT'){						
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_STORE_MERCHANT',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$store_id,
							 'PRIMARIKEY_VAL'=>$merchantId
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_STORE_MERCHANT',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$store_id,
									 'PRIMARIKEY_VAL'=>$merchantId,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan AND $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}				
					}
				$modelView=StoreMerchant::find()->Where(['MERCHANT_ID'=>$merchantId])->one();
			}elseif($cntStore AND !$merchantId){
				$modelView=StoreMerchant::find()->where(['STORE_ID'=>$store_id])->all();
			}elseif(!$cntStore AND $merchantId){
				$modelView=StoreMerchant::find()->Where(['MERCHANT_ID'=>$merchantId])->one();
			}else{
				return array('result'=>'Store-Or-Merchant-Not-Exist');
			}
			return array('MERCHANT'=>$modelView);
		}else{
			return array('result'=>'POST-or-GET');
		}
	}
	
	public function actionUpdate()
    {  
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: MERCHANT PER-STORE
		  * Metode		: POST (UPDATE)
		  * URL			: http://production.kontrolgampang.com/master/merchants
		  * Body Param	: MERCHANT_ID(Key) & BANK_NM & TYPE_PAY_ID(key) & BANK_ID(key) & MERCHANT_NM/MERCHANT_NO/MERCHANT_TOKEN/MERCHANT_URL;
		  * Suport Api	: http://production.kontrolgampang.com/master//master/merchant-types  [TYPE_PAY_ID]
		  *				  http://production.kontrolgampang.com/master//master/merchant-banks  [BANK_ID]
		  *	Inquery API	: [POST] http://production.kontrolgampang.com/inquery/merchants
		  *				  Status [2=enable] => B to B Inguery to Bank "Inquery Success, Merchant status change to enable".
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$merchantId		= isset($paramsBody['MERCHANT_ID'])!=''?$paramsBody['MERCHANT_ID']:'';
		$typePayId		= isset($paramsBody['TYPE_PAY_ID'])!=''?$paramsBody['TYPE_PAY_ID']:'';
		$bankId			= isset($paramsBody['BANK_ID'])!=''?$paramsBody['BANK_ID']:'';
		$merchantNm		= isset($paramsBody['MERCHANT_NM'])!=''?$paramsBody['MERCHANT_NM']:'';
		$merchantNo		= isset($paramsBody['MERCHANT_NO'])!=''?$paramsBody['MERCHANT_NO']:'';
		$merchantToken	= isset($paramsBody['MERCHANT_TOKEN'])!=''?$paramsBody['MERCHANT_TOKEN']:'';
		$merchantUrl	= isset($paramsBody['MERCHANT_URL'])!=''?$paramsBody['MERCHANT_URL']:'';
		$modelMerchant= StoreMerchant::find()->where(['MERCHANT_ID'=>$merchantId])->one();
		//==STATUS== [0=Disable;1=Enable;3=Disable]
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';

		if($modelMerchant){
			//$modelMerchant->BANK_NM='ok zone1';
			if ($typePayId!=''){$modelMerchant->TYPE_PAY_ID=$typePayId;};
			if ($bankId!=''){$modelMerchant->BANK_ID=$bankId;};
			if ($merchantNm!=''){$modelMerchant->MERCHANT_NM=$merchantNm;};
		    if ($merchantNo!=''){$modelMerchant->MERCHANT_NO=$merchantNo;};
			if ($merchantToken!=''){$modelMerchant->MERCHANT_TOKEN=$merchantToken;};
			if ($merchantUrl!=''){$modelMerchant->MERCHANT_URL=$merchantUrl;};
			if ($stt!=''){$modelMerchant->STATUS=$stt;};
			if($modelMerchant->save()){
				$modelView=StoreMerchant::find()->where(['MERCHANT_ID'=>$merchantId])->one();				
				return array('MERCHANT'=>$modelView);
			}else{
				return array('result'=>$modelMerchant->errors);
			}
		}else{
			return array('result'=>'Merchant-Not-Exist');
		};			
	}
	
	public function actionDelete()
    {  
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: MERCHANT PER-STORE
		  * Metode		: POST (DELETE)
		  * URL			: http://production.kontrolgampang.com/master/merchants
		  * Body Param	: MERCHANT_ID(Key), 
		  * Keterangan	: Status=3
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$merchantId		= isset($paramsBody['MERCHANT_ID'])!=''?$paramsBody['MERCHANT_ID']:'';
		$modelMerchant= StoreMerchant::find()->where(['MERCHANT_ID'=>$merchantId])->one();
		if($modelMerchant){
			$modelMerchant->STATUS=3;
			if($modelMerchant->save()){			
				$modelView=StoreMerchant::find()->where(['MERCHANT_ID'=>$merchantId])->one();
				return array('MERCHANT'=>$modelView);
			}else{
				return array('result'=>$modelMerchant->errors);
			}
		}else{
			return array('result'=>'Merchant-Not-Exist');
		};			
	}
}


