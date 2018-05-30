<?php

namespace api\modules\transaksi\controllers;

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

use api\modules\transaksi\models\TransOpenclose;

class TransOpencloseController extends ActiveController
{

	public $modelClass = 'api\modules\transaksi\models\TransOpenclose';

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
		$opencloseID	= isset($paramsBody['OPENCLOSE_ID'])!=''?$paramsBody['OPENCLOSE_ID']:'';
		$accessGrp		= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$acsID			= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tglOpen		= isset($paramsBody['TGL_OPEN'])!=''?$paramsBody['TGL_OPEN']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		//PROPERTY
		$caseInPeti		= isset($paramsBody['CASHINDRAWER'])!=''?$paramsBody['CASHINDRAWER']:'';
		$caseAdd		= isset($paramsBody['ADDCASH'])!=''?$paramsBody['ADDCASH']:'';
		$ttlRefund		= isset($paramsBody['TOTALREFUND'])!=''?$paramsBody['TOTALREFUND']:'';
		$ttlDonasi		= isset($paramsBody['TOTALDONASI'])!=''?$paramsBody['TOTALDONASI']:'';
		
		if($metode=='GET'){
			/**
			* @author 	: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: TRANSAKSI OPEN / CLOSING KASIR.
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/transaksi/trans-opencloses
			* Body Param	: METHODE=GET & STORE_ID(Key) or  OPENCLOSE_ID(key) 
			*				: STORE_ID='' All data Open/Closing per-STORE_ID.
			*				: STORE_ID<>'' data Open/Closing per-OPENCLOSE_ID.
			*/			
			if($opencloseID<>''){
				//Model Openclose BY OPENCLOSE_ID
				$modelCnt= TransOpenclose::find()->where(['OPENCLOSE_ID'=>$opencloseID])->count();
				$model= TransOpenclose::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();		
				//$model->scenario = "ambil_data";
				if($modelCnt){			
					return array('LIST_OPENCLOSE'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}else{				
				if($tglOpen<>''){					
					//Model Openclose BY STORE_ID
					$modelCnt= TransOpenclose::find()->where(['STORE_ID'=>$store_id])->andWhere(['like','TGL_OPEN',date('Y-m-d', strtotime($tglOpen))])->count();
					$model= TransOpenclose::find()->where(['STORE_ID'=>$store_id])->andWhere(['like','TGL_OPEN',date('Y-m-d', strtotime($tglOpen))])->all();		
					if($modelCnt){
						return array('LIST_OPENCLOSE'=>$model);
					}else{
						return array('result'=>'data-empty');
					};
				}else{
					//Model Openclose BY STORE_ID
					$modelCnt= TransOpenclose::find()->where(['STORE_ID'=>$store_id])->count();
					$model= TransOpenclose::find()->where(['STORE_ID'=>$store_id])->all();		
					if($modelCnt){
						return array('LIST_OPENCLOSE'=>$model);
					}else{
						return array('result'=>'data-empty');
					};
				}				
			}			
		}elseif($metode=='POST'){
			/**
			* @author 	: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: TRANSAKSI OPEN / CLOSING KASIR.
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/transaksi/trans-opencloses
			* Body Param	: METHODE=POST & STORE_ID(Key) & ACCESS_ID(key) & TGL_OPEN
			* PROPERTY		: CASHINDRAWER,STATUS
			*/
			$check = TransOpenclose::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
			if($check){
				return array('result'=>'OPENCLOSE_ID-exist');
			}else{
				$modelNew = new TransOpenclose();
				$modelNew->scenario = "create";
				if ($store_id<>''){$modelNew->STORE_ID=$store_id;};
				if ($opencloseID<>''){$modelNew->OPENCLOSE_ID=$opencloseID;};
				if ($acsID<>''){$modelNew->ACCESS_ID=$acsID;};
				if ($tglOpen<>''){$modelNew->TGL_OPEN=date('Y-m-d H:i:s', strtotime($tglOpen));};
				if ($caseInPeti<>''){$modelNew->CASHINDRAWER=$caseInPeti;};
				if ($caseAdd<>''){$modelNew->ADDCASH=$caseAdd;};
				if ($ttlRefund<>''){$modelNew->TOTALREFUND=$ttlRefund;};
				if ($ttlDonasi<>''){$modelNew->TOTALDONASI=$ttlDonasi;};				
				if ($stt<>''){$modelNew->STATUS=$stt;};
				if($modelNew->save()){
					$modelView=TransOpenclose::find()->where(['STORE_ID'=>$store_id])->orderBy(['ID' => SORT_DESC])->limit(1)->one();
					return array('LIST_OPENCLOSE'=>$modelView);
				}else{
					return array('result'=>$modelNew->errors);
				}
			}
		};
	}
	
	public function actionUpdate()
    {  	
		/**
		* @author 	: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: TRANSAKSI OPEN / CLOSING KASIR.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/transaksi/trans-opencloses
		* Body Param	: OPENCLOSE_ID(key Master)
		* PROPERTIES	: ADDCASH,SELLCASH,TOTALCASH,TOTALCASH_ACTUAL,STATUS
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		//KEY MASTER		
		//KEY
		$opencloseID	= isset($paramsBody['OPENCLOSE_ID'])!=''?$paramsBody['OPENCLOSE_ID']:'';
		$tglClose		= isset($paramsBody['TGL_CLOSE'])!=''?$paramsBody['TGL_CLOSE']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		//PROPERTY
		$caseInDrawer	= isset($paramsBody['CASHINDRAWER'])!=''?$paramsBody['CASHINDRAWER']:'';
		$caseAdd		= isset($paramsBody['ADDCASH'])!=''?$paramsBody['ADDCASH']:'';
		$caseSell		= isset($paramsBody['SELLCASH'])!=''?$paramsBody['SELLCASH']:'';
		$caseActual		= isset($paramsBody['TOTALCASH_ACTUAL'])!=''?$paramsBody['TOTALCASH_ACTUAL']:'';
		$ttlRefund		= isset($paramsBody['TOTALREFUND'])!=''?$paramsBody['TOTALREFUND']:'';
		$ttlDonasi		= isset($paramsBody['TOTALDONASI'])!=''?$paramsBody['TOTALDONASI']:'';
		$caseNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
	
		$modelEdit = TransOpenclose::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
		
		if($modelEdit){
			if ($tglClose<>''){$modelEdit->TGL_CLOSE=date('Y-m-d H:i:s', strtotime($tglClose));}; 			
			if ($stt<>''){$modelEdit->STATUS=$stt;};		
			if ($caseInDrawer<>''){$modelEdit->CASHINDRAWER=$caseInDrawer;};
			if ($caseAdd<>''){$modelEdit->ADDCASH=$caseAdd;};			
			if ($caseSell<>''){$modelEdit->SELLCASH=$caseSell;};			
			if ($caseActual<>''){$modelEdit->TOTALCASH_ACTUAL=$caseActual;};			
			if ($ttlRefund<>''){$modelEdit->TOTALREFUND=$ttlRefund;};			
			if ($ttlDonasi<>''){$modelEdit->TOTALDONASI=$ttlDonasi;};			
			if ($caseNote<>''){$modelEdit->DCRP_DETIL=$caseNote;};	
			$modelEdit->scenario = "update";			
			if($modelEdit->save()){
				$modelView=TransOpenclose::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
				return array('LIST_OPENCLOSE'=>$modelView);
			}else{
				return array('result'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'OPENCLOSE_ID-not-exist');
		}
	}
	
}
    
	
	
	
	
