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

class CounterGroup extends Model
{
	public $ACCESS_GROUP;
	public $STORE_ID;
	public $THN;
	
	public function rules()
    {
        return [
            [['ACCESS_GROUP','STORE_ID'], 'safe'],
		];	
    }

	public function fields()
	{
		return [
			'PER_ACCESS_GROUP'=>function($model){
				return $this->perAccessGroup();
			},
		];
	}
		
	public function perAccessGroup(){
		$sql="
			SELECT 
				ACCESS_GROUP,
				SUM(CNT_STORE) AS CNT_STORE,
				SUM(CNT_STORE_AKTIF) AS CNT_STORE_AKTIF,
				SUM(CNT_PERNGKAT) AS CNT_PERNGKAT,
				SUM(CNT_PERNGKAT_AKTIF) AS CNT_PERNGKAT_AKTIF,
				SUM(CNT_PRODUK) AS CNT_PRODUK,
				SUM(CNT_KARYAWAN) AS CNT_KARYAWAN,
				SUM(CNT_KARYAWAN_AKTIF) AS CNT_KARYAWAN_AKTIF,
				SUM(CNT_USER_OPS) AS CNT_USER_OPS,
				SUM(CNT_CUS_MEMBER) AS CNT_CUS_MEMBER, 
				SUM(CNT_JUMLAH_TRANSAKSI) AS CNT_JUMLAH_TRANSAKSI,
				SUM(CNT_PENJUALAN_HARIAN) AS CNT_PENJUALAN_HARIAN,
				SUM(CNT_PENJUALAN_MINGGUAN) AS CNT_PENJUALAN_MINGGUAN,
				SUM(CNT_PENJUALAN_BULANAN) AS CNT_PENJUALAN_BULANAN
			FROM ptr_store_count
			WHERE ACCESS_GROUP='".$this->ACCESS_GROUP."'
			GROUP BY ACCESS_GROUP; 		
		";		
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		$modelData=$dataProvider->getModels();
		if(!$modelData){
			$rslt= [
				['ACCESS_GROUP'=>'',
					'CNT_STORE'=>'0',
					'CNT_STORE_AKTIF'=>'0',
					'CNT_PERNGKAT'=>'0',
					'CNT_PERNGKAT_AKTIF'=>'0',
					'CNT_PRODUK'=>'0',
					'CNT_KARYAWAN'=>'0',
					'CNT_KARYAWAN_AKTIF'=>'0',
					'CNT_USER_OPS'=>'0',
					'CNT_CUS_MEMBER'=>'0', 
					'CNT_JUMLAH_TRANSAKSI'=>'0',
					'CNT_PENJUALAN_HARIAN'=>'0',
					'CNT_PENJUALAN_MINGGUAN'=>'0',
					'CNT_PENJUALAN_BULANAN'=>'0'
				]
			];			 
		}else{
			$rslt= $modelData;
		} 
       return $rslt;
	}
}
