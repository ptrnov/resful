<?php

namespace api\modules\master\models;

use Yii;
use api\modules\master\models\ProductGroup;
use api\modules\master\models\ProductHarga;
use api\modules\master\models\ProductDiscount;
use api\modules\master\models\ProductPromo;
use api\modules\master\models\ProductStock;
use api\modules\master\models\ProductUnit;

/**
 * This is the model class for table "product".
 *
 * @property string $ID
 * @property string $ACCESS_GROUP
 * @property string $STORE_ID
 * @property integer $GROUP_ID
 * @property string $PRODUCT_ID
 * @property string $PRODUCT_QR
 * @property string $PRODUCT_NM
 * @property string $PRODUCT_WARNA
 * @property string $PRODUCT_SIZE
 * @property string $PRODUCT_SIZE_UNIT
 * @property string $PRODUCT_HEADLINE
 * @property integer $UNIT_ID
 * @property double $STOCK_LEVEL
 * @property integer $INDUSTRY_ID
 * @property string $INDUSTRY_NM
 * @property string $INDUSTRY_GRP_NM
 * @property string $CREATE_BY
 * @property string $CREATE_AT
 * @property string $UPDATE_BY
 * @property string $UPDATE_AT
 * @property integer $STATUS
 * @property string $DCRP_DETIL
 * @property integer $YEAR_AT
 * @property integer $MONTH_AT
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
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
           // [['ACCESS_GROUP', 'STORE_ID', 'PRODUCT_ID', 'YEAR_AT', 'MONTH_AT'], 'required'],
            [['STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['PRODUCT_SIZE', 'STOCK_LEVEL'], 'number'],
            [['CREATE_AT', 'UPDATE_AT','CREATE_UUID','UPDATE_UUID','CURRENT_PRICE','INDUSTRY_ID','INDUSTRY_GRP_ID','CURRENT_STOCK','CURRENT_HPP','CURRENT_PPN','IMG_FILE'], 'safe'],
            [['DCRP_DETIL'], 'string'],
            [['ACCESS_GROUP','UNIT_ID'], 'string', 'max' => 15],
            [['STORE_ID'], 'string', 'max' => 20],
            [['PRODUCT_ID'], 'string', 'max' => 35],
            [['PRODUCT_QR', 'PRODUCT_NM', 'PRODUCT_HEADLINE','GROUP_ID'], 'string', 'max' => 100],
            [['PRODUCT_WARNA', 'PRODUCT_SIZE_UNIT', 'CREATE_BY', 'UPDATE_BY'], 'string', 'max' => 50],
            [['INDUSTRY_NM', 'INDUSTRY_GRP_NM'], 'string', 'max' => 255],
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
            'GROUP_ID' => 'Group  ID',
            'PRODUCT_ID' => 'Product  ID',
            'PRODUCT_QR' => 'Product  Qr',
            'PRODUCT_NM' => 'Product  Nm',
            'PRODUCT_WARNA' => 'Product  Warna',
            'PRODUCT_SIZE' => 'Product  Size',
            'PRODUCT_SIZE_UNIT' => 'Product  Size  Unit',
            'PRODUCT_HEADLINE' => 'Product  Headline',
            'UNIT_ID' => 'Unit  ID',
            'STOCK_LEVEL' => 'Stock  Level',
			'CURRENT_STOCK'=>'CURRENT_STOCK',
            'CURRENT_HPP' => 'CURRENT_HPP',
            'CURRENT_PPN' => 'CURRENT_PPN',
            'CURRENT_PRICE' => 'Harga Jual',
            'INDUSTRY_ID' => 'Industry  ID',
            'INDUSTRY_NM' => 'Industry  Nm',
            'INDUSTRY_GRP_ID' => 'Industry  Grp.ID',
            'INDUSTRY_GRP_NM' => 'Industry  Grp  Nm',
            'CREATE_BY' => 'Create  By',
            'CREATE_AT' => 'Create  At',
            'UPDATE_BY' => 'Update  By',
            'UPDATE_AT' => 'Update  At',
            'CREATE_UUID' => 'CREATE_UUID',
            'UPDATE_UUID' => 'UPDATE_UUID',
            'STATUS' => 'Status',
            'DCRP_DETIL' => 'Dcrp  Detil',
            'YEAR_AT' => 'Year  At',
            'MONTH_AT' => 'Month  At',
        ];
    }
	
	public function fields()
	{
		return [            
			'GROUP_ID'=>function($model){
				return $model->GROUP_ID;
			},
			'GROUP_NM'=>function(){
				return $this->groupNm;
			}, 			
            'PRODUCT_ID'=>function($model){
				return $model->PRODUCT_ID;
			},	
            'PRODUCT_QR'=>function($model){
				return $model->PRODUCT_QR;
			},	
            'PRODUCT_NM'=>function($model){
				if($model->PRODUCT_NM){
					return $model->PRODUCT_NM;
				}else{
					return 'none';
				}	
			},	            
            'PRODUCT_WARNA'=>function($model){
				if($model->PRODUCT_WARNA){
					return $model->PRODUCT_WARNA;
				}else{
					return 'none';
				}	
			},	
            'PRODUCT_SIZE'=>function($model){
				if($model->PRODUCT_SIZE){
					return $model->PRODUCT_SIZE;
				}else{
					return 0;
				}		
			},	
            'PRODUCT_SIZE_UNIT'=>function($model){
				if($model->PRODUCT_SIZE_UNIT){
					return $model->PRODUCT_SIZE_UNIT;
				}else{
					return 'none';
				}	
			},	
            'PRODUCT_HEADLINE'=>function($model){
				return $model->PRODUCT_HEADLINE;
			},
			'UNIT_ID'=>function($model){
				return $model->UNIT_ID;
			},			
            'UNIT_NM'=>function($model){
				return $this->unitNm;
			},	
            'STOCK_LEVEL'=>function($model){
				if($model->STOCK_LEVEL){
					return $model->STOCK_LEVEL;
				}else{
					return 0;
				}					
			},	
            'INDUSTRY_ID'=>function($model){
				if($model->INDUSTRY_ID){
					return $model->INDUSTRY_ID;
				}else{
					return 'none';
				}				
			},	
             'INDUSTRY_NM'=>function($model){
				if($model->INDUSTRY_ID){
					return $model->INDUSTRY_NM;
				}else{
					return 'none';
				}				
			},	
            'INDUSTRY_GRP_ID'=>function($model){				
				return $model->INDUSTRY_GRP_ID;
			},	 
			'INDUSTRY_GRP_NM'=>function($model){				
				if($model->INDUSTRY_ID){
					return $model->INDUSTRY_GRP_NM;
				}else{
					return 'none';
				}
			},	
            'STATUS'=>function($model){
				$rslt=$model->STATUS;
				return $rslt;//==0?'disable':'enable';
			},	
            'DCRP_DETIL'=>function($model){
				return $model->DCRP_DETIL;
			},
			'CURRENT_STOCK'=>function($model){
				return $model->CURRENT_STOCK;
				// return $this->productStockTbl;
			},	
            'CURRENT_HPP'=>function($model){
				return $model->CURRENT_HPP;
			},	 
			'CURRENT_PPN'=>function($model){
				return $model->CURRENT_PPN;
			},	 
			'CURRENT_PRICE'=>function($model){
				return $model->CURRENT_PRICE;
			},	 
			'CURRENT_DISCOUNT'=>function($model){
				return $this->productDicountTbl;
			},
			'CURRENT_PROMO'=>function($model){
				return $this->productPromoTbl;
			},	
			'IMG_FILE'=>function($model){
				return $model->IMG_FILE;
			},	
        ];		
	}
	
	//Join to Group Table
	public function getProductGroupTbl(){
		return $this->hasOne(ProductGroup::className(), ['GROUP_ID' => 'GROUP_ID']);
	}	
	public function getGroupNm(){
		$rslt = $this->productGroupTbl['GROUP_NM'];
		if ($rslt){
			return $rslt;
		}else{
			return "none";
		}; 
	}
	
	public function getProductUnitTbl(){
		return $this->hasOne(ProductUnit::className(), ['UNIT_ID' => 'UNIT_ID']);
	}	
	public function getUnitNm(){
		$rslt = $this->productUnitTbl['UNIT_NM'];
		if ($rslt){
			return $rslt;
		}else{
			return "none";
		}; 
	}
	
	
	/*
	 * CURRENT PRICE 
	 * Join to Table Harga where PRODUCT_ID, (current_date PERIODE_TGL1 AND PERIODE_TGL2)
	*/
	public function getProductHargaTbl(){
		//Check Table Harga where PRODUCT_ID,PERIODE_TGL1 AND PERIODE_TGL2 to current_date
		$modalHarga= ProductHarga::find()->where("
			PRODUCT_ID='".$this->PRODUCT_ID."' AND 
			('".date('Y-m-d')."' BETWEEN PERIODE_TGL1 AND PERIODE_TGL2)
		")->one();
		
		if($modalHarga){
			//Jika ditemukan data pada table harga, maka harga tersebut di simpan pada table "product->CURRENT_PRICE"
			$modalProduct = Product::find()->where(['PRODUCT_ID' =>$this->PRODUCT_ID])->one();
			$modalProduct->CURRENT_PRICE=$modalHarga->HARGA_JUAL;
			$modalProduct->save();
			return  $modalHarga->HARGA_JUAL;
		}else{
			//Jika Tidak ditemukan perubahan data pada table harga, seting default CURRENT_PRICE
			//return  0;
			return $this->CURRENT_PRICE!=''?$this->CURRENT_PRICE:'0';	
		}
	}	
	
	/*
	 * CURRENT DISCOUNT 
	 * Join to Table Discount where PRODUCT_ID, (current_date PERIODE_TGL1 AND PERIODE_TGL2)
	*/
	public function getProductDicountTbl(){
		//Check Table Discount where PRODUCT_ID,PERIODE_TGL1 AND PERIODE_TGL2 to current_date
		$modalDiscount= ProductDiscount::find()->where("
			PRODUCT_ID='".$this->PRODUCT_ID."' AND 
			('".date('Y-m-d')."' BETWEEN PERIODE_TGL1 AND PERIODE_TGL2)
		")->one();		
		if($modalDiscount){			
			return  $modalDiscount->DISCOUNT;
		}else{
			return  "0.00";	
		}
	}	
	
	/*
	 * CURRENT PROMO 
	 * Join to Table Promo where PRODUCT_ID, (current_date PERIODE_TGL1 AND PERIODE_TGL2)
	*/
	public function getProductPromoTbl(){
		//Check Table Discount where PRODUCT_ID,PERIODE_TGL1 AND PERIODE_TGL2 to current_date
		$modalPromo= ProductPromo::find()->where("
			PRODUCT_ID='".$this->PRODUCT_ID."' AND 
			('".date('Y-m-d')."' BETWEEN PERIODE_TGL1 AND PERIODE_TGL2)
		")->one();
		if($modalPromo){			
			return  $modalPromo->PROMO;
		}else{
			return  "";	
		}
	}	
	
	/*
	 * CURRENT STOCK 
	 * Join to Table Stock where PRODUCT_ID, (current_date PERIODE_TGL1 AND PERIODE_TGL2)
	*/
	public function getProductStockTbl(){
		$modalStock= ProductStock::find()->where("
			PRODUCT_ID='".$this->PRODUCT_ID."' AND INPUT_DATE='".date('Y-m-d')."'
		")->one();
		if($modalStock){			
			return  $modalStock->SISA_STOCK;
		}else{
			return  0;	
		}
	}	
}
