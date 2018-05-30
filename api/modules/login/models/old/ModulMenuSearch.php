<?php

namespace api\modules\login\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ModulerpSearch represents the model behind the search form about `lukisongroup\sistem\models\erpmodul\Modulerp`.
 */
class ModulMenuSearch extends ModulMenu
{
	public function attributes()
	{
		//Author -ptr.nov- add related fields to searchable attributes 
		return array_merge(parent::attributes(), ['UserUnix']);
	}
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['MODUL_DCRP'], 'string'],
            [['MODUL_STS', 'SORT','MODUL_ID','MODUL_GRP'], 'integer'],
            [['MODUL_NM','BTN_NM',], 'string', 'max' => 100],
            [['BTN_URL','BTN_ICON',], 'safe']
        ];
    }
	
	/**
	 * LOGIN ACCESS
	 * USER FOR MENU-TREE
     * Single User.
	*/
    public function searchUserMenu($params)
    {
        $query = ModulMenu::find()->JoinWith('modulMenuTbl',true,'INNER JOIN');//->where(['USER_UNIX' =>$params['UserUnix']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination'=>[
				'pageSize'=>100,
			]   
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

		$query->andFilterWhere([ 
			'USER_UNIX' =>$this->UserUnix,			
		]);
		
		
			
		$query->asArray();       

        return $dataProvider;
    }
	
}
