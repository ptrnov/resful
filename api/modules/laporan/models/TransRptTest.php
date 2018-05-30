<?php

namespace api\modules\laporan\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\data\ArrayDataProvider;
use yii\debug\components\search\Filter;
use yii\debug\components\search\matchers;


class TransRptTest extends \yii\base\DynamicModel
{
	public function rules()
    {
        return [
            [['storeId', 'transDate1', 'transDate2'], 'safe'],
		];
    }
	
	public function fields()
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
	}
	
	public function jumlahTransaksi(){	
		if($this->qrySource()){		
			$i=0;
			foreach($this->qrySource() as $row =>$val){
				if($i<=2){
					$splitLabelQty=explode("_",$row);
					$jumlahTransaksiQty_label[]=$splitLabelQty[0];
					// $jumlahTransaksiLabel[]=$row;
					$jumlahTransaksiQty_value[]=(int)$val;
				}
				$i=$i+1;
			};
			$rslt["label"]=$jumlahTransaksiQty_label;
			$rslt["value"]=$jumlahTransaksiQty_value;
			return $rslt; 
		}else{
			$rslt["label"]=["TUNAI","EDC","CC"];
			$rslt["value"]=[0,0,0];
			return $rslt;
		}
	}
	
	private function totalRupiah(){		
		if($this->qrySource()){
			$i=0;
			foreach($this->qrySource() as $row =>$val){
				if($i>=3 AND $i<=5){
					$splitLabelHarga=explode("_",$row);
					$jumlahTransaksiHarga_label[]=$splitLabelHarga[0];
					$jumlahTransaksiHarga_value[]=(int)$val;
				}			
				$i=$i+1;
			};
			$rslt["label"]=$jumlahTransaksiHarga_label;
			$rslt["value"]=$jumlahTransaksiHarga_value;
			return $rslt; 
		}else{
			$rslt["label"]=["TUNAI","EDC","CC"];
			$rslt["value"]=[0,0,0];
			return $rslt;			
		}
	}
	
	private function productTerjual_top5Qty(){
		
			$rslt["label"]=["p1","p2","p3"];
			$rslt["value"]=[50,40,30];
			return $rslt;//ArrayHelper::toArray();
		
	}
	
	private function productTerjual_top5Rupiah(){
		$rslt["label"]=["p1","p2","p3"];
		$rslt["value"]=[30000,20000,10000];
		return $rslt;//ArrayHelper::toArray();
	}
	
	private function productTerjual_Qty(){
		
		if(count($this->qrySource2())){
			foreach($this->qrySource2() as $row){
				$rsltLbl[]=$row['PRODUCT_NM'];
				$rsltVal[]=$row['PRODUCT_QTY'];
			};						
			$rslt["label"]=$rsltLbl;
			$rslt["value"]=$rsltVal;
			return $rslt;
		}else{
			$rslt["label"]=["none"];
			$rslt["value"]=[0];
			return $rslt;
		}
	}
	
	private function productTerjual_Rupiah(){
		if(count($this->qrySource2())){
			foreach($this->qrySource2() as $row){
				$rsltLbl[]=$row['PRODUCT_NM'];
				$rsltVal[]=$row['SUB_HARGA_JUAL'];
			};						
			$rslt["label"]=$rsltLbl;
			$rslt["value"]=$rsltVal;
			return $rslt;
		}else{
			$rslt["label"]=["none"];
			$rslt["value"]=[0];
			return $rslt;
		}
	}
	
	private function qrySource(){
		$strId=$storeId;
	    $tgl1=date("Y-m-d", strtotime($this->transDate1));
	    $tgl2=date("Y-m-d", strtotime($this->transDate2!=''?$this->transDate2:$this->transDate1));
		$year1=date("Y", strtotime($this->transDate1));
		$year2=date("Y", strtotime($this->transDate2!=''?$this->transDate2:$this->transDate1));
		$bln1=date("m", strtotime($this->transDate1));
		$bln2=date("m", strtotime($this->transDate2!=''?$this->transDate2:$this->transDate1));
		$strId='170726220936.0001';
		// $tgl1='2017-09-29';
		// $tgl2='2017-09-29';
		// $year1='2017';
		// $year2='2017';
		// $bln1='09';
		// $bln2='09';
		$sql="
			SELECT 
				SUM(CASE WHEN a1.TYPE_PAY_ID=0 THEN a1.PRODUCT_QTY  ELSE 0 END) AS TUNAI_QTY,
				SUM(CASE WHEN a1.TYPE_PAY_ID=2 THEN a1.PRODUCT_QTY  ELSE 0 END) AS EDC_QTY,
				SUM(CASE WHEN a1.TYPE_PAY_ID=3 THEN a1.PRODUCT_QTY  ELSE 0 END) AS CC_QTY,
				SUM(CASE WHEN a1.TYPE_PAY_ID=0 THEN a1.SUB_HARGA_JUAL  ELSE 0 END) AS TUNAI_HARGA,
				SUM(CASE WHEN a1.TYPE_PAY_ID=2 THEN a1.SUB_HARGA_JUAL  ELSE 0 END) AS EDC_HARGA,
				SUM(CASE WHEN a1.TYPE_PAY_ID=3 THEN a1.SUB_HARGA_JUAL  ELSE 0 END) AS CC_HARGA
			FROM 
			( 
				SELECT 
					x2.TRANS_DATE,x2.TYPE_PAY_NM,x2.BANK_NM,x2.TYPE_PAY_ID,x1.PRODUCT_NM,x1.PRODUCT_QTY,x1.HARGA_JUAL,x1.DISCOUNT,(x1.PRODUCT_QTY * x1.HARGA_JUAL) as SUB_HARGA_JUAL
					,x1.PRODUCT_ID,x2.ACCESS_GROUP,x2.STORE_ID,x2.TRANS_ID,x2.OFLINE_ID,x2.OPENCLOSE_ID
				FROM trans_penjualan_detail  x1 
				LEFT JOIN trans_penjualan_header x2 
				ON x2.ACCESS_GROUP=x1.ACCESS_GROUP AND x2.STORE_ID=x1.STORE_ID AND x1.TRANS_ID=x2.TRANS_ID
				WHERE  ((x1.YEAR_AT BETWEEN '".$year1."' AND '".$year2."') AND (x1.MONTH_AT BETWEEN '".$bln1."' AND '".$bln2."') AND
					    (x2.YEAR_AT BETWEEN '".$year1."' AND '".$year2."') AND (x2.MONTH_AT BETWEEN '".$bln1."' AND '".$bln2."')
					   ) AND
					   x1.STORE_ID='".$strId."' AND      
					   (date(x1.TRANS_DATE) BETWEEN '".$tgl1."' AND '".$tgl2."')
				ORDER BY x1.TRANS_ID,x1.TRANS_DATE ASC
			) a1 GROUP BY date(a1.TRANS_DATE)
			
		";		
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);
		
		// if (!($this->load($params) && $this->validate())) {
 			// return $dataProvider;
 		// }
		
		// print_r($dataProvider);
		// die(); 
		
		// $filter = new Filter();
 		// $this->addCondition($filter, 'KAR_ID', true);
 		// $this->addCondition($filter, 'KAR_NM', true);	
 		// $this->addCondition($filter, 'DEP_NM', true);	
		// $this->addCondition($filter, 'STATUS', true);	
 		// $dataProvider->allModels = $filter->filter($qrySql);
		
         return $dataProvider->getModels()[0];
	}
	
	/* private function qrySource2(){		
		//$sql='call RPT1("'.$storeId.'","'.date("Y-m-d", strtotime($this->transDate1)).'")';
		//$sql="call RPT1('170726220936.0001','2017-09-29')";
		$sql="call DAILY_CONT_RPT_TOP5_QTY('170726220936.0001','2017-09-29')";
		$qrySql= Yii::$app->production_api->createCommand($sql)->queryAll(); 		
		$dataProvider= new ArrayDataProvider([	
			'allModels'=>$qrySql,	
			'pagination' => [
				'pageSize' =>1000,
			],			
		]);		
		return $dataProvider->getModels();
	} */
}
