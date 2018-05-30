<?php

namespace api\modules\laporan\models;

use Yii;

/**
 * This is the model class for table "trans_penjualan_header_summary_daily".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property string $TAHUN
 * @property integer $BULAN
 * @property string $TGL
 * @property string $TOTAL_HPP
 * @property string $TOTAL_SALES
 * @property integer $TOTAL_PRODUCT
 * @property integer $JUMLAH_TRANSAKSI
 * @property integer $CNT_TUNAI
 * @property integer $CNT_DEBET
 * @property integer $CNT_KREDIT
 * @property integer $CNT_EMONEY
 * @property string $TTL_TUNAI
 * @property string $TTL_DEBET
 * @property string $TTL_KREDIT
 * @property string $TTL_EMONEY
 * @property integer $CNT_BCA
 * @property integer $CNT_MANDIRI
 * @property integer $CNT_BNI
 * @property integer $CNT_BRI
 * @property string $CREATE_AT
 * @property string $UPDATE_AT
 * @property string $KETERANGAN
 */
class TransPenjualanHeaderSummaryDaily extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trans_penjualan_header_summary_daily';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('production_api');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['BULAN', 'TOTAL_PRODUCT', 'JUMLAH_TRANSAKSI', 'CNT_TUNAI', 'CNT_DEBET', 'CNT_KREDIT', 'CNT_EMONEY', 'CNT_BCA', 'CNT_MANDIRI', 'CNT_BNI', 'CNT_BRI'], 'integer'],
            [['TGL', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['TOTAL_HPP', 'TOTAL_SALES', 'TTL_TUNAI', 'TTL_DEBET', 'TTL_KREDIT', 'TTL_EMONEY'], 'number'],
            [['KETERANGAN'], 'string'],
            [['ACCESS_GROUP'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['TAHUN'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'ACCESS_GROUP' => 'Access  Group',
            'STORE_ID' => 'Store  ID',
            'TAHUN' => 'Tahun',
            'BULAN' => 'Bulan',
            'TGL' => 'Tgl',
            'TOTAL_HPP' => 'Total  Hpp',
            'TOTAL_SALES' => 'Total  Sales',
            'TOTAL_PRODUCT' => 'Total  Product',
            'JUMLAH_TRANSAKSI' => 'Jumlah  Transaksi',
            'CNT_TUNAI' => 'Cnt  Tunai',
            'CNT_DEBET' => 'Cnt  Debet',
            'CNT_KREDIT' => 'Cnt  Kredit',
            'CNT_EMONEY' => 'Cnt  Emoney',
            'TTL_TUNAI' => 'Ttl  Tunai',
            'TTL_DEBET' => 'Ttl  Debet',
            'TTL_KREDIT' => 'Ttl  Kredit',
            'TTL_EMONEY' => 'Ttl  Emoney',
            'CNT_BCA' => 'Cnt  Bca',
            'CNT_MANDIRI' => 'Cnt  Mandiri',
            'CNT_BNI' => 'Cnt  Bni',
            'CNT_BRI' => 'Cnt  Bri',
            'CREATE_AT' => 'Create  At',
            'UPDATE_AT' => 'Update  At',
            'KETERANGAN' => 'Keterangan',
        ];
    }
	
	public function fields()
	{
		return [			
			'ACCESS_GROUP'=>function($model){
				return $model->ACCESS_GROUP;
			},
			'STORE_ID'=>function($model){
				return $model->STORE_ID;
			},
			'TAHUN'=>function($model){
				return $model->TAHUN;
			},
			'BULAN'=>function($model){
				return $model->BULAN;
			},
			'TGL'=>function($model){
				return $model->TGL;
			},
			'TOTAL_SALES'=>function($model){
				return $model->TOTAL_SALES;
			},
			'JUMLAH_TRANSAKSI'=>function($model){
				return $model->JUMLAH_TRANSAKSI;
			},					
			'TOTAL_PRODUCT'=>function($model){
				return $model->TOTAL_PRODUCT;
			},
			'CNT_TUNAI'=>function($model){
				return $model->CNT_TUNAI;
			},
			'CNT_DEBET'=>function($model){
				return $model->CNT_DEBET;
			},
			'CNT_KREDIT'=>function($model){
				return $model->CNT_KREDIT;
			},
			'CNT_EMONEY'=>function($model){
				return $model->CNT_EMONEY;
			},
			'TTL_TUNAI'=>function($model){
				return $model->TTL_TUNAI;
			},
			'TTL_DEBET'=>function($model){
				return $model->TTL_DEBET;
			},
			'TTL_KREDIT'=>function($model){
				return $model->TTL_KREDIT;
			},
			'TTL_EMONEY'=>function($model){
				return $model->TTL_EMONEY;
			}
		];
	}
}
