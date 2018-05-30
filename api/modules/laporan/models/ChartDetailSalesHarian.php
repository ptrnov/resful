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

class ChartDetailSalesHarian extends DynamicModel
{
	public function rules()
    {
        return [
            [['ACCESS_GROUP','STORE_ID','PERANGKAT','TGL','PILIH'], 'safe'],
		];	
    }

	public function fields()
	{
		return [			
			'chart'=>function($model){
				return self::chartlabel();
			},
			'categories'=>function(){
				return self::categorieslabel();
			},
			'dataset'=>function($model){
				return self::datasetChart();
			}
		];
	}
	
	private function getData(){
		$valAccessGoup=$this->ACCESS_GROUP!=''?$this->ACCESS_GROUP:'';
		
		if($this->STORE_ID=='' OR $this->ACCESS_GROUP==$this->STORE_ID){
			$sql="
				#==GROUPING===
				SELECT	ACCESS_GROUP,STORE_ID,BULAN,TAHUN,
						sum(PRODUK_TTL_JUALPPNDISCOUNT) AS REVENUES,
						sum(PRODUK_TTL_DISCOUNT) AS DISCOUNT,
						sum(PRODUK_TTL_PPN) AS PPN,
						sum(PRODUK_TTL_HARGAJUAL) AS HARGAJUAL,
						#sum(PRODUK_TTL_JUALPPNDISCOUNT-(PRODUK_TTL_PPN+PRODUK_TTL_DISCOUNT)) AS PRODUK_TTL_HARGAJUAL_TEST,
						sum(PRODUK_TTL_HPP) AS MODAL,				
						sum((PRODUK_TTL_JUALPPNDISCOUNT)-PRODUK_TTL_HPP) AS PROFITS			
				FROM ptr_kasir_td3a
				WHERE ACCESS_GROUP='".$valAccessGoup."' 
					  AND TAHUN=YEAR('".$this->TGL."')
					  AND BULAN=MONTH('".$this->TGL."')
				GROUP BY ACCESS_GROUP,TAHUN,BULAN,DATE(TRANS_DATE)
				ORDER BY ACCESS_GROUP,TAHUN,BULAN,DATE(TRANS_DATE) ASC;				
			";	
		}else{
			$sql="
				#==STORE_ID===
				SELECT	ACCESS_GROUP,STORE_ID,BULAN,TAHUN,
						sum(PRODUK_TTL_JUALPPNDISCOUNT) AS REVENUES,
						sum(PRODUK_TTL_DISCOUNT) AS DISCOUNT,
						sum(PRODUK_TTL_PPN) AS PPN,
						sum(PRODUK_TTL_HARGAJUAL) AS HARGAJUAL,
						#sum(PRODUK_TTL_JUALPPNDISCOUNT-(PRODUK_TTL_PPN+PRODUK_TTL_DISCOUNT)) AS PRODUK_TTL_HARGAJUAL_TEST,
						sum(PRODUK_TTL_HPP) AS MODAL,				
						sum((PRODUK_TTL_HARGAJUAL)-PRODUK_TTL_HPP) AS PROFITS			
				FROM ptr_kasir_td3a
				WHERE ACCESS_GROUP='".$valAccessGoup."' 
					  AND STORE_ID='".$this->STORE_ID."'
					  AND TAHUN=YEAR('".$this->TGL."')
					  AND BULAN=MONTH('".$this->TGL."')
				GROUP BY ACCESS_GROUP,STORE_ID,TAHUN,BULAN,DATE(TRANS_DATE)
				ORDER BY ACCESS_GROUP,STORE_ID,TAHUN,BULAN ASC;
			";	
		}				
		
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		$modelMonth=$dataProvider->getModels();		
		return $modelMonth;
	}
	
	public function datasetChart(){
		
		$modelMonth=self::getData();
		if($modelMonth){			
			$dataval1=[];			
			foreach ($modelMonth as $row => $val){				
				$rslt1['seriesName']='Pendapatan';				
				$dataval1[]=['value'=>$val['REVENUES']];
				$rslt1['data']=$dataval1;
			};
			$dataset[]=$rslt1;
			
			$dataval1=[];
			foreach ($modelMonth as $row => $val){				
				$rslt1['seriesName']='Modal';				
				$dataval1[]=['value'=>$val['MODAL']];
				$rslt1['data']=$dataval1;
				$rslt1['renderAs']="area";
                $rslt1['showValues']="0";				
			}
			$dataset[]=$rslt1;//$rsltDataSet1;	
			
			$dataval1=[];			
			foreach ($modelMonth as $row => $val){				
				$rslt1['seriesName']='Keuntungan';				
				$dataval1[]=['value'=>$val['PROFITS']];
				$rslt1['data']=$dataval1;
				//$rslt1['parentYAxis']="S";
                $rslt1['renderAs']="line";
                $rslt1['showValues']="0";
                $rslt1['placeValuesInside']="0";				
			};
			$dataset[]=$rslt1;
			
			//===PPN===
			$dataval1=[];			
			foreach ($modelMonth as $row => $val){				
				$rslt1['seriesName']='PPN';				
				$dataval1[]=['value'=>$val['PPN']];
				$rslt1['data']=$dataval1;
				//$rslt1['parentYAxis']="S";
                $rslt1['renderAs']="line";
                $rslt1['showValues']="0";				
			};
			$dataset[]=$rslt1;
		}else{
			$dataval1=[];	
			$dataset[]=[
					"seriesName"=>'Data-Empty',
					"data"=>[]			
			];
		}
	
		$datasetRslt=$dataset;	
	
		return $datasetRslt;
	}
	
	private function chartlabel(){
		$nmBulan		= date('F', strtotime($this->TGL)); // Nama Bulan
		$varTahun		= date('Y', strtotime($this->TGL));;
				
		$chart=[
			"caption"=> "Pendapatan dan Keuntungan",
			"subCaption"=> "Periode ".$nmBulan." ".$varTahun,
			"captionFontSize"=>"12",
			"subcaptionFontSize"=>"10",
			"subcaptionFontBold"=>"0",
			//"xAxisname"=> "Month",
			"pYAxisName"=> "Nilai Rupiah (In IDR)",
			//"sYAxisName"=> "Profit %",
			//"numberPrefix"=> "$",
			//"sNumberSuffix" => "%",
			//"sYAxisMaxValue" => "50",
			//Primary Y-Axis Name font properties
			"pYAxisNameFont"=> "Arial",
			"pYAxisNameFontSize"=> "12",
			"pYAxisNameFontColor"=> "#003366",
			"pYAxisNameFontBold"=> "1",
			"pYAxisNameFontItalic"=> "1",
			"pYAxisNameAlpha"=> "50",
			//Secondary Y-Axis Name font properties
			"sYAxisNameFont"=> "Arial",
			"sYAxisNameFontSize"=> "12",
			"sYAxisNameFontColor"=> "#003366",
			"sYAxisNameFontBold"=> "1",
			"sYAxisNameFontItalic"=> "1",
			"sYAxisNameAlpha"=> "50",
			"theme"=> "fint",
			"bgcolor"=> "#ffffff",
			"showBorder"=> "0",
			"showShadow"=> "0",
			"usePlotGradientColor"=> "0",			
			//"showAxisLines"=> "0",
			"showAlternateHGridColor"=> "0",
			"divlineThickness"=> "1",
			"divLineIsDashed"=> "0",
			"divLineDashLen"=> "1",
			"divLineGapLen"=> "0",
			"vDivLineDashed"=> "0",
			"numVDivLines"=> "6",
			"vDivLineThickness"=> "1",			
			"anchorradius"=> "0",
			"plotHighlightEffect"=> "fadeout|color=#f6f5fd, alpha=60",
			"showValues"=> "0",
			"rotateValues"=> "0",
			"placeValuesInside"=> "0",
			"formatNumberScale"=> "0",
			"decimalSeparator"=> ",",
			"thousandSeparator"=> ".",
			"numberPrefix"=> "Rp ",
			"ValuePadding"=> "0",
			//==
			"bgcolor"=>"#ffffff",
			"showBorder"=>"0",
			"showShadow"=>"1",			
			//"xAxisname"=> "Sales",
			//"yAxisName"=> "Revenue (In USD)",
			//"numberPrefix"=> "$",
			 "paletteColors"=> "#1496fa,#e92525,#7fff00,#ffdf0f",
			// "borderAlpha"=> "20",
			// "showCanvasBorder"=> "0",
			// "usePlotGradientColor"=> "0",
			// "plotBorderAlpha"=> "10",
			// "legendBorderAlpha"=> "0",
			// "legendShadow"=> "0",
			// "valueFontColor"=> "#ffffff",                
			// "xAxisLineColor"=> "#999999",
			// "divlineColor"=> "#999999",               
			// "divLineIsDashed"=> "1",
			// "showAlternateVGridColor"=> "0",
			// "showHoverEffect"=>"0",
			////=== y Interval ===
			// "sXaxisminvalue"=> "1",
			// "sXaxismaxvalue"=> "100",
			// "numDivLines"=> "2",
			// "showSum"=> "2",       
			////==Format value ==
			// "stack100Percent"=> "1",
			// "placeValuesInside"=> "0",
			// "formatNumberScale"=> "0",
			// "decimalSeparator"=> ",",
			// "thousandSeparator"=> ".",
			// "showPercentInTooltip" => "1",
			// "showValues" =>"1",
			// "showPercentValues" => "0"
			//== SCROLL HORIZONTAL ===
			"divlinecolor"=> "#CCCCCC",
			"showcanvasborder"=> "0",
			"linethickness"=> "3",
			"plotfillalpha"=> "50",
			"plotgradientcolor"=> "",
			"numVisiblePlot"=> "12",
			"divlineAlpha"=> "100",
			//"divlineColor"=> "#999999",
			"divlineThickness"=> "1",
			"divLineIsDashed"=> "1",
			"divLineDashLen"=> "1",
			"divLineGapLen"=> "1",
			"scrollheight"=>"10",
			"flatScrollBars"=>"1",
			"scrollShowButtons"=>"0",
			"scrollColor"=>"#cccccc",
			"showHoverEffect"=>"1",
			"anchorradius"=>"3",
		];
		return $chart;
	}	
	
	private function categorieslabel(){
		
		$lastDayThisMonth = date("t",strtotime($this->TGL));
		for ($x=1;$x<=$lastDayThisMonth; $x++){
			$data['category'][]=[ "label"=>(string)$x];
		}
		// $categories=[			
                // "category"=> [
                    // [ "label"=> "Jan" ], 
                    // [ "label"=> "Feb" ], 
                    // [ "label"=> "Mar" ], 
                    // [ "label"=> "Apr" ], 
                    // [ "label"=> "May" ], 
                    // [ "label"=> "Jun" ], 
                    // [ "label"=> "Jul" ], 
                    // [ "label"=> "Aug" ], 
                    // [ "label"=> "Sep" ], 
                    // [ "label"=> "Oct" ], 
                    // [ "label"=> "Nov" ], 
                    // [ "label"=> "Dec" ]
                // ]
            
		// ];
		$subCtg[]=['category'=>$data['category']];
		return $subCtg;
	}
	
	
	/*
	 * FUNCTION GET WEEK
	*/
	function weekOfMonthMysql($date) {
		$minggu= date('W', strtotime($date));
		if ($minggu<>0){
			return ($minggu)-1;
		} else{
			return $minggu;
		}
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
