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

class ChartProdukTopMonth extends DynamicModel
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
				if ($this->PILIH=='QTY'){
					return self::groupProdukBulanQty();
				}elseif($this->PILIH=='HPPJUAL'){
					return self::groupProdukBulanHppJual();
				};				
			}
		];
	}
	
	private function getData(){
		$valAccessGoup=$this->ACCESS_GROUP!=''?$this->ACCESS_GROUP:'';
		
		if($this->STORE_ID=='' OR $this->ACCESS_GROUP==$this->STORE_ID){
			$sql="
				#==GROUPING===
				SELECT	td1c.ACCESS_GROUP,td1c.STORE_ID,td1c.TAHUN,td1c.BULAN,
						td1c.PRODUCT_NM,td1c.PRODUK_SUBTTL_QTY,p1.CURRENT_HPP,p1.CURRENT_PRICE
				FROM ptr_kasir_td1c  td1c 
				LEFT JOIN product p1 on p1.PRODUCT_ID=td1c.PRODUCT_ID			
				WHERE td1c.ACCESS_GROUP='".$valAccessGoup."' AND td1c.TAHUN=YEAR('".$this->TGL."') AND td1c.BULAN=MONTH('".$this->TGL."')
				GROUP BY td1c.ACCESS_GROUP,td1c.TAHUN,td1c.BULAN,td1c.PRODUCT_ID
				ORDER BY td1c.PRODUK_SUBTTL_QTY DESC LIMIT 10;
			";	
		}else{
			$sql="
				#==GROUPING===
				SELECT	td1c.ACCESS_GROUP,td1c.STORE_ID,td1c.TAHUN,td1c.BULAN,
						td1c.PRODUCT_NM,td1c.PRODUK_SUBTTL_QTY,p1.CURRENT_HPP,p1.CURRENT_PRICE
				FROM ptr_kasir_td1c  td1c 
				LEFT JOIN product p1 on p1.PRODUCT_ID=td1c.PRODUCT_ID			
				WHERE td1c.ACCESS_GROUP='".$valAccessGoup."' AND td1c.STORE_ID='".$this->STORE_ID."' AND td1c.TAHUN=YEAR('".$this->TGL."') AND td1c.BULAN=MONTH('".$this->TGL."')
				GROUP BY td1c.ACCESS_GROUP,td1c.STORE_ID,td1c.TAHUN,td1c.BULAN,td1c.PRODUCT_ID
				ORDER BY td1c.PRODUK_SUBTTL_QTY DESC LIMIT 10;
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
	
	public function groupProdukBulanQty(){
		
		$modelMonth=self::getData();
		if($modelMonth){			
			// $dataval1=[];			
			// foreach ($modelMonth as $row => $val){				
				// $rslt1['seriesname']='HPP';				
				// $dataval1[]=['value'=>$val['CURRENT_HPP']];
				// $rslt1['data']=$dataval1;
			// };
			// $dataset[]=$rslt1;
			
			// $dataval1=[];
			// foreach ($modelMonth as $row => $val){				
				// $rslt1['seriesname']='HARGA_JUAL';				
				// $dataval1[]=['value'=>$val['CURRENT_PRICE']];
				// $rslt1['data']=$dataval1;
			// }
			// $dataset[]=$rslt1;//$rsltDataSet1;	
			
			$dataval1=[];			
			foreach ($modelMonth as $row => $val){				
				$rslt1['seriesname']='Jumlah Qty terjual';				
				$dataval1[]=['value'=>$val['PRODUK_SUBTTL_QTY']];
				$rslt1['data']=$dataval1;
			};
			$dataset[]=$rslt1;
		}else{
			$dataset[]=[
					"seriesname"=>'Data-Empty',
					"data"=>[]			
			];
		}
		// unset($modelMonth);
		// unset($dataProvider);
		$datasetRslt=$dataset;	
	
		return $datasetRslt;
	}
	
	public function groupProdukBulanHppJual(){
		
		$modelMonth=self::getData();
		if($modelMonth){			
			$dataval2=[];			
			foreach ($modelMonth as $row => $val){				
				$rslt2['seriesname']='HPP';				
				$dataval2[]=['value'=>$val['CURRENT_HPP']];
				$rslt2['data']=$dataval2;
			};
			$dataset2[]=$rslt2;
			
			$dataval2=[];
			foreach ($modelMonth as $row => $val){				
				$rslt2['seriesname']='HARGA_JUAL';				
				$dataval2[]=['value'=>$val['CURRENT_PRICE']];
				$rslt2['data']=$dataval2;
			}
			$dataset2[]=$rslt2;//$rsltDataSet1;	
			
			// $dataval2=[];			
			// foreach ($modelMonth as $row => $val){				
				// $rslt2['seriesname']='QTY';				
				// $dataval1[]=['value'=>$val['PRODUK_SUBTTL_QTY']];
				// $rslt2['data']=$dataval2;
			// };
			// $dataset2[]=$rslt2;
		}else{
			$dataset2[]=[
					"seriesname"=>'Data-Empty',
					"data"=>[]					
			];
		}
		// unset($modelMonth);
		// unset($dataProvider);
		$datasetRslt2=$dataset2;	
	
		return $dataset2;
	}
	
	private function chartlabel(){
		$nmBulan		= date('F', strtotime($this->TGL)); // Nama Bulan
		$varTahun		= date('Y', strtotime($this->TGL));;
				
		$chartQty=[
			"caption"=>"TOP 10 PRODUK",
			"subCaption"=>"Qty Bulan ".$nmBulan." ".$varTahun,
			"captionFontSize"=>"12",
			"subcaptionFontSize"=>"10",
			"subcaptionFontBold"=>"0",
			//"paletteColors"=> "#ff4040,#0000ff,#7fff00,#ff7f24,#ff7256,#ffb90f",
			"bgcolor"=>"#ffffff",
			"showBorder"=>"1",
			"showShadow"=>"0",			
			"xAxisname"=> "Produk",
			//"yAxisName"=> "Revenue (In USD)",
			//"numberPrefix"=> "$",
			"paletteColors"=> "#54b8d9,#ff7256,#ff7f24",
			"borderAlpha"=> "20",
			"showCanvasBorder"=> "0",
			"usePlotGradientColor"=> "0",
			"plotBorderAlpha"=> "10",
			"legendBorderAlpha"=> "0",
			"legendShadow"=> "0",
			"valueFontColor"=> "#ffffff",                
			"xAxisLineColor"=> "#999999",
			"divlineColor"=> "#999999",               
			"divLineIsDashed"=> "1",
			"showAlternateVGridColor"=> "0",
			"showHoverEffect"=>"0",
			//=== y Interval ===
			"sXaxisminvalue"=> "1",
			"sXaxismaxvalue"=> "100",
			"numDivLines"=> "2",
			"showSum"=> "2",       
			//==Format value ==
			"stack100Percent"=> "0",
			"placeValuesInside"=> "0",
			"formatNumberScale"=> "0",
			"decimalSeparator"=> ",",
			"thousandSeparator"=> ".",
			"showPercentInTooltip" => "1",
			"showValues" =>"1",
			"showPercentValues" => "0",
		];
		
		$chartHppJual=[
			"caption"=>"TOP 10 PRODUK ",
			"subCaption"=>"Hpp & Harga Jual, Bulan ".$nmBulan." ".$varTahun,
			"captionFontSize"=>"12",
			"subcaptionFontSize"=>"10",
			"subcaptionFontBold"=>"0",
			//"paletteColors"=> "#ff4040,#0000ff,#7fff00,#ff7f24,#ff7256,#ffb90f",
			"bgcolor"=>"#ffffff",
			"showBorder"=>"1",
			"showShadow"=>"0",			
			"xAxisname"=> "Produk",
			//"yAxisName"=> "Revenue (In USD)",
			//"numberPrefix"=> "$",
			"paletteColors"=> "#0075c2,#ff7256,#ff7f24",
			"borderAlpha"=> "20",
			"showCanvasBorder"=> "0",
			"usePlotGradientColor"=> "0",
			"plotBorderAlpha"=> "10",
			"legendBorderAlpha"=> "0",
			"legendShadow"=> "0",
			"valueFontColor"=> "#ffffff",                
			"xAxisLineColor"=> "#999999",
			"divlineColor"=> "#999999",               
			"divLineIsDashed"=> "1",
			"showAlternateVGridColor"=> "0",
			"showHoverEffect"=>"0",
			//=== y Interval ===
			"sXaxisminvalue"=> "1",
			"sXaxismaxvalue"=> "100",
			"numDivLines"=> "2",
			"showSum"=> "2",       
			//==Format value ==
			"stack100Percent"=> "1",
			"placeValuesInside"=> "0",
			"formatNumberScale"=> "0",
			"decimalSeparator"=> ",",
			"thousandSeparator"=> ".",
			"showPercentInTooltip" => "1",
			"showValues" =>"1",
			"showPercentValues" => "0",
		];
		
		if ($this->PILIH=='QTY'){
			return $chartQty;
		}elseif($this->PILIH=='HPPJUAL'){
			return $chartHppJual;
		};		
	}
	
	
	
	
	
	private function categorieslabel(){
		$modelMonth=self::getData();
		$dataval=[];	
		if($modelMonth){					
			foreach ($modelMonth as $row => $val){				
				$dataval[]=['label'=>$val['PRODUCT_NM']];
					
				$categories['category']=$dataval;
			};		
			$i=count($modelMonth);	
			for ($x=$i+1; $x<=10; $x++){
				$dataval[]=['label'=>"Produk"." ".$x];
			};			
			$categories['category']=$dataval;
		}else{
			$categories=[
				"category"=> [
					[
						"label"=> "Produk 1"
					],
					[
						"label"=> "Produk 2"
					],
					[
						"label"=> "Produk 3"
					],
					[
						"label"=> "Produk 4"
					],
					[
						"label"=> "Produk 5"
					],
					[
						"label"=> "Produk 6"
					],
					[
						"label"=> "Produk 7"
					],
					[
						"label"=> "Produk 8"
					],
					[
						"label"=> "Produk 9"
					],
					[
						"label"=> "Produk 10"
					]
				]
			];
		}	
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
