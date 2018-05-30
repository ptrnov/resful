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

use api\modules\pembayaran\models\DompetSaldo;
use api\modules\master\models\SyncPoling;

class DompetController extends ActiveController
{

	public $modelClass = 'api\modules\pembayaran\models\DompetSaldo';

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
		  * Subject			: DOMPET SALDO
		  * Metode			: POST 
		  * URL				: http://production.kontrolgampang.com/pembayaran/dompets
		  * param Metode	: POST & GET
		  * Param KEY		: ACCESS_GROUP 
		  * Param Create	:  
		 */
		$paramsBody 	= Yii::$app->request->bodyParams;		
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$accessGroup	= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';	
		
		//POLING SYNC nedded ACCESS_ID
		$accessID=isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling=isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID=isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		//VALIDATION STORE
		$cntDompetSaldo= DompetSaldo::find()->where(['ACCESS_GROUP'=>$accessGroup])->count();
		
		if($metode=='GET'){
			if($cntDompetSaldo){				
				if ($tblPooling=='TBL_SYNC_DOMPET'){						
					//==GET DATA POLLING
					$modelPoling=SyncPoling::find()->where([
						 'NM_TABLE'=>'TBL_SYNC_DOMPET',
						 'ACCESS_GROUP'=>$accessGroup,
						 'PRIMARIKEY_VAL'=>$accessGroup,
					])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
					//==UPDATE DATA POLLING UUID
					if($modelPoling){							
						foreach($modelPoling as $row => $val){
							$modelSimpan=SyncPoling::find()->where([
								 'NM_TABLE'=>'TBL_SYNC_DOMPET',
								 'ACCESS_GROUP'=>$accessGroup,
								 'PRIMARIKEY_VAL'=>$accessGroup,
								 'TYPE_ACTION'=>$val->TYPE_ACTION
							])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
							if($modelSimpan AND $paramlUUID){
								$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
								$modelSimpan->save();
							}
						}							
					}
				}
				$modelView=DompetSaldo::find()->Where(['ACCESS_GROUP'=>$accessGroup])->one();
			}elseif($cntDompetSaldo){
				$modelView=DompetSaldo::find()->where(['ACCESS_GROUP'=>$accessGroup])->all();
			}else{
				return array('result'=>'Dompet-Not-Exist');
			}
			return array('DOMPET'=>$modelView);
		}else{
			return array('result'=>'POST-or-GET');
		}
	}	
}
    
	
	
	
	
