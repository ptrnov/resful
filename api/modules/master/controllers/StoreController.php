<?php

namespace api\modules\master\controllers;

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

use api\modules\master\models\Store;
use api\modules\login\models\UserLogin;
use api\modules\master\models\SyncPoling;

/**
  * @author 	: ptrnov  <piter@lukison.com>
  * @since 		: 1.2
  * Subject		: MASTER STORE
  * Metode		: POST (update)
  * URL			: http://production.kontrolgampang.com/master/stores
  * Body Param	: ACCESS_GROUP & STORE_NM & ALAMAT & PIC & TLP & FAX & PROVINCE_ID & CITY_ID.
 */
class StoreController extends ActiveController
{	
    //public $modelClass = 'common\models\User';
    public $modelClass = 'api\modules\login\models\Store';

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
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: ADD STORE
		  * Metode		: POST(POST)View / POST(GET) View.
		  * URL			: http://production.kontrolgampang.com/master/stores
		  * Body Param	: METHODE=POST/GET & ACCESS_GROUP (key),PROVINCE_ID(key), INDUSTRY_ID(key), INDUSTRY_GRP_ID(key)
		  * Result 		: STORE_ID (Auto Generate).
		  * Properties	: STORE_NM, ALAMAT,PIC,TLP,FAX,PROVINCE_NM,CITY_NAME,INDUSTRY_NM,INDUSTRY_GRP_NM
		  * Support Api	: http://production.kontrolgampang.com/master/kotas 					(All Master)
		  *				  http://production.kontrolgampang.com/master/provinsis					(All Master)
		  *				  http://production.kontrolgampang.com/master/product-industris			(All Master)
		  *				  http://production.kontrolgampang.com/master/product-industri-groups	(All Master)
		*/ 
		$paramsBody 			= Yii::$app->request->bodyParams;
		$metode					= isset($paramsBody['METHODE'])!=''?$paramsBody['METHODE']:'';
		$storeID				= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		//PROPERTY
		$accessGroup			= isset($paramsBody['ACCESS_GROUP'])!=''?$paramsBody['ACCESS_GROUP']:'';
		$storeNm				= isset($paramsBody['STORE_NM'])!=''?$paramsBody['STORE_NM']:'';
		$alamat					= isset($paramsBody['ALAMAT'])!=''?$paramsBody['ALAMAT']:'';
		$pic					= isset($paramsBody['PIC'])!=''?$paramsBody['PIC']:'';
		$tlp					= isset($paramsBody['TLP'])!=''?$paramsBody['TLP']:'';
		$fax					= isset($paramsBody['FAX'])!=''?$paramsBody['FAX']:'';
		//LOCATION
		$provinsiID				= isset($paramsBody['PROVINCE_ID'])!=''?$paramsBody['PROVINCE_ID']:'';
		$kotaId					= isset($paramsBody['CITY_ID'])!=''?$paramsBody['CITY_ID']:'';
		//INDUSTRY
		$industriId				= isset($paramsBody['INDUSTRY_ID'])!=''?$paramsBody['INDUSTRY_ID']:'';
		$industriNm				= isset($paramsBody['INDUSTRY_NM'])!=''?$paramsBody['INDUSTRY_NM']:'';
		$industriGrpId			= isset($paramsBody['INDUSTRY_GRP_ID'])!=''?$paramsBody['INDUSTRY_GRP_ID']:'';
		$industriGrpNm			= isset($paramsBody['INDUSTRY_GRP_NM'])!=''?$paramsBody['INDUSTRY_GRP_NM']:'';
		//KOORDINAT
		$lat					= isset($paramsBody['LATITUDE'])!=''?$paramsBody['LATITUDE']:'';
		$long					= isset($paramsBody['LONGITUDE'])!=''?$paramsBody['LONGITUDE']:'';
		
		//POLING SYNC nedded ACCESS_ID
		$accessID		= isset($paramsBody['ACCESS_ID'])!=''?$paramsBody['ACCESS_ID']:'';
		$tblPooling		= isset($paramsBody['NM_TABLE'])!=''?$paramsBody['NM_TABLE']:'';
		$paramlUUID		= isset($paramsBody['UUID'])!=''?$paramsBody['UUID']:'';
				
		if($metode=='POST'){
			if($accessGroup){
				$cntOwner= UserLogin::find()->where(['ACCESS_GROUP'=>$accessGroup])->count();
				if($cntOwner){
					$modelStore= new Store();
					//$model->scenario = 'createuserapi';
					$modelStore->ACCESS_GROUP=$accessGroup;
					$modelStore->CREATE_AT=date('Y-m-d H:i:s');
					if ($storeNm!=''){$modelStore->STORE_NM=$storeNm;};
					if ($alamat!=''){$modelStore->ALAMAT =$alamat;};
					if ($pic!=''){$modelStore->PIC=$pic;};
					if ($tlp!=''){$modelStore->TLP=$tlp;};
					if ($fax!=''){$modelStore->FAX =$fax;};
					if ($provinsiID!=''){$modelStore->PROVINCE_ID=$provinsiID;};
					if ($kotaId!=''){$modelStore->CITY_ID=$kotaId;};
					if ($industriId!=''){$modelStore->INDUSTRY_ID=$industriId;};
					if ($industriNm!=''){$modelStore->INDUSTRY_NM=$industriNm;};
					if ($industriGrpId!=''){$modelStore->INDUSTRY_GRP_ID=$industriGrpId;};
					if ($industriGrpNm!=''){$modelStore->INDUSTRY_GRP_NM=$industriGrpNm;};					
					if ($lat!=''){$modelStore->LATITUDE=$lat;};
					if ($long!=''){$modelStore->LONGITUDE=$long;};					
					if ($modelStore->save()){
						$rsltMax=Store::find()->where(['ACCESS_GROUP'=>$accessGroup])->max('STORE_ID');
						$modelView=Store::find()->where(['STORE_ID'=>$rsltMax])->one();
						return array('store'=>$modelView);
					}else{
						return array('result'=>$modelStore->errors);
					} 									
				}else{
					return array('result'=>'Not Exist Owner');
				}
			}else{
				return array('result'=>'Not Exist access_group');
			}
		}elseif($metode=='GET'){
			if($accessGroup And $storeID){
					/*===========================
					 *=== POLLING UPDATE UUID ===
					 *===========================
					*/
					$dataHeader=explode('.',$storeID);
					if ($tblPooling=='TBL_STORE'){						
						//==GET DATA POLLING
						$modelPoling=SyncPoling::find()->where([
							 'NM_TABLE'=>'TBL_STORE',
							 'ACCESS_GROUP'=>$dataHeader[0],
							 'STORE_ID'=>$storeID,
							 'PRIMARIKEY_VAL'=>$storeID
						])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();
						//==UPDATE DATA POLLING UUID
						if($modelPoling){							
							foreach($modelPoling as $row => $val){
								$modelSimpan=SyncPoling::find()->where([
									 'NM_TABLE'=>'TBL_STORE',
									 'ACCESS_GROUP'=>$dataHeader[0],
									 'STORE_ID'=>$storeID,
									 'PRIMARIKEY_VAL'=>$storeID,
									 'TYPE_ACTION'=>$val->TYPE_ACTION
								])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
								if($modelSimpan AND $paramlUUID){
									$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
									$modelSimpan->save();
								}
							}							
						}				
					}				
				$modelView=Store::find()->Where(['STORE_ID'=>$storeID])->one();
			}elseif($accessGroup AND !$storeID){
				$modelView=Store::find()->where(['ACCESS_GROUP'=>$accessGroup])->all();
			}elseif(!$accessGroup AND $storeID){
				$modelView=Store::find()->Where(['STORE_ID'=>$storeID])->one();
			}else{
				return array('result'=>'Group-Or-Store-Not-Exist');
			}
			return array('store'=>$modelView);
		}else{
			return array('result'=>'POST-or-GET');
		}
	}
	
	public function actionUpdate()
    {        	
		/**
		* @author 	: ptrnov  <piter@lukison.com>
		* @since 		: 1.2
		* Metode		: PUT (UPDATE)
		* Subject		: UPDATE PROPERTY STORE
		* URL			: http://production.kontrolgampang.com/master/stores
		* Body Param	: STORE_ID (key)
		* Properties	: STORE_NM, ALAMAT,PIC,TLP,FAX,PROVINCE_NM,CITY_NAME,INDUSTRY_NM,INDUSTRY_GRP_NM
		* Support Api	: http://production.kontrolgampang.com/master/kotas 					(All Master)
		*				  http://production.kontrolgampang.com/master/provinsis					(All Master)
		*				  http://production.kontrolgampang.com/master/product-industris			(All Master)
		*				  http://production.kontrolgampang.com/master/product-industri-groups	(All Master)
		*/ 
		$paramsBody 			= Yii::$app->request->bodyParams;
		$storeId				= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		$storeNm				= isset($paramsBody['STORE_NM'])!=''?$paramsBody['STORE_NM']:'';
		$alamat					= isset($paramsBody['ALAMAT'])!=''?$paramsBody['ALAMAT']:'';
		$pic					= isset($paramsBody['PIC'])!=''?$paramsBody['PIC']:'';
		$tlp					= isset($paramsBody['TLP'])!=''?$paramsBody['TLP']:'';
		$fax					= isset($paramsBody['FAX'])!=''?$paramsBody['FAX']:'';
		//LOCATION
		$provinsiID				= isset($paramsBody['PROVINCE_ID'])!=''?$paramsBody['PROVINCE_ID']:'';
		$kotaId					= isset($paramsBody['CITY_ID'])!=''?$paramsBody['CITY_ID']:'';
		//INDUSTRY
		$industriId				= isset($paramsBody['INDUSTRY_ID'])!=''?$paramsBody['INDUSTRY_ID']:'';
		$industriNm				= isset($paramsBody['INDUSTRY_NM'])!=''?$paramsBody['INDUSTRY_NM']:'';
		$industriGrpId			= isset($paramsBody['INDUSTRY_GRP_ID'])!=''?$paramsBody['INDUSTRY_GRP_ID']:'';
		$industriGrpNm			= isset($paramsBody['INDUSTRY_GRP_NM'])!=''?$paramsBody['INDUSTRY_GRP_NM']:'';
		//KOORDINAT
		$lat					= isset($paramsBody['LATITUDE'])!=''?$paramsBody['LATITUDE']:'';
		$long					= isset($paramsBody['LONGITUDE'])!=''?$paramsBody['LONGITUDE']:'';
		//==STATUS== [0=Disable;1=Enable;3=Disable]
		$stt					= isset($paramsBody['STATUS'])!=''?$paramsBody['STATUS']:'';
		
		if($storeId){
			$modelStore= Store::find()->where(['STORE_ID'=>$storeId])->one();
			//$model->scenario = 'createuserapi';
			if ($storeNm!=''){$modelStore->STORE_NM=$storeNm;};
			if ($alamat!=''){$modelStore->ALAMAT =$alamat;};
			if ($pic!=''){$modelStore->PIC=$pic;};
			if ($tlp!=''){$modelStore->TLP=$tlp;};
			if ($fax!=''){$modelStore->FAX =$fax;};
			if ($provinsiID!=''){$modelStore->PROVINCE_ID=$provinsiID;};
			if ($kotaId!=''){$modelStore->CITY_ID=$kotaId;};
			if ($industriId!=''){$modelStore->INDUSTRY_ID=$industriId;};
			if ($industriNm!=''){$modelStore->INDUSTRY_NM=$industriNm;};
			if ($industriGrpId!=''){$modelStore->INDUSTRY_GRP_ID=$industriGrpId;};
			if ($industriGrpNm!=''){$modelStore->INDUSTRY_GRP_NM=$industriGrpNm;};	
			if ($lat!=''){$modelStore->LATITUDE=$lat;};
			if ($long!=''){$modelStore->LONGITUDE=$long;};				
			if ($stt!=''){$modelStore->STATUS=$stt;};
			if ($modelStore->save()){
				return array('store'=>$modelStore);
			}else{
				return array('result'=>$modelStore->errors);
			} 									
		}else{
			return array('result'=>'Not Exist Store');
		}
	}
	
	public function actionDelete()
    {        	
		/**
		  * @author 	: ptrnov  <piter@lukison.com>
		  * @since 		: 1.2
		  * Subject		: DELETE STORE
		  * Metode		: DELETE (DELETE)
		  * URL			: http://production.kontrolgampang.com/master/stores
		  * Body Param	: STORE_ID (key).
		  * Keterangan	: Delete Status: (0=Trial; 1=Active; 2=Deactive; 3=Delete; 4=Tenggang);
		*/ 
		$paramsBody 			= Yii::$app->request->bodyParams;
		$storeId				= isset($paramsBody['STORE_ID'])!=''?$paramsBody['STORE_ID']:'';
		
		if($storeId){
			$modelStore= Store::find()->where(['STORE_ID'=>$storeId])->one();
			//$model->scenario = 'createuserapi';
			$modelStore->STATUS=3;
			if ($modelStore->save()){
				$modelViews= Store::find()->where(['STORE_ID'=>$storeId])->one();
				return array('store'=>$modelViews);
			}else{
				return array('result'=>$modelStore->errors);
			} 									
		}else{
			return array('result'=>'Not Exist Store');
		}
	}
}


