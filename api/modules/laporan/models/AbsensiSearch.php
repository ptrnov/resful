<?php

namespace api\modules\laporan\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\data\ArrayDataProvider;
use yii\debug\components\search\Filter;
use yii\debug\components\search\matchers;

class AbsensiSearch extends \yii\base\DynamicModel
{
	public $TAHUN;
	public $BULAN;
	public $ACCESS_GROUP;
	public $STORE_ID;
	public $KARYAWAN_ID;
	public $KARYAWAN;
	public $TGL;
	public $MASUK;
	public $KELUAR;
	public $LONGITUDE;
	public $LATITUDE;
	
	public function rules()
    {
        return [
            [['TAHUN', 'BULAN', 'ACCESS_GROUP','STORE_ID','KARYAWAN_ID','KARYAWAN','TGL','MASUK','KELUAR','LONGITUDE','LATITUDE'], 'safe'],
		];	

    }
	
	/* public function fields()
	{
		return [		
			'PER_TYPE_PEMBAYARAN_JUMLAH_TRANSAKSI'=>function(){
				//return $this->jumlahTransaksi();
				return Yii::$app->rpt->dailyPerStoreTypePembayaranJumlah($this->storeId,$this->transDate1);
				// return [];
			},
			'PER_TYPE_PEMBAYARAN_TOTAL_RUPAH'=>function(){
				//return $this->totalRupiah();
				return Yii::$app->rpt->dailyPerStoreTypePembayaranRupiah($this->storeId,$this->transDate1);
			},
			'PRODUCT_TERJUAL_TOP5_QTY'=>function(){
				return Yii::$app->rpt->dailyPerStoreProductTerjualQtyTop5($this->storeId,$this->transDate1);
			},
			'PRODUCT_TERJUAL_TOP5_RUPIAH'=>function(){
				return Yii::$app->rpt->dailyPerStoreProductTerjualRupiahTop5($this->storeId,$this->transDate1);
			},
			'PRODUCT_TERJUAL_QTY'=>function(){
				return Yii::$app->rpt->dailyPerStoreProductTerjualQty($this->storeId,$this->transDate1);
			},
			'PRODUCT_TERJUAL_RUPIAH'=>function(){
				return Yii::$app->rpt->dailyPerStoreProductTerjualRupiah($this->storeId,$this->transDate1);
			}
		];
	} */
	
	//WHERE a.MONTH_AT='".date("Y-m-d", strtotime($this->BULAN))."' AND a.ACCESS_GROUP='".$this->ACCESS_GROUP."' AND a.STORE_ID='".$this->STORE_ID."'
	public function search($params){
		$sql="
			SELECT 
				a.YEAR_AT AS TAHUN,a.MONTH_AT AS BULAN,a.ACCESS_GROUP,a.STORE_ID,a.KARYAWAN_ID,
				CONCAT(k.NAMA_DPN,' ',k.NAMA_TGH,' ',k.NAMA_BLK) AS KARYAWAN, a.TGL, 
				max(CASE WHEN a.STATUS=0 THEN a.WAKTU END) AS MASUK,
				max(CASE WHEN a.STATUS=1 THEN a.WAKTU END) AS KELUAR,
				a.LONGITUDE,a.LATITUDE
			FROM hrd_absen a LEFT JOIN karyawan k on k.KARYAWAN_ID=a.KARYAWAN_ID			
			GROUP BY a.ACCESS_GROUP,a.STORE_ID,a.KARYAWAN_ID,a.TGL
			ORDER BY a.KARYAWAN_ID,a.TGL		
		";		
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		
		if (!($this->load($params) && $this->validate())) {
 			return $dataProvider;
 		}
		
		// print_r($dataProvider);
		// die(); 
		
		$filter = new Filter();
 		$this->addCondition($filter, 'TAHUN', true);
 		$this->addCondition($filter, 'BULAN', true);	
 		$this->addCondition($filter, 'ACCESS_GROUP', true);	
		$this->addCondition($filter, 'STORE_ID', true);	
		$this->addCondition($filter, 'KARYAWAN_ID', true);	
		$this->addCondition($filter, 'KARYAWAN', true);	
		$this->addCondition($filter, 'TGL', true);	
		$this->addCondition($filter, 'MASUK', true);	
		$this->addCondition($filter, 'KELUAR', true);	
		$this->addCondition($filter, 'LONGITUDE', true);	
		$this->addCondition($filter, 'LATITUDE', true);	
 		$dataProvider->allModels = $filter->filter($qrySql);
        return $dataProvider;//->getModels()[0];
	} 
	
	public function addCondition(Filter $filter, $attribute, $partial = false)
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
