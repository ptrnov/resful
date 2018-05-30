<?php

namespace api\modules\transaksi\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\transaksi\models\PenjualanDetail;

/**
 * PenjualanDetailSearch represents the model behind the search form of `frontend\backend\transaksi\models\PenjualanDetail`.
 */
class PenjualanDetailSearch extends PenjualanDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS', 'DISCOUNT_STT'], 'integer'],
            [['CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT', 'TRANS_ID', 'ACCESS_UNIX', 'TRANS_DATE', 'OUTLET_ID', 'OUTLET_NM', 'ITEM_ID', 'ITEM_NM', 'SATUAN'], 'safe'],
            [['ITEM_QTY', 'HARGA', 'DISCOUNT'], 'number'],
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
        $query = PenjualanDetail::find();

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
            'CREATE_AT' => $this->CREATE_AT,
            'UPDATE_AT' => $this->UPDATE_AT,
            'STATUS' => $this->STATUS,
            'TRANS_DATE' => $this->TRANS_DATE,
            'ITEM_QTY' => $this->ITEM_QTY,
            'HARGA' => $this->HARGA,
            'DISCOUNT' => $this->DISCOUNT,
            'DISCOUNT_STT' => $this->DISCOUNT_STT,
        ]);

        $query->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY])
            ->andFilterWhere(['like', 'TRANS_ID', $this->TRANS_ID])
            ->andFilterWhere(['like', 'ACCESS_UNIX', $this->ACCESS_UNIX])
            ->andFilterWhere(['like', 'OUTLET_ID', $this->OUTLET_ID])
            ->andFilterWhere(['like', 'OUTLET_NM', $this->OUTLET_NM])
            ->andFilterWhere(['like', 'ITEM_ID', $this->ITEM_ID])
            ->andFilterWhere(['like', 'ITEM_NM', $this->ITEM_NM])
            ->andFilterWhere(['like', 'SATUAN', $this->SATUAN]);

        return $dataProvider;
    }
}
