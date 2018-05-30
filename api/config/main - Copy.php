<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),    
    'bootstrap' => ['log'],
    'modules' => [
		'admin' => [
				'class' => 'mdm\admin\Module',
				'controllerMap' => [
					 'assignment' => [
						'class' => 'mdm\admin\controllers\AssignmentController',
						'userClassName' => 'common\models\User',
						'idField' => 'id', //user_id id field of model User
					]
				],  
				/*'layout'=>'mdm\admin\views\layouts\top-menu',*/
				//'layout'=>'left-menu',
				'menus' =>[
					'assignment'=>[
						'label'=>'Grand Access'
					],
					/*'route'=> null,*/
				],
		],		
		'contoh' =>[
            'class'=>'api\modules\contoh\Module',
        ],
		'apisistem' =>[
            'class'=>'api\modules\sistem\Modul',
        ],
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'
        ],		
		'login' => [
            'basePath' => '@app/modules/login',
            'class' => 'api\modules\login\Module',
        ],	
		'esm' => [
            'basePath' => '@app/modules/esm',
            'class' => 'api\modules\esm\Module',
        ],
		'gsn' => [
            'basePath' => '@app/modules/gsn',
            'class' => 'api\modules\gsn\Module',
        ],
		'master' => [
            'basePath' => '@app/modules/master',
            'class' => 'api\modules\master\Module',
        ],
		'notify' => [
            'basePath' => '@app/modules/notify',
            'class' => 'api\modules\notify\Module',
        ],
		'sss' => [
            'basePath' => '@app/modules/sss',
            'class' => 'api\modules\sss\Module',
        ],
		/*Author -ptr.nov- Ready Use*/
		'chart' => [
            'basePath' => '@app/modules/chart',
            'class' => 'api\modules\chart\Module',
        ]			
    ],
    'components' => [        
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
			'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'ambilkonci' =>[
            'class'=>'common\components\AmbilkeyComponent',
        ],
        'ambilStatus' =>[
            'class'=>'common\components\Static_sttComponent',
        ],
		'ambilKonvesi' =>[
            'class'=>'common\components\TgljamconvertComponent',
        ],  
		'getUserOpt' =>[
            'class'=>'api\components\Useroption',
        ],
		/*input Json -ptr.nov- */
		'request' => [
			'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			]
		],	
		/*
		'errorHandler' => [
			'errorAction' => ''v1/country',
		],
		*/
        'urlManager' => [
           'enablePrettyUrl' => true,
           'enableStrictParsing' => true,
            'showScriptName' => false,
			 'rules' => [
                 //'<controller:\w+>/<id:\d+>' => '<controller>/view',
                 //'<controller:\w+>/<action:\w+>/<id:\w+>' => '<controller>/<action>',
                 //'<controller:\w+>/<action:\w+>' => '<controller>/<action>',

					[
						'class' => 'yii\rest\UrlRule',  
						'controller' =>['sss/chart', 'sss/charts/view'],
						//'except' => ['view', 'create', 'update'],						
					],
				   
				   [
					'class' => 'yii\rest\UrlRule',  
                    'controller' =>[   //ptr,.nov penting buat API
							'site/error',
							'contoh/chart-bar2d',
							'login/user',
							'login/password',
							'login/signature',
							'login/profile',							
							'chart/pilotp',
							'chart/hrm-personalia',
							'chart/esm-warehouse',
							'chart/esm-sales',		
							'v1/country',
							'esm/country',
							'gsn/provinsi','gsn/kota',
							'master/barangumum','master/kategori','master/suplier','master/tipebarang','master/unitbarang',
						    'master/perusahaan',
                            'master/provinsi',
                            'master/distributor',
                            'master/employee',                            
							'master/inventories/view',							
							'notify/hrd_persona',//'notify/hrd_persona/view','notify/hrd_persona/delete',
							'notify/gps_customer',
							'notify/gps_customer/create',
							//'sss/chart',
					],
					[
						'class' => 'yii\rest\UrlRule',
						'controller' => ['master/inventory' => 'master/inventory'],
						'patterns' => [
							'PUT' => 'update','GET' => 'index'
						]
					] 
					//'extraPatterns' => [
                     //  'GET {key}' => 'viewkey',
                    //    'GET {token}' => 'tokens',

                        //'GET countryview/<id:\w+>' => 'countryview'
                    //'GET Countryview/<id:\w+>' => '<id:\\w+>',
                    //],
					//'extraPatterns' => ['GET country' => 'country'],
					//'extraPatterns' => ['GET dast' => 'fast'],

                   'tokens' => [
                     '{id}' => '<id:\\w+>',
                        //'{id}' => '<id:\\w[\\w,]*>',
                       //   '{key}' => '<key:\\w+>',
                   //       '{token}' => '<token:\\w+>',
                       //'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                        //'{gameid}' => '<gameid:\\d+>',
                   ],
                ]
            ],        
        ],
		/* Author -ptr.nov- : Test Project  */
        'db_rpt' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=202.53.254.83;dbname=dbc000',
            'username' => 'lgoffice321',
            'password' =>'r4h4514',
            'charset' => 'utf8',
        ], 	
		/* Author -ptr.nov- : Test Project  */
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=202.53.254.83;dbname=dbm001',
			//'dsn' => 'mysql:host=10.10.99.2;dbname=db_test',
            'username' => 'lgoffice321',
            'password' =>'r4h4514',
            'charset' => 'utf8',
        ], 	
		'db_user' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=202.53.254.83;dbname=dbm001',
            'username' => 'lgoffice321',
            'password' =>'r4h4514',
            'charset' => 'utf8',
        ],			
		/* Author -ptr.nov- : Admin Menu  */
        'db1' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=202.53.254.83;dbname=dbm001',
            'username' => 'lgoffice321',
            'password' =>'r4h4514',
            'charset' => 'utf8',
        ],
		
		 /* Author -ptr.nov- : HRD | Dashboard I */
        /* Author -ptr.nov- : HRD | Dashboard I */
        'db_hrm' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=202.53.254.83;dbname=dbm002',
            'username' => 'lgoffice321',
            'password' =>'r4h4514',
            'charset' => 'utf8',
        ],

		 /* Author -ptr.nov- : ESM DB_project*/ 
		 
        'db3' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=202.53.254.83;dbname=dbc002',
            'username' => 'lgoffice321',
            'password' =>'r4h4514',
            //'dsn' => 'oci:dbname=//10.10.99.3:1521/gosent',
            //'username' => 'gosent',
            //'password' => 'asd123',
            'charset' => 'utf8',
        ],		
        'db4' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=202.53.254.83;dbname=dbm000',
                'username' => 'lgoffice321',
                'password' =>'r4h4514',
                'charset' => 'utf8',
        ],

        /* Author -ptr.nov- : IT | Dashboard I */
        'db_widget' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=202.53.254.83;dbname=dbm005',
            'username' => 'lgoffice321',
            'password' =>'r4h4514',
            'charset' => 'utf8',
        ],
		
		'db_sss' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=202.53.254.83;dbname=dbc001',
                'username' => 'lgoffice321',
                'password' =>'r4h4514',
                'charset' => 'utf8',
        ],
        'db_esm' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=202.53.254.83;dbname=dbc002',
                'username' => 'lgoffice321',
                'password' =>'r4h4514',
                'charset' => 'utf8',
        ],
				
		'db_lipat' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=202.53.254.83;dbname=dbc003',
                'username' => 'lgoffice321',
                'password' =>'r4h4514',
                'charset' => 'utf8',
        ],

		'db_gsn' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=202.53.254.83;dbname=dbc004',
                'username' => 'lgoffice321',
                'password' =>'r4h4514',
                'charset' => 'utf8',
        ],		
		'db_test' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=202.53.254.83;dbname=db_test',
                'username' => 'lgoffice321',
                'password' =>'r4h4514',
                'charset' => 'utf8',
        ],
		
		/*-ptr.nov- : Public Permission */
        'as access'=>[
            'class'=>'mdm\admin\components\AccessControl',
            'allowActions'=>[
                '*',
                //'site/login',
                //'site/error',
            ]
        ], 
		'errorHandler' => [
            'maxSourceLines' => 20,
        ],
		/**
		 * Handle Ajax content parsing & _CSRF
		 * @author ptrnov  <piter@lukison.com>
		 * @since 1.1   	
		 */
		'request' => [
            'cookieValidationKey' => 'dWut4SrmYAaXg0NfqpPwnJa23RMIUG7j_it',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser', // required for POST input via `php://input`
            ]
        ],
    ],
    'params' => $params,
];



