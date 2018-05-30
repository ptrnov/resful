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
use yii\web\Request;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use api\modules\login\models\UserloginSearch;
use api\modules\login\models\User;

/**
  * Data user login by Token.
  *
  * @author ptrnov  <piter@lukison.com>
  * @since 1.1
  * CMD : curl -i http://api.kontrolgampang.int/login/users -H "Authorization: Bearer Yt4kLWLYlQf9OfnFSpZ5IO3128Gvw2gP"
 */
class UserController extends ActiveController
{	

public static function allowedDomains()
{
    return [
         '*',                        // star allows all domains
        'http://localhost:3000',
        'localhost:3000',
        //'http://test1.example.com',
       // 'http://test2.example.com',
    ];
}




	/**
	  * Source Database declaration 
	 */
    //public $modelClass = 'common\models\User';
    public $modelClass = 'api\modules\login\models\UserloginSearch';
	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'User',
	];
	
	/* public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        parent::beforeAction($action);

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            // End it, otherwise a 401 will be shown.
            Yii::$app->end();
        }

        return true;
    }  */
	/**
     * @inheritdoc
     */
    public function behaviors()    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    ['class' => HttpBearerAuth::className()],
                    // ['class' => QueryParamAuth::className(), 'tokenParam' => 'access-token'],
                ],
                'except' => ['options']
            ],
			'bootstrap'=> [
				'class' => ContentNegotiator::className(),
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
			'corsFilter' => [
				'class' => \yii\filters\Cors::className(),
				'cors' => [
					// restrict access to
					//'Origin' => ['http://lukisongroup.com', 'http://lukisongroup.int','http://localhost','http://103.19.111.1','http://202.53.354.82'],
					'Origin' => static::allowedDomains(),
					'Access-Control-Request-Method' => ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
					//'Access-Control-Request-Headers' => ['*'],
					'Access-Control-Request-Headers' => ['*'],
					// Allow only headers 'X-Wsse'
					'Access-Control-Allow-Credentials' => false,
					// Allow OPTIONS caching
					'Access-Control-Max-Age' => 3600,
				]		 
			]
        ]);
    }

	public function actions()
    {		
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => function () {					
					$param=["UserloginSearch"=>Yii::$app->request->queryParams];
					//return $param;
                    $searchModel = new UserloginSearch();
					return $searchModel->search($param);
                },
            ],
			/*  'options' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => function () {					
					$param=["UserloginSearch"=>Yii::$app->request->queryParams];
					//return $param;
                    $searchModel = new UserloginSearch();
					return $searchModel->search($param);
                },
            ],  */
        ];
    }	 
	
	//private $_verbs = ['GET','HEAD','POST','OPTIONS'];
	//private $_verbs =$request->getHeaders()->get('Authorization');

	/* public function actionOptions()
	{
		if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS') {
			Yii::$app->getResponse()->setStatusCode(405);
		}
		//return $this->index;
		//$options =$this->_verbs;
		 //$options = preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches);//$this->_verbs;
		$rq= new Request;
		return $rq->getHeaders();//->get('Authorization');
		//return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];//->get('Authorization');
		//return Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $options));
		//return  $request->getHeaders()->get('Authorization');
		//return $_verbs;
		
	}	 */ 
}


