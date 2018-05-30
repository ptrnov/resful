<?php

namespace api\modules\laporan\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\data\ArrayDataProvider;
use yii\base\Model;
use \yii\base\DynamicModel;
use yii\debug\components\search\Filter;
use yii\debug\components\search\matchers;

class PollingChart extends DynamicModel
{
	
	public function rules()
    {
        return [
            [['ACCESS_GROUP','STORE_ID','PERANGKAT','STATUS_NM'], 'safe'],
		];	
    }

	public function fields()
	{
		/*=================================================
		 *=== INIT OBJECT NAME per-GroupOwner/per-Store ===
		 *=================================================
		*/
		if($this->STORE_ID==null){
			$objNm='POLLING_GROUP';
		}else{
			$objNm='POLLING_STORE';
		};
		
		/*================================
		 *=== RETURN <- GET DATA FIELD ===
		 *================================
		*/
		return [
			$objNm=>function(){
				if($this->STORE_ID==null){
					$rslt=self::pollingGroup();
				}else{
					$rslt=self::pollingPerStore();
				}
				return $rslt;
			},
		];
	}
		
	private function pollingGroup(){		
		$sql="
			SELECT 
				CHART_TRAFFICK_DAY,CHART_SALES_WEEK,CHART_SALES_MONTH,
				LABEL_VISIT_DAILY,LABEL_SALES_DAILY,LABEL_SALES_WEEKLY,LABEL_SALES_MONTHLY,
				CHART_SALESPRODUK_DAILY,CHART_SALESPRODUK_WEEKLY,CHART_SALESPRODUK_MONTHLY
			FROM ptr_dashboard_polling_group
			WHERE ACCESS_GROUP='".$this->ACCESS_GROUP."' AND FIND_IN_SET('".$this->PERANGKAT."',ARY_DEVICE)
		";		
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		$modelData=$dataProvider->getModels();        
		if($modelData){																			//== jika PERANGKAT ada, ambil database
			$rsltModel=$modelData[0];
			$rslt=[
				'CHART_TRAFFICK_DAY'=>$rsltModel['CHART_TRAFFICK_DAY'],
				'CHART_SALES_WEEK'=>$rsltModel['CHART_SALES_WEEK'],
				'CHART_SALES_MONTH'=>$rsltModel['CHART_SALES_MONTH'],
				'LABEL_VISIT_DAILY'=>$rsltModel['LABEL_VISIT_DAILY'],
				'LABEL_SALES_DAILY'=>$rsltModel['LABEL_SALES_DAILY'],
				'LABEL_SALES_WEEKLY'=>$rsltModel['LABEL_SALES_WEEKLY'],
				'LABEL_SALES_MONTHLY'=>$rsltModel['LABEL_SALES_MONTHLY'],
				'CHART_SALESPRODUK_DAILY'=>$rsltModel['CHART_SALESPRODUK_DAILY'],
				'CHART_SALESPRODUK_WEEKLY'=>$rsltModel['CHART_SALESPRODUK_WEEKLY'],
				'CHART_SALESPRODUK_MONTHLY'=>$rsltModel['CHART_SALESPRODUK_MONTHLY'],
			];
		}else{																					//== jika PERANGKAT tidak ada. ambill Array
			$rslt=[
				'CHART_TRAFFICK_DAY'=>"1",
				'CHART_SALES_WEEK'=>"1",
				'CHART_SALES_MONTH'=>"1",
				'LABEL_VISIT_DAILY'=>"1",
				'LABEL_SALES_DAILY'=>"1",
				'LABEL_SALES_WEEKLY'=>"1",
				'LABEL_SALES_MONTHLY'=>"1",
				'CHART_SALESPRODUK_DAILY'=>"1",
				'CHART_SALESPRODUK_WEEKLY'=>"1",
				'CHART_SALESPRODUK_MONTHLY'=>"1",
			];
		}
		
		return $rslt;
	}
	
	private function pollingPerStore(){		
		$sql="
			SELECT 
				CHART_TRAFFICK_DAY,CHART_SALES_WEEK,CHART_SALES_MONTH,
				LABEL_VISIT_DAILY,LABEL_SALES_DAILY,LABEL_SALES_WEEKLY,LABEL_SALES_MONTHLY,
				CHART_SALESPRODUK_DAILY,CHART_SALESPRODUK_WEEKLY,CHART_SALESPRODUK_MONTHLY
			FROM ptr_dashboard_polling_perstore
			WHERE STORE_ID='".$this->STORE_ID."' AND FIND_IN_SET('".$this->PERANGKAT."',ARY_DEVICE)
		";	
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		$modelData=$dataProvider->getModels();
		if($modelData){
			$rsltModel=$modelData[0];
			$rslt=[
				'CHART_TRAFFICK_DAY'=>$rsltModel['CHART_TRAFFICK_DAY'],
				'CHART_SALES_WEEK'=>$rsltModel['CHART_SALES_WEEK'],
				'CHART_SALES_MONTH'=>$rsltModel['CHART_SALES_MONTH'],
				'LABEL_VISIT_DAILY'=>$rsltModel['LABEL_VISIT_DAILY'],
				'LABEL_SALES_DAILY'=>$rsltModel['LABEL_SALES_DAILY'],
				'LABEL_SALES_WEEKLY'=>$rsltModel['LABEL_SALES_WEEKLY'],
				'LABEL_SALES_MONTHLY'=>$rsltModel['LABEL_SALES_MONTHLY'],
				'CHART_SALESPRODUK_DAILY'=>$rsltModel['CHART_SALESPRODUK_DAILY'],
				'CHART_SALESPRODUK_WEEKLY'=>$rsltModel['CHART_SALESPRODUK_WEEKLY'],
				'CHART_SALESPRODUK_MONTHLY'=>$rsltModel['CHART_SALESPRODUK_MONTHLY'],
			];
		}else{
			$rslt=[
				'CHART_TRAFFICK_DAY'=>"1",
				'CHART_SALES_WEEK'=>"1",
				'CHART_SALES_MONTH'=>"1",
				'LABEL_VISIT_DAILY'=>"1",
				'LABEL_SALES_DAILY'=>"1",
				'LABEL_SALES_WEEKLY'=>"1",
				'LABEL_SALES_MONTHLY'=>"1",
				'CHART_SALESPRODUK_DAILY'=>"1",
				'CHART_SALESPRODUK_WEEKLY'=>"1",
				'CHART_SALESPRODUK_MONTHLY'=>"1",
			];
		};
		
		return $rslt;
	}
}
