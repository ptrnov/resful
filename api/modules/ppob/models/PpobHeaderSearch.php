<?php

namespace api\modules\ppob\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\ppob\models\PpobHeader;

/**
 * PpobHeaderSearch represents the model behind the search form about `frontend\backend\ppob\models\PpobHeader`.
 */
class PpobHeaderSearch extends PpobHeader
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS'], 'integer'],
            [['HEADER_ID', 'HEADER_NM', 'HEADER_DCRP', 'CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT'], 'safe'],
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
        $query = PpobHeader::find();

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
            'STATUS' => $this->STATUS,
            'CREATE_AT' => $this->CREATE_AT,
            'UPDATE_AT' => $this->UPDATE_AT,
        ]);

        $query->andFilterWhere(['like', 'HEADER_ID', $this->HEADER_ID])
            ->andFilterWhere(['like', 'HEADER_NM', $this->HEADER_NM])
            ->andFilterWhere(['like', 'HEADER_DCRP', $this->HEADER_DCRP])
            ->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY]);

        return $dataProvider;
    }
}
