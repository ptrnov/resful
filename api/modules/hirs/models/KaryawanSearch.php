<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Karyawan;

/**
 * KaryawanSearch represents the model behind the search form about `frontend\models\Karyawan`.
 */
class KaryawanSearch extends Karyawan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['ACCESS_GROUP', 'STORE_ID', 'KARYAWAN_ID', 'NAMA_DPN', 'NAMA_TGH', 'NAMA_BLK', 'KTP', 'TMP_LAHIR', 'TGL_LAHIR', 'GENDER', 'ALAMAT', 'STS_NIKAH', 'TLP', 'HP', 'EMAIL', 'CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT', 'DCRP_DETIL'], 'safe'],
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
        $query = Karyawan::find();

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
            'TGL_LAHIR' => $this->TGL_LAHIR,
            'CREATE_AT' => $this->CREATE_AT,
            'UPDATE_AT' => $this->UPDATE_AT,
            'STATUS' => $this->STATUS,
            'YEAR_AT' => $this->YEAR_AT,
            'MONTH_AT' => $this->MONTH_AT,
        ]);

        $query->andFilterWhere(['like', 'ACCESS_GROUP', $this->ACCESS_GROUP])
            ->andFilterWhere(['like', 'STORE_ID', $this->STORE_ID])
            ->andFilterWhere(['like', 'KARYAWAN_ID', $this->KARYAWAN_ID])
            ->andFilterWhere(['like', 'NAMA_DPN', $this->NAMA_DPN])
            ->andFilterWhere(['like', 'NAMA_TGH', $this->NAMA_TGH])
            ->andFilterWhere(['like', 'NAMA_BLK', $this->NAMA_BLK])
            ->andFilterWhere(['like', 'KTP', $this->KTP])
            ->andFilterWhere(['like', 'TMP_LAHIR', $this->TMP_LAHIR])
            ->andFilterWhere(['like', 'GENDER', $this->GENDER])
            ->andFilterWhere(['like', 'ALAMAT', $this->ALAMAT])
            ->andFilterWhere(['like', 'STS_NIKAH', $this->STS_NIKAH])
            ->andFilterWhere(['like', 'TLP', $this->TLP])
            ->andFilterWhere(['like', 'HP', $this->HP])
            ->andFilterWhere(['like', 'EMAIL', $this->EMAIL])
            ->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY])
            ->andFilterWhere(['like', 'DCRP_DETIL', $this->DCRP_DETIL]);

        return $dataProvider;
    }
}
