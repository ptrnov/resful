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

class SalesBulananPerstore extends DynamicModel
{
	public function rules()
    {
        return [
            [['ACCESS_GROUP','STORE_ID','PERANGKAT','THN','BLN'], 'safe'],
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
				return self::salesBulananGroup();
			}
		];
	}
	
	public function salesBulananGroup(){
		$varThn=$this->THN!=''?$this->THN:date('Y');
		$valAccessGoup=$this->ACCESS_GROUP!=''?$this->ACCESS_GROUP:'';
		//return ['aa'=>$this->ACCESS_GROUP];
		$sqlthn="
			#==GROUPING===
			SELECT	TAHUN,STORE_ID
			FROM ptr_kasir_td3c
			#WHERE ACCESS_GROUP=".$valAccessGoup." AND TAHUN='".$varThn."'
			WHERE ACCESS_GROUP=".$valAccessGoup." 
			GROUP BY ACCESS_GROUP,TAHUN,STORE_ID
			ORDER BY TAHUN ASC;
		";		
		$qrySqlThn= Yii::$app->production_api->createCommand($sqlthn)->queryAll(); 		
		$dataProviderThn= new ArrayDataProvider([	
			'allModels'=>$qrySqlThn,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		$modelTahun=$dataProviderThn->getModels();
		$datasetRslt[]=[];
		foreach($modelTahun as $rowThn => $valThn){
			$sql="
				#==GROUPING===
				SELECT	ACCESS_GROUP,STORE_ID,BULAN,TAHUN,
						sum(PRODUK_TTL_JUALPPNDISCOUNT) AS PRODUK_TTL_JUALPPNDISCOUNT
				FROM ptr_kasir_td3c
				WHERE ACCESS_GROUP=".$valAccessGoup." AND TAHUN='".$valThn['TAHUN']."' AND STORE_ID='".$valThn['STORE_ID']."'
					#ACCESS_GROUP='170726220936' AND TAHUN=YEAR('2018-02-01')
				GROUP BY ACCESS_GROUP,BULAN; 	
			";		
			$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
			$dataProvider= new ArrayDataProvider([	
				'allModels'=>$qrySql,	
				'pagination' => [
					'pageSize' =>1000,
				],			
			]);
			$modelMonth=$dataProvider->getModels();
			if($modelMonth){	
				foreach ($modelMonth as $row => $val){
					$rslt1['seriesname']=$valThn['STORE_ID'];
					$dataval1=[];
					$valData=[];
					//=[3]==LOOPING 24 hour
					for( $i= 1 ; $i <= 12 ; $i++ ) {
						//$cariWeek=$firstWeekOfMonth + $i;					
						$valData=Yii::$app->arrayBantuan->array_find($modelMonth,'BULAN',$i);					
						if($valData){
							$dataval1[]=['label'=>'bln'.$valData[0]['BULAN'],'value'=>$valData[0]['PRODUK_TTL_JUALPPNDISCOUNT']];
						}else{
							$dataval1[]=['label'=>'w'.$i,'value'=>'0'];
						};				
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
			// unset($modelMonth);
			// unset($dataProvider);
			$datasetRslt[]=$dataset;
			
		}
		return $datasetRslt;
	}
	
	private function chartlabel(){
		// $varTahun		= $this->TGL!=''?date('Y',strtotime($this->TGL)):date('Y');
		// $varBulan		= $this->TGL!=''?date('m',strtotime($this->TGL)):date('m');
		// $varTgl			= $this->TGL!=''?date('Y-m-d',strtotime($this->TGL)):date('Y-m-d');		
		// $nmBulan		= date('F', strtotime($varTahun.'-'.str_pad($varBulan, 2, '0', STR_PAD_LEFT).'-01')); // Nama Bulan
		$varTahun		= $this->THN!=''?$this->THN:date('Y');
		
		
		$chart=[
			"caption"=>"RINGKASAN PENJUALAN BULANAN",
			"subCaption"=>"TAHUN ".$varTahun,
			"captionFontSize"=>"12",
			"subcaptionFontSize"=>"10",
			"subcaptionFontBold"=>"0",
			"paletteColors"=> "#ff4040,#0000ff,#7fff00,#ff7f24,#ff7256,#ffb90f",
			"bgcolor"=>"#ffffff",
			"showBorder"=>"1",
			"showShadow"=>"0",				
			"usePlotGradientColor"=>"0",
			"legendBorderAlpha"=>"0",
			"showAxisLines"=>"0",
			"showAlternateHGridColor"=>"0",
			"divlineThickness"=>"1",
			"divLineIsDashed"=>"0",				
			"divLineDashLen"=>"1",				
			"divLineGapLen"=>"1",
			"vDivLineDashed"=>"0",
			"numVDivLines"=>"11",
			"vDivLineThickness"=>"1",
			"xAxisName"=>"Toko",
			"yAxisName"=>"Rupiah",				
			"anchorradius"=>"6",
			"plotHighlightEffect"=>"fadeout|color=#f6f5fd, alpha=60",
			"showValues"=>"0",
			"rotateValues"=>"0",
			"placeValuesInside"=>"0",
			"formatNumberScale"=>"0",
			"decimalSeparator"=>",",
			"thousandSeparator"=>".",
			"numberPrefix"=>"",
			"ValuePadding"=>"0",
			"yAxisValuesStep"=>"1",
			"xAxisValuesStep"=>"0",
			"yAxisMinValue"=>"0",
			"numDivLines"=>"8",
			"xAxisNamePadding"=>"30",
			"showHoverEffect"=>"1",
			"animation"=>"1" ,
			"exportEnabled"=>"1",
			"exportFileName"=>"RINGKASAN-BULANAN",
			"exportAtClientSide"=>"1",
			"showValues"=>"1",
			//==LEGEND==
			//"legendBorderAlpha"=> "0",
			"legendShadow"=> "1",
			 //"legendAllowDrag"=> "0",			//=== DRAG POSITION LEGEND
			 "legendPosition"=>"right",			//=== POSISI LEGEND			
		];
		return $chart;
	}
	
	private function categorieslabel(){
		$categories=[
			"category"=> [
				[
					"label"=> "january"
				],
				[
					"label"=> "February"
				],
				[
					"label"=> "March"
				],
				[
					"label"=> "April"
				],
				[
					"label"=> "Mey"
				],
				[
					"label"=> "June"
				],
				[
					"label"=> "July"
				],
				[
					"label"=> "Agustus"
				],
				[
					"label"=> "September"
				],
				[
					"label"=> "Oktober"
				],
				[
					"label"=> "November"
				],
				[
					"label"=> "Desember"
				]									
			],
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
