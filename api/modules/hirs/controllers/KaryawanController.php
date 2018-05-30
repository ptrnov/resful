<?php

namespace api\modules\hirs\controllers;

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

use api\modules\hirs\models\Karyawan;
use api\modules\master\models\SyncPoling;

class KaryawanController extends ActiveController
{

	public $modelClass = 'api\modules\hirs\models\Karyawan';

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
		//==KEY==
		$karyawanID		= isset($paramsBody['KARYAWAN_ID'])!=''?$paramsBody['KARYAWAN_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		//===Property==
		$namaDepan		= isset($paramsBody['NAMA_DPN'])!=''?$paramsBody['NAMA_DPN']:'';
		$namaTengah		= isset($paramsBody['NAMA_TGH'])!=''?$paramsBody['NAMA_TGH']:'';
		$namaBelakang	= isset($paramsBody['NAMA_BLK'])!=''?$paramsBody['NAMA_BLK']:'';
		$ktp			= isset($paramsBody['KTP'])!=''?$paramsBody['KTP']:'';
		$tempatLahir	= isset($paramsBody['TMP_LAHIR'])!=''?$paramsBody['TMP_LAHIR']:'';
		$jenisKelamin	= isset($paramsBody['GENDER'])!=''?$paramsBody['GENDER']:'';
		$alamat			= isset($paramsBody['ALAMAT'])!=''?$paramsBody['ALAMAT']:'';
		$statusNikah	= isset($paramsBody['STS_NIKAH'])!=''?$paramsBody['STS_NIKAH']:'';
		$tlp			= isset($paramsBody['TLP'])!=''?$paramsBody['TLP']:'';
		$hp				= isset($paramsBody['HP'])!=''?$paramsBody['HP']:'';
		$email			= isset($paramsBody['EMAIL'])!=''?$paramsBody['EMAIL']:'';
		$note			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		
		//POLING SYNC nedded ACCESS_ID
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		= isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
		
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: KARYAWAN
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/hirs/karyawans
			* Body Param	: METHODE=GET & STORE_ID(Key) or  KARYAWAN_ID(key) 
			*				: STORE_ID='' All data karyawan per-STORE_ID.
			*				: STORE_ID<>'' data karyawan per-KARYAWAN_ID.
			*/
			if($store_id<>''){
				if($karyawanID<>''){
					//Model Openclose BY STORE_ID
					$modelCnt= Karyawan::find()->where(['KARYAWAN_ID'=>$karyawanID])->count();
					$model= Karyawan::find()->where(['KARYAWAN_ID'=>$karyawanID])->one();		
					if($modelCnt){
						/*===========================
						 *=== POLLING UPDATE UUID ===
						 *===========================
						*/
						$dataHeader=explode('.',$store_id);
						if ($tblPooling=='TBL_KARYAWAN'){						
							//==GET DATA POLLING
							$modelPoling=SyncPoling::find()->where([
								 'NM_TABLE'=>'TBL_KARYAWAN',
								 'ACCESS_GROUP'=>$dataHeader[0],
								 'STORE_ID'=>$store_id,
								 'PRIMARIKEY_VAL'=>$karyawanID
							])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
							//==UPDATE DATA POLLING UUID
							if($modelPoling){							
								foreach($modelPoling as $row => $val){
									$modelSimpan=SyncPoling::find()->where([
										 'NM_TABLE'=>'TBL_KARYAWAN',
										 'ACCESS_GROUP'=>$dataHeader[0],
										 'STORE_ID'=>$store_id,
										 'PRIMARIKEY_VAL'=>$karyawanID,
										 'TYPE_ACTION'=>$val->TYPE_ACTION
									])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
									if($modelSimpan AND $paramlUUID){
										$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
										$modelSimpan->save();
									}
								}							
							}				
						}
						return array('LIST_KARYAWAN'=>$model);
					}else{
						return array('error'=>$model->errors);
					};
				}else{
					//Model Openclose BY STORE_ID
					$modelCnt= Karyawan::find()->where(['STORE_ID'=>$store_id])->count();
					$model= Karyawan::find()->where(['STORE_ID'=>$store_id])->all();		
					if($modelCnt){
						return array('LIST_KARYAWAN'=>$model);
					}else{
						return array('error'=>$model->errors);
					};
				}
			}else{
				if($karyawanID<>''){
					//Model Openclose BY STORE_ID
					$modelCnt= Karyawan::find()->where(['KARYAWAN_ID'=>$karyawanID])->count();
					$model= Karyawan::find()->where(['KARYAWAN_ID'=>$karyawanID])->one();		
					if($modelCnt){
						return array('LIST_KARYAWAN'=>$model);
					}else{
						return array('error'=>$model->errors);
					};
				}else{
					return array('result'=>'STORE_ID-KARYAWAN_ID-is-empty');
				}
			}		 		
		}elseif($metode=='POST'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: KARYAWAN
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/hirs/karyawans
			* Body Param	: METHODE=POST & STORE_ID(Key) or  KARYAWAN_ID(key) 
			* PROPERTIES	: ACCESS_GROUP,STORE_ID,NAMA_DPN,NAMA_TGH,NAMA_BLK,KTP,GENDER,
			*				  ALAMAT,STS_NIKAH,TLP,HP,EMAIL,DCRP_DETIL
			*/
			$modelNew = new Karyawan();
			$modelNew->scenario = "create";
			if ($store_id<>''){$modelNew->STORE_ID=$store_id;};
			if ($namaDepan<>''){$modelNew->NAMA_DPN=$namaDepan;};
			if ($namaTengah<>''){$modelNew->NAMA_TGH=$namaTengah;};
			if ($namaBelakang<>''){$modelNew->NAMA_BLK=$namaBelakang;};
			if ($ktp<>''){$modelNew->KTP=$ktp;};
			if ($jenisKelamin<>''){$modelNew->GENDER=$jenisKelamin;};
			if ($alamat<>''){$modelNew->ALAMAT=$alamat;};
			if ($statusNikah<>''){$modelNew->STS_NIKAH=$statusNikah;};
			if ($tlp<>''){$modelNew->TLP=$tlp;};
			if ($hp<>''){$modelNew->HP=$hp;};
			if ($email<>''){$modelNew->EMAIL=$email;};
			if ($note<>''){$modelNew->DCRP_DETIL=$note;};
			if($modelNew->save()){
				$rsltMax=Karyawan::find()->where(['STORE_ID'=>$store_id])->max('KARYAWAN_ID');
				$modelView=Karyawan::find()->where(['KARYAWAN_ID'=>$rsltMax])->one();
				return array('LIST_KARYAWAN'=>$modelView);
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
		* Subject		: KARYAWAN
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/hirs/karyawans
		* Body Param	: KARYAWAN_ID(key) 
		* PROPERTIES	: ACCESS_GROUP,STORE_ID,NAMA_DPN,NAMA_TGH,NAMA_BLK,KTP,GENDER,
		*				  ALAMAT,STS_NIKAH,TLP,HP,EMAIL,DCRP_DETIL
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		$karyawanID		= isset($paramsBody['KARYAWAN_ID'])!=''?$paramsBody['KARYAWAN_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		//===Property==
		$namaDepan		= isset($paramsBody['NAMA_DPN'])!=''?$paramsBody['NAMA_DPN']:'';
		$namaTengah		= isset($paramsBody['NAMA_TGH'])!=''?$paramsBody['NAMA_TGH']:'';
		$namaBelakang	= isset($paramsBody['NAMA_BLK'])!=''?$paramsBody['NAMA_BLK']:'';
		$ktp			= isset($paramsBody['KTP'])!=''?$paramsBody['KTP']:'';
		$tempatLahir	= isset($paramsBody['TMP_LAHIR'])!=''?$paramsBody['TMP_LAHIR']:'';
		$jenisKelamin	= isset($paramsBody['GENDER'])!=''?$paramsBody['GENDER']:'';
		$alamat			= isset($paramsBody['ALAMAT'])!=''?$paramsBody['ALAMAT']:'';
		$statusNikah	= isset($paramsBody['STS_NIKAH'])!=''?$paramsBody['STS_NIKAH']:'';
		$tlp			= isset($paramsBody['TLP'])!=''?$paramsBody['TLP']:'';
		$hp				= isset($paramsBody['HP'])!=''?$paramsBody['HP']:'';
		$email			= isset($paramsBody['EMAIL'])!=''?$paramsBody['EMAIL']:'';
		$note			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		$modelEdit=Karyawan::find()->where(['KARYAWAN_ID'=>$karyawanID])->one();		
		if($modelEdit){			
			if ($store_id<>''){$modelEdit->STORE_ID=$store_id;};
			if ($namaDepan<>''){$modelEdit->NAMA_DPN=$namaDepan;};
			if ($namaTengah<>''){$modelEdit->NAMA_TGH=$namaTengah;};
			if ($namaBelakang<>''){$modelEdit->NAMA_BLK=$namaBelakang;};
			if ($ktp<>''){$modelEdit->KTP=$ktp;};
			if ($jenisKelamin<>''){$modelEdit->GENDER=$jenisKelamin;};
			if ($alamat<>''){$modelEdit->ALAMAT=$alamat;};
			if ($statusNikah<>''){$modelEdit->STS_NIKAH=$statusNikah;};
			if ($tlp<>''){$modelEdit->TLP=$tlp;};
			if ($hp<>''){$modelEdit->HP=$hp;};
			if ($email<>''){$modelEdit->EMAIL=$email;};
			if ($note<>''){$modelEdit->DCRP_DETIL=$note;};
			if ($stt<>''){$modelEdit->STATUS=$stt;};
			$modelEdit->scenario = "update";			
			if($modelEdit->save()){
				$modelView=Karyawan::find()->where(['KARYAWAN_ID'=>$karyawanID])->one();
				return array('LIST_KARYAWAN'=>$modelView);
			}else{
				return array('result'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'LIST_KARYAWAN-not-exist');
		}
	}
}