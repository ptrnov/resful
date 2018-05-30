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
use yii\data\ArrayDataProvider;


/** 
  * ARRAY HELEPER CUSTOMIZE
  * @author ptrnov  <piter@lukison.com>
  * @since 1.1
*/
class ArrayBantuan extends Component{	
	/**
	 * Array Sort
	 * Yii::$app->arrayBantuan->array_group_by($arr, $key)
	*/
	public static function array_group_by($arr, $key)
	{
		if (!is_array($arr)) {
			trigger_error('array_group_by(): The first argument should be an array', E_USER_ERROR);
		}
		if (!is_string($key) && !is_int($key) && !is_float($key)) {
			trigger_error('array_group_by(): The key should be a string or an integer', E_USER_ERROR);
		}
		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($arr as $value) {
			$grouped[$value[$key]][] = $value;
		}
		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2) {
			$args = func_get_args();
			foreach ($grouped as $key => $value) {
				$parms = array_merge([$value], array_slice($args, 2, func_num_args()));
				$grouped[$key] = call_user_func_array('array_group_by', $parms);
			}
		}
		return $grouped;
	}
	
	/**
	 * Find content on Column.
	 * Author	: ptr.nov@gmail.com.
	 * Update	: 15/02/2017.
	 *  Yii::$app->arrayBantuan->array_find($arr, $key,$value)
	*/
	function array_find($array, $keys, $searchContent)
	{	
		$rslt=[];
		foreach($array as $key => $value) {
			if(trim($value[$keys])===trim($searchContent)){
				$rslt[]=$value;
			}
		}
		return $rslt;
	}
	
	function ArrayPaletteColors(){
		$hexColor="#0B1234,#68acff,#00fd83,#e700c4,#8900ff,#fb0909,#0000ff,#ff4040,#7fff00,#ff7f24,#ff7256,#ffb90f,#006400,#030303,#ff69b4,#8b814c,#3f6b52,#744f4f,#6fae93,#858006,#426506,#055c5a,#a7630d,#4d8a9c,#449f9c,#8da9ab,#c4dfdd,#bf7793,#559e96,#afca84,#608e97,#806d88,#688b94,#b5dfe7,#b29cba,#83adb5,#c7bbc9,#2d5867,#e1e9b7,#bcd2d0,#f96161,#c9bbbb,#bfc5ce,#8f6d4d,#a87f99,#62909b,#a0acc0,#94b9b8";		
		return $hexColor;
	}
	function ArrayRowPaletteColors(){
		$hexColor=["#0B1234","#68acff","#00fd83","#e700c4","#8900ff","#fb0909","#0000ff","#ff4040","#7fff00","#ff7f24","#ff7256","#ffb90f","#006400","#030303","#ff69b4","#8b814c","#3f6b52","#744f4f","#6fae93","#858006","#426506","#055c5a","#a7630d","#4d8a9c","#449f9c","#8da9ab","#c4dfdd","#bf7793","#559e96","#afca84","#608e97","#806d88","#688b94","#b5dfe7","#b29cba","#83adb5","#c7bbc9","#2d5867","#e1e9b7","#bcd2d0","#f96161","#c9bbbb","#bfc5ce","#8f6d4d","#a87f99","#62909b","#a0acc0","#94b9b8"];		
		return $hexColor;
		// foreach($hexColor as $rows => $value){
			// $rslt[]=[$rows =>$value];
		// };
		// return $rslt;
	}
	
	//Yii::$app->arrayBantuan->strJson($strJson)
	public function strJson($json){
		$json = preg_replace('~(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)~', '', $json);
    	//	trailing commas
    	$json=preg_replace('~,\s*([\]}])~mui', '$1', $json);
    	//	empty cells
    	$json = preg_replace('~(.+?:)(\s*)?([\]},])~mui', '$1null$3', $json);
    	// $json = preg_replace('~.+?({.+}).+~', '$1', $json);
    	//	codes	//	@TODO: add \x
    	$json = str_replace(["\n","\r","\t","\0"], '', $json);
		return $json;
	}
}