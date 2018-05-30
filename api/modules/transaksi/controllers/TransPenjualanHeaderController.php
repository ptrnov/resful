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

use api\modules\transaksi\models\TransPenjualanHeader;

class TransPenjualanHeaderController extends ActiveController
{

	public $modelClass = 'api\modules\transaksi\models\TransPenjualanHeader';

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
		$store_id			= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$transHeaderKey1	= isset($paramsBody['TRANS_ID'])!=''?$paramsBody['TRANS_ID']:'';
		$transHeaderKey2	= isset($paramsBody['OFLINE_ID'])!=''?$paramsBody['OFLINE_ID']:'';
		$transHeaderKey3	= isset($paramsBody['TRANS_REF'])!=''?$paramsBody['TRANS_REF']:'';
		$tglTrans			= isset($paramsBody['TRANS_DATE'])!=''?$paramsBody['TRANS_DATE']:'';
		$transType			= isset($paramsBody['TRANS_TYPE'])!=''?$paramsBody['TRANS_TYPE']:'';
		$accessId			= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$opencloseId		= isset($paramsBody['OPENCLOSE_ID'])!=''?$paramsBody['OPENCLOSE_ID']:'';
		//==PROPERTIES===
		$ttlProduct			= isset($paramsBody['TOTAL_PRODUCT'])!=''?$paramsBody['TOTAL_PRODUCT']:'';
		$totalHarga			= isset($paramsBody['TOTAL_HARGA'])!=''?$paramsBody['TOTAL_HARGA']:'';
		$subTotalHarga		= isset($paramsBody['SUB_TOTAL_HARGA'])!=''?$paramsBody['SUB_TOTAL_HARGA']:'';
		$ppnPajak			= isset($paramsBody['PPN'])!=''?$paramsBody['PPN']:'';		
		$dokem				= isset($paramsBody['DO_KEM'])!=''?$paramsBody['DO_KEM']:'';		
		$dokemType			= isset($paramsBody['DO_KEM_TYPE'])!=''?$paramsBody['DO_KEM_TYPE']:'';		
		$cunsumerId			= isset($paramsBody['CONSUMER_ID'])!=''?$paramsBody['CONSUMER_ID']:'';
		$cunsumerNm			= isset($paramsBody['CONSUMER_NM'])!=''?$paramsBody['CONSUMER_NM']:'';
		$cunsumerEmail		= isset($paramsBody['CONSUMER_EMAIL'])!=''?$paramsBody['CONSUMER_EMAIL']:'';
		$cunsumerPhone		= isset($paramsBody['CONSUMER_PHONE'])!=''?$paramsBody['CONSUMER_PHONE']:'';
		$note				= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$merchantId			= isset($paramsBody['MERCHANT_ID'])!=''?$paramsBody['MERCHANT_ID']:'';
		$stt				= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: TRANSAKSI HEADER.
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/transaksi/trans-penjualan-heades
			* Body Param	: METHODE=GET & TRANS_ID(Master Key) OR  OFLINE_ID(Master key) OR  STORE_ID(key) OR CREATE_AT(Filter) & OPENCLOSE_ID
			*				: STORE_ID='' All Transaksi Header and Detail.  				    (LIST ALL)
			*				: STORE_ID<>''Transaksi Header and Detail By TRANS_ID or OFLINE_ID. (SINGLE DATA)
			*				: CREATE_AT<>'' Filter By date.
			*				: TRANS_ID=(CREATE AUTOMATICLY DATABASE);OFLINE_ID=(CREATE FROM MOBILE)
			*/
			if($store_id<>''){				
				//MODEL TARNS HEADER BY STORE_ID
				if($tglTrans<>''){	
					$modelCnt= TransPenjualanHeader::find()->where(['STORE_ID'=>$store_id])->andWhere(['like','TRANS_DATE',$tglTrans])->count();
					$model= TransPenjualanHeader::find()->where(['STORE_ID'=>$store_id])->andWhere(['like','TRANS_DATE',$tglTrans])->all();		
					if($modelCnt){
						return array('LIST_TRANS_HEADER'=>$model);
					}else{
						return array('result'=>'data-empty');
					};
				}else{
					$modelCnt= TransPenjualanHeader::find()->where(['STORE_ID'=>$store_id])->count();
					$model= TransPenjualanHeader::find()->where(['STORE_ID'=>$store_id])->all();		
					if($modelCnt){
						return array('LIST_TRANS_HEADER'=>$model);
					}else{
						return array('result'=>'data-empty');
					};
				}
			}else{
				//TARNS HEADER BY TRANS_ID
				$modelCnt= TransPenjualanHeader::find()->where(['TRANS_ID'=>$transHeaderKey1])->count();
				$model= TransPenjualanHeader::find()->where(['TRANS_ID'=>$transHeaderKey1])->one();		
				if($modelCnt){			
					return array('LIST_TRANS_HEADER'=>$model);
				}else{
					return array('result'=>'data-empty');
				}		
			}			
		}elseif($metode=='POST'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: TRANSAKSI HEADER.
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/transaksi/trans-penjualan-heades
			* Body Param	: METHODE=GET & TRANS_ID(Master Key) & OFLINE_ID(Master key) & STORE_ID(key) 
			*                 AND ACCESS_ID(key) & OR TRANS_DATE(Filter)
			*				: IF MERCHANT_ID=0 [CASH MANUAL] ELSE PEYMENT ONLINE
			* PROPERTIES	: TOTAL_PRODUCT,SUB_TOTAL_HARGA,PPN,TOTAL_HARGA,CONSUMER_ID,CONSUMER_NM,CONSUMER_EMAIL,CONSUMER_PHONE,DCRP_DETIL,
			*				  MERCHANT_ID [TYPE_PAY_ID,TYPE_PAY_NM,BANK_ID,BANK_NM,MERCHANT_NM,MERCHANT_NO]->inquery
			*/
			$modelTransCheck=TransPenjualanHeader::find()->where(['TRANS_ID'=>$transHeaderKey1])->one();
			if(!$modelTransCheck){
				$modelNew = new TransPenjualanHeader();
				$modelNew->scenario = "create";
				//==KEY=			
				if ($opencloseId<>''){$modelNew->OPENCLOSE_ID=$opencloseId;};
				if ($store_id<>''){$modelNew->STORE_ID=$store_id;};
				if ($transHeaderKey1<>''){$modelNew->TRANS_ID=$transHeaderKey1;};
				if ($transHeaderKey2<>''){$modelNew->OFLINE_ID=$transHeaderKey2;};
				if ($transHeaderKey3<>''){$modelNew->TRANS_REF=$transHeaderKey3;};
				if ($tglTrans<>''){$modelNew->TRANS_DATE=date('Y-m-d H:i:s', strtotime($tglTrans));};
				if ($transType<>''){$modelNew->TRANS_TYPE=$transType;};
				if ($accessId<>''){$modelNew->ACCESS_ID=$accessId;};			
				//==PROPERTIES=			
				if ($ttlProduct<>''){$modelNew->TOTAL_PRODUCT=$ttlProduct;};
				if ($totalHarga<>''){$modelNew->TOTAL_HARGA=$totalHarga;};
				if ($subTotalHarga<>''){$modelNew->SUB_TOTAL_HARGA=$subTotalHarga;};
				if ($ppnPajak<>''){$modelNew->PPN=$ppnPajak;};
				if ($dokem<>''){$modelNew->DO_KEM=$dokem;};
				if ($dokemType<>''){$modelNew->DO_KEM_TYPE=$dokemType;};
				if ($cunsumerId<>''){$modelNew->CONSUMER_ID=$cunsumerId;};
				if ($cunsumerNm<>''){$modelNew->CONSUMER_NM=$cunsumerNm;};
				if ($cunsumerEmail<>''){$modelNew->CONSUMER_EMAIL=$cunsumerEmail;};
				if ($cunsumerPhone<>''){$modelNew->CONSUMER_PHONE=$cunsumerPhone;};
				if ($note<>''){$modelNew->DCRP_DETIL=$note;};
				if ($merchantId<>''){$modelNew->MERCHANT_ID=$merchantId;};
				if ($stt<>''){$modelNew->STATUS=$stt;};			
				if($modelNew->save()){
					$modelView=TransPenjualanHeader::find()->where(['TRANS_ID'=>$transHeaderKey1])->orderBy(['ID' => SORT_DESC])->limit(1)->one();
					return array('LIST_TRANS_HEADER'=>$modelView);
				}else{
					return array('error'=>$modelNew->errors);
				}
			}else{
				return array('error'=>'TRANS_ID-EXIST');
			}
		};
	}
	
	public function actionUpdate()
    {  	
		/**
		* @author 		: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: TRANSAKSI HEADER.
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/transaksi/trans-penjualan-heades
		* Body Param	: TRANS_ID(Master Key) & OFLINE_ID(Master key) & OPENCLOSE_ID
		* PROPERTIES	: TOTAL_PRODUCT,SUB_TOTAL_HARGA,PPN,TOTAL_HARGA,CONSUMER_ID,CONSUMER_NM,CONSUMER_EMAIL,CONSUMER_PHONE,DCRP_DETIL,
		*				  MERCHANT_ID [TYPE_PAY_ID,TYPE_PAY_NM,BANK_ID,BANK_NM,MERCHANT_NM,MERCHANT_NO]->inquery
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;	
		//==KEY==
		$transHeaderKey1	= isset($paramsBody['TRANS_ID'])!=''?$paramsBody['TRANS_ID']:'';
		$transHeaderKey2	= isset($paramsBody['OFLINE_ID'])!=''?$paramsBody['OFLINE_ID']:'';
		$opencloseId		= isset($paramsBody['OPENCLOSE_ID'])!=''?$paramsBody['OPENCLOSE_ID']:'';
		//==PROPERTIES===
		$transType			= isset($paramsBody['TRANS_TYPE'])!=''?$paramsBody['TRANS_TYPE']:'';
		$ttlProduct			= isset($paramsBody['TOTAL_PRODUCT'])!=''?$paramsBody['TOTAL_PRODUCT']:'';
		$totalHarga			= isset($paramsBody['TOTAL_HARGA'])!=''?$paramsBody['TOTAL_HARGA']:'';
		$subTotalHarga		= isset($paramsBody['SUB_TOTAL_HARGA'])!=''?$paramsBody['SUB_TOTAL_HARGA']:'';
		$ppnPajak			= isset($paramsBody['PPN'])!=''?$paramsBody['PPN']:'';
		$dokem				= isset($paramsBody['DO_KEM'])!=''?$paramsBody['DO_KEM']:'';		
		$dokemType			= isset($paramsBody['DO_KEM_TYPE'])!=''?$paramsBody['DO_KEM_TYPE']:'';			
		$cunsumerId			= isset($paramsBody['CONSUMER_ID'])!=''?$paramsBody['CONSUMER_ID']:'';
		$cunsumerNm			= isset($paramsBody['CONSUMER_NM'])!=''?$paramsBody['CONSUMER_NM']:'';
		$cunsumerEmail		= isset($paramsBody['CONSUMER_EMAIL'])!=''?$paramsBody['CONSUMER_EMAIL']:'';
		$cunsumerPhone		= isset($paramsBody['CONSUMER_PHONE'])!=''?$paramsBody['CONSUMER_PHONE']:'';
		$note				= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$merchantId			= isset($paramsBody['MERCHANT_ID'])!=''?$paramsBody['MERCHANT_ID']:'';
		$stt				= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		$modelEdit=TransPenjualanHeader::find()->where(['TRANS_ID'=>$transHeaderKey1])->one();
		if($modelEdit){
			//==REQUIRED=
			if ($opencloseId<>''){$modelEdit->OPENCLOSE_ID=$opencloseId;};
			if ($ttlProduct<>''){$modelEdit->TOTAL_PRODUCT=$ttlProduct;};
			//==PROPERTIES=			
			if ($totalHarga<>''){$modelEdit->TOTAL_HARGA=$totalHarga;};
			if ($subTotalHarga<>''){$modelEdit->SUB_TOTAL_HARGA=$subTotalHarga;};
			if ($ppnPajak<>''){$modelEdit->PPN=$ppnPajak;};
			if ($dokem<>''){$modelEdit->DO_KEM=$dokem;};
			if ($dokemType<>''){$modelEdit->DO_KEM_TYPE=$dokemType;};
			if ($transType<>''){$modelEdit->TRANS_TYPE=$transType;};			
			if ($cunsumerId<>''){$modelEdit->CONSUMER_ID=$cunsumerId;};
			if ($cunsumerNm<>''){$modelEdit->CONSUMER_NM=$cunsumerNm;};
			if ($cunsumerEmail<>''){$modelEdit->CONSUMER_EMAIL=$cunsumerEmail;};
			if ($cunsumerPhone<>''){$modelEdit->CONSUMER_PHONE=$cunsumerPhone;};
			if ($note<>''){$modelEdit->DCRP_DETIL=$note;};
			if ($merchantId<>''){$modelEdit->MERCHANT_ID=$merchantId;};	
			if ($stt<>''){$modelEdit->STATUS=$stt;};			
			$modelEdit->scenario = "update";
			if($modelEdit->save()){
				$modelView=TransPenjualanHeader::find()->where(['TRANS_ID'=>$transHeaderKey1])->all();
				return array('LIST_TRANS_HEADER'=>$modelView);
			}else{
				return array('error'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'save-not-exist');
		}
	}	
}
    
	
	
	
	
