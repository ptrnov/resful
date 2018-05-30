<?php

namespace api\modules\login\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProvinceSearch represents the model behind the search form about `lukisongroup\esm\models\Province`.
 */
class LocateProvinceSearch extends Province
{
    /**
     * @inheritdoc
     */
   
    public function rules()
    {
        return [
            [['PROVINCE_ID'], 'integer'],
            [['PROVINCE'], 'safe'],
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
        $query = Province::find();

        $dataproviderpro = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 20,
			],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataproviderpro;
        }

        $query->andFilterWhere([
            'PROVINCE_ID' => $this->PROVINCE_ID,
        ]);

        $query->andFilterWhere(['like', 'PROVINCE', $this->PROVINCE]);

        return $dataproviderpro;
    }
}
