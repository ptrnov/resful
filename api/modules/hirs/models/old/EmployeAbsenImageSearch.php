<?php

namespace api\modules\hirs\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\hirs\models\EmployeAbsenImage;

/**
 * EmployeAbsenImageSearch represents the model behind the search form of `frontend\backend\hris\models\EmployeAbsenImage`.
 */
class EmployeAbsenImageSearch extends EmployeAbsenImage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS'], 'integer'],
            [['CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT', 'EMP_ID', 'TGL', 'IMG_MASUK', 'IMG_KELUAR'], 'safe'],
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
        $query = EmployeAbsenImage::find();

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
            'TGL' => $this->TGL,
        ]);

        $query->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY])
            ->andFilterWhere(['like', 'EMP_ID', $this->EMP_ID])
            ->andFilterWhere(['like', 'IMG_MASUK', $this->IMG_MASUK])
            ->andFilterWhere(['like', 'IMG_KELUAR', $this->IMG_KELUAR]);

        return $dataProvider;
    }
}
