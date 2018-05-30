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

class FrkTransHarianGrp extends DynamicModel
{
	// public $ACCESS_GROUP;
	// public $STORE_ID;
	
	public function rules()
    {
        return [
            [['ACCESS_GROUP','STORE_ID','PERANGKAT','TGL'], 'safe'],
		];	

    }

	public function fields()
	{
		return [			
			'chart'=>function($model){
				return self::chartlabel();
			},
			'categories'=>function(){
				return [
					self::categorieslabel()
				];
			},
			'dataset'=>function($model){
				return self::frekuensiTransaksiHarian();
			},
			'trendlines'=>function(){
				return [
					[
						"line"=> [
							[
								"startvalue"=> "2",
								"color"=> "#fc2c33",
								"valueOnRight"=> "1",
								"displayvalue"=> "Rata2",
								"thickness"=> "2",
								"dashed"=> "1",
								"dashLen"=> "4",
								"dashGap"=> "4"
							],
							[
								"startvalue"=>"100",
								"color"=> "6baa01",
								"displayvalue"=> "Target",
								"thickness"=>"2"
							
							]

						]
					]
					
				];
			}
		];
	}
	
	public function frekuensiTransaksiHarian(){
		$varTgl=$this->TGL!=''?$this->TGL:date('Y-m-d');
		$valAccessGoup=$this->ACCESS_GROUP!=''?$this->ACCESS_GROUP:'1';
		//==UPDATE DEVICE HAVE SYNCRONIZE ===
		Yii::$app->production_api->createCommand('
			UPDATE ptr_dashboard_polling_group 
			SET CHART_TRAFFICK_DAY=0,ARY_DEVICE=CONCAT(ARY_DEVICE,",'.$this->PERANGKAT.'") 
			WHERE ACCESS_GROUP="'.$valAccessGoup.'" AND CHART_TRAFFICK_DAY<>0
		')->execute(); 
		
		//=== DATA CHART ===
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
			#WHERE ACCESS_GROUP=".$valAccessGoup." AND TGL='2018-02-01'
			WHERE ACCESS_GROUP=".$valAccessGoup." AND TGL='".$varTgl."'
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
				$rslt1['seriesname']='Semua Toko';
				$rslt1['seriesname']='Semua Toko';
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
					"seriesname"=>'Data-Empty',//"Tidak ditemukan data",
					"data"=>"null"					
			];
		}
		$datasetRslt[]=$dataset;
		return $datasetRslt;
	}
	
	private function chartlabel(){
		$varTahun		= $this->TGL!=''?date('Y',strtotime($this->TGL)):date('Y');
		$varBulan		= $this->TGL!=''?date('m',strtotime($this->TGL)):date('m');
		$varTgl			= $this->TGL!=''?date('Y-m-d',strtotime($this->TGL)):date('Y-m-d');		
		$nmBulan=date('F', strtotime($varTahun.'-'.'0'.$varBulan.'-01')); // Nama Bulan
		$chart=[
			//==LOGO==
			// "logoURL"=>"https://image.kontrolgampang.com/brand/kg.jpg",
			// "logoAlpha"=> "3",
			// "logoScale"=> "90",
			// "logoPosition"=> "TR",
			//==TOOLS TIPS==
			"showToolTip"=> "1",
			"caption"=> " TRAFFIC TRANSAKSI HARIAN",
			"subCaption"=>"Tanggal ".$varTgl,
			"captionFontSize"=> "12",
			"subcaptionFontSize"=> "10",
			"subcaptionFontBold"=> "0",
			"paletteColors"=> "#0000ff,#ff4040,#7fff00,#ff7f24,#ff7256,#ffb90f,#006400,#030303,#ff69b4,#8b814c,#3f6b52,#744f4f,#6fae93,#858006,#426506,#055c5a,#a7630d,#4d8a9c,#449f9c,#8da9ab,#c4dfdd,#bf7793,#559e96,#afca84,#608e97,#806d88,#688b94,#b5dfe7,#b29cba,#83adb5,#c7bbc9,#2d5867,#e1e9b7,#bcd2d0,#f96161,#c9bbbb,#bfc5ce,#8f6d4d,#a87f99,#62909b,#a0acc0,#94b9b8",
			"bgcolor"=> "#ffffff",
			"showBorder"=> "0",
			"showShadow"=> "0",
			"usePlotGradientColor"=> "0",			
			"showAxisLines"=> "0",
			"showAlternateHGridColor"=> "0",
			"divlineThickness"=> "1",
			"divLineIsDashed"=> "0",
			"divLineDashLen"=> "1",
			"divLineGapLen"=> "1",
			"vDivLineDashed"=> "0",
			"numVDivLines"=> "6",
			"vDivLineThickness"=> "1",			
			"anchorradius"=> "5",
			"plotHighlightEffect"=> "fadeout|color=#f6f5fd, alpha=60",
			"showValues"=> "0",
			"rotateValues"=> "0",
			"placeValuesInside"=> "0",
			"formatNumberScale"=> "0",
			"decimalSeparator"=> ",",
			"thousandSeparator"=> ".",
			"numberPrefix"=> "",
			"ValuePadding"=> "0",
			"yAxisValuesStep"=> "1",
			"xAxisValuesStep"=> "0",
			"yAxisMinValue"=> "0",
			"numDivLines"=> "10",
			"xAxisNamePadding"=> "30",
			"showHoverEffect"=> "1",
			"animation"=> "1",
			//=== TREND LINES ===
			"trendValueBorderColor"=> "ff0000",
			"trendValueBorderAlpha"=> "50",
			"trendValueBorderPadding"=> "0",
			"trendValueBorderRadius"=> "5",
			"trendValueBorderThickness"=> "2",
			//"yaxismaxvalue"=>"50",
			//==LEGEND==
			"legendBorderAlpha"=> "0",
			"legendShadow"=> "0",
			//"legendAllowDrag"=> "1"			//=== DRAG POSITION LEGEND
			//"legendPosition"=>"right",		//=== POSISI LEGEND
			// "legendIconSides"=> "5",
			// "legendIconStartAngle"=> "60",
			//"legendCaption"=> "per-Toko",
			//"legendCaptionBold"=> "1",
			//"legendCaptionFont"=> "Arial",
			//"legendCaptionFontSize"=> "10",
			//"legendCaptionFontColor"=> "#333333"
			//==TITLE==
			"xAxisName"=> "24 Hour",
			"yAxisName"=> "Jumlah Transaction",
			"yAxisNameBorderColor"=> "#6666FF",
			"yAxisNameBorderAlpha"=> "50",
			"yAxisNameBorderPadding"=> "6",
			"yAxisNameBorderRadius"=> "3",
			"yAxisNameBorderThickness"=> "2",
			"yAxisNameBorderDashed"=> "1",
			"yAxisNameBorderDashLen"=> "4",
			"yAxisNameBorderDashGap"=> "2",
			"xAxisNameBorderColor"=> "#6666FF",
			"xAxisNameBorderAlpha"=> "50",
			"xAxisNameBorderPadding"=> "6",
			"xAxisNameBorderRadius"=> "3",
			"xAxisNameBorderThickness"=> "2",
			"xAxisNameBorderDashed"=> "1",
			"xAxisNameBorderDashLen"=> "4",
			"xAxisNameBorderDashGap"=> "2",
			//== Exprort Chart ==
			"exportEnabled"=>"1",
			"exportFileName"=>"RINGKASAN-BULANAN",
			"exportAtClientSide"=>"1",
			
			
		];
		return $chart;
	}
	
	private function categorieslabel(){
		$categories=[
			"category"=>[
				[
					"label"=> "01"
				],
				[
					"label"=> "02"
				],
				[
					"label"=> "03"
				],
				[
					"label"=> "04"
				],
				[
					"label"=> "05"
				],
				[
					"label"=> "06"
				],
				[
					"label"=> "07"
				],
				[
					"label"=> "08"
				],
				[
					"label"=> "09"
				],
				[
					"label"=> "10"
				],
				[
					"label"=> "11"
				],
				[
					"label"=> "12"
				],
				[
					"label"=> "13"
				],
				[
					"label"=> "14"
				],
				[
					"label"=> "15"
				],
				[
					"label"=> "16"
				],
				[
					"label"=> "17"
				],
				[
					"label"=> "18"
				],
				[
					"label"=> "19"
				],
				[
					"label"=> "20"
				],
				[
					"label"=> "21"
				],
				[
					"label"=> "22"
				],
				[
					"label"=> "23"
				],
				[
					"label"=> "24"
				]						
			]
		 ];
		 return $categories;
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
