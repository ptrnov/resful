<?php

namespace api\modules\master\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\master\models\ItemStock;

/**
 * ItemStockSearch represents the model behind the search form of `app\backend\master\models\ItemStock`.
 */
class ItemStockSearch extends ItemStock
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS'], 'integer'],
            [['ACCESS_UNIX','CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT', 'ITEM_ID', 'OUTLET_CODE', 'TGL_BELI', 'DCRIPT'], 'safe'],
            [['STOCK', 'HARGA_BELI', 'MARGIN_KEUNTUNGAN', 'MAX_DISCOUNT'], 'number'],
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
        $query = ItemStock::find();

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
			'ACCESS_UNIX' => $this->ACCESS_UNIX,
            'CREATE_AT' => $this->CREATE_AT,
            'UPDATE_AT' => $this->UPDATE_AT,
            'STATUS' => $this->STATUS,
            'STOCK' => $this->STOCK,
            'TGL_BELI' => $this->TGL_BELI,
            'HARGA_BELI' => $this->HARGA_BELI,
            'MARGIN_KEUNTUNGAN' => $this->MARGIN_KEUNTUNGAN,
            'MAX_DISCOUNT' => $this->MAX_DISCOUNT,
        ]);

        $query->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY])
            ->andFilterWhere(['like', 'ITEM_ID', $this->ITEM_ID])
            ->andFilterWhere(['like', 'OUTLET_CODE', $this->OUTLET_CODE])
            ->andFilterWhere(['like', 'DCRIPT', $this->DCRIPT]);

        return $dataProvider;
    }
}
