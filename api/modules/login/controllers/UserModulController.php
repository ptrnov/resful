<?php

namespace api\modules\login\controllers;

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

use api\modules\login\models\AppModul;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: List Modul Permission.
  * Metode		: POST (CRUD)
  * URL			: http://production.kontrolgampang.com/login/user-moduls
  * Body Param	: MODUL_ID (key)
  *				  {"result": "Modul-Not-Exist"}	=> Modul tidak ada atau tidak ditemukan.
 */
class UserModulController extends ActiveController
{
    public $modelClass = 'api\modules\login\models\AppModul';
	
	/**
     * Behaviors
	 * Mengunakan Auth HttpBasicAuth.
	 * Chacking logintest.
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
					'application/json' => Response::FORMAT_JSON,
				],
			],
			'corsFilter' => [
				'class' => \yii\filters\Cors::className(),
				'cors' => [
					// restrict access to
					//'Origin' => ['http://lukisongroup.com', 'http://lukisongroup.int','http://localhost','http://103.19.111.1','http://202.53.354.82'],
					'Origin' => ['*'],
					'Access-Control-Request-Method' => ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
					//'Access-Control-Request-Headers' => ['*'],
					'Access-Control-Request-Headers' => ['*'],
					// Allow only headers 'X-Wsse'
					'Access-Control-Allow-Credentials' => false,
					// Allow OPTIONS caching
					'Access-Control-Max-Age' => 3600,

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
		unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
		return $actions;
	} 
	public function actionIndex()
    {
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: List Modul Permission.
		  * Metode		: GET (Views)
		  * URL			: http://production.kontrolgampang.com/login/user-moduls
		  * Body Param	: No Filed.
		  *				  {"result": "Modul-Not-Exist"}	=> Modul tidak ada atau tidak ditemukan.
		 */
		$paramsBody 			= Yii::$app->request->bodyParams;
	
		$cntModul= AppModul::find()->count();
		if($cntModul){
			//$modalParent= AppModul::find()->where('MODUL_ID=MODUL_GRP')->orderBy([SORT_PARENT=>SORT_ASC])->all();
			//$parentModul=ArrayHelper::getColumn($modalParent, 'MODUL_ID');
			//$modalView= AppModul::find()->where('MODUL_ID=MODUL_GRP')->orderBy([SORT_PARENT=>SORT_ASC])->all();
			$modalView= AppModul::find()->orderBy([SORT=>SORT_ASC,SORT_PARENT=>SORT_ASC])->all();
			return array('LIST_MODULS'=>ArrayHelper::index($modalView, null, 'MODUL_GRP'));
			//return ArrayHelper::map($modalView, 'MODUL_NM', 'MODUL_NM', 'MODUL_GRP');;
		}else{
			return array('result'=>'Store-Empty');
		}
	}	
	
	public function actionCreate()
    {
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: List Modul Permission.
		  * Metode		: POST (CREATE)
		  * URL			: http://production.kontrolgampang.com/login/user-moduls
		  * Body Param	: (key)=Auto Generate [MODUL_NM,MODUL_GRP,SORT_PARENT,SORT,MODUL_DCRP]
		  *				  {"result": "Modul-Not-Exist"}	=> Modul tidak ada atau tidak ditemukan.
		 */
		$paramsBody 		= Yii::$app->request->bodyParams;
		$mdlNM				= isset($paramsBody['MODUL_NM'])!=''?$paramsBody['MODUL_NM']:'';
		$mdlGrp				= isset($paramsBody['MODUL_GRP'])!=''?$paramsBody['MODUL_GRP']:'';
		$mdlSrtGrp			= isset($paramsBody['SORT_PARENT'])!=''?$paramsBody['SORT_PARENT']:'';
		$mdlSrt				= isset($paramsBody['SORT'])!=''?$paramsBody['SORT']:'';
		$msdDscp			= isset($paramsBody['MODUL_DCRP'])!=''?$paramsBody['MODUL_DCRP']:'';
		
		$model= new AppModul();
		//$model->scenario = 'createuserapi';
		$model->MODUL_NM=$mdlNM;
		$model->MODUL_GRP=$mdlGrp;
		$model->SORT_PARENT=$mdlSrtGrp;
		$model->SORT=$mdlSrt;
		$model->MODUL_DCRP =$msdDscp;
		if ($model->save()){
			$rsltMax=AppModul::find()->max('MODUL_ID');
			$modelView=AppModul::find()->where(['MODUL_ID'=>$rsltMax])->one();
			return array('LIST_MODUL'=>$modelView);
		}else{
			return array('result'=>$model->errors);
		} 
	}
	
	public function actionUpdate()
    {
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: List Modul Permission.
		  * Metode		: POST (UPDATE)
		  * URL			: http://production.kontrolgampang.com/login/user-moduls
		  * Body Param	: MODUL_ID(key) [MODUL_NM,MODUL_GRP,SORT_PARENT,SORT,MODUL_DCRP,MODUL_STS]
		  *				  {"result": "Modul-Not-Exist"}	=> Modul tidak ada atau tidak ditemukan.
		 */
		$paramsBody 		= Yii::$app->request->bodyParams;
		$mdlID				= isset($paramsBody['MODUL_ID'])!=''?$paramsBody['MODUL_ID']:'';
		$mdlNM				= isset($paramsBody['MODUL_NM'])!=''?$paramsBody['MODUL_NM']:'';
		$mdlGrp				= isset($paramsBody['MODUL_GRP'])!=''?$paramsBody['MODUL_GRP']:'';
		$mdlSrtGrp			= isset($paramsBody['SORT_PARENT'])!=''?$paramsBody['SORT_PARENT']:'';
		$mdlSrt				= isset($paramsBody['SORT'])!=''?$paramsBody['SORT']:'';
		$mdlStt				= isset($paramsBody['MODUL_STS'])!=''?$paramsBody['MODUL_STS']:'';
		$msdDscp			= isset($paramsBody['MODUL_DCRP'])!=''?$paramsBody['MODUL_DCRP']:'';
		
		if($mdlID){
			$cntModul= AppModul::find()->where(['MODUL_ID'=>$mdlID])->count();
			if($cntModul){
				$modalModul= AppModul::find()->where(['MODUL_ID'=>$mdlID])->one();
				if ($mdlNM!=''){$modalModul->MODUL_NM=$mdlNM;};
				if ($mdlGrp!=''){$modalModul->MODUL_GRP=$mdlGrp;};
				if ($mdlSrtGrp!=''){$modalModul->SORT_PARENT=$mdlSrtGrp;};
				if ($mdlSrt!=''){$modalModul->SORT=$mdlSrt;};
				if ($mdlStt!=''){$modalModul->MODUL_STS=$mdlStt;};
				if ($msdDscp!=''){$modalModul->MODUL_DCRP=$msdDscp;};
				if($modalModul->save()){
					$modelView=AppModul::find()->where(['MODUL_ID'=>$mdlID])->one();
					return array('LIST_MODUL'=>$modelView);
				}else{
					return array('result'=>'failed-save');
				}				
			}else{
				return array('result'=>'ModulID-Not-Exist');
			}
		}else{
			return array('result'=>'MODUL_ID-Empty');
		}
	}
	
	public function actionDelete()
    {        	
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: List Modul Permission.
		  * Metode		: DELETE (DELETE)
		  * URL			: http://production.kontrolgampang.com/login/user-moduls
		  * Body Param	: MODUL_ID(key) [MODUL_STS]; STATUS=3
		  *				  {"result": "Modul-Not-Exist"}	=> Modul tidak ada atau tidak ditemukan.
		 */
		$paramsBody 		= Yii::$app->request->bodyParams;
		$mdlID				= isset($paramsBody['MODUL_ID'])!=''?$paramsBody['MODUL_ID']:'';
		
		if($mdlID){
			$modelModul= AppModul::find()->where(['MODUL_ID'=>$mdlID])->one();
			//$model->scenario = 'createuserapi';
			$modelModul->MODUL_STS=0;
			if ($modelModul->save()){
				$modelViews= AppModul::find()->where(['MODUL_ID'=>$mdlID])->one();
				return array('store'=>$modelViews);
			}else{
				return array('result'=>$modelStore->errors);
			} 									
		}else{
			return array('result'=>'Not Exist Modul');
		}
	}
}


