<?php

namespace api\modules\ppob\models;

use Yii;

/**
 * This is the model class for table "ppob_master_harga".
 *
 * @property string $ID_PRODUK
 * @property string $TYPE_NM
 * @property string $KELOMPOK
 * @property string $KTG_ID
 * @property string $KTG_NM
 * @property string $ID_CODE
 * @property string $CODE
 * @property string $NAME
 * @property string $DENOM
 * @property string $HARGA_BARU
 * @property string $TGL_AKTIF
 * @property string $HARGA_DASAR
 * @property string $MARGIN_FEE_KG
 * @property string $MARGIN_FEE_MEMBER
 * @property string $HARGA_JUAL
 * @property int $PERMIT
 * @property string $FUNGSI
 * @property int $STATUS 0=deactife; 1=Active; 2=NewPrice
 * @property string $KETERANGAN
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 */
class PpobMasterHarga extends \yii\db\ActiveRecord
{
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
    public static function tableName()
    {
        return 'ppob_master_harga';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID_PRODUK'], 'required'],
            [['NAME', 'KETERANGAN'], 'string'],
            [['DENOM', 'HARGA_BARU', 'HARGA_DASAR', 'MARGIN_FEE_KG', 'MARGIN_FEE_MEMBER', 'HARGA_JUAL'], 'number'],
            [['TGL_AKTIF', 'CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['PERMIT', 'STATUS'], 'integer'],
            [['ID_PRODUK', 'FUNGSI'], 'string', 'max' => 100],
            [['TYPE_NM', 'KELOMPOK', 'KTG_NM'], 'string', 'max' => 255],
            [['KTG_ID', 'ID_CODE', 'CODE', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['ID_PRODUK'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID_PRODUK' => 'Id  Produk',
            'TYPE_NM' => 'Type  Nm',
            'KELOMPOK' => 'Kelompok',
            'KTG_ID' => 'Ktg  ID',
            'KTG_NM' => 'Ktg  Nm',
            'ID_CODE' => 'Id  Code',
            'CODE' => 'Code',
            'NAME' => 'Name',
            'DENOM' => 'Denom',
            'HARGA_BARU' => 'Harga  Baru',
            'TGL_AKTIF' => 'Tgl  Aktif',
            'HARGA_DASAR' => 'Harga  Dasar',
            'MARGIN_FEE_KG' => 'Margin  Fee  Kg',
            'MARGIN_FEE_MEMBER' => 'Margin  Fee  Member',
            'HARGA_JUAL' => 'Harga  Jual',
            'PERMIT' => 'Permit',
            'FUNGSI' => 'Fungsi',
            'STATUS' => 'Status',
            'KETERANGAN' => 'Keterangan',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
        ];
    }
	
	public function fields()
	{
		return [			
			'ID_PRODUK'=>function($model){
				return $model->ID_PRODUK;
			},
			'TYPE_NM'=>function($model){
				return $model->TYPE_NM;
			},
			'KELOMPOK'=>function($model){
				return $model->KELOMPOK;
			},
			'KTG_ID'=>function($model){
				return $model->KTG_ID;
			},
			'KTG_NM'=>function($model){
				return $model->KTG_NM;
			},
			'ID_CODE'=>function($model){
				return $model->ID_CODE;
			},
			'CODE'=>function($model){
				return $model->CODE;
			},
			'NAME'=>function($model){
				return $model->NAME;
			},
			'DENOM'=>function($model){
				return $model->DENOM;
			},
			'HARGA_JUAL'=>function($model){
				return $model->HARGA_JUAL;
			},
		];
	}
}
