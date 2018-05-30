<?php
$params = array_merge(
    require(__DIR__ . '/../config/params.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
		'login' => [
            'basePath' => '@app/modules/login',
            'class' => 'api\modules\login\Module',
        ],
		'master' => [
            'basePath' => '@app/modules/master',
            'class' => 'api\modules\master\Module',
        ],
		'transaksi' => [
            'basePath' => '@app/modules/transaksi',
            'class' => 'api\modules\transaksi\Module',
        ],
		'hirs' => [
            'basePath' => '@app/modules/hirs',
            'class' => 'api\modules\hirs\Module',
        ],
		'laporan' => [
            'basePath' => '@app/modules/laporan',
            'class' => 'api\modules\laporan\Module',
        ],
		'ppob' => [
            'basePath' => '@app/modules/ppob',
            'class' => 'api\modules\ppob\Module',
        ],
		'pembayaran' => [
            'basePath' => '@app/modules/pembayaran',
            'class' => 'api\modules\pembayaran\Module',
        ],
		'VirtualAccounth2h' => [
            'basePath' => '@app/modules/VirtualAccounth2h',
            'class' => 'api\modules\VirtualAccounth2h\Module',
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'api\modules\login\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
			'loginUrl' => null,
        ],
		// 'formatter' => [
           // 'dateFormat' => 'd-M-Y',
           // 'datetimeFormat' => 'd-M-Y H:i:s',
           // 'timeFormat' => 'H:i:s',
		 // ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
		'getPolling' =>[
            'class'=>'api\components\Polling',
        ],
		'statusCode' =>[
            'class'=>'common\components\StatusCode',
        ],
		'rpt' => [
            'class' =>'common\components\Reporting'
        ],
		'arrayBantuan' =>[
            'class'=>'api\components\ArrayBantuan',
        ],
		'apippob' =>[
            'class'=>'api\components\PpobH2h',
        ],
		
        // 'errorHandler' => [
            // 'errorAction' => 'site/error',
        // ],
		
		/*input Json -ptr.nov- */
		// 'request' => [
			// 'class' => '\yii\web\Request',
            // 'enableCookieValidation' => false,
			// 'parsers' => [
				// 'application/json' => 'yii\web\JsonParser',
			// ]
		// ],
		/*
		'errorHandler' => [
			'errorAction' => ''v1/country',
		],
		*/
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
				[
					'class' => 'yii\rest\UrlRule',
					'controller' =>[						
						'login/user-signup-owner', 		//Signup OWNER=(Atuh Login & Manual Login & Aktivasi Manual Login)
						'login/user-signup-opr',		//Signup OPERATIONAL
						'login/user-reset',
						'login/user-login',
						'login/user-login-test',		//sementara editor
						'login/user-link',
						'login/user-profile',
						'login/user-image',
						'login/user-operational',
						'login/user-permission',
						'login/user-modul'
					],						
					'patterns' => [
						'PUT,PATCH' => 'update',
						'DELETE' => 'delete',
						'GET,HEAD' => 'view',
						'POST' => 'create',
						'GET,HEAD' => 'index',
						'{id}' => 'options',
						'' => 'options',
					]
                ],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' =>[
						'master/kota',
						'master/provinsi',						
						'master/store',							
						'master/customer',							
						'master/merchant',							
						'master/merchant-type',							
						'master/merchant-type-test',							
						'master/merchant-bank',
						'master/product-test',						
						'master/product',						
						'master/product-group',							
						'master/product-unit',							
						'master/product-unit-group',							
						'master/product-industri',							
						'master/product-industri-group',							
						'master/product-stock',							
						'master/product-discount',							
						'master/product-harga',							
						'master/product-promo',							
						'master/product-image',							
						'master/polling',							
						'master/polling-test'							
					],
					'patterns' => [
						'PUT' => 'update',
						'DELETE' => 'delete',
						'GET,HEAD {id}' => 'view',
						'POST' => 'create',
						'GET,HEAD' => 'index',
						'{id}' => 'options',
						'' => 'options',
					],
					'extraPatterns' => [
							'POST uuid' => 'uuid',
						]
                ],
				[
                        'class' => 'yii\rest\UrlRule',
                        'controller' =>[
							'transaksi/trans-openclose',
							'transaksi/trans-storan',
							'transaksi/trans-penjualan-header',
							'transaksi/trans-penjualan-detail'
						],
						'patterns' => [
							'PUT,PATCH' => 'update',
							'DELETE {id}' => 'delete',
							'GET,HEAD {id}' => 'view',
							'POST' => 'create',
							'GET,HEAD' => 'index',
							'{id}' => 'options',
							'' => 'options',
						]
                ],
				[
                        'class' => 'yii\rest\UrlRule',
                        'controller' =>[
							'pembayaran/store-kasir',
							'pembayaran/store-invoice',
							'pembayaran/store-payment',
							'pembayaran/dompet',
						],
						'patterns' => [
							'PUT,PATCH' => 'update',
							'DELETE {id}' => 'delete',
							'GET,HEAD {id}' => 'view',
							'POST' => 'create',
							'GET,HEAD' => 'index',
							'{id}' => 'options',
							'' => 'options',
						],
						'extraPatterns' => [
							'POST list-paket' => 'list-paket',
							'POST list-payment-methode' => 'list-payment-methode',
							'POST list-auto-debet' => 'list-auto-debet',
							'POST payment-metode' => 'payment-metode',
							'POST list-uuid' => 'list-uuid',
							'POST ganti-perangkat' => 'ganti-perangkat',
							'POST setting-pembayaran' => 'setting-pembayaran',
							'POST tambah-perangkat' => 'tambah-perangkat',
							
						]
                ],
				[
                        'class' => 'yii\rest\UrlRule',
                        'controller' =>[
							'hirs/karyawan',
							'hirs/absensi'
						],
						'patterns' => [
							'PUT,PATCH' => 'update',
							'DELETE {id}' => 'delete',
							'GET,HEAD {id}' => 'view',
							'POST' => 'create',
							'GET,HEAD' => 'index',
							'{id}' => 'options',
							'' => 'options',
						]
                ],
				[
                        'class' => 'yii\rest\UrlRule',
                        'controller' =>[
							'laporan/contoh-chart',
							'laporan/rpt-penjualan',							
							'laporan/store-map',							
							'laporan/trans-rpt-trans',
							'laporan/trans-rpt1',
							'laporan/trans-rpt2',
							'laporan/trans-rpt-test',
							'laporan/trans-rpt-absensi',
							'laporan/counter',
							'laporan/sales-chart',
							'laporan/polling-chart',
							'laporan/produk-chart'
						],
						'patterns' => [
							'PUT,PATCH' => 'update',
							'DELETE {id}' => 'delete',
							'GET,HEAD {id}' => 'view',
							'POST' => 'create',
							'GET,HEAD' => 'index',
							'{id}' => 'options',
							'' => 'options',
						],
						'extraPatterns' => [
							'POST angulargauge' => 'angulargauge',
							'POST line' => 'line',
							'POST column2d' => 'column2d',
							'POST column2d-image' => 'column2d-image',
							'POST mscombidy2d' => 'mscombidy2d',
							'POST line-img' => 'line-img',
							'POST pie3d' => 'pie3d',
							'POST doughnut3d' => 'doughnut3d',
							'POST stackedbar2d' => 'stackedbar2d',
							'GET posisi-koordinat' => 'posisi-koordinat',
							'POST polling-group' => 'polling-group',
							'POST polling-perstore' => 'polling-perstore',
							'POST per-access-group' => 'per-access-group',
							'POST per-store' => 'per-store',		
							'POST frek-trans-day-group' => 'frek-trans-day-group',						
							'POST frek-trans-day-store' => 'frek-trans-day-store',						
							'POST sales-bulanan-group' => 'sales-bulanan-group',
							'POST sales-bulanan-perstore' => 'sales-bulanan-perstore',
							'POST sales-bulanan-produk-perstore' => 'sales-bulanan-produk-perstore',							
							'POST sales-bulanan-produkrefund-perstore' => 'sales-bulanan-produkrefund-perstore',							
							'POST sales-mingguan-group' => 'sales-mingguan-group',					
							'POST sales-mingguan-perstore' => 'sales-mingguan-perstore',					
							'POST sales-mingguan-produkrefund-perstore' => 'sales-mingguan-produkrefund-perstore',					
							'POST sales-mingguan-produk-perstore' => 'sales-mingguan-produk-perstore',					
							'POST produk-daily-transaksi' => 'produk-daily-transaksi',					
							'POST produk-daily-refund' => 'produk-daily-refund',	
							//=== PRODUK CHART ===
							'POST bulanan-top-produk' => 'bulanan-top-produk',	
							'POST mingguan-top-produk' => 'mingguan-top-produk',	
							'POST harian-top-produk' => 'harian-top-produk',
							'POST level-buffer-produk' => 'level-buffer-produk',
							//=== CHART SALES DETAIL ===
							'POST detail-sales-bulanan' => 'detail-sales-bulanan',
							'POST detail-sales-bulanan-tunai' => 'detail-sales-bulanan-tunai',
							'POST detail-sales-harian' => 'detail-sales-harian',
							'POST detail-sales-harian-tunai' => 'detail-sales-harian-tunai',
						]
                ],
				[
                        'class' => 'yii\rest\UrlRule',
                        'controller' =>[
							'ppob/sibisnis',
							'ppob/data',
							'ppob/data-test',
							'ppob/header',
							'ppob/detail',
							'ppob/nominal',
							'ppob/transaksi',
							'ppob/test-detail',
							'ppob/test-nominal',
							'ppob/test-api',
						],
							'patterns' => [
							'PUT,PATCH {ID}' => 'update',
							'DELETE {id}' => 'delete',
							'GET,HEAD {id}' => 'view',
							'POST' => 'create',
							'GET,HEAD' => 'index',
							'{id}' => 'options',
							'' => 'options',
						],
						'extraPatterns' => [
							'POST kelompok-group' => 'kelompok-group',
							'POST kelompok-kategori' => 'kelompok-kategori',
							'POST produk' => 'produk',
							'POST transaksi' => 'transaksi',
							'POST saldo' => 'saldo',
							'POST get-info-kelompok' => 'get-info-kelompok',
							'POST master-data' => 'master-data',
						]
                ],
				[
                        'class' => 'yii\rest\UrlRule',
                        'controller' =>[
							'VirtualAccounth2h/services',
						],
						'patterns' => [
							'PUT,PATCH {ID}' => 'update',
							'DELETE {id}' => 'delete',
							'GET,HEAD {id}' => 'view',
							'POST' => 'create',
							'GET,HEAD' => 'index',
							'{id}' => 'options',
							'' => 'options',
						],
						/* 'extraPatterns' => [
							//'POST get-bill' => 'get-bill',
							'POST GetBill' => 'GetBill',
						] */
                ],
            ],
        ],
		// 'db' => [
            // 'class' => 'yii\db\Connection',
            // 'dsn' => 'mysql:host=localhost;dbname=kasir',
            // 'username' => 'root',
            // 'password' => '',
            // 'charset' => 'utf8',
        // ],
		/*SERVER CACHED -ptr.nov-*/
		/* 'cache' => [
			'class' => 'yii\caching\MemCache',
			'servers' => [
				[
					'host' => 'localhost',
					'port' => 11211,
					'weight' => 100,
				],
				// [
					// 'host' => 'server2',
					// 'port' => 11211,
					// 'weight' => 50,
				// ],
			],
		], */
		// 'errorHandler' => [
            // 'maxSourceLines' => 20,
        // ],
		/**
		 * Handle Ajax content parsing & _CSRF
		 * @author ptrnov  <piter@lukison.com>
		 * @since 1.1
		 */
		'request' => [
			'enableCsrfValidation'=>false,
            'cookieValidationKey' => 'dWut4SrmYAaXg0NfqpPwnJa23RMIUG7j_kgapi',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser', // required for POST input via `php://input`
            ]
        ],
		'response' => [
			'format' => yii\web\Response::FORMAT_JSON,
			'charset' => 'UTF-8',
			//'class' => 'yii\web\Response',
            // 'on beforeSend' => function ($event) {
                // $response = $event->sender;
                // if ($response->data !== null && Yii::$app->request->get('suppress_response_code')) {
                    // $response->data = [
                        // 'success' => $response->isSuccessful,
                        // 'data' => $response->data,
                    // ];
                    // $response->statusCode = 200;
                // }
            // },
		]
    ],
     'params' => $params,
];



