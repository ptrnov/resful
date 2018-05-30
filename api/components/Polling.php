<?php
/**
 * Created by PhpStorm.
 * User: ptr.nov
 * Date: 10/08/15
 * Time: 19:44
 */

namespace api\components;
use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\Component;
use Yii\base\Model;
use api\modules\master\models\SyncPoling;
/** 
  * Components Polling
  * @author ptrnov  <piter@lukison.com>
  * @since 1.1
*/
class Polling extends Component{	
	
	/** 
	 * ===== UPDATE PILLING =======
     * POLLING UPDATE UUID, Setelah terlebih dahulu di create dari triger table.	  
	 * CLI : $polling=Yii::$app->polling->uuidUpdate($table,$primaryVal,$storeID,$action,$paramlUUID);
	*/
	 public function uuidUpdate($table ='none',$primaryVal='',$storeID='',$action,$paramlUUID){
		 $accessGroup=explode('.',$storeID); 				//0=ACCESS_GROUP;1=STORE_ID
		  $typeAction=strtoupper($action)=='CREATE'?1:(strtoupper($action)=='UPDATE'?2:(strtoupper($action)=='DELETE'?3:0));
		  if (isset($accessGroup)){
			  if ($accessGroup[0]<>''){
				  //==GET DATA POLLING
				    $arytest['table'][]=$table;
				    $arytest['accessGroup'][]=$accessGroup[0];
				    $arytest['storeID'][]=$storeID;
				    $arytest['primaryVal'][]=$primaryVal;
				    $arytest['paramlUUID'][]=$paramlUUID;
					$modelPoling=SyncPoling::find()->where([
						 'NM_TABLE'=>$table,
						 'ACCESS_GROUP'=>$accessGroup[0],
						 'STORE_ID'=>$storeID,
						 'PRIMARIKEY_VAL'=>$primaryVal
					])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->all();  //ARY_UUID=text (tidak bisa null harus gunakan empety)
					//==UPDATE DATA POLLING UUID
					if($modelPoling){							
						foreach($modelPoling as $row => $val){
							$modelSimpan=SyncPoling::find()->where([
								 'NM_TABLE'=>$table,
								 'ACCESS_GROUP'=>$accessGroup[0],
								 'STORE_ID'=>$storeID,
								 'PRIMARIKEY_VAL'=>$primaryVal,
								 'TYPE_ACTION'=>$typeAction
							])->andWhere("FIND_IN_SET('".$paramlUUID."',ARY_UUID)=0")->one();
							if($modelSimpan AND $paramlUUID){
								$modelSimpan->ARY_UUID=$modelSimpan->ARY_UUID.','.$paramlUUID;
								$modelSimpan->save();
							}
						}
						return array('message'=>'success');
					}else{
						 return array('message'=>'polling-not-exist');	
					}
					// return array('message'=>$modelPoling);
			 }else{
				 return array('message'=>'access_group-empty');
			 }
		  }else{
			 return array('message'=>'store-id-Invalid');
		  }
	 }	 
}