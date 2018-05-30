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
use yii\web\HttpException;
/**
  * Option user, employe, modul, permission
  * @author ptrnov  <piter@lukison.com>
  * @since 1.1
*/
class UserTokenSearch extends UserToken
{
    public function rules()
    {
         return [
			//[['username', 'ACCESS_ID','ACCESS_GROUP'], 'required'],
            [['auth_key'], 'string'],
            [['status', 'ACCESS_SITE', 'ONLINE', 'lft', 'rgt', 'lvl', 'icon_type', 'YEAR_AT', 'MONTH_AT'], 'integer'],
            [['create_at', 'updated_at'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'icon'], 'string', 'max' => 255],
            [['ACCESS_ID', 'ACCESS_GROUP'], 'string', 'max' => 15],
            [['ACCESS_LEVEL'], 'string', 'max' => 50],
			[['ID_GOOGLE','ID_FB','ID_TWITTER','ID_LINKEDIN','ID_YAHOO'], 'string', 'max' => 255],
		];
    }

    public function search($params)
    {
		$query = UserToken::find()->where($params);
			
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

			//$query->andFilterWhere(['like', 'username', $this->username]);
			// return $dataProvider;
			if($dataProvider->getmodels()){		
				return $dataProvider;
			}else{
				 //return Yii::$app->statusCode->apihandling(204);
				// return $this->handleFailure($response);
				return new \yii\web\HttpException(204, 'Not Data Content');
			}	
    }

}
