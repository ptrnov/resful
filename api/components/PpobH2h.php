<?php
/**
 * Created by PhpStorm.
 * User: ptr.nov
 * Date: 10/08/15
 * Time: 19:44
 */

namespace api\components;
use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\Component;
use Yii\base\Model;
use yii\data\ArrayDataProvider;

//==PANGGIL==
// Yii::$app->apippob->function()

/** ================================================
 *  ============ H2H SIBISNIS FORMAT ===============
 * 	================================================
	 {
	   "apikey":"55c450f34a57c3160d5a8bf050f14068",
	   "page":"host2host-ppob",
	   "function":"get-info-kelompok",
	   "param":{
			"memberid":"ZON13121710",
			"tipe":"PRE"   
		}		
	 }
  * =================================================
  * Param load Path	: 	console\config\params-local
  * @author ptrnov  <piter@lukison.com>
  * @since 1.1 
*/	

class PpobH2h extends Component{	


	public function LabtestSibisnis($function='',$memberid='ZON13121710',$tipe='',$kategori_id='',$produk='',$id_pelanggan='',$reff_id='',$msisdn=''){
		
		if (strtoupper($function)==strtoupper("get-info-kelompok")){	
			$dataBody = [
			   "apikey"=>"55c450f34a57c3160d5a8bf050f14068",
			   "page"=>"host2host-ppob",
			   "function"=>"get-info-kelompok",
			   "param"=>[
					"memberid"=>"ZON13121710",
					"tipe"=>strtoupper($tipe)						//[POST/PRE]
				],	
			];
			
		}elseif(strtoupper($function)==strtoupper("get-info-produk")){	
			$dataBody = [
			   "apikey"=>"55c450f34a57c3160d5a8bf050f14068",
			   "page"=>"host2host-ppob",
			   "function"=>"get-info-produk",
			   "param"=>[
					"memberid"=>"ZON13121710",
					"kategori_id"=>$kategori_id						//ID Karegori Kelompok
				],	
			];		
		}elseif(strtoupper($function)==strtoupper("h2h-inquiry")){
			$dataBody = [
			   "apikey"=>"55c450f34a57c3160d5a8bf050f14068",
			   "page"=>"host2host-ppob",
			   "function"=>"h2h-inquiry",
			   "param"=>[
					"memberid"=>"ZON13121710",
					"produk"=>$produk,
					"id_pelanggan"=>$id_pelanggan						//ID Karegori Kelompok
				],	
			];				
		}elseif(strtoupper($function)==strtoupper("h2h-bayar")){
			$dataBody = [
			   "apikey"=>"55c450f34a57c3160d5a8bf050f14068",
			   "page"=>"host2host-ppob",
			   "function"=>"h2h-bayar",
			   "param"=>[
					"memberid"=>"ZON13121710",
					"produk"=>$produk,
					"reff_id"=>$reff_id,					//reff_id inquery
					"msisdn"=>$msisdn,						//no tlp/no pelanggan
					'id_pelanggan'=>$id_pelanggan,
				],	
			];			
		}			

		$client = new \GuzzleHttp\Client();		
		$res = $client->post('http://dev.api.aptmi.com', ['body' => json_encode($dataBody), 'future' => true]);	
		$dataBody='';		
		return json_decode($res->getBody());
		// return [json_decode(json_encode(json_decode($res->getBody())),true)];
	}
	
	/* =========================================================
	 * =================== ALL TYPE KELOMPOK ===================
	 * [1] TYPE
	 * [2] KELOMPOK
	 * [3] KATEGORY
	 * [4] COMBINASI DATA
	 * CLI		:	Yii::$app->ppobh2h->ArrayKelompokAllType();
	 * Authorby	: 	ptr.nov@gmail.com
	 * ========================================================= 
	*/	
	public function ArrayKelompokAllType(){
		//[1] TYPE	=====================	
		$valType = [
			['ID'=>1,'TYPE_CODE'=>'POST'],	//PASCABAYAR	=> PAKAI DULU KEMUDIAN BAYAR
			['ID'=>2,'TYPE_CODE'=>'PRE'],	//PRABAYAR		=> BAYAR DULU KEMUDIAN PAKAI
		];
		$aryType=ArrayHelper::map($valType,'ID','TYPE_CODE');
		//$modelType=PpobMasterType::find()->all();
		
		//=== LOOPING TYPE (POST/PRE) => GET KELOMPOK & KATEGORI KELOMPOK
		foreach($valType as $row1 => $value1){
			//$rslt[]=$value1['TYPE_CODE'];
			$client = new \GuzzleHttp\Client();
			$dataBody = [
				// "apikey"=>\Yii::$app->params['apikey'],
				// "page" =>\Yii::$app->params['page'],
				// "function" => \Yii::$app->params['infoKelompok'],
				// "param" => [
					// 'memberid'=>\Yii::$app->params['memberid'],
					// 'tipe'=>$value1['TYPE_CODE']
				// ],			
				
			   "apikey"=>"55c450f34a57c3160d5a8bf050f14068",
			   "page"=>"host2host-ppob",
			   "function"=>"get-info-kelompok",
			   "param"=>[
					"memberid"=>"ZON13121710",
					"tipe"=>"PRE"   
				],	
			];
			$res = $client->post('http://dev.api.aptmi.com', ['body' => json_encode($dataBody), 'future' => true]);
			//$res = $client->post(\Yii::$app->params['urlApi'], ['body' => json_encode($dataBody), 'future' => true]);
			// echo $res->getStatusCode();										// RESPONE URL STATUS	| FORMAT_JSON 
			// $rsltKelompok[]=json_decode($res->getStatusCode());				// RESPONE URL STATUS	| ARRAY OBJECT
			// echo $res->getBody();											// RESPONE URL BODY		| FORMAT_JSON 			
			//$stt=json_decode($res->getBody())->errcode;							// RESPONE DATA STATUS	| ARRAY OBJECT
			$data=json_decode(json_encode(json_decode($res->getBody())),true);	// RESPONE DATA BODY	| ARRAY FORMAT
			$allKelompok[]=$data;			
		}
		//=== VALIDASI MERGER DATA.
		// if($allKelompok[0]['errcode']<>'406' AND $allKelompok[1]['errcode']<>'406'){
			// $aryAllKelompok=array_merge($allKelompok[0]['data'],$allKelompok[1]['data']); 	// MERGE ARRAY [0]['data']=>PASCABAYAR,[1]['data']=PRABAYAR
		// }elseif($allKelompok[0]['errcode']!=406 AND $allKelompok[1]['errcode']==406){		
			// $aryAllKelompok=$allKelompok[0]['data'];										// NO MERGE ARRAY [0]['data']=>PASCABAYAR
		// }elseif($allKelompok[0]['errcode']==406 AND $allKelompok[1]['errcode']!=406){		
			// $aryAllKelompok=$allKelompok[1]['data'];										// NO MERGE ARRAY [1]['data']=PRABAYAR
		// }else{
			// $aryAllKelompok=['errcode'=>'406'];												// data field
		// };
		return $allKelompok;
	}		
		
	/* ==================================================
	 * =================== GET PTODUK ===================
	 * CLI		:	Yii::$app->ppobh2h->ArrayProduk($ktg);
	 * Authorby	: 	ptr.nov@gmail.com
	 * ==================================================
	*/	
	public function ArrayProduk($kategori_id)
    {
		$client = new \GuzzleHttp\Client();
		$dataBody = [
			"apikey"=>\Yii::$app->params['apikey'],
			"page" =>\Yii::$app->params['page'],
			"function" => \Yii::$app->params['infoProduk'],
			"param" => [
				'memberid'=>\Yii::$app->params['memberid'],
				'kategori_id'=>$kategori_id,
			],			
		];
		$res = $client->post(\Yii::$app->params['urlApi'], ['body' => json_encode($dataBody), 'future' => false]);
		// echo $res->getStatusCode();
		// echo $res->getBody();
		$data=json_decode(json_encode(json_decode($res->getBody())->data),true);	
		return $data;		
	}
	
	
	/* ==========================================================================
	 * ===============  H2H BAYAR ===============================================
	 * CLI		:	Yii::$app->ppobh2h->ArrayBayar($produkId,$msisdn,$reff_id);
	 * Authorby	: 	ptr.nov@gmail.com
	 * ==========================================================================
	*/	
	public function ArrayBayar($devplopment=true,$produkId ='',$msisdn='',$id_pelanggan='', $reff_id='')
    {
		$client = new \GuzzleHttp\Client();
		if ($reff_id<>''){																//JIKA PASCABAYAR		 
			$dataBody = [
				"apikey"=>\Yii::$app->params['apikey'],
				"page" =>\Yii::$app->params['page'],
				"function" => \Yii::$app->params['bayar'],
				"param" => [
					'memberid'=>\Yii::$app->params['memberid'],
					'produk'=>$produkId,
					'reff_id'=>$reff_id,
					'msisdn'=>$msisdn,
				],			
			];
		}else{																			//JIKA PRABAYAE		
			if($id_pelanggan<>''){														//JIKA MENGUNAKAN NO.PELANGGAN		
				$dataBody = [
					"apikey"=>\Yii::$app->params['apikey'],
					"page" =>\Yii::$app->params['page'],
					"function" => \Yii::$app->params['bayar'],
					"param" => [
						'memberid'=>\Yii::$app->params['memberid'],
						'produk'=>$produkId,
						'msisdn'=>$msisdn,
						'id_pelanggan'=>$id_pelanggan,
					],			
				];
			}else{
				$dataBody = [															//JIKA TIDAK MENGUNAKAN NO.PELANGGAN		
					"apikey"=>\Yii::$app->params['apikey'],
					"page" =>\Yii::$app->params['page'],
					"function" => \Yii::$app->params['bayar'],
					"param" => [
						'memberid'=>\Yii::$app->params['memberid'],
						'produk'=>$produkId,
						'msisdn'=>$msisdn,
					],			
				];
			}
			
		}
		
		if ($devplopment){
				/* ====================
				 * === DEVELOPMENT ====
				 * ====================
				*/
				if ($reff_id<>''){	
					/* ==========================
					 * === RESPON PASCABAYAR ====
					 * ==========================
					*/
					 $rslt['status']='success';
					 $rslt['errcode']='200';
					 $rslt['remarks']='development';
					 // $rslt['data']=$val;
					 //----------------------------------------
					 $rslt['tgl_lunas']='2018-01-14 03:52';					//$val['tgl_lunas'];
					 $rslt['no_reff']='073521V00KF5052824744O6B0RG94167';	//$val['no_reff'];
					 $rslt['id_pelanggan']='537316344073';					//$val['id_pelanggan'];
					 $rslt['thn_bln']='201608, 201609';						//$val['thn_bln'];
					 $rslt['nama']='Piter Novian';							//$val['nama'];
					 $rslt['stand_meter']='01685500-01754200';				//$val['stand_meter'];
					 $rslt['tarif_daya']='R1/000000900VA';					//$val['tarif_daya'];
					 $rslt['non_subsidi']='0';								//$val['non_subsidi'];
					 $rslt['rp_tag_pln']='383.795,00';						//$val['rp_tag_pln'];
					 $rslt['adm_bank']='3.200,00';							//$val['adm_bank'];
					 $rslt['total_bayar']='386.995,00';						//$val['total_bayar'];
					 $rslt['sn']='';										//isset($val['sn'])?$val['sn']:'';
					 $rslt['struk']='
					 
					 
					 ';														//$val['struk'];
				
				}else{
					//=== RESPON PRABAYAR ===
					 $rslt['status']='success';
					 $rslt['errcode']='200';
					 $rslt['remarks']='development';
					 //----------------------------------------
					 $rslt['kode_voucher']='212';
					 $rslt['operator']='KG PROVIDER';
					 $rslt['nominal']='10000';
					 $rslt['msisdn']='085883319929';
					 $rslt['message']='Berhasil TopUp Development';
					 $rslt['struk']=' STRUK PEMBELIAN VOUCHER PRABAYAR
						  |--------------------------------
						  |Tanggal   : 2018-01-09 22:34
						  |Nopel     : 12345678|Provider  : KG DEVELOPMENT PROVIDER DATA
						  |Nominal   : 1000|VSN       : 0427161252160154302
						  |Status    : BERHASIL
						  |
						  |--------- TERIMA KASIH ---------
					 ';
					 $rslt['id_pelanggan']='123456789';
					 $rslt['sn']='0427161252160154302';
				}
			
		}else{
				/* ====================
				 * === PRODUCTION ====
				 * ====================
				*/
				$res = $client->post(\Yii::$app->params['urlApi'], ['body' => json_encode($dataBody), 'future' => false]);
				// echo $res->getStatusCode();
				// echo $res->getBody();
				 $rslt='';
				 $status=json_decode(json_encode(json_decode($res->getBody())->status),true);
				 $errcode=json_decode(json_encode(json_decode($res->getBody())->errcode),true);
				 $remarks=json_decode(json_encode(json_decode($res->getBody())->remarks),true);
				 //$data=json_decode(json_encode(json_decode($res->getBody())->data),true);	
				 $data=json_decode(json_encode(json_decode($res->getBody())),true);	
				 if (strtoupper($status)<>'FAILED'){
					 if ($reff_id<>''){	
						/* ==========================
						 * === RESPON PASCABAYAR ====
						 * ==========================
						*/
						 foreach($data as $rows => $val){
							 $rslt['status']=$status;
							 $rslt['errcode']=$errcode;
							 $rslt['remarks']=$remarks;
							 $rslt['data']=$val;
							 //----------------------------------------
							 // $rslt['tgl_lunas']='2018-01-14 03:52';//$val['tgl_lunas'];
							 // $rslt['no_reff']='073521V00KF5052824744O6B0RG94167';//$val['no_reff'];
							 // $rslt['id_pelanggan']='537316344073';//$val['id_pelanggan'];
							 // $rslt['thn_bln']='201608, 201609';//$val['thn_bln'];
							 // $rslt['nama']='Rukanda';//$val['nama'];
							 // $rslt['stand_meter']='01685500-01754200';//$val['stand_meter'];
							 // $rslt['tarif_daya']='R1/000000900VA';//$val['tarif_daya'];
							 // $rslt['non_subsidi']='0';//$val['non_subsidi'];
							 // $rslt['rp_tag_pln']='383.795,00';//$val['rp_tag_pln'];
							 // $rslt['adm_bank']='3.200,00';//$val['adm_bank'];
							 // $rslt['total_bayar']='386.995,00';//$val['total_bayar'];
							 // $rslt['sn']='';//isset($val['sn'])?$val['sn']:'';
							 // $rslt['struk']='';//$val['struk'];
						 }
					 }else{	
						/* ==========================
						 * ===  RESPON PRABAYAR  ====
						 * ==========================
						*/
						foreach($data as $rows => $val){
							 $rslt['status']=$status;
							 $rslt['errcode']=$errcode;
							 $rslt['remarks']=$remarks;
							 //----------------------------------------
							 $rslt['kode_voucher']=$val['kode_voucher'];
							 $rslt['operator']=$val['operator'];
							 $rslt['nominal']=$val['nominal'];
							 $rslt['msisdn']=$val['msisdn'];
							 $rslt['message']=$val['message'];
							 $rslt['struk']=$val['struk'];
							 $rslt['id_pelanggan']=isset($val['id_pelanggan'])?$val['id_pelanggan']:'';			//field kosong dari sibisnis
							 $rslt['sn']=isset($val['sn'])?$val['sn']:'';
						}
					 }
				 }else{
					 $rslt['status']=$status;
					 $rslt['errcode']=$errcode;
					 $rslt['remarks']=$remarks;
				 }
		}	 
		return $rslt;	
		// return json_decode($res->getBody());	
		
		/* ==================================== RESPON PASCABAYAR ===========================================
		(
			[struk] => <h3 style="margin:0;font-size:16px;font-weight:bold;">STRUK PEMBAYARAN TAGIHAN LISTRIK</h3>
						Tgl Lunas    : 2018-01-09 23:38         No. Reff     : 073521V00KF5052824744O6B0RG94167
						ID Pelanggan : 537316344073             Tahun/Bulan  : 201608, 201609
						Nama         : Rukanda                  Stand Meter  : 01685500-01754200
						Tarif/Daya   : R1/000000900VA           Non Subsidi  : 0
						RP TAG PLN   : Rp.  383.795,00          Admin Bank   : Rp.    3.200,00
						Total Bayar  : Rp.  386.995,00

						<i>PLN menyatakan struk ini sebagai bukti pembayaran yang sah, Mohon disimpan</i>
						  Rincian tagihan dapat diakses di http://www.pln.co.id/
						  informasi hub Call Center : 123 atau hub PLN terdekat
									Terima kasih atas kepercayaan Anda
			[struk_json] => Array
				(
					[header] => Array
						(
							[0] => STRUK PEMBAYARAN TAGIHAN LISTRIK
						)

					[content] => Array
						(
							[tgl_lunas] => 2018-01-09 23:38
							[no_reff] => 073521V00KF5052824744O6B0RG94167
							[id_pelanggan] => 537316344073
							[thn_bln] => 201608, 201609
							[nama] => Rukanda
							[stand_meter] => 01685500-01754200
							[tarif_daya] => R1/000000900VA
							[non_subsidi] => 0
							[rp_tag_pln] =>  383.795,00
							[adm_bank] =>    3.200,00
							[total_bayar] =>  386.995,00
						)

					[footer] => Array
						(
							[0] => PLN menyatakan struk ini sebagai bukti pembayaran yang sah, Mohon disimpan
							[1] => Rincian tagihan dapat diakses di http://www.pln.co.id/
							[2] => informasi hub Call Center : 123 atau hub PLN terdekat
							[3] => Terima kasih atas kepercayaan Anda
						)

				)

		) 
		
		========================================== RESPON PRABAYAR =============================================
			(
				[kode_voucher] => AXBR1
				[potong_saldo] => 16000
				[operator] => AXIS DATA
				[nominal] => 1000
				[msisdn] => 12345678
				[message] => Pembelian 500MB 00-06 + 500MB 00-23.59 Aktif 30 hari 12345678 BERHASIL!
				[struk] => STRUK PEMBELIAN VOUCHER PRABAYAR
						  |--------------------------------
						  |Tanggal   : 2018-01-09 22:34
						  |Nopel     : 12345678|Provider  : AXIS DATA
						  |Nominal   : 1000|VSN       : 0427161252160154302
						  |Status    : BERHASIL
						  |
						  |--------- TERIMA KASIH ---------
				[sn] => 0427161252160154302
			) 
		 * ======================================================================================================
		*/
	}
	
	
	
	/* ==========================================================================
	 * ===============  H2H INQUIRY ===============================================
	 * CLI		:	Yii::$app->ppobh2h->ArrayInquery($produkId,$idPelanggan);
	 * Authorby	: 	ptr.nov@gmail.com
	 * ==========================================================================
	*/	
	public function ArrayInquery($devplopment=true,$produkId ='',$idPelanggan='')
    {
		$client = new \GuzzleHttp\Client();
		$dataBody = [
			"apikey"=>\Yii::$app->params['apikey'],
			"page" =>\Yii::$app->params['page'],
			"function" => \Yii::$app->params['inquery'],
			"param" => [
				'memberid'=>\Yii::$app->params['memberid'],
				'produk'=>$produkId,
				'id_pelanggan'=>$idPelanggan,
			],			
		];
		
		if ($devplopment){
				/* ====================
				 * === DEVELOPMENT ====
				 * ====================
				*/
				$struksub['ID_PELANGGAN']='537316344073';
				$struksub['NAMA']='Piter Novian';
				$struksub['TARIF/DAYA']='R1/900';
				$struksub['PERIODE']=' AGU16, SEP16';
				$struksub['STAND_METER']='01685500-01754200';
				$struksub['TAGIHAN']='Rp. 383.795,00';
				$struksub['ADMIN_BANK']='Rp.   3.200,00';
				$struksub['TOTAL_BAYAR']='Rp. 386.995,00';
				$struksub['NOMOR_REFF']='16090600530';
				//-------------------------------
				$rslt['status']='success';
				$rslt['errcode']='200';
				$rslt['remarks']='development';
				//------------------------------
				$rslt['id_pelanggan']='537316344073';
				$rslt['nama_pelanggan']='Piter Novian';
				$rslt['tagihan']='383795';
				$rslt['admin_bank']='3200';
				$rslt['total_bayar']='386995';
				$rslt['reff_id']='16090600530';
				$rslt['struk']=$struksub;				
		}else{
			/* ====================
			 * === PRODUCTIOAN ====
			 * ====================
			*/		
			$res = $client->post(\Yii::$app->params['urlApi'], ['body' => json_encode($dataBody), 'future' => false]);
			// echo $res->getStatusCode();
			// echo $res->getBody();
			$status=json_decode(json_encode(json_decode($res->getBody())->status),true);
			$errcode=json_decode(json_encode(json_decode($res->getBody())->errcode),true);
			$remarks=json_decode(json_encode(json_decode($res->getBody())->remarks),true);
			$data=json_decode(json_encode(json_decode($res->getBody())->data),true);		
			//$struk=json_decode(json_encode(json_decode($res->getBody())->struk),true);		
			// return $data;	
			foreach($data['detail'] as $row =>$value){
				$rslt['status']=$status;
				$rslt['errcode']=$errcode;
				$rslt['remarks']=$remarks;
				//----------------------------------
				$rslt[$row]=$value;
				//------------struk-----------------
				$rslt['struk']=$data['struk'];//$struk;
			}
		}
	    return $rslt;
		
		/* ===============================================================
			(
				[id_pelanggan] => 537316344073
				[nama_pelanggan] => Rukanda
				[tagihan] => 383795
				[admin_bank] => 3200
				[total_bayar] => 386995
				[reff_id] => 16090600530
				[struk] => 	ID Pelanggan     : 537316344073
							Nama    : Rukanda
							Tarif/Daya      : R1/900
							Periode : AGU16, SEP16
							Stand Meter     : 01685500-01754200
							Tagihan : Rp. 383.795,00
							Admin Bank      : Rp.   3.200,00
							Total Bayar     : Rp. 386.995,00
							Nomor Reff      : 16090600530
			) 
		* ===============================================================
		*/
	}	
	
	public function actionKelompokPostpaid()
    {
		//\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//$urlApi="http://dev.api.aptmi.com";
		// echo \Yii::$app->params['page'];
		// die();
		
		$client = new \GuzzleHttp\Client();
		//$dataHeader = ["ID"=>5,"NAMA" => "EKAX"];
		$dataBody = [
			"apikey"=>\Yii::$app->params['apikey'],
			"page" =>\Yii::$app->params['page'],
			"function" => \Yii::$app->params['infoKelompok'],
			"param" => [
				'memberid'=>\Yii::$app->params['memberid'],
				'tipe'=>'POST'
			],			
		];
		// echo json_encode($dataBody);
		$res = $client->post(\Yii::$app->params['urlApi'], ['body' => json_encode($dataBody), 'future' => true]);
		//echo $res->getStatusCode();
		echo $res->getBody();	
				
	}
	
	public function actionKelompokPrepaid()
    {
		$client = new \GuzzleHttp\Client();
		$dataBody = [
			"apikey"=>\Yii::$app->params['apikey'],
			"page" =>\Yii::$app->params['page'],
			"function" => \Yii::$app->params['infoKelompok'],
			"param" => [
				'memberid'=>\Yii::$app->params['memberid'],
				'tipe'=>'PRE'
			],			
		];
		// echo json_encode($dataBody);
		$res = $client->post(\Yii::$app->params['urlApi'], ['body' => json_encode($dataBody), 'future' => true]);
		//echo $res->getStatusCode();
		echo $res->getBody();	
				
	}
	
	
	public function actionProdukCodekelompok($kelompok)
    {
		$client = new \GuzzleHttp\Client();
		$dataBody = [
			"apikey"=>\Yii::$app->params['apikey'],
			"page" =>\Yii::$app->params['page'],
			"function" => \Yii::$app->params['infoProduk'],
			"param" => [
				'memberid'=>\Yii::$app->params['memberid'],
				'kategori_id'=>$kelompok,
			],			
		];
		// echo json_encode($dataBody);
		$res = $client->post(\Yii::$app->params['urlApi'], ['body' => json_encode($dataBody), 'future' => false]);
		echo $res->getStatusCode();
		echo $res->getBody();					
	}
	
}