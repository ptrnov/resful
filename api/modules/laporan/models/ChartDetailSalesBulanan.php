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

class ChartDetailSalesBulanan extends DynamicModel
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
				return [
					self::categorieslabel()
				];
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
						sum((PRODUK_TTL_HARGAJUAL)-PRODUK_TTL_HPP) AS PROFITS			
				FROM ptr_kasir_td3c
				WHERE ACCESS_GROUP='".$valAccessGoup."' 
					  AND TAHUN=YEAR('".$this->TGL."')
					 # AND BULAN=MONTH('".$this->TGL."')
				GROUP BY ACCESS_GROUP,TAHUN,BULAN
				ORDER BY ACCESS_GROUP,TAHUN,BULAN ASC;				
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
				FROM ptr_kasir_td3c
				WHERE ACCESS_GROUP='".$valAccessGoup."' 
					  AND STORE_ID='".$this->STORE_ID."'
					  AND TAHUN=YEAR('".$this->TGL."')
					  #AND BULAN=MONTH('".$this->TGL."')
				GROUP BY ACCESS_GROUP,STORE_ID,TAHUN,BULAN
				ORDER BY BULAN ASC;
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
			//foreach ($modelMonth as $row => $val){
				$rslt1['seriesName']='Pendapatan';							
				for( $i=1 ; $i <= 12 ; $i++ ) {
					$valData=[];
					$valData=Yii::$app->arrayBantuan->array_find($modelMonth,'BULAN',$i);						
					if($valData){
						$dataval1[]=['value'=>$valData[0]['REVENUES']];
					}else{
						$dataval1[]=['value'=>(string)0];
					};			
				} 			
				$rslt1['data']=$dataval1;
			//};
			$dataset[]=$rslt1;
			
			$dataval1=[];
			// foreach ($modelMonth as $row => $val){				
				// $rslt1['seriesName']='Modal';				
				// $dataval1[]=['value'=>$val['MODAL']];
				// $rslt1['data']=$dataval1;
				// $rslt1['renderAs']="area";
                // $rslt1['showValues']="0";
			// }
				$rslt1['seriesName']='Modal';				
				for( $i=1 ; $i <= 12 ; $i++ ) {	
					$valData=[];				
					$valData=Yii::$app->arrayBantuan->array_find($modelMonth,'BULAN',$i);						
					if($valData){
						$dataval1[]=['value'=>$valData[0]['MODAL']];
					}else{
						$dataval1[]=['value'=>(string)0];
					};			
				}
				$rslt1['data']=$dataval1;	
				$rslt1['renderAs']="area";
                $rslt1['showValues']="0";				
			$dataset[]=$rslt1;
			
			$dataval1=[];			
			/* foreach ($modelMonth as $row => $val){				
				$rslt1['seriesName']='Keuntungan';				
				$dataval1[]=['value'=>$val['PROFITS']];
				$rslt1['data']=$dataval1;
				//$rslt1['parentYAxis']="S";
                $rslt1['renderAs']="line";
                $rslt1['showValues']="0";
                $rslt1['showLabels']="1";               				
			}; */
				$rslt1['seriesName']='Keuntungan';				
				for( $i=1 ; $i <= 12 ; $i++ ) {	
					$valData=[];				
					$valData=Yii::$app->arrayBantuan->array_find($modelMonth,'BULAN',$i);						
					if($valData){
						$dataval1[]=['value'=>$valData[0]['PROFITS']];
					}else{
						$dataval1[]=['value'=>(string)0];
					};			
				}
				$rslt1['data']=$dataval1;	
				$rslt1['renderAs']="line";
                $rslt1['showValues']="0";
			$dataset[]=$rslt1;
			
			//===PPN===
			$dataval1=[];			
			// foreach ($modelMonth as $row => $val){				
				// $rslt1['seriesName']='PPN';				
				// $dataval1[]=['value'=>$val['PPN']];
				// $rslt1['data']=$dataval1;
				// $rslt1['renderAs']="line";
                // $rslt1['showValues']="0";
				
			// };
			$rslt1['seriesName']='PPN';				
				for( $i=1 ; $i <= 12 ; $i++ ) {	
					$valData=[];				
					$valData=Yii::$app->arrayBantuan->array_find($modelMonth,'BULAN',$i);						
					if($valData){
						$dataval1[]=['value'=>$valData[0]['PPN']];
					}else{
						$dataval1[]=['value'=>(string)0];
					};			
				}
				$rslt1['data']=$dataval1;	
				$rslt1['renderAs']="line";
                $rslt1['showValues']="0";
			$dataset[]=$rslt1;
			
		}else{
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
			
			/* "captionFontSize"=>"12",
			"subcaptionFontSize"=>"10",
			"subcaptionFontBold"=>"0",
			"pYAxisName"=> "Nilai Rupiah (In IDR)",
			"pYAxisNameFont"=> "Arial",
			"pYAxisNameFontSize"=> "12",
			"pYAxisNameFontColor"=> "#003366",
			"pYAxisNameFontBold"=> "1",
			"pYAxisNameFontItalic"=> "1",
			"pYAxisNameAlpha"=> "50",
			"sYAxisNameFont"=> "Arial",
			"sYAxisNameFontSize"=> "12",
			"sYAxisNameFontColor"=> "#003366",
			"sYAxisNameFontBold"=> "1",
			"sYAxisNameFontItalic"=> "1",
			"sYAxisNameAlpha"=> "50",
			"theme"=> "fint",
			//===PALLATE===
			"paletteColors"=> "#1496fa,#ff0000,#7fff00,#ffdf0f",
			"plotHighlightEffect"=> "fadeout|color=#f6f5fd, alpha=60",		//== EFFECT HIGLIFGT
			//"plotfillalpha"=> "50",											//== TRANSFARAN BARCHART			
			
			//===COLOR MODIFY===
			//"bgcolor"=> "ABCAD3,B3CCE1",
			"bgcolor"=> "#ffffff",
			"canvasborderalpha"=> "0",
			"canvasbgalpha"=> "0",	
			"divlinecolor"=> "ABCAD3,B3CCE1",
			//"bgalpha"=>"5,60",											//===Gradiasi Background
			"showHoverEffect"=>"1",
			"anchorradius"=>"3",
			
			//"canvasbgcolor"=> "F7F0F9",	
			//== TOOL TIP ===	
			"toolTipColor"=> "#ffffff",
			"toolTipBorderThickness"=> "0",
			"toolTipBgColor"=> "#000000",
			"toolTipBgAlpha"=> "80",
			"toolTipBorderRadius"=> "2",
			"toolTipPadding"=> "5",
			
			//=== LEGEND ===
			"showLegend"=> "1",
			"legendShadow"=> "1",
			"legendBorderAlpha"=> "1",
			
			//== SCROLL HORIZONTAL ===			
			// "linethickness"=> "3",
			// "plotfillalpha"=> "50",
			// "plotgradientcolor"=> "",
			// "numVisiblePlot"=> "12",
			
			////==Format value ==
			"showValues"=> "1",
			"rotateValues"=> "0",
			"placeValuesInside"=> "0",
			"formatNumberScale"=> "0",
			"decimalSeparator"=> ",",
			"thousandSeparator"=> ".",
			"numberPrefix"=> "",
			
			"usePlotGradientColor"=> "0",			
			"showAxisLines"=> "0",
			"vDivLineThickness"=> "1",	
			"showAlternateHGridColor"=> "0",
			"divlineThickness"=> "1",
			"divLineIsDashed"=> "1",
			"divLineDashLen"=> "1",
			"divLineGapLen"=> "0",
			"vDivLineDashed"=> "0",
			"numVDivLines"=> "6",
			"vDivLineThickness"=> "1",			
			"anchorradius"=> "0", */
			
			/* "basefontcolor"=> "37444A",
			//"xAxisname"=> "Month",
			
			//"sYAxisName"=> "Profit %",
			//"numberPrefix"=> "$",
			//"sNumberSuffix" => "%",
			//"sYAxisMaxValue" => "50",
			//Primary Y-Axis Name font properties
			
			//Secondary Y-Axis Name font properties
			
			//"theme"=> "fint",
			"showBorder"=> "1",
			"showShadow"=> "1",
			"usePlotGradientColor"=> "0",			
			"showAxisLines"=> "0",
			"showAlternateHGridColor"=> "1",
			"divlineThickness"=> "1",
			"divLineIsDashed"=> "0",
			"divLineDashLen"=> "1",
			"divLineGapLen"=> "0",
			"vDivLineDashed"=> "0",
			"numVDivLines"=> "6",
			"vDivLineThickness"=> "1",			
			"anchorradius"=> "0",
					
			"ValuePadding"=> "1",
			
			
			
			//"plotgradientcolor"=> "0000FF",
			//"bordercolor"=> "9DBCCC",
			//"vdivlinecolor"=> "6281B5",
			//"plotfillangle"=> "90",			
			//"showcanvasborder"=> "0",			
			//"xAxisname"=> "Sales",
			//"yAxisName"=> "Revenue (In USD)",
			//"numberPrefix"=> "$",
			//=== Pallete Setting ==
			
			 "showplotborder"=>"1",			
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
			
			// "stack100Percent"=> "1",
			// "showPercentInTooltip" => "1",
			// "showPercentValues" => "0"
			// "numbersuffix"=> "%25",
			
			
			//"divlineColor"=> "#999999",
			// "divlineThickness"=> "1",
			// "divLineIsDashed"=> "1",
			// "divLineDashLen"=> "1",
			// "divLineGapLen"=> "1",
			// "scrollheight"=>"10",
			// "flatScrollBars"=>"1",
			// "scrollShowButtons"=>"0",
			// "scrollColor"=>"#cccccc",
			 */
		];
		return $chart;
	}	
	
	private function categorieslabel(){
		$categories=[
			
                "category"=> [
                    [ "label"=> "Jan" ], 
                    [ "label"=> "Feb" ], 
                    [ "label"=> "Mar" ], 
                    [ "label"=> "Apr" ], 
                    [ "label"=> "May" ], 
                    [ "label"=> "Jun" ], 
                    [ "label"=> "Jul" ], 
                    [ "label"=> "Aug" ], 
                    [ "label"=> "Sep" ], 
                    [ "label"=> "Oct" ], 
                    [ "label"=> "Nov" ], 
                    [ "label"=> "Dec" ]
                ]
            
		];
		return $categories;
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
