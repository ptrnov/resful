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
use api\modules\laporan\models\Store;

class SalesMingguanPerstore extends DynamicModel
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['BULAN', 'MINGGU','ACCESS_GROUP'], 'integer'],
			[['TAHUN','TOTAL_JUAL'], 'string', 'max' => 5],
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
				return self::chartData();				
			}
		];
	}
	
	private function chartData(){		
		$varAccessGroup = $this->ACCESS_GROUP!=''?$this->ACCESS_GROUP:'';
		$varTahun		= $this->TAHUN!=''?$this->TAHUN:date('Y');
		$varBulan		= $this->BULAN!=''?$this->BULAN:date('m');
		$varBulanDigit	= str_pad($varBulan, 2, '0', STR_PAD_LEFT); //2 gigit bulan
		
		$modelStore=Store::find()->where(['ACCESS_GROUP'=>$varAccessGroup])->all();
		foreach($modelStore as $rowStore => $valStore){
			$datasetRslt=[];
				$sql="
					#==GROUPING===
					SELECT	ACCESS_GROUP,STORE_ID,BULAN,TAHUN,MINGGU,
							sum(PRODUK_TTL_JUALPPNDISCOUNT) AS PRODUK_TTL_JUALPPNDISCOUNT
					FROM ptr_kasir_td3b
					WHERE ACCESS_GROUP='".$varAccessGroup."' AND 
						  STORE_ID='".$valStore['STORE_ID']."' AND
						  TAHUN='".$this->TAHUN."' AND 
						  BULAN='".$this->BULAN."' 
					GROUP BY ACCESS_GROUP,STORE_ID,TAHUN,BULAN,MINGGU; 	
				";		
				$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
				$dataProvider= new ArrayDataProvider([	
					'allModels'=>$qrySql,	
					'pagination' => [
						'pageSize' =>1000,
					],			
				]);
				$modelWeekOfMonth=$dataProvider->getModels();
				
				if ($modelWeekOfMonth){	
					
						$firstWeekOfMonth=self::weekOfMonthMysql(date('Y-m-d',strtotime($varTahun.'-'.$varBulanDigit.'-01')));
						$nmBulan=date('F', strtotime($modelWeekOfMonth[0]['TAHUN'].'-'.$modelWeekOfMonth[0]['BULAN'].'-01')); 
						$rslt1['seriesname']=$valStore['STORE_NM']; //$nmBulan.'-w'.$modelWeekOfMonth[0]['MINGGU'];
						$dataval1=[];
						//=[3]==LOOPING 5 MINGGU
						//$x=1;
						//$cariWeek=0;
						//$xs=($firstWeekOfMonth+5);
						for( $i=0 ; $i <= 4 ; $i++ ) {
							$cariWeek=(integer)($firstWeekOfMonth) + $i;	
							$valData='';
							$valData=Yii::$app->arrayBantuan->array_find($modelWeekOfMonth,'MINGGU',$cariWeek);
							
							if($valData){
							//	$dataval1[]=['label'=>'w'.$valData[0]['MINGGU'],'value'=>$valData[0]['TOTAL_JUAL']];
								$dataval1[]=['label'=>'w'.$cariWeek,'value'=>$valData[0]['PRODUK_TTL_JUALPPNDISCOUNT']];
							}else{
								$dataval1[]=['label'=>'w'.$cariWeek,'value'=>'0'];
								//$dataval1[]=['label'=>'w'.$i,'value'=>$cariWeek];
							};				
							//$x=$x+1;
						} 
						//=[4]==SETTING ARRAY
						$rslt1['data']=$dataval1;	
						//$rsltDataSet1[]=$rslt1;
			
						$dataset[]=$rslt1;//$rsltDataSet1;	
					
				}else{
					//=[6]== SCENARIO DATA KOSONG				
					$dataset[]=[
							"seriesname"=>$valStore['STORE_NM'],//"Tidak ditemukan data",
							"data"=>[[]]					
					];
				}
			
			$datasetRslt=$dataset;
		}
		return $datasetRslt;			
	}
	
	private function chartlabel(){
		$varTahun		= $this->TAHUN!=''?$this->TAHUN:date('Y');
		$varBulan		= $this->BULAN!=''?$this->BULAN:date('m');
		$nmBulan=date('F', strtotime($varTahun.'-'.$varBulan.'-01')); // Nama Bulan
		$chart=[
			"caption"=>"RINGKASAN PENJUALAN MINGGUAN",
			"subCaption"=>"TAHUN ".$varTahun.', '.$nmBulan,
			"captionFontSize"=>"12",
			"subcaptionFontSize"=>"10",
			"subcaptionFontBold"=>"0",
			"paletteColors"=>Yii::$app->arrayBantuan->ArrayPaletteColors(),
			"bgcolor"=>"#ffffff",
			"showBorder"=>"1",
			"showShadow"=>"0",				
			"usePlotGradientColor"=>"0",
			"legendBorderAlpha"=>"0",
			"legendShadow"=>"1",
			"showAxisLines"=>"0",
			"showAlternateHGridColor"=>"0",
			"divlineThickness"=>"1",
			"divLineIsDashed"=>"0",				
			"divLineDashLen"=>"1",				
			"divLineGapLen"=>"1",
			"vDivLineDashed"=>"0",
			"numVDivLines"=>"11",
			"vDivLineThickness"=>"1",
			"xAxisName"=>"Minggu",
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
			"exportFileName"=>"RINGKASAN-SALES-MINGGUN",
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
					"label"=>"Minggu-1"
				],
				[
					"label"=>"Minggu-2"
				],
				[
					"label"=>"Minggu-3"
				],
				[
					"label"=>"Minggu-4"
				],
				[
					"label"=>"Minggu-5"
				],
								
			],
		 ];
		 return $categories;
	}
	
	
	/*
	 * FUNCTION GET WEEK
	*/
	function weekOfMonthMysql($date) {
		$minggu= date('W', strtotime($date));
		// return $mingguInt;
		if ($minggu==0){
			return $minggu;
		}else{
			return $minggu-1;
		}
	}
}
