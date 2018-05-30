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

use api\modules\transaksi\models\TransPenjualanDetail;

class TransPenjualanDetailController extends ActiveController
{

	public $modelClass = 'api\modules\transaksi\models\TransPenjualanDetail';

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
		$transHeaderKey1	= isset($paramsBody['TRANS_ID'])!=''?$paramsBody['TRANS_ID']:'';
		$transHeaderKey2	= isset($paramsBody['OFLINE_ID'])!=''?$paramsBody['OFLINE_ID']:'';
		
		//CREATE`
		$storeID			= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$accessId			= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$golongan			= isset($paramsBody['GOLONGAN'])!=''?$paramsBody['GOLONGAN']:'';
		$tglTrans			= isset($paramsBody['TRANS_DATE'])!=''?$paramsBody['TRANS_DATE']:'';
		$transType			= isset($paramsBody['TRANS_TYPE'])!=''?$paramsBody['TRANS_TYPE']:'';
		$prdID				= isset($paramsBody['PRODUCT_ID'])!=''?$paramsBody['PRODUCT_ID']:'';
		$prdNM				= isset($paramsBody['PRODUCT_NM'])!=''?$paramsBody['PRODUCT_NM']:'';
		$prdProvider		= isset($paramsBody['PRODUCT_PROVIDER'])!=''?$paramsBody['PRODUCT_PROVIDER']:'';
		$prdProviderNm		= isset($paramsBody['PRODUCT_PROVIDER_NM'])!=''?$paramsBody['PRODUCT_PROVIDER_NM']:'';
		$prdProviderNo		= isset($paramsBody['PRODUCT_PROVIDER_NO'])!=''?$paramsBody['PRODUCT_PROVIDER_NO']:'';
		$prdQty				= isset($paramsBody['PRODUCT_QTY'])!=''?$paramsBody['PRODUCT_QTY']:'';
		$prdUnitId			= isset($paramsBody['UNIT_ID'])!=''?$paramsBody['UNIT_ID']:'';
		$prdUnitNM			= isset($paramsBody['UNIT_NM'])!=''?$paramsBody['UNIT_NM']:'';
		$hpp				= isset($paramsBody['HPP'])!=''?$paramsBody['HPP']:'';
		$ppn				= isset($paramsBody['PPN'])!=''?$paramsBody['PPN']:'';
		$hargaJual			= isset($paramsBody['HARGA_JUAL'])!=''?$paramsBody['HARGA_JUAL']:'';
		$discount			= isset($paramsBody['DISCOUNT'])!=''?$paramsBody['DISCOUNT']:'';
		$promo				= isset($paramsBody['PROMO'])!=''?$paramsBody['PROMO']:'';

		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: TRANSAKSI DETAILS.
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/transaksi/trans-penjualan-details
			* Body Param	: METHODE=GET & TRANS_ID(Master Key) OR  OFLINE_ID(Master key) OR  STORE_ID(key)
			*				: TRANS_ID/OFLINE_ID=(GET FROM TRANSAKSI HEADER).=> List detail per-TRANS_ID;
			*/
			if($transHeaderKey1<>''){				
				//MODEL TARNS DETAILS BY TRANS_ID/OFLINE_ID
				$modelCnt= TransPenjualanDetail::find()->where(['TRANS_ID'=>$transHeaderKey1])->count();
				$model= TransPenjualanDetail::find()->where(['TRANS_ID'=>$transHeaderKey1])->all();		
				if($modelCnt){
					return array('LIST_TRANS_DETAILS'=>$model);
				}else{
					return array('result'=>'TRANS_ID-not-exist');
				};
			}else{
				return array('result'=>'TRANS_ID-not-exist');
			}			
		}elseif($metode=='POST'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: TRANSAKSI DETAILS.
			* Metode		: POST (CREATE`)
			* URL			: http://production.kontrolgampang.com/transaksi/trans-penjualan-details
			* Body Param	: METHODE=POST & TRANS_ID(Master Key) & OFLINE_ID(Master key) & STORE_ID(key) 
			* PROPERTIES    : GOLONGAN [1=KG(pembelian);2=PPOB(pembelian);3=PPOP(pembayaran)],
            *				  '1=KG(pembelian)	'=>ACCESS_ID,GOLONGAN,TRANS_DATE,PRODUCT_ID,PRODUCT_NM,PRODUCT_QTY,
			*                                      UNIT_ID,UNIT_NM,HARGA_JUAL,DISCOUNT,PROMO
			*				  '2=PPOB(pembelian)'=>ACCESS_ID,GOLONGAN,TRANS_DATE,PRODUCT_ID,PRODUCT_NM,
			*									   PRODUCT_PROVIDER,PRODUCT_PROVIDER_NO,PRODUCT_PROVIDER_NM,
			*									   PRODUCT_QTY,UNIT_ID,UNIT_NM,HARGA_JUAL,DISCOUNT,PROMO
			*				  '3=PPOB(pembayaran)'=>ACCESS_ID,GOLONGAN,TRANS_DATE,PRODUCT_ID,PRODUCT_NM,
			*									   PRODUCT_PROVIDER,PRODUCT_PROVIDER_NO,PRODUCT_PROVIDER_NM,
			*									   PRODUCT_QTY,UNIT_ID,UNIT_NM,HARGA_JUAL,DISCOUNT,PROMO	  			
			*/	
			$modelCheck=TransPenjualanDetail::find()->where("PRODUCT_ID='".$prdID."' AND ( TRANS_ID='".$transHeaderKey1."')")->count();
			if($modelCheck){
				// $modelCheckView=TransPenjualanDetail::find()->where(['TRANS_ID'=>$transHeaderKey1])->all();
				// return array('LIST_TRANS_DETAILS'=>$modelCheckView);
			}else{	
				$modelNew = new TransPenjualanDetail();
				$modelNew->scenario = "create";
				//==KEY=
				if ($storeID<>''){$modelNew->STORE_ID=$storeID;};
				if ($transHeaderKey1<>''){$modelNew->TRANS_ID=$transHeaderKey1;};
				if ($transHeaderKey2<>''){$modelNew->OFLINE_ID=$transHeaderKey2;};
				if ($accessId<>''){$modelNew->ACCESS_ID=$accessId;};
				if ($golongan<>''){$modelNew->GOLONGAN=$golongan;};
				//==PROPERTES==
				if ($tglTrans<>''){$modelNew->TRANS_DATE=$tglTrans;};
				if ($transType<>''){$modelNew->TRANS_TYPE=$transType;};
				if ($prdID<>''){$modelNew->PRODUCT_ID=$prdID;};
				if ($prdNM<>''){$modelNew->PRODUCT_NM=$prdNM;};
				if ($prdProvider<>''){$modelNew->PRODUCT_PROVIDER=$prdProvider;};
				if ($prdProviderNm<>''){$modelNew->PRODUCT_PROVIDER_NM=$prdProviderNm;};
				if ($prdProviderNo<>''){$modelNew->PRODUCT_PROVIDER_NO=$prdProviderNo;};
				if ($prdQty<>''){$modelNew->PRODUCT_QTY=$prdQty;};
				if ($prdUnitId<>''){$modelNew->UNIT_ID=$prdUnitId;};
				if ($prdUnitNM<>''){$modelNew->UNIT_NM=$prdUnitNM;};
				if ($hpp<>''){$modelNew->HPP=$hpp;};
				if ($ppn<>''){$modelNew->PPN=$ppn;};
				if ($hargaJual<>''){$modelNew->HARGA_JUAL=$hargaJual;};
				if ($discount<>''){$modelNew->DISCOUNT=$discount;};
				if ($promo<>''){$modelNew->PROMO=$promo;};		
				if($modelNew->save()){
					$modelView=TransPenjualanDetail::find()->where(['TRANS_ID'=>$transHeaderKey1,'PRODUCT_ID'=>$prdID,'TRANS_TYPE'=>$transType])->one();
					return array('LIST_TRANS_DETAILS'=>$modelView);
				}else{
					return array('error'=>$modelNew->errors);
				}
			}
		};
	}
}
    
	
	
	
	
