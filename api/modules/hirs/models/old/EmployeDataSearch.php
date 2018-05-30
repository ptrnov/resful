<?php

namespace api\modules\hirs\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\hirs\models\EmployeData;

/**
 * EmployeDataSearch represents the model behind the search form of `frontend\backend\hris\models\EmployeData`.
 */
class EmployeDataSearch extends EmployeData
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'STATUS'], 'integer'],
            [['CREATE_BY', 'CREATE_AT', 'UPDATE_BY', 'UPDATE_AT', 'ACCESS_UNIX', 'OUTLET_CODE', 'EMP_ID', 'EMP_NM_DPN', 'EMP_NM_TGH', 'EMP_NM_BLK', 'EMP_KTP', 'EMP_ALAMAT', 'EMP_GENDER', 'EMP_STS_NIKAH', 'EMP_TLP', 'EMP_HP', 'EMP_EMAIL'], 'safe'],
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
        $query = EmployeData::find();

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
            ->andFilterWhere(['like', 'EMP_NM_DPN', $this->EMP_NM_DPN])
            ->andFilterWhere(['like', 'EMP_NM_TGH', $this->EMP_NM_TGH])
            ->andFilterWhere(['like', 'EMP_NM_BLK', $this->EMP_NM_BLK])
            ->andFilterWhere(['like', 'EMP_KTP', $this->EMP_KTP])
            ->andFilterWhere(['like', 'EMP_ALAMAT', $this->EMP_ALAMAT])
            ->andFilterWhere(['like', 'EMP_GENDER', $this->EMP_GENDER])
            ->andFilterWhere(['like', 'EMP_STS_NIKAH', $this->EMP_STS_NIKAH])
            ->andFilterWhere(['like', 'EMP_TLP', $this->EMP_TLP])
            ->andFilterWhere(['like', 'EMP_HP', $this->EMP_HP])
            ->andFilterWhere(['like', 'EMP_EMAIL', $this->EMP_EMAIL]);

        return $dataProvider;
    }
}
