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

use api\modules\transaksi\models\TransStoran;
use api\modules\transaksi\models\TransStoranImage;

class TransStoranController extends ActiveController
{

	public $modelClass = 'api\modules\transaksi\models\TransStoran';

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
		$accessgroup	= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$acsID			= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tglCreate		= isset($paramsBody['CREATE_AT'])!=''?$paramsBody['CREATE_AT']:'';
		$tglStoran		= isset($paramsBody['TGL_STORAN'])!=''?$paramsBody['TGL_STORAN']:'';
		
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		//PROPERTY
		$ttlCash		= isset($paramsBody['TOTALCASH'])!=''?$paramsBody['TOTALCASH']:'';
		$storanNominal	= isset($paramsBody['NOMINAL_STORAN'])!=''?$paramsBody['NOMINAL_STORAN']:'';
		$bankNm			= isset($paramsBody['BANK_NM'])!=''?$paramsBody['BANK_NM']:'';
		$bankNo			= isset($paramsBody['BANK_NO'])!=''?$paramsBody['BANK_NO']:'';
		$caseNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$attachImage	= isset($paramsBody['STORAN_IMAGE'])!=''?$paramsBody['STORAN_IMAGE']:'';
		
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: TRANSAKSI STORAN.
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/transaksi/trans-storans
			* Body Param	: METHODE=GET & STORE_ID(Key) or  OPENCLOSE_ID(key) or CREATE_AT(Filter)
			*				: STORE_ID='' All data Storan.  				 (LIST ALL)
			*				: STORE_ID<>''data data Storan per-OPENCLOSE_ID. (ONE DATA)
			*				: CREATE_AT<>'' Filter By date.
			*				: CREATE AUTOMATICLY FROM OPEN/CLOSEING.  
			*/
			if($store_id<>''){				
				//MODEL STORAN BY STORE_ID
				if($tglStoran<>''){	
					$modelCnt= TransStoran::find()->where(['STORE_ID'=>$store_id])->andWhere(['like','TGL_STORAN',date('Y-m-d', strtotime($tglStoran))])->count();
					$model= TransStoran::find()->where(['STORE_ID'=>$store_id])->andWhere(['like','TGL_STORAN',date('Y-m-d', strtotime($tglStoran))])->all();		
					if($modelCnt){
						return array('LIST_STORAN'=>$model);
					}else{
						return array('result'=>'data-empty');
					};
				}else{
					$modelCnt= TransStoran::find()->where(['STORE_ID'=>$store_id])->count();
					$model= TransStoran::find()->where(['STORE_ID'=>$store_id])->all();		
					if($modelCnt){
						return array('LIST_STORAN'=>$model);
					}else{
						return array('result'=>'data-empty');
					};
				}
			}else{
				//MODEL STORAN BY OPENCLOSE_ID
				$modelCnt= TransStoran::find()->where(['OPENCLOSE_ID'=>$opencloseID])->count();
				$model= TransStoran::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();		
				if($modelCnt){			
					return array('LIST_STORAN'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}			
		}elseif($metode=='POST'){
			// return array('result'=>'use-update');
			
			$modelNew = new TransStoran();
			$modelNew->scenario = "create";	
			if ($accessgroup<>''){$modelNew->ACCESS_GROUP=$accessgroup;}; 
			if ($store_id<>''){$modelNew->STORE_ID=$store_id;}; 
			if ($acsID<>''){$modelNew->ACCESS_ID=$acsID;}; 
			if ($opencloseID<>''){$modelNew->OPENCLOSE_ID=$opencloseID;}; 
			
			if ($tglStoran<>''){$modelNew->TGL_STORAN=date('Y-m-d H:i:s', strtotime($tglStoran));}; 			
			if ($stt<>''){$modelNew->STATUS=$stt;};		
			if ($ttlCash<>''){$modelNew->TOTALCASH=$ttlCash;};		
			if ($storanNominal<>''){$modelNew->NOMINAL_STORAN=$storanNominal;};		
			if ($bankNm<>''){$modelNew->BANK_NM=$bankNm;};		
			if ($bankNo<>''){$modelNew->BANK_NO=$bankNo;};		
			if ($caseNote<>''){$modelNew->DCRP_DETIL=$caseNote;};	
			$modelNew->CREATE_AT=date('Y-m-d H:i:s', strtotime($tglStoran));	
			if($modelNew->save()){
					//UPDATE ATTCH IMAGE STORAN.
					$modelUpdateImage=TransStoranImage::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
					if ($attachImage<>''){$modelUpdateImage->STORAN_IMAGE=$attachImage;};	
					$modelUpdateImage->STATUS=1;
					$modelUpdateImage->save();
					//VIEW STORAN PROPERTIES
					$modelView=TransStoran::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
					return array('LIST_OPENCLOSE'=>$modelView);
			}else{
				return array('result'=>$modelNew->errors);
			}
			
		};
	}
	
	public function actionUpdate()
    {  	
		/**
		* @author 		: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: TRANSAKSI STORAN.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/transaksi/trans-opencloses
		* Body Param	: OPENCLOSE_ID(key) 
		* PROPERTIES	: ADDCASH,SELLCASH,TOTALCASH,TOTALCASH_ACTUAL,STATUS,BANK_NM,BANK_NO,STORAN_IMAGE
		* KETERANGAN	: STATUS=2 (belum closing) tidak bisa update."AUTOMAICLY CREATE FROM TRANS_OPENCLOSE"
		*				  STATUS=0 (closing) dapat melakukan setoran."AUTOMAICLY UPDATE FROM TRANS_OPENCLOSE"
		* 				  STATUS=1 Exist Update Storan & Image.
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		
		//==KEY==
		$opencloseID	= isset($paramsBody['OPENCLOSE_ID'])!=''?$paramsBody['OPENCLOSE_ID']:'';
		$tglStoran		= isset($paramsBody['TGL_STORAN'])!=''?$paramsBody['TGL_STORAN']:'';
		
		//==STATUS== [0=Disable;1=Enable;3=Disable]
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		//PROPERTY
		$storanNominal	= isset($paramsBody['NOMINAL_STORAN'])!=''?$paramsBody['NOMINAL_STORAN']:'';
		$bankNm			= isset($paramsBody['BANK_NM'])!=''?$paramsBody['BANK_NM']:'';
		$bankNo			= isset($paramsBody['BANK_NO'])!=''?$paramsBody['BANK_NO']:'';
		$caseNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$attachImage	= isset($paramsBody['STORAN_IMAGE'])!=''?$paramsBody['STORAN_IMAGE']:'';
	
		$modelEdit = TransStoran::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
		
		if($modelEdit){
			//if($modelEdit->STATUS==0){
				if ($tglStoran<>''){$modelEdit->TGL_STORAN=date('Y-m-d H:i:s', strtotime($tglStoran));}; 			
				if ($stt<>''){$modelEdit->STATUS=$stt;};		
				if ($storanNominal<>''){$modelEdit->NOMINAL_STORAN=$storanNominal;};		
				if ($bankNm<>''){$modelEdit->BANK_NM=$bankNm;};		
				if ($bankNo<>''){$modelEdit->BANK_NO=$bankNo;};		
				if ($caseNote<>''){$modelEdit->DCRP_DETIL=$caseNote;};	
				$modelEdit->scenario = "update";			
				if($modelEdit->save()){
					//UPDATE ATTCH IMAGE STORAN.
					$modelUpdateImage=TransStoranImage::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
					if ($attachImage<>''){$modelUpdateImage->STORAN_IMAGE=$attachImage;};	
					$modelUpdateImage->STATUS=1;
					$modelUpdateImage->save();
					//VIEW STORAN PROPERTIES
					$modelView=TransStoran::find()->where(['OPENCLOSE_ID'=>$opencloseID])->one();
					return array('LIST_OPENCLOSE'=>$modelView);
				}else{
					return array('error'=>$modelEdit->errors);
				}
			// }elseif($modelEdit->STATUS==2){
				// return array('result'=>'Closing-First');
			// }else{
				// return array('result'=>'Locked');
			// }
		}else{
			return array('result'=>'OPENCLOSE_ID-not-exist');
		}
	}	
}
    
	
	
	
	
