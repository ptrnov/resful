<?php
/**
 * NOTE: Nama Class harus diawali Hurup Besar
 * Server Linux 	: hurup besar/kecil bermasalah -case sensitif-
 * Server Windows 	: hurup besar/kecil tidak bermasalah
 * Author: -ptr.nov-
*/

namespace api\modules\login\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
  * Option user, employe, modul, permission
  * @author ptrnov  <piter@lukison.com>
  * @since 1.1
*/
class UserloginSearch extends Userlogin
{
    public function rules()
    {
        return [
           	[['username','auth_key','password_hash','password_reset_token'], 'string'],
			[['email'], 'string'],
			[['id','status','create_at','update_at'],'safe'],
			[['ACCESS_UNIX','ACCESS_GROUP','ACCESS_LEVEL','ACCESS_SITE','ONLINE','UUID'], 'safe'],
        ];
    }

    public function search($params)
    {
		$query = Userlogin::find();
			
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination'=>[
					'pageSize'=>100,
				]   
			]);

			$this->load($params);
			if (!$this->validate()) {
				return $dataProvider;
			}

			$query->andFilterWhere(['like', 'username', $this->username])
				->andFilterWhere(['like', 'email', $this->email])
				->andFilterWhere(['like', 'status', $this->status]);
			//return $dataProvider;
			if($dataProvider->getmodels()){		
				return $dataProvider;
			}else{
				return new \yii\web\HttpException(204, 'Not Data Content');
			}	
    }

}
