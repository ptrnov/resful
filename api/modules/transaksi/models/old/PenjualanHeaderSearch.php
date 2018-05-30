<?php

namespace api\modules\transaksi\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\transaksi\models\PenjualanHeader;

/**
 * PenjualanHeaderSearch represents the model behind the search form of `frontend\backend\transaksi\models\PenjualanHeader`.
 */
class PenjualanHeaderSearch extends PenjualanHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS'], 'integer'],
			[['TOTAL_ITEM','TOTAL_HARGA','TYPE_PAY','BANK_NM','BANK_NO'], 'safe'],
            [['CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT', 'TRANS_ID', 'ACCESS_UNIX', 'TRANS_DATE', 'OUTLET_ID', 'CONSUMER_NM', 'CONSUMER_EMAIL', 'CONSUMER_PHONE'], 'safe'],
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
        $query = PenjualanHeader::find();

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
        ]);

        $query->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY])
            ->andFilterWhere(['like', 'TRANS_ID', $this->TRANS_ID])
            ->andFilterWhere(['like', 'ACCESS_UNIX', $this->ACCESS_UNIX])
            ->andFilterWhere(['like', 'OUTLET_ID', $this->OUTLET_ID])
            ->andFilterWhere(['like', 'CONSUMER_NM', $this->CONSUMER_NM])
            ->andFilterWhere(['like', 'CONSUMER_EMAIL', $this->CONSUMER_EMAIL])
            ->andFilterWhere(['like', 'CONSUMER_PHONE', $this->CONSUMER_PHONE]);

        return $dataProvider;
    }
}
