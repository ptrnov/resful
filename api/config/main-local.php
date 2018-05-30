<?php
$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'dWut4SrmYAaXg0NfqpPwnJa23RMIUG7j_kgapi',
        ],
    ],
];

#if (!YII_ENV_TEST) {
#    // configuration adjustments for 'dev' environment
#    $config['bootstrap'][] = 'debug';
#    $config['modules']['debug'] = 'yii\debug\Module';

#    $config['bootstrap'][] = 'gii';
#    $config['modules']['gii'] = 'yii\gii\Module';
#}
if (!YII_ENV_TEST) {
   //configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

     $config['bootstrap'][] = 'gii';
    $config['modules']['gii']=[
                                'class' =>  'yii\gii\Module',
                                'allowedIPs' => ['*'],
                              ];
 }  

return $config;
