<?php

namespace api\modules\laporan\controllers;

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


use api\modules\laporan\models\CounterSearch;
use api\modules\laporan\models\CounterGroup;
use api\modules\laporan\models\TransPenjualanHeader;

class ContohChartController extends ActiveController
{

	public $modelClass = 'api\modules\laporan\models\TransPenjualanHeader';

	/**
     * Behaviors
	 * Mengunakan Auth HttpBasicAuth.
	 * Chacking logintest.
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
					'application/json' => Response::FORMAT_JSON,
					'charset' => 'utf8_encode',
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
		unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
		return $actions;
	} 
	
	/*	=========== ptrnov Widget ======
		$viewAngulargauge= Chart::Widget([
			'urlSource'=> 'https://production.kontrolgampang.com/laporan/contoh-charts/angulargauge',
			'metode'=>'POST',
			'param'=>[
				'ACCESS_GROUP'=>Yii::$app->getUserOpt->user()['ACCESS_GROUP'],
				'THN'=>date("Y"),
			],
			'type'=>'angulargauge',						
			'renderid'=>'angulargauge-id',				
			'autoRender'=>true,
			'width'=>'100%',
			'height'=>'300px',
		]);	
	*/
	public function actionAngulargauge()
	{
		/*  =========== HEADER DEFINITION ======
			var cSatScoreChart = new FusionCharts({
			type: 'angulargauge',
			renderAt: 'chart-container',
			width: '450',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/	
		
		$angulargauge=[
			"chart"=> [
				//===>1
				// "caption"=> "Customer Satisfaction Score",
				// "subcaption"=> "Last week",
				// "lowerLimit"=> "0",
				// "upperLimit"=> "100",
				// "theme"=> "fint"
				//===>2
				// "caption"=> "Customer Satisfaction Score",
				// "lowerlimit"=> "0",
				// "upperlimit"=> "100",
				// "lowerlimitdisplay"=> "Bad",
				// "upperlimitdisplay"=> "Good",
				// "palette"=> "1",
				// "numbersuffix"=> "%",
				// "tickvaluedistance"=> "10",
				// "showvalue"=> "0",
				// "gaugeinnerradius"=> "0",
				// "bgcolor"=> "FFFFFF",
				// "pivotfillcolor"=> "333333",
				// "pivotradius"=> "8",
				// "pivotfillmix"=> "333333, 333333",
				// "pivotfilltype"=> "radial",
				// "pivotfillratio"=> "0,100",
				// "showtickvalues"=> "1",
				// "showborder"=> "0"
				//===>3
				"caption"=> "Pengunjung",
				"manageresize"=> "1",
				"origw"=> "400",
				"origh"=> "250",
				"managevalueoverlapping"=> "1",
				"autoaligntickvalues"=> "1",
				"bgcolor"=> "FFFFFF",
				"fillangle"=> "45",
				"upperlimit"=> "2500000",
				"lowerlimit"=> "1600000",
				"majortmnumber"=> "10",
				"majortmheight"=> "8",
				"showgaugeborder"=> "0",
				"gaugeouterradius"=> "140",
				"gaugeoriginx"=> "205",
				"gaugeoriginy"=> "206",
				"gaugeinnerradius"=> "2",
				"formatnumberscale"=> "1",
				"numberprefix"=> "",
				"decmials"=> "2",
				"tickmarkdecimals"=> "1",
				"pivotradius"=> "10",
				"showpivotborder"=> "1",
				"pivotbordercolor"=> "000000",
				"pivotborderthickness"=> "10",
				"pivotfillmix"=> "666666",
				"tickvaluedistance"=> "10",
				"valuebelowpivot"=> "1",
				"showvalue"=> "1",
				"showborder"=> "0"
			],
			"colorRange"=> [
				"color"=> [
					//==>1,2
					// [
						// "minValue"=> "0",
						// "maxValue"=> "50",
						// "code"=> "#e44a00"
					// ], 
					// [
						// "minValue"=> "50",
						// "maxValue"=> "75",
						// "code"=> "#f8bd19"
					// ], 
					// [
						// "minValue"=> "75",
						// "maxValue"=> "100",
						// "code"=> "#6baa01"
					// ]
					//==>3
					[
						"minvalue"=> "160",
						"maxvalue"=> "193",
						"code"=> "e44a00"
					],
					[
						"minvalue"=> "193",
						"maxvalue"=> "217",
						"code"=> "f8bd19"
					],
					[
						"minvalue"=> "217",
						"maxvalue"=> "250",
						"code"=> "6baa01"
					]
				]
			],
			"dials"=> [
				"dial"=> [
					[
						//"value"=> "67"
						//==>3
						 "value"=> "2100000",
						"borderalpha"=> "0",
						"bgcolor"=> "000000",
						"basewidth"=> "20",
						"topwidth"=> "1",
						"radius"=> "130"
					]
				]
			]
		];
		return $angulargauge;
	}
	
	public function actionLine()
	{
		/*  =========== LINE CHART DEFINITION ======
			var cSatScoreChart = new FusionCharts({
			type: 'angulargauge',
			renderAt: 'chart-container',
			width: '450',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/	
		
		$line=[
			"chart"=> [
				//===>1
				// "caption"=> "Customer Satisfaction Score",
				// "subcaption"=> "Last week",
				// "lowerLimit"=> "0",
				// "upperLimit"=> "100",
				// "theme"=> "fint"
				//===>2
				// "caption"=> "Customer Satisfaction Score",
				// "lowerlimit"=> "0",
				// "upperlimit"=> "100",
				// "lowerlimitdisplay"=> "Bad",
				// "upperlimitdisplay"=> "Good",
				// "palette"=> "1",
				// "numbersuffix"=> "%",
				// "tickvaluedistance"=> "10",
				// "showvalue"=> "0",
				// "gaugeinnerradius"=> "0",
				// "bgcolor"=> "FFFFFF",
				// "pivotfillcolor"=> "333333",
				// "pivotradius"=> "8",
				// "pivotfillmix"=> "333333, 333333",
				// "pivotfilltype"=> "radial",
				// "pivotfillratio"=> "0,100",
				// "showtickvalues"=> "1",
				// "showborder"=> "0"
				//===>3
				"caption"=> "Revenue - 2013",
				"manageresize"=> "1",
				"origw"=> "400",
				"origh"=> "250",
				"managevalueoverlapping"=> "1",
				"autoaligntickvalues"=> "1",
				"bgcolor"=> "FFFFFF",
				"fillangle"=> "45",
				"upperlimit"=> "2500000",
				"lowerlimit"=> "1600000",
				"majortmnumber"=> "10",
				"majortmheight"=> "8",
				"showgaugeborder"=> "0",
				"gaugeouterradius"=> "140",
				"gaugeoriginx"=> "205",
				"gaugeoriginy"=> "206",
				"gaugeinnerradius"=> "2",
				"formatnumberscale"=> "1",
				"numberprefix"=> "$",
				"decmials"=> "2",
				"tickmarkdecimals"=> "1",
				"pivotradius"=> "10",
				"showpivotborder"=> "1",
				"pivotbordercolor"=> "000000",
				"pivotborderthickness"=> "10",
				"pivotfillmix"=> "666666",
				"tickvaluedistance"=> "10",
				"valuebelowpivot"=> "1",
				"showvalue"=> "1",
				"showborder"=> "0"
			],
			"colorRange"=> [
				"color"=> [
					//==>1,2
					// [
						// "minValue"=> "0",
						// "maxValue"=> "50",
						// "code"=> "#e44a00"
					// ], 
					// [
						// "minValue"=> "50",
						// "maxValue"=> "75",
						// "code"=> "#f8bd19"
					// ], 
					// [
						// "minValue"=> "75",
						// "maxValue"=> "100",
						// "code"=> "#6baa01"
					// ]
					//==>3
					[
						"minvalue"=> "1600000",
						"maxvalue"=> "1930000",
						"code"=> "e44a00"
					],
					[
						"minvalue"=> "1930000",
						"maxvalue"=> "2170000",
						"code"=> "f8bd19"
					],
					[
						"minvalue"=> "2170000",
						"maxvalue"=> "2500000",
						"code"=> "6baa01"
					]
				]
			],
			"dials"=> [
				"dial"=> [
					[
						//"value"=> "67"
						//==>3
						 "value"=> "2100000",
						"borderalpha"=> "0",
						"bgcolor"=> "000000",
						"basewidth"=> "20",
						"topwidth"=> "1",
						"radius"=> "130"
					]
				]
			]
		];
		return $line;
	}
	
	public function actionColumn2d()
	{
		/* =========== COLUMN2D CHART DEFINITION ======
			type: 'column2d',
			renderAt: 'chart-container',
			width: '500',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/
		$column2d=[
			"chart"=> [
				"caption"=> "Monthly Revenue",
				"subCaption"=> "Last year",
				"xAxisName"=> "Month",
				"yAxisName"=> "Amount (In USD)",
				"numberPrefix"=> "$",
				"theme"=> "fint",
				//Attributes to configure rotated label display
				"labelDisplay"=> "rotate",
				"slantLabels"=> "1",
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
			],
			"data"=> [
				[
					"label"=> "January",
					"value"=> "420000"
				], 
				[
					"label"=> "February",
					"value"=> "810000"
				], 
				[
					"label"=> "March",
					"value"=> "720000"
				], 
				[
					"label"=> "April",
					"value"=> "550000"
				], 
				[
					"label"=> "May",
					"value"=> "910000"
				], 
				[
					"label"=> "June",
					"value"=> "510000"
				], 
				[
					"label"=> "July",
					"value"=> "680000"
				],
				[
					"label"=> "August",
					"value"=> "620000"
				], 
				[
					"label"=> "September",
					"value"=> "610000"
				], 
				[
					"label"=> "October",
					"value"=> "490000"
				], 
				[
					"label"=> "November",
					"value"=> "900000"
				], 
				[
					"label"=> "December",
					"value"=> "730000"
				]
			]	
		];	
		
		return $column2d;
	}
	
	public function actionColumn2dImage()
	{
		/* =========== COLUMN2D CHART DEFINITION ======
			type: 'column2d',
			renderAt: 'chart-container',
			width: '500',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/
		$column2dImage=[
			"chart"=> [
				"caption"=>"Revenue by store managers",
                "subCaption"=>"Last quarter",
                "xAxisName"=>"Managers",
                "yAxisName"=>"Revenue (In USD)",
                "numberPrefix"=>"$",
                "theme"=>"fint",
                "LabelPadding"=>"50",
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
			],
			"annotations"=> [
                "groups"=> [
                    [
                        "id"=> "user-images",
                        "items"=> [
                            [
                                "id"=> "jennifer-user-icon",
                                "type"=> "image",
                                "url"=> "https://image.kontrolgampang.com/user/1.png",
								"x"=>	'$xaxis.label.0.x - 24',
								"y"=>	'$xaxis.label.0.y - 48'
                            ], 
                            [
                                "id"=> "tom-user-icon",
                                "type"=> "image",
                                "url"=> "https://image.kontrolgampang.com/user/3.png",
								"x"=>	'$xaxis.label.1.x - 24',
								"y"=>	'$xaxis.label.1.y - 48'
                            ], 
                            [
                                "id"=> "Milton-user-icon",
                                "type"=> "image",
                                "url"=> "https://image.kontrolgampang.com/user/4.png",
								"x"=>	'$xaxis.label.2.x - 24',
								"y"=>	'$xaxis.label.2.y - 48'
                            ], 
                            [
                                "id"=> "Brian-user-icon",
                                "type"=> "image",
                                "url"=> "https://image.kontrolgampang.com/user/5.png",
								"x"=>	'$xaxis.label.3.x - 24',
								"y"=>	'$xaxis.label.3.y - 48'
                            ], 
                            [
                                "id"=> "Lynda-user-icon",
                                "type"=> "image",
                                "url"=> "https://image.kontrolgampang.com/user/2.png",
								"x"=>	'$xaxis.label.4.x - 24',
								"y"=>	'$xaxis.label.4.y - 48'
                            ]
                        ]
                    ]
                ]
            ],
            "data"=> [
                [
                    "label"=> "Jennifer",
                    "value"=> "92000"
                ], 
                [
                    "label"=> "Tom",
                    "value"=> "87000"
                ], 
                [
                    "label"=> "Milton",
                    "value"=> "83000"
                ], 
                [
                    "label"=> "Brian",
                    "value"=> "66000"
                ], 
                [
                    "label"=> "Lynda",
                    "value"=> "58000"
                ]
            ]
		];	
		
		return $column2dImage;
	}
	
	public function actionLineImg()
	{
		/* =========== LINE IMAGE CHART DEFINITION ======
			type: 'line',
			renderAt: 'chart-container',
			width: '500',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/
		$lineImg=[
			"chart"=> [
                "caption"=> "Top Employees",
                "subcaption"=> "Last six months",
                "xAxisName"=> "Month",
                "yAxisName"=> "Rating",
                "yaxisminvalue"=> "0",
                "yaxismaxvalue"=> "10",
                "yAxisValuesPadding"=> "15",
                "valuePosition" => "below",
                "numDivlines"=> "5",
                "lineAlpha"=> "1",
                "anchorAlpha"=> "100",
                //Theme
               // "theme"=>"fint",
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
				"anchorradius"=> "0",
				"plotHighlightEffect"=> "fadeout|color=#f6f5fd, alpha=60",
				"showValues"=> "1",
				"rotateValues"=> "0",
				"placeValuesInside"=> "10",
				"formatNumberScale"=> "0",
				"decimalSeparator"=> ",",
				"thousandSeparator"=> ".",
				"numberPrefix"=> "",
				"ValuePadding"=> "0",				
            ],
            "data"=> [
                [
                    "label"=> "July",
                    "value"=> "7.8",
                    "displayValue" =>"John, 7.8",
                    "tooltext" =>"July => John, 7.8",
                    "anchorImageUrl"=>"https://image.kontrolgampang.com/user/1.png"
                    
                ], 
                [
                    "label"=> "August",
                    "value"=> "6.9",
                    "displayValue" =>"Mac, 6.9",
                    "tooltext" =>"August => Mac, 6.9",
                    "anchorImageUrl"=>"https://image.kontrolgampang.com/user/2.png"
                ], 
                [
                    "label"=> "September",
                    "value"=> "8",
                    "displayValue" =>"Phillips, 8",
                    "tooltext" =>"September => Phillips, 8",
                    "anchorImageUrl"=>"https://image.kontrolgampang.com/user/3.png"
                ], 
                [
                    "label"=> "October",
                    "value"=> "7.5",
                    "displayValue" =>"Terrin, 7.5",
                    "tooltext" =>"October => Terrin, 7.5",
                    "anchorImageUrl"=>"https://image.kontrolgampang.com/user/4.png"
                ], 
                [
                    "label"=> "November",
                    "value"=> "7.7",
                    "displayValue" =>"Tom, 7.7",
                    "tooltext" =>"November => Tom, 7.7",
                    "anchorImageUrl"=>"https://image.kontrolgampang.com/user/5.png"
                ], 
                [
                    "label"=> "December",
                    "value"=> "6.7",
                    "displayValue" =>"Martha, 6.7",
                    "tooltext" =>"December => Martha, 6.7",
                    "anchorImageUrl"=>"https://image.kontrolgampang.com/user/6.png"
                ]
            ]
		];
		
		return $lineImg;
	}
	
	
	public function actionMscombidy2d()
	{
		/* =========== LINE IMAGE CHART DEFINITION ======
			type: 'mscombidy2d',
			renderAt: 'chart-container',
			width: '550',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/
		$mscombidy2d=[
			"chart"=> [
                "caption"=> "Revenues and Profits",
                "subCaption"=> "For last year",
                "xAxisname"=> "Month",
                "pYAxisName"=> "Amount (In USD)",
                "sYAxisName"=> "Profit %",
                "numberPrefix"=> "$",
                "sNumberSuffix" => "%",
                "sYAxisMaxValue" => "50",
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
				"anchorradius"=> "0",
				"plotHighlightEffect"=> "fadeout|color=#f6f5fd, alpha=60",
				"showValues"=> "1",
				"rotateValues"=> "0",
				"placeValuesInside"=> "10",
				"formatNumberScale"=> "0",
				"decimalSeparator"=> ",",
				"thousandSeparator"=> ".",
				"numberPrefix"=> "",
				"ValuePadding"=> "0",	
            ],
            "categories"=> [
				[
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
            ]
                          ],
            "dataset"=> [
                [
                    "seriesName"=> "Revenues",
                    "data"=> [
                        [ "value" => "16000" ],
                        [ "value" => "20000" ],
                        [ "value" => "18000" ],
                        [ "value" => "19000" ],
                        [ "value" => "15000" ],
                        [ "value" => "21000" ],
                        [ "value" => "16000" ],
                        [ "value" => "20000" ],
                        [ "value" => "17000" ],
                        [ "value" => "22000" ],
                        [ "value" => "19000" ],
                        [ "value" => "23000" ]
                    ]
                ], 
                [
                    "seriesName"=> "Profits",
                    "renderAs"=> "area",
                    "showValues"=> "0",
                    "data"=> [
                        [ "value" => "4000" ],
                        [ "value" => "5000" ],
                        [ "value" => "3000" ],
                        [ "value" => "4000" ],
                        [ "value" => "1000" ],
                        [ "value" => "7000" ],
                        [ "value" => "1000" ],
                        [ "value" => "4000" ],
                        [ "value" => "1000" ],
                        [ "value" => "8000" ],
                        [ "value" => "2000" ],
                        [ "value" => "7000" ]
                    ]
                ], 
                [
                    "seriesName"=> "Profit %age",
                    "parentYAxis"=> "S",
                    "renderAs"=> "line",
                    "showValues"=> "0",
                    "data"=> [
                        [ "value" => "25" ],
                        [ "value" => "25" ],
                        [ "value" => "16.66" ],
                        [ "value" => "21.05" ],
                        [ "value" => "6.66" ],
                        [ "value" => "33.33" ],
                        [ "value" => "6.25" ],
                        [ "value" => "25" ],
                        [ "value" => "5.88" ],
                        [ "value" => "36.36" ],
                        [ "value" => "10.52" ],
                        [ "value" => "30.43"]
                    ]
                ]
            ]
		];
		
		return $mscombidy2d;
	}
	
	public function actionPie3d()
	{
		/* =========== PIE CHART DEFINITION ======
			type: 'pie3d',
			renderAt: 'chart-container',
			width: '450',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/
		$pie3d=[
			"chart"=>[
                "caption"=>"Non Tunai Transaksi Bank",
                //"subCaption"=>"Last Year",
                "startingAngle"=>"120",
                "showLabels"=>"1",
                "enableMultiSlicing"=>"0",
                "slicingDistance"=>"15",
                //To show the values in percentage
                "showPercentValues"=>"1",
                "showPercentInTooltip"=>"0",
                "plotTooltext"=>'Age group =>$label<br>Total visit =>$datavalue',
                "theme"=>"fint",
				"numberPrefix"=> "Rp ",
				"formatNumberScale"=> "0",
				"decimalSeparator"=> ",",
				"thousandSeparator"=> ".",
				//=== LEGEND ===
				"showLegend"=> "1",
				"legendShadow"=> "1",
				"legendBorderAlpha"=> "1"
            ],
            "data"=>[
				[
					"label"=>"BCA",
					"value"=>"1250400"
				], 
				[
					"label"=>"MANDIRI",
					"value"=>"1463300"
				],
				[
					"label"=>"PERMATA",
					"value"=>"1050700"
				],
				[
					"label"=>"BNI",
					"value"=>"491000"
				],
				[
					"label"=>"BRI",
					"value"=>"491000"
				]
			]		
		];		
		
		return $pie3d;	
	}
	
	public function actionDoughnut3d()
	{
		/* =========== DONAT CHART DEFINITION ======
			type: 'doughnut3d',
			renderAt: 'chart-container',
			width: '450',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/
		$doughnut3d=[
			 "chart"=> [
                "caption"=> "Split of Revenue by Product Categories",
                "subCaption"=> "Last year",
                "numberPrefix"=> "$",
                "paletteColors"=> "#0075c2,#1aaf5d,#f2c500,#f45b00,#8e0000",
                "bgColor"=> "#ffffff",
                "showBorder"=> "0",
                "use3DLighting"=> "0",
                "showShadow"=> "0",
                "enableSmartLabels"=> "0",
                "startingAngle"=> "310",
                "showLabels"=> "0",
                "showPercentValues"=> "1",
                "showLegend"=> "1",
                "legendShadow"=> "0",
                "legendBorderAlpha"=> "0",                                
                "decimals"=> "0",
                "captionFontSize"=> "14",
                "subcaptionFontSize"=> "14",
                "subcaptionFontBold"=> "0",
                "toolTipColor"=> "#ffffff",
                "toolTipBorderThickness"=> "0",
                "toolTipBgColor"=> "#000000",
                "toolTipBgAlpha"=> "80",
                "toolTipBorderRadius"=> "2",
                "toolTipPadding"=> "5",
            ],
            "data"=> [
                [
                    "label"=> "Food",
                    "value"=> "28504"
                ], 
                [
                    "label"=> "Apparels",
                    "value"=> "14633"
                ], 
                [
                    "label"=> "Electronics",
                    "value"=> "10507"
                ], 
                [
                    "label"=> "Household",
                    "value"=> "4910"
                ]
            ]	
		];		
		
		return $doughnut3d;	
	}
	
	public function actionStackedbar2d()
	{
		/* =========== DONAT CHART DEFINITION ======
			type: 'stackedbar2d',
			renderAt: 'chart-container',
			width: '450',
			height: '300',
			dataFormat: 'json',
			dataSource: {}
		*/
		$stackedbar2d=[
		 "chart"=> [
                "caption"=> "Product-wise quarterly revenue in current year",
                "subCaption"=> "Harry's SuperMart",
                "xAxisname"=> "Quarter",
                "yAxisName"=> "Revenue (In USD)",
                "numberPrefix"=> "$",
                "paletteColors"=> "#0075c2,#1aaf5d",
                "bgColor"=> "#ffffff",
                "borderAlpha"=> "20",
                "showCanvasBorder"=> "0",
                "usePlotGradientColor"=> "0",
                "plotBorderAlpha"=> "10",
                "legendBorderAlpha"=> "0",
                "legendShadow"=> "0",
                "valueFontColor"=> "#ffffff",                
                "showXAxisLine"=> "1",
                "xAxisLineColor"=> "#999999",
                "divlineColor"=> "#999999",               
                "divLineIsDashed"=> "1",
                "showAlternateVGridColor"=> "0",
                "subcaptionFontBold"=> "0",
                "subcaptionFontSize"=> "14",
                "showHoverEffect"=>"0",
				"showAxisLines"=> "0",	                
            ],
            "categories"=> [
                [
                    "category"=> [
                        [
                            "label"=> "Q1"
                        ],
                        [
                            "label"=> "Q2"
                        ],
                        [
                            "label"=> "Q3"
                        ],
                        [
                            "label"=> "Q4"
                        ]
                    ]
                ]
            ],
            "dataset"=> [
                [
                    "seriesname"=> "Food Products",
                    "data"=> [
                        [
                            "value"=> "121000"
                        ],
                        [
                            "value"=> "135000"
                        ],
                        [
                            "value"=> "123500"
                        ],
                        [
                            "value"=> "145000"
                        ]
                    ]
                ],
                [
                    "seriesname"=> "Non-Food Products",
                    "data"=> [
                        [
                            "value"=> "131400"
                        ],
                        [
                            "value"=> "154800"
                        ],
                        [
                            "value"=> "98300"
                        ],
                        [
                            "value"=> "131800"
                        ]
                    ]
                ],
                [
                    "seriesname"=> "Non-Food Products1",
                    "data"=> [
                        [
                            "value"=> "131400"
                        ],
                        [
                            "value"=> "154800"
                        ],
                        [
                            "value"=> "98300"
                        ],
                        [
                            "value"=> "131800"
                        ]
                    ]
                ]
            ]
		];
		return $stackedbar2d;
	}
}
    
	
	
	
	
