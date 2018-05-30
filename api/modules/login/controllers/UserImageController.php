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
use api\modules\login\models\UserImage;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: USER IMAGE (PROFILE)
  * URL			: http://production.kontrolgampang.com/login/user-images
 */
class UserImageController extends ActiveController
{
    public $modelClass = 'api\modules\login\models\UserImage';
	
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

	public function actionCreate()
    {        
		$paramsBody 	= Yii::$app->request->bodyParams;
		$metode			= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';		
		//KEY
		$accessId		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		//PROPERTY
		$accessImage	= isset($paramsBody['ACCESS_IMAGE'])!=''?$paramsBody['ACCESS_IMAGE']:'';
		$accessStt		= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$accessNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		
		if($metode=='GET'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: USER IMAGE (PROFILE)
			* Metode		: POST (VIEW)
			* URL			: http://production.kontrolgampang.com/login/user-images
			* Body Param	: METHODE=GET & ACCESS_ID(Key) 
			*/
			if($accessId<>''){				
				$modelCnt= UserImage::find()->where(['ACCESS_ID'=>$accessId])->count();
				$model= UserImage::find()->where(['ACCESS_ID'=>$accessId])->one();					
				if($modelCnt){
					return array('ACCESS_IMAGE'=>$model);
				}else{
					return array('result'=>'data-empty');
				}				
			}else{				
				return array('result'=>'ACCESS_ID-not-exist');	
			}			
		}elseif($metode=='POST'){
			/**
			* @author 		: ptrnov  <piter@lukison.com>
			* @since 		: 1.2
			* Subject		: USER IMAGE (PROFILE)
			* Metode		: POST (CREATE)
			* URL			: http://production.kontrolgampang.com/login/user-images
			* Body Param	: METHODE=POST & ACCESS_ID(Key) or  ACCESS_IMAGE(key) 
			* PROPERTY		: STATUS,DCRP_DETIL
			*/
			$modelCnt= UserImage::find()->where(['ACCESS_ID'=>$accessId])->count();
			if(!$modelCnt){
				$modelNew = new UserImage();
				$modelNew->scenario='create';
				if ($accessId<>''){$modelNew->ACCESS_ID=$accessId;};
				if ($accessImage<>''){$modelNew->ACCESS_IMAGE=$accessImage;};
				if ($accessStt<>''){$modelNew->STATUS=$accessStt;};
				if ($accessNote<>''){$modelNew->DCRP_DETIL=$accessNote;};
				if($modelNew->save()){
					$modelView=UserImage::find()->where(['ACCESS_ID'=>$accessId])->one();	;
					return array('ACCESS_IMAGE'=>$modelView);
				}else{
					return array('error'=>$modelNew->errors);
				}
			}else{
				return array('result'=>'ACCESS_ID-already-exist');
			}
		}else{
			return array('result'=>'Methode-Unknown');
		}		
	}
	
	public function actionUpdate()
    {  	
		/**
		* @author 		: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Subject		: USER IMAGE (PROFILE)
		* Metode		: PUT (UPDATE)
		* URL			: http://production.kontrolgampang.com/login/user-images
		* Body Param	: ACCESS_ID(Key)
		* PROPERTY		: ACCESS_IMAGE,STATUS,DCRP_DETIL
		*/
		$paramsBody 	= Yii::$app->request->bodyParams;
		//KEY
		$accessId		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		//PROPERTY
		$accessImage	= isset($paramsBody['ACCESS_IMAGE'])!=''?$paramsBody['ACCESS_IMAGE']:'';
		$accessStt		= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		$accessNote		= isset($paramsBody['DCRP_DETIL'])!=''?$paramsBody['DCRP_DETIL']:'';
		
		$modelEdit=UserImage::find()->where(['ACCESS_ID'=>$accessId])->one();
		if($modelEdit){			
			if ($accessImage<>''){$modelEdit->ACCESS_IMAGE=$accessImage;};
			if ($accessStt<>''){$modelEdit->STATUS=$accessStt;};
			if ($accessNote<>''){$modelEdit->DCRP_DETIL=$accessNote;};
			$modelEdit->scenario='update';
			if($modelEdit->save()){
				$modelView=UserImage::find()->where(['ACCESS_ID'=>$accessId])->one();
				return array('ACCESS_IMAGE'=>$modelView);
			}else{
				return array('error'=>$modelEdit->errors);
			}
		}else{
			return array('result'=>'ACCESS_ID-not-exist');
		}
	}
}


