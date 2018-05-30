<?php

namespace api\modules\hirs\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\hirs\models\EmployeDataImage;

/**
 * EmployeDataSearch represents the model behind the search form of `frontend\backend\hris\models\EmployeData`.
 */
class EmployeDataImageSearch extends EmployeDataImage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [['CREATE_AT', 'UPDATE_AT'], 'safe'],
            [['STATUS'], 'integer'],
            [['CREATE_BY', 'UPDATE_BY', 'ACCESS_UNIX', 'OUTLET_CODE', 'EMP_ID'], 'string', 'max' => 50],
            ['IMG64'], 'safe'],
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
        $query = EmployeDataImage::find();

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
        ]);

        $query->andFilterWhere(['like', 'CREATE_BY', $this->CREATE_BY])
            ->andFilterWhere(['like', 'UPDATE_BY', $this->UPDATE_BY])
            ->andFilterWhere(['like', 'ACCESS_UNIX', $this->ACCESS_UNIX])
            ->andFilterWhere(['like', 'OUTLET_CODE', $this->OUTLET_CODE])
            ->andFilterWhere(['like', 'EMP_ID', $this->EMP_ID])
            ->andFilterWhere(['like', 'IMG64', $this->EMP_NM_DPN]);

        return $dataProvider;
    }
}
