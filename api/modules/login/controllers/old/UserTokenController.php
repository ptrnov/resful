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
use api\modules\login\models\UserTokenSearch;
use common\models\User;

/**
  * logintest AND CHECK TOKEN USER.
  * auth_key		: Token Primary.
  * access_token 	: Token Access, after logintest for Access Api data POST,GET,PUT.
  * @author ptrnov  <piter@lukison.com>
  * @since 1.2
  * CMD : curl -u username:password http://api.kontrolgampang.int/logintest/user-tokens?username=trial1
  * CMD : curl -u trial1:semangat2016 http://api.kontrolgampang.int/logintest/user-tokens?username=trial1
 */
/* class MySerializer extends Serializer 
{
    public function serialize($data) 
    {
        $d = parent::serialize($data);
        $m = $d['_meta'];
        unset($d['_meta']);
        return array_merge($d, $m);
    }
} */
use api\modules\login\models\UserToken;

class UserTokenController extends ActiveController
{
	/**
	  * Source Database declaration.
	  *
	 */
    //public $modelClass = 'common\models\User';
    public $modelClass = 'api\modules\login\models\UserTokenSearch';
	// public $serializer = [
		// 'class' => 'yii\rest\Serializer',
		// 'collectionEnvelope' => 'User',
		// 'linksEnvelope'=> false,
	// ];	
	
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
		//unset($actions['update'], $actions['create'], $actions['delete'], $actions['view']);
		return $actions;
	} 
	
	
	/**
     * Model Search Data.
     */
	/* public function actions()
    {		
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => function () {					
					$param=["UserTokenSearch"=>Yii::$app->request->queryParams];
					//return $param;
					//return !empty($param['UserTokenSearch']);
                    $searchModel = new UserTokenSearch();
                    if($searchModel && !empty($param['UserTokenSearch'])){
						return $searchModel->search($param);
					}else{
						$nodata=[
							"status"=> 404,
							'message'=> 'no-data',
						];
						return $nodata;
					}					
                },
            ],
        ];
    } */

	public function actionCreate()
    {
		$paramsBody 			= Yii::$app->request->bodyParams;		
		$username				= isset($paramsBody['username'])!=''?$paramsBody['username']:'';
		$password_hash			= isset($paramsBody['password'])!=''?$paramsBody['password']:'';
		
		//MODEL
		$modelCnt= UserToken::find()->where(['username'=>$username])->count();
		$model= UserToken::find()->where(['username'=>$username])->one();		
		
		if($modelCnt){
			if($model->validateLoginPassword($password_hash)){
				//return $model->attributes;
				//$param=["UserTokenSearch"=>['username'=>$username]];
				// $param=['username'=>$username];
				// $searchModel = new UserTokenSearch();			
				// $rslt=$searchModel->search($param);
				//return $rslt;
				// foreach($rslt as $rslt){ 
					// $response = json_encode($rslt); 
				// } 
				// return array('User'=>$response[1]);
				//return array('User'=>$rslt["0"]);
				return array('USER'=>$model);
			}else{
				return array('result'=>'wrong-password');
			}
		}else{
			return array('result'=>'wrong-username');
		}		
	}
}


