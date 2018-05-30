<?php

namespace api\modules\login\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * ModulPermissionSearch represents the model behind the search form about `lukisongroup\sistem\models\erpmodul\ModulPermission`.
 */
class ModulPermissionSearch extends ModulPermission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
         return [
            [['MODUL_ID'], 'integer'],
			[['USER_UNIX'],'string'],
			[['STATUS','BTN_VIEW','BTN_REVIEW', 'BTN_CREATE','BTN_EDIT', 'BTN_DELETE'], 'integer'],
			[['BTN_SIGN1', 'BTN_SIGN2', 'BTN_SIGN3','BTN_SIGN4','BTN_SIGN5'], 'integer'],
			[['CREATE_BY','UPDATE_BY'],'string'],
			[['CREATE_AT','UPDATE_AT'],'safe']
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
        $query = ModulPermission::find();

        $dataProvider = new ActiveDataProvider([
			 'query' => $query,
			 'pagination'=>[
				'pageSize'=>200,
			]   
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ID' => $this->ID,
            'USER_UNIX' => $this->USER_UNIX,
            'MODUL_ID' => $this->MODUL_ID,
            'STATUS' => $this->STATUS,
			'BTN_VIEW' => $this->BTN_VIEW,
			'BTN_REVIEW' => $this->BTN_REVIEW,
            'BTN_CREATE' => $this->BTN_CREATE,
            'BTN_EDIT' => $this->BTN_EDIT,
            'BTN_DELETE' => $this->BTN_DELETE,            
            'BTN_SIGN1' => $this->BTN_SIGN1,
            'BTN_SIGN2' => $this->BTN_SIGN2,
            'BTN_SIGN3' => $this->BTN_SIGN3,
            'BTN_SIGN5' => $this->BTN_SIGN5,
            'CREATE_BY' => $this->CREATE_BY,
            'CREATE_AT' => $this->CREATE_AT,
            'UPDATE_BY' => $this->UPDATE_BY,
            'UPDATE_AT' => $this->UPDATE_AT,
        ]);

        return $dataProvider;
    }
	
}
