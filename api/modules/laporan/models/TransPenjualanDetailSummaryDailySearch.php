<?php

namespace api\modules\laporan\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\laporan\models\TransPenjualanDetailSummaryDaily;

/**
 * TransPenjualanDetailSummaryDailySearch represents the model behind the search form about `frontend\backend\laporan\models\TransPenjualanDetailSummaryDaily`.
 */
class TransPenjualanDetailSummaryDailySearch extends TransPenjualanDetailSummaryDaily
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'BULAN'], 'integer'],
            [['ACCESS_GROUP', 'STORE_ID', 'TAHUN', 'TGL', 'PRODUCT_ID', 'PRODUCT_NM', 'PRODUCT_PROVIDER', 'PRODUCT_PROVIDER_NO', 'PRODUCT_PROVIDER_NM', 'CREATE_AT', 'UPDATE_AT', 'KETERANGAN'], 'safe'],
            [['PRODUCT_QTY', 'HPP', 'HARGA_JUAL', 'SUB_TOTAL'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TransPenjualanDetailSummaryDaily::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ID' => $this->ID,
            'BULAN' => $this->BULAN,
            'TGL' => $this->TGL,
            'PRODUCT_QTY' => $this->PRODUCT_QTY,
            'HPP' => $this->HPP,
            'HARGA_JUAL' => $this->HARGA_JUAL,
            'SUB_TOTAL' => $this->SUB_TOTAL,
            'CREATE_AT' => $this->CREATE_AT,
            'UPDATE_AT' => $this->UPDATE_AT,
        ]);

        $query->andFilterWhere(['like', 'ACCESS_GROUP', $this->ACCESS_GROUP])
            ->andFilterWhere(['like', 'STORE_ID', $this->STORE_ID])
            ->andFilterWhere(['like', 'TAHUN', $this->TAHUN])
            ->andFilterWhere(['like', 'PRODUCT_ID', $this->PRODUCT_ID])
            ->andFilterWhere(['like', 'PRODUCT_NM', $this->PRODUCT_NM])
            ->andFilterWhere(['like', 'PRODUCT_PROVIDER', $this->PRODUCT_PROVIDER])
            ->andFilterWhere(['like', 'PRODUCT_PROVIDER_NO', $this->PRODUCT_PROVIDER_NO])
            ->andFilterWhere(['like', 'PRODUCT_PROVIDER_NM', $this->PRODUCT_PROVIDER_NM])
            ->andFilterWhere(['like', 'KETERANGAN', $this->KETERANGAN]);
		$query->orderBy(['SUB_TOTAL'=>SORT_DESC]);
        return $dataProvider;
    }
}
