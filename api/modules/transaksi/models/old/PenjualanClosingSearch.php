<?php

namespace api\modules\transaksi\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\transaksi\models\PenjualanClosing;

/**
 * PenjualanClosingSearch represents the model behind the search form of `frontend\backend\transaksi\models\PenjualanClosing`.
 */
class PenjualanClosingSearch extends PenjualanClosing
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS'], 'integer'],
            [['CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT', 'CLOSING_ID', 'ACCESS_UNIX', 'CLOSING_DATE', 'OUTLET_ID'], 'safe'],
            [['TTL_MODAL', 'TTL_UANG', 'TTL_QTY', 'TTL_STORAN', 'TTL_SISA'], 'number'],
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
        $query = PenjualanClosing::find();

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
            'CLOSING_DATE' => $this->CLOSING_DATE,
            'TTL_MODAL' => $this->TTL_MODAL,
            'TTL_UANG' => $this->TTL_UANG,
            'TTL_QTY' => $this->TTL_QTY,
            'TTL_STORAN' => $this->TTL_STORAN,
            'TTL_SISA' => $this->TTL_SISA,
        ]);

        $query->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY])
            ->andFilterWhere(['like', 'CLOSING_ID', $this->CLOSING_ID])
            ->andFilterWhere(['like', 'ACCESS_UNIX', $this->ACCESS_UNIX])
            ->andFilterWhere(['like', 'OUTLET_ID', $this->OUTLET_ID]);

        return $dataProvider;
    }
}
