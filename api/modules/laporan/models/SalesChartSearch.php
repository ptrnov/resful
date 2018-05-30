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

class SalesChartSearch extends DynamicModel
{
	// public $ACCESS_GROUP;
	// public $STORE_ID;
	
	public function rules()
    {
        return [
            [['ACCESS_GROUP','STORE_ID'], 'safe'],
		];	

    }

	public function fields()
	{
		return [			
			'chart'=>function($model){
				return self::frekuensiTransaksiHarian();
			},
			'categories'=>function(){
				// return [
					// self::categorieslabel()
				// ];
				return '123456789';
			},
			'dataset'=>function($model){
				//return self::chartData($this->ACCESS_GROUP,$this->TGL);	
				return 'ADC';				
			}
		];
	}
	
	public function frekuensiTransaksiHarian(){
		//return ['aa'=>$this->ACCESS_GROUP];
		$sql="
			#==PER-STORE===
			#SELECT 
			#	ACCESS_GROUP,STORE_ID,
			#	VAL1,VAL2,VAL3,VAL4,VAL5,VAL6,VAL7,VAL8,VAL9,VAL10,VAL11,VAL12,
			#	VAL13,VAL14,VAL15,VAL16,VAL17,VAL18,VAL19,VAL20,VAL21,VAL22,VAL23,VAL24
			#FROM ptr_kasir_th1_hour
			##WHERE TGL=CURRENT_DATE(); 		
			#WHERE TGL='2018-01-29'; 
			
			#==GROUPING===
			SELECT 
				ACCESS_GROUP,STORE_ID,
				SUM(VAL1) AS VAL1,SUM(VAL2) AS VAL2,SUM(VAL3) AS VAL3,SUM(VAL4) AS VAL4,SUM(VAL5) AS VAL5,SUM(VAL6) AS VAL6,
				SUM(VAL7) AS VAL7,SUM(VAL8) AS VAL8,SUM(VAL9) AS VAL9,SUM(VAL10) AS VAL10,SUM(VAL11) AS VAL11,SUM(VAL12) AS VAL12,
				SUM(VAL13) AS VAL13,SUM(VAL14) AS VAL14,SUM(VAL15) AS VAL15,SUM(VAL16) AS VAL16,SUM(VAL17) AS VAL17,SUM(VAL18) AS VAL18,
				SUM(VAL19) AS VAL19,SUM(VAL20) AS VAL20,SUM(VAL21) AS VAL21,SUM(VAL22) AS VAL22,SUM(VAL23) AS VAL23,SUM(VAL24) AS VAL24
			FROM ptr_kasir_th1_hour
			#WHERE TGL=CURRENT_DATE(); 		
			WHERE TGL='2018-01-29'
			GROUP BY ACCESS_GROUP; 	
		";		
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		
		$filter = new Filter();
 		$this->addCondition($filter, 'ACCESS_GROUP', true);	
		$this->addCondition($filter, 'STORE_ID', true);	
 		$dataProvider->allModels = $filter->filter($qrySql);
       // return ['Frekuensi_Transaksi_Harian'=>$dataProvider->getModels()];
		$modelHour=$dataProvider->getModels();
		if ($modelHour){	
			foreach ($modelHour as $row => $val){
				$rslt1['seriesname']='Semua Toko';//$modelWeek[0]['STORE_NM'];
				$dataval1=[];
				//=[3]==LOOPING 24 hour
				for( $i= 1 ; $i <= 24 ; $i++ ) {
					$dataval1[]=['label'=>$i,'value'=>$val['VAL'.$i],'anchorBgColor'=>'#00fd83'];
				}
			
				//=[4]==SETTING ARRAY
				$rslt1['data']=$dataval1;	
				//$rsltDataSet1[]=$rslt1;
			}
			$dataset=$rslt1;//$rsltDataSet1;	
		}else{
			//=[6]== SCENARIO DATA KOSONG				
			$dataset=[
					"seriesname"=>$valStore['STORE_NM'],//"Tidak ditemukan data",
					"data"=>"null"					
			];
		}
		$datasetRslt[]=$dataset;
		return $datasetRslt;
	} 

	private function addCondition(Filter $filter, $attribute, $partial = false)
    {
        $value = $this->$attribute;

        if (mb_strpos($value, '>') !== false) {
            $value = intval(str_replace('>', '', $value));
            $filter->addMatcher($attribute, new matchers\GreaterThan(['value' => $value]));

        } elseif (mb_strpos($value, '<') !== false) {
            $value = intval(str_replace('<', '', $value));
            $filter->addMatcher($attribute, new matchers\LowerThan(['value' => $value]));
        } else {
            $filter->addMatcher($attribute, new matchers\SameAs(['value' => $value, 'partial' => $partial]));
        }
    }
}
