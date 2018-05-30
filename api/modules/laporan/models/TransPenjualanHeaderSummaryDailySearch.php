<?php

namespace api\modules\laporan\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\laporan\models\TransPenjualanHeaderSummaryDaily;

/**
 * TransPenjualanHeaderSummaryDailySearch represents the model behind the search form about `frontend\backend\laporan\models\TransPenjualanHeaderSummaryDaily`.
 */
class TransPenjualanHeaderSummaryDailySearch extends TransPenjualanHeaderSummaryDaily
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'BULAN', 'TOTAL_PRODUCT', 'JUMLAH_TRANSAKSI', 'CNT_TUNAI', 'CNT_DEBET', 'CNT_KREDIT', 'CNT_EMONEY', 'CNT_BCA', 'CNT_MANDIRI', 'CNT_BNI', 'CNT_BRI'], 'integer'],
            [['ACCESS_GROUP', 'STORE_ID', 'TAHUN', 'TGL', 'CREATE_AT', 'UPDATE_AT', 'KETERANGAN'], 'safe'],
            [['TOTAL_HPP', 'TOTAL_SALES', 'TTL_TUNAI', 'TTL_DEBET', 'TTL_KREDIT', 'TTL_EMONEY'], 'number'],
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
        $query = TransPenjualanHeaderSummaryDaily::find();

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
            'TOTAL_HPP' => $this->TOTAL_HPP,
            'TOTAL_SALES' => $this->TOTAL_SALES,
            'TOTAL_PRODUCT' => $this->TOTAL_PRODUCT,
            'JUMLAH_TRANSAKSI' => $this->JUMLAH_TRANSAKSI,
            'CNT_TUNAI' => $this->CNT_TUNAI,
            'CNT_DEBET' => $this->CNT_DEBET,
            'CNT_KREDIT' => $this->CNT_KREDIT,
            'CNT_EMONEY' => $this->CNT_EMONEY,
            'TTL_TUNAI' => $this->TTL_TUNAI,
            'TTL_DEBET' => $this->TTL_DEBET,
            'TTL_KREDIT' => $this->TTL_KREDIT,
            'TTL_EMONEY' => $this->TTL_EMONEY,
            'CNT_BCA' => $this->CNT_BCA,
            'CNT_MANDIRI' => $this->CNT_MANDIRI,
            'CNT_BNI' => $this->CNT_BNI,
            'CNT_BRI' => $this->CNT_BRI,
            'CREATE_AT' => $this->CREATE_AT,
            'UPDATE_AT' => $this->UPDATE_AT,
        ]);

        $query->andFilterWhere(['like', 'ACCESS_GROUP', $this->ACCESS_GROUP])
            ->andFilterWhere(['like', 'STORE_ID', $this->STORE_ID])
            ->andFilterWhere(['like', 'TAHUN', $this->TAHUN])
            ->andFilterWhere(['like', 'KETERANGAN', $this->KETERANGAN]);

        return $dataProvider;
    }
}
