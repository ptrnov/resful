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

use api\modules\transaksi\models\PenjualanHeader;
use api\modules\transaksi\models\PenjualanHeaderSearch;
/**
 * PenjualanHeaderController implements the CRUD actions for PenjualanHeader model.
 */
class PenjualanHeaderController extends ActiveController
{
    public $modelClass = 'api\modules\transaksi\models\PenjualanHeader';
	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'header',
	];

	public function behaviors()    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    //['class' => HttpBearerAuth::className()],
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
					'Origin' => ['*'],
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
					$param=["PenjualanHeaderSearch"=>Yii::$app->request->queryParams];
					//return $param;
                    $searchModel = new PenjualanHeaderSearch();
					return $searchModel->search($param);
                },
            ],
        ];
    }
	
	public function actionCreate()
    {
        $model = new PenjualanHeader();
		$params     = $_REQUEST;        
        $model->attributes=$params;
        $model->CREATE_AT = date('Y:m:d H:i:s');//'2017-12-12 00:00';
        $model->UPDATE_AT = date('Y:m:d H:i:s');//'2017-12-12 00:00';
        $model->TRANS_DATE = date('Y:m:d H:i:s');//'2017-12-12 00:00';
        if ($model->save()) 
        {
            return $model->attributes;
        } 
        else
        {
            return array('errors'=>$model->errors);
        } 
    }
}
    /**
     * Lists all PenjualanHeader models.
     * @return mixed
     */
    // public function actionIndex()
    // {
        // $searchModel = new PenjualanHeaderSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // return $this->render('index', [
            // 'searchModel' => $searchModel,
            // 'dataProvider' => $dataProvider,
        // ]);
    // }

    /**
     * Displays a single PenjualanHeader model.
     * @param string $id
     * @return mixed
     */
    // public function actionView($id)
    // {
        // return $this->render('view', [
            // 'model' => $this->findModel($id),
        // ]);
    // }

    /**
     * Creates a new PenjualanHeader model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
        // $model = new PenjualanHeader();

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // return $this->redirect(['view', 'id' => $model->ID]);
        // } else {
            // return $this->render('create', [
                // 'model' => $model,
            // ]);
        // }
    // }

    /**
     * Updates an existing PenjualanHeader model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    // public function actionUpdate($id)
    // {
        // $model = $this->findModel($id);

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // return $this->redirect(['view', 'id' => $model->ID]);
        // } else {
            // return $this->render('update', [
                // 'model' => $model,
            // ]);
        // }
    // }

    /**
     * Deletes an existing PenjualanHeader model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
        // $this->findModel($id)->delete();

        // return $this->redirect(['index']);
    // }

    /**
     * Finds the PenjualanHeader model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PenjualanHeader the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    // protected function findModel($id)
    // {
        // if (($model = PenjualanHeader::findOne($id)) !== null) {
            // return $model;
        // } else {
            // throw new NotFoundHttpException('The requested page does not exist.');
        // }
    // }
// }
