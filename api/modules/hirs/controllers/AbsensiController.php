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
use api\modules\hirs\models\HrdAbsen;
use api\modules\hirs\models\HrdAbsenImg;

class AbsensiController extends ActiveController
{

	public $modelClass = 'api\modules\hirs\models\HrdAbsen';

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
		//==KEY MASTER ==
		$absenId		= isset($paramsBody['ABSEN_ID'])!=''?$paramsBody['ABSEN_ID']:'';
		$oflineID		= isset($paramsBody['OFLINE_ID'])!=''?$paramsBody['OFLINE_ID']:'';
		//==KEY==
		$karyawanID		= isset($paramsBody['KARYAWAN_ID'])!=''?$paramsBody['KARYAWAN_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';

		//===Property==
		$tgl			= isset($paramsBody['TGL'])!=''?$paramsBody['TGL']:'';
		$waktu			= isset($paramsBody['WAKTU'])!=''?$paramsBody['WAKTU']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$note			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		//TABLE IMAGE
		$absenImage		= isset($paramsBody['ABSEN_IMAGE'])!=''?$paramsBody['ABSEN_IMAGE']:'';
		$latitude		= isset($paramsBody['LATITUDE'])!=''?$paramsBody['LATITUDE']:'';
		$longitude		= isset($paramsBody['LONGITUDE'])!=''?$paramsBody['LONGITUDE']:'';
		
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: ABSENSI
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/hirs/absensi
			* Body Param	: METHODE=GET & STORE_ID(Key) or  KARYAWAN_ID(key) or ABSEN_ID(key) & TGL (filter)
			*				: STORE_ID='' All data karyawan per-STORE_ID. and  TGL (filter)
			*				: STORE_ID<>'' data karyawan per-KARYAWAN_ID. and  TGL (filter)
			*/
			if($store_id<>''){
				if($karyawanID<>''){
					if($tgl<>''){
						//Model ABSENSI BY STORE_ID
						$modelCnt= HrdAbsen::find()->where(['STORE_ID'=>$store_id,'KARYAWAN_ID'=>$karyawanID,'TGL'=>date('Y-m-d', strtotime($tgl))])
												   ->orWhere(['STORE_ID'=>$store_id,'OFLINE_ID'=>$oflineID,'TGL'=>date('Y-m-d', strtotime($tgl))])
												   ->count();
						$model= HrdAbsen::find()->where(['STORE_ID'=>$store_id,'KARYAWAN_ID'=>$karyawanID,'TGL'=>date('Y-m-d', strtotime($tgl))])
												->orWhere(['STORE_ID'=>$store_id,'OFLINE_ID'=>$oflineID,'TGL'=>date('Y-m-d', strtotime($tgl))])
												->all();		
						if($modelCnt){
							return array('LIST_ABSENSI'=>$model);
						}else{
							return array('error'=>'ABSENSI-KARYAWAN_ID-is-empty');
						};
					}else{
						//Model ABSENSI BY STORE_ID
						$modelCnt= HrdAbsen::find()->where(['STORE_ID'=>$store_id,'KARYAWAN_ID'=>$karyawanID])->count();
						$model= HrdAbsen::find()->where(['STORE_ID'=>$store_id,'KARYAWAN_ID'=>$karyawanID])->all();		
						if($modelCnt){
							return array('LIST_ABSENSI'=>$model);
						}else{
							return array('error'=>'ABSENSI-KARYAWAN_ID-is-empty');
						};
					}
				}else{
					if($tgl<>''){
						//Model ABSENSI BY KARYAWAN_ID
						$modelCnt=HrdAbsen::find()->where(['STORE_ID'=>$store_id,'TGL'=>date('Y-m-d', strtotime($tgl))])
												   ->orWhere(['STORE_ID'=>$store_id,'OFLINE_ID'=>$oflineID,'TGL'=>date('Y-m-d', strtotime($tgl))])
												   ->count();
						$model= HrdAbsen::find()->where(['STORE_ID'=>$store_id,'TGL'=>date('Y-m-d', strtotime($tgl))])
											    ->orWhere(['STORE_ID'=>$store_id,'OFLINE_ID'=>$oflineID,'TGL'=>date('Y-m-d', strtotime($tgl))])
											    ->all();		
						if($modelCnt){
							return array('LIST_ABSENSI'=>$model);
						}else{
							return array('error'=>'ABSENSI-KARYAWAN_ID-is-empty');
						};
					}else{
						//Model ABSENSI BY KARYAWAN_ID
						$modelCnt= HrdAbsen::find()->where(['STORE_ID'=>$store_id])->count();
						$model= HrdAbsen::find()->where(['STORE_ID'=>$store_id])->all();		
						if($modelCnt){
							return array('LIST_ABSENSI'=>$model);
						}else{
							return array('error'=>'ABSENSI-KARYAWAN_ID-is-empty');
						};
					}
					
				}
			}else{
				if($karyawanID<>''){
					if($tgl<>''){
						//Model Openclose BY STORE_ID
						$modelCnt= HrdAbsen::find()->where(['KARYAWAN_ID'=>$karyawanID])->andWhere(['TGL'=>date('Y-m-d', strtotime($tgl))])->count();
						$model= HrdAbsen::find()->where(['KARYAWAN_ID'=>$karyawanID])->andWhere(['TGL'=>date('Y-m-d', strtotime($tgl))])->all();		
						if($modelCnt){
							return array('LIST_ABSENSI'=>$model);
						}else{
							return array('error'=>'ABSENSI-KARYAWAN_ID-is-empty');
						};
					}else{
						//Model Openclose BY STORE_ID
						$modelCnt= HrdAbsen::find()->where(['KARYAWAN_ID'=>$karyawanID])->count();
						$model= HrdAbsen::find()->where(['KARYAWAN_ID'=>$karyawanID])->all();		
						if($modelCnt){
							return array('LIST_ABSENSI'=>$model);
						}else{
							return array('error'=>'ABSENSI-KARYAWAN_ID-is-empty');
						};
					}
				}else{
					return array('result'=>'ABSENSI-KARYAWAN_ID-is-empty');
				}
			}		 		
		}elseif($metode=='POST'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: ABSENSI
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/hirs/absensi
			* Body Param	: METHODE=POST & STORE_ID(Key) or  KARYAWAN_ID(key) or ABSEN_ID(key) 
			*				  STORE_ID='' All data karyawan per-STORE_ID.
			*				  STORE_ID<>'' data karyawan per-KARYAWAN_ID.
			* 				  PARAM ABSEN_IMAGE : Base64
			*/
			$modelNew = new HrdAbsen();
			$modelNew->scenario = "create";			
			if ($store_id<>''){$modelNew->STORE_ID=$store_id;};
			if ($oflineID<>''){$modelNew->OFLINE_ID=$oflineID;};
			if ($karyawanID<>''){$modelNew->KARYAWAN_ID=$karyawanID;};			
			if ($tgl<>''){$modelNew->TGL=date('Y-m-d', strtotime($tgl));};
			if ($waktu<>''){$modelNew->WAKTU=date('H:i:s', strtotime($waktu));};
			if ($latitude<>''){$modelNew->LATITUDE=$latitude;};
			if ($longitude<>''){$modelNew->LONGITUDE=$longitude;};
			if ($stt<>''){$modelNew->STATUS=$stt;};
			if ($note<>''){$modelNew->DCRP_DETIL=$note;};
			
			if($modelNew->save()){
				$rsltMax=HrdAbsen::find()->where(['KARYAWAN_ID'=>$karyawanID])->andWhere(['TGL'=>date('Y-m-d', strtotime($tgl))])->max('ABSEN_ID');				
				$modelImage=HrdAbsenImg::find()->where(['KARYAWAN_ID'=>$karyawanID,'ABSEN_ID'=>$rsltMax])->one();
				if ($absenImage<>''){$modelImage->ABSEN_IMAGE=$absenImage;};
				$modelImage->save();
				$modelView=HrdAbsen::find()->where(['ABSEN_ID'=>$rsltMax,'KARYAWAN_ID'=>$karyawanID])->one();
				return array('LIST_ABSENSI'=>$modelView);
			
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
		* Subject		: ABSENSI
		* Metode		: PUT (UPDATEE)
		* URL			: http://production.kontrolgampang.com/hirs/absensi
		* Body Param	: KARYAWAN_ID(Master Key) & ABSEN_ID(Master Key) 
		* 				  PARAM ABSEN_IMAGE : Base64
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;				
		//==KEY MASTER==	
		$absenId		= isset($paramsBody['ABSEN_ID'])!=''?$paramsBody['ABSEN_ID']:'';
		$oflineID		= isset($paramsBody['OFLINE_ID'])!=''?$paramsBody['OFLINE_ID']:'';
		//==KEY==
		$karyawanID		= isset($paramsBody['KARYAWAN_ID'])!=''?$paramsBody['KARYAWAN_ID']:'';
		$store_id		= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
	
		//===Property==
		$tgl			= isset($paramsBody['TGL'])!=''?$paramsBody['TGL']:'';
		$waktu			= isset($paramsBody['WAKTU'])!=''?$paramsBody['WAKTU']:'';
		$stt			= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$note			= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';		
		//TABLE IMAGE
		$absenImage		= isset($paramsBody['ABSEN_IMAGE'])!=''?$paramsBody['ABSEN_IMAGE']:'';
		
		$modelEdit=HrdAbsen::find()->where(['ABSEN_ID'=>$absenId,'KARYAWAN_ID'=>$karyawanID])->one();
		$latitude		= isset($paramsBody['LATITUDE'])!=''?$paramsBody['LATITUDE']:'';
		$longitude		= isset($paramsBody['LONGITUDE'])!=''?$paramsBody['LONGITUDE']:'';			
		
		if($modelEdit){			
			//if ($store_id<>''){$modelEdit->STORE_ID=$store_id;};
			//if ($karyawanID<>''){$modelEdit->KARYAWAN_ID=$karyawanID;};
			if ($oflineID<>''){$modelEdit->OFLINE_ID=$oflineID;};
			if ($tgl<>''){$modelEdit->TGL=date('Y-m-d', strtotime($tgl));};
			if ($waktu<>''){$modelEdit->WAKTU=date('H:i:s', strtotime($waktu));};
			if ($latitude<>''){$modelNew->LATITUDE=$latitude;};
			if ($longitude<>''){$modelNew->LONGITUDE=$longitude;};
			if ($stt<>''){$modelEdit->STATUS=$stt;};
			if ($note<>''){$modelEdit->DCRP_DETIL=$note;};
			$modelEdit->scenario = "update";
			if($modelEdit->save()){
				$modelImage=HrdAbsenImg::find()->where(['KARYAWAN_ID'=>$karyawanID,'ABSEN_ID'=>$absenId])->one();
				if ($absenImage<>''){$modelImage->ABSEN_IMAGE=$absenImage;};
				$modelImage->save();
				$modelView=HrdAbsen::find()->where(['ABSEN_ID'=>$absenId,'KARYAWAN_ID'=>$karyawanID])->one();
				return array('LIST_KARYAWAN'=>$modelView);
			}else{
				return array('result'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'ABSEN_ID-not-exist');
		}
	}
}