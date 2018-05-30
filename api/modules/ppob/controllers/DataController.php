<?php

namespace api\modules\ppob\controllers;

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

use api\modules\ppob\models\PpobMasterKtg;
use api\modules\ppob\models\PpobMasterKelompok;
use api\modules\ppob\models\PpobMasterHarga;
use api\modules\ppob\models\PpobTransaksi;
use api\modules\ppob\models\PpobSaldoStore;

class DataController extends ActiveController
{

	public $modelClass = 'api\modules\ppob\models\PpobMasterKtg';

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
		 unset($actions['index'],$actions['kategori'],$actions['produk']);
		 //, $actions['update']);
		 //, $actions['create'], $actions['delete'], $actions['view']);
		 //unset($actions['update'], $actions['create'], $actions['delete'], $actions['view']);
		 return $actions;
	 }
	
	/* 
	public function actionIndex()
	{
		$model= PpobMasterKtg::find()->all();
		return $model;
	} 
	*/
	
	/**	
	* Subject		: GROUP KELOMPOK PPOB (group kelompok, sebagai grouping Kategori).
	* Metode		: POST (CRUD)
	* URL			: http://production.kontrolgampang.com/ppob/datas/kelompok-group
	* Action		: kelompok-group
	* Param Headers	: -
	* Param URl 	: -
	* Param Body	: -
	* KETERANGAN	: -
	* TRIGER		: (Create/Update/Status Delete) to POLLING.
	* @author 		: ptrnov  <piter@lukison.com>
	* @since 		: 1.2
	*/
	public function actionKelompokGroup()
	{
		$model= PpobMasterKelompok::find()->all();
		return $model;
	}
	
	/**	
	* Subject		: KATEGORI KELOMPOK PPOB (kategori kelompok, sebagai grouping produk)
	* Metode		: POST (CRUD)
	* URL			: http://production.kontrolgampang.com/ppob/datas/kelompok-kategori
	* Action		: kelompok-kategori
	* Param Headers	: -
	* Param URl 	: -
	* Param Body	: KTG_ID,KELOMPOK 
	* KETERANGAN	: KTG_ID=(per-kategori) OR KELOMPOK=(semua-kategori-dalam-kelompok).
	* TRIGER		: (Create/Update/Status Delete) to POLLING.
	* @author 		: ptrnov  <piter@lukison.com>
	* @since 		: 1.2
	*/
	public function actionKelompokKategori()
	{
		//$params     		= $_REQUEST;
		// $paramsHeader	= Yii::$app->request->headers;
		$paramsBody 		= Yii::$app->request->bodyParams;		
		$ktgId				= isset($paramsBody['KTG_ID'])!=''?$paramsBody['KTG_ID']:'';
		$kelomppok			= isset($paramsBody['KELOMPOK'])!=''?$paramsBody['KELOMPOK']:'';
		// $ktgId				= isset($paramsBody['KTG_ID'])!=''?$params['KTG_ID']:'';
		// $kelomppok			= isset($paramsBody['KELOMPOK'])!=''?$params['KELOMPOK']:'';
		if($ktgId<>'' OR $kelomppok<>''){
			$model= PpobMasterKtg::find()->where([
				'KTG_ID'=>$ktgId,			
			])->orWhere(['KELOMPOK'=>$kelomppok])->all();
			return $model;
		}else{		//ALL DATA
			$model= PpobMasterKtg::find()->all();
			return $model;
		}
		
	}
	
	/**	
	* Subject		: PRODUK PPOB (kategori kelompok, sebagai grouping produk)
	* Metode		: POST (CRUD)
	* URL			: http://production.kontrolgampang.com/ppob/datas/produk
	* Action		: produk
	* Param Headers	: -
	* Param URl 	: -
	* Param Body	: ID_PRODUK,ID_CODE,KTG_ID,KELOMPOK;
	* KETERANGAN	: ID_PRODUK=(ID PRODUK by KG),ID_CODE=(ID Produk By SIBISNIS)
	*				  KTG_ID=(per-kategori) OR KELOMPOK=(semua-kategori-dalam-kelompok).
	* TRIGER		: (Create/Update/Status Delete) to POLLING.
	* @author 		: ptrnov  <piter@lukison.com>
	* @since 		: 1.2
	*/
	public function actionProduk()
	{
		//$params     		= $_REQUEST;
		// $paramsHeader	= Yii::$app->request->headers;
		$paramsBody 		= Yii::$app->request->bodyParams;		
		$produkId			= isset($paramsBody['ID_PRODUK'])!=''?$paramsBody['ID_PRODUK']:'';
		$codeId				= isset($paramsBody['ID_CODE'])!=''?$paramsBody['ID_CODE']:'';
		$ktgId				= isset($paramsBody['KTG_ID'])!=''?$paramsBody['KTG_ID']:'';
		$kelompok			= isset($paramsBody['KELOMPOK'])!=''?$paramsBody['KELOMPOK']:'';
		$typeNm				= isset($paramsBody['TYPE_NM'])!=''?$paramsBody['TYPE_NM']:'';
		if(strtoupper($kelompok)=='ALL'){
			$model= PpobMasterHarga::find()->all();
		}else{
			$model= PpobMasterHarga::find()
			->where(['ID_PRODUK'=>$produkId])
			->orWhere(['ID_CODE'=>$codeId])
			->orWhere(['KTG_ID'=>$ktgId])
			->orWhere(['KELOMPOK'=>$kelompok])
			->orWhere(['TYPE_NM'=>$typeNm])->all();
		}		
		return $model;
	}
	
	/**	
	* Subject		: TRANSAKSI PPOB
	* Metode		: POST (CRUD)
	* URL			: http://production.kontrolgampang.com/ppob/datas/transaksi
	* Action		: transaksi
	* Param Headers	: -
	* Param URl 	: -
	* Param Body	: TYPE_NM,TRANS_ID,TRANS_DATE,STORE_ID,ID_PRODUK,MSISDN,ID_PELANGGAN,PEMBAYARAN
	* VALIDATION	: PASCABAYAR REQUERED	: (TYPE_NM,TRANS_ID,TRANS_DATE,STORE_ID,ID_PRODUK,ID_PELANGGAN,PEMBAYARAN) => MSISDN (Nofiry to end user)
	*				  PRABAYAR  REQUERED	: (TYPE_NM,TRANS_ID,TRANS_DATE,STORE_ID,ID_PRODUK,MSISDN,PEMBAYARAN);
	* KETERANGAN	: TRANS_ID=nomor transaksi kasir, TRANS_DATE= tanggal transaksi kasir, STORE_ID=id toko
	*				  ID_PRODUK= id produk PPOB (under KG), 
	*				  MSISDN= nomor telphone, 
	*				  ID_PELANGGAN= id pelanggan (PASCABAYAR).
	*				  PEMBAYARAN (harga jual) untuk PRABAYAR,
	*				  PEMBAYARAN (manual/respon total bayar) untuk PASCABAYAR,
	* RESPON 		: NAMA_PELANGGAN,ADMIN_BANK,TAGIHAN,TOTAL_BAYAR,MESSAGE,STRUK,TOKEN,SN,STATUS
	* KETERANGAN	: REFF_ID,NAMA_PELANGGAN,ADMIN_BANK,TAGIHAN,TOTAL_BAYAR,MESSAGE,STRUK,TOKEN (PASCABAYAR)
	* 				: MESSAGE,SN (PRABAYAR).
	*				: STATUS (0=first transaksi(triger to SIBISBIS; 1=(success B to B to A to C); 2=Panding; 3=Gagal).
	* TRIGER		: (Create/Update/Status Delete) to POLLING.
	* @author 		: ptrnov  <piter@lukison.com>
	* @since 		: 1.2
	*/
	public function actionTransaksi()
	{
		//$params     		= $_REQUEST;
		// $paramsHeader	= Yii::$app->request->headers;
		$paramsBody 		= Yii::$app->request->bodyParams;		
		$typeNm				= isset($paramsBody['TYPE_NM'])!=''?$paramsBody['TYPE_NM']:'';
		$transId			= isset($paramsBody['TRANS_ID'])!=''?$paramsBody['TRANS_ID']:'';
		$transDate			= isset($paramsBody['TRANS_DATE'])!=''?$paramsBody['TRANS_DATE']:'';
		$storeId			= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$produkId			= isset($paramsBody['ID_PRODUK'])!=''?$paramsBody['ID_PRODUK']:'';
		$telphone			= isset($paramsBody['MSISDN'])!=''?$paramsBody['MSISDN']:'';
		$pelanganId			= isset($paramsBody['ID_PELANGGAN'])!=''?$paramsBody['ID_PELANGGAN']:'';
		$pembayaran			= isset($paramsBody['PEMBAYARAN'])!=''?$paramsBody['PEMBAYARAN']:'';
		
		//====== VALIDASI ============
		//CREATE : INPUT=TRANS_ID+ID_PRODUK+MSISDN+ID_PELANGGAN
		//UPDATE : GET TYPE_NM (check PASCABAYAR/PRABAYAR) + INPUT=TRANS_ID+ID_PRODUK+MSISDN+ID_PELANGGAN;
		//FUNGSI VALIDASI = AGAR TIDAK TAPAT DI UPDATE(JIKA UPDATE DIABAIKAN).
		//$model->scenario = "pascabayar";
		//$model->scenario = "prabayar";
		
		if ($typeNm<>''){
			if (strtoupper($typeNm)==strtoupper('PASCABAYAR')){				
				//== VALIDASI TRANSAKSI PASCABAYAR / NOT FOR UPDATE ===
				$validationInputPascabayar = PpobTransaksi::find()->where([
					'TRANS_ID'=>$transId,
					'STORE_ID'=>$storeId,
					'ID_PRODUK'=>$produkId,
					'ID_PELANGGAN'=>$pelanganId,	
				])->one();		
				if(!$validationInputPascabayar){
					//== SIMPAN NEW TRANSAKSI PASCABAYAR ===
					$modelNewPascaBayar = new PpobTransaksi();
					$modelNewPascaBayar->scenario = "pascabayar";
					$modelNewPascaBayar->scenario = "prabayar";
					$modelNewPascaBayar->TRANS_ID=$transId;
					$modelNewPascaBayar->TRANS_DATE=$transDate;
					$modelNewPascaBayar->STORE_ID=$storeId;
					$modelNewPascaBayar->ID_PRODUK=$produkId;
					$modelNewPascaBayar->ID_PELANGGAN=$pelanganId;
					$modelNewPascaBayar->MSISDN=$telphone;
					$modelNewPascaBayar->PEMBAYARAN=$pembayaran;
					if($modelNewPascaBayar->save()){
						$modelViewPascabayar=PpobTransaksi::find()->where([
							'TRANS_ID'=>$transId,
							'STORE_ID'=>$storeId,
							'ID_PRODUK'=>$produkId,
							'ID_PELANGGAN'=>$pelanganId,						
						])->one();
						return array('transaksi'=>$modelViewPascabayar);
					}else{
						return array('error'=>$modelNewPascaBayar->errors);
					}
				}else{
					//== UPDATE DATA TRANSAKSI PASCABAYAR ===
					$validationInputPascabayar->PEMBAYARAN=$pembayaran;
					if($validationInputPascabayar->save()){
						$responPascabayar = PpobTransaksi::find()->where(['TRANS_UNIK'=>$validationInputPascabayar['TRANS_UNIK']])->one();
						return array('respon'=>$responPascabayar);
					}else{
						return array('error'=>'not-update');
					}								
				}
				//=== END TRANSAKSI PASCABAYAR ====
				
			}elseif(strtoupper($typeNm)==strtoupper('PRABAYAR')){
				//== VALIDASI TRANSAKSI PRABAYAR / NOT FOR UPDATE ===
				$validationInputPrabayar = PpobTransaksi::find()->where([
					'TRANS_ID'=>$transId,
					'STORE_ID'=>$storeId,
					'ID_PRODUK'=>$produkId,
					'MSISDN'=>$telphone,	
				])->one();	
				if(!$validationInputPrabayar){				
					//== SIMPAN NEW TRANSAKSI PRABAYAR ===
					$modelNewPrabayar = new PpobTransaksi();
					$modelNewPrabayar->scenario = "prabayar";
					$modelNewPrabayar->TRANS_ID=$transId;
					$modelNewPrabayar->TRANS_DATE=$transDate;
					$modelNewPrabayar->STORE_ID=$storeId;
					$modelNewPrabayar->ID_PRODUK=$produkId;
					$modelNewPrabayar->MSISDN=$telphone;
					$modelNewPrabayar->PEMBAYARAN=$pembayaran;
					if($modelNewPrabayar->save()){
						$modelViewPrabayar=PpobTransaksi::find()->where([
							'TRANS_ID'=>$transId,
							'STORE_ID'=>$storeId,
							'ID_PRODUK'=>$produkId,
							'MSISDN'=>$telphone,					
						])->one();
						return array('transaksi'=>$modelViewPrabayar);
					}else{
						return array('error'=>$modelNewPrabayar->errors);
					}
				}else{
					//== UPDATE DATA TRANSAKSI PRABAYAR ===
					$validationInputPrabayar->PEMBAYARAN=$pembayaran;
					if($validationInputPrabayar->save()){
						$responPrabayar = PpobTransaksi::find()->where(['TRANS_UNIK'=>$validationInputPrabayar['TRANS_UNIK']])->one();
						return array('respon'=>$responPrabayar);
					}else{
						return array('error'=>'not-update');
					}					
				}
				//=== END TRANSAKSI PRABAYAR ====
				
			}else{
				return array('error'=>'Data Not Valid');
			}
		}else{
			return array('error'=>'typename-not-blank');
		}
	}
	
	/**	
	* Subject		: Saldo PPOB (Saldo Per-Store)
	* Metode		: POST (CRUD)
	* URL			: http://production.kontrolgampang.com/ppob/datas/saldo
	* Action		: Saldo
	* Param Headers	: -
	* Param URl 	: -
	* Param Body	: STORE_ID;
	* KETERANGAN	: ID_PRODUK=(ID PRODUK by KG),ID_CODE=(ID Produk By SIBISNIS)
	*				  KTG_ID=(per-kategori) OR KELOMPOK=(semua-kategori-dalam-kelompok).
	* TRIGER		: (Create/Update/Status Delete) to POLLING.
	* @author 		: ptrnov  <piter@lukison.com>
	* @since 		: 1.2
	*/
	public function actionSaldo()
	{
		//$params     		= $_REQUEST;
		// $paramsHeader	= Yii::$app->request->headers;
		$paramsBody 		= Yii::$app->request->bodyParams;		
		$storeID			= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		
		if($storeID==''){
			$model= PpobSaldoStore::find()->all();
		}else{
			$model= PpobSaldoStore::find()
			->where(['STORE_ID'=>$storeID])->one();
		}		
		return $model;
	}
	
}
    
	
	
	
	
