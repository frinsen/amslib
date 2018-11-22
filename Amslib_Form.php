<?php
/*******************************************************************************
 * Copyright (c) {15/03/2008} {Christopher Thomas}
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
* Contributors/Author:
*    {Christopher Thomas} - Creator - chris.thomas@antimatter-studios.com
*
*******************************************************************************/

/**
 * 	class:	Amslib_Form
 *
 *	group:	core
 *
 *	file:	Amslib_Form.php
 *
 *	description: todo write description
 *
 * 	todo: write documentation
 *
 */
class Amslib_Form
{
	/**
	 * 	method:	selectOptions
	 *
	 * 	todo: write documentation
	 */
	static public function selectOptions($array,$selected=NULL,$indexText=NULL,$indexValue=NULL,$createAttributes=false)
	{
		$options = array();

		foreach(Amslib_Array::valid($array) as $arrayKey=>$item){
			if(is_array($item)){
				$text	=	$indexText && isset($item[$indexText])		?	$item[$indexText]	:	"";
				$value	=	$indexValue && isset($item[$indexValue])	?	$item[$indexValue]	:	"";
			}else if(is_string($item) && $indexText == "use_key"){
				$text	=	$item;
				$value	=	$arrayKey;
			}else{
				$text = $value = $item;
			}

			$attributes = array();
			if($createAttributes && is_array($item) && isset($item[$indexText]) && isset($item[$indexValue])){
				unset($item[$indexText],$item[$indexValue]);

				foreach($item as $k=>&$v) $v="$k='$v'";

				$attributes = $item;
			}
			$attributes = implode(" ",$attributes);

			if(strlen($text) == 0 || strlen($value) == 0) continue;

			$enabled = $value == $selected ? "selected='selected'" : "";

			$options[] = "<option $enabled value='$value' $attributes>$text</option>";
		}

		return implode("",$options);
	}

	/**
	 * 	method:	monthOptions
	 *
	 * 	todo: write documentation
	 */
	static public function monthOptions($start=-1,$stop=-1,$selected=NULL,$pad=NULL)
	{
		if($start < 0 || $start > 12) $start = 1;
		if($stop < 0 || $stop > 12) $stop = 12;

		$keys = range($start,$stop);

		$months = array();
		foreach($keys as $k){
			$index = $k;

			if($pad !== NULL && is_string($pad)){
				$index = str_pad($k,strlen($pad),$pad[0],STR_PAD_LEFT);
			}

			$months[$index] = date("F",mktime(0,0,0,$k));
		}

		return self::selectOptions($months,$selected,"use_key");
	}

	/**
	 * 	method:	numericSelectOptions
	 *
	 * 	todo: write documentation
	 *
	 * 	note: is this method deprecated now? seems so?
	 */
	static public function numericSelectOptions($start,$stop,$selected=NULL,$pad=NULL)
	{
		return self::numberSequenceToSelectOptions($start,$stop,$selected,$pad);
	}

	/**
	 * 	method:	numberSequenceToSelectOptions
	 *
	 * 	todo: write documentation
	 */
	static public function numberSequenceToSelectOptions($start,$stop,$selected=NULL,$pad=NULL)
	{
		$options = "";

		if(!is_numeric($start) || !is_numeric($stop)) return $options;

		foreach(range($start,$stop) as $a){
			$enabled = ($a == $selected) ? "selected='selected'" : "";

			if($pad !== NULL && is_string($pad)) $a = str_pad($a,strlen($pad),$pad[0],STR_PAD_LEFT);

			$options .= "<option $enabled value='$a'>$a</option>";
		}

		return $options;
	}

	/**
	 * 	method:	isChecked
	 *
	 * 	todo:	write documentation
	 *
	 * 	note:	this is a shorter, more compact, easier to remember version of the above and
	 * 			removes the duplication of having two methods identical functionality with different names
	 */
	static public function isChecked($value,$compare)
	{
		return $value == $compare ? "checked='checked'" : "";
	}

	/**
	 * 	method:	getFilename
	 *
	 * 	todo: write documentation
	 */
	static public function getFilename($name)
	{
		$file = Amslib_FILES::get($name);

		return ($file && isset($file["name"])) ? $file["name"] : false;
	}

	/**
	 * 	method:	getTempFilename
	 *
	 * 	todo: write documentation
	 */
	static public function getTempFilename($name)
	{
		$file = Amslib_FILES::get($name);

		return ($file && isset($file["tmp_name"])) ? $file["tmp_name"] : false;
	}

	/**
	 * 	method:	arrayToSelectOptions
	 *
	 * 	todo: write documentation
	 *
	 * 	DEPRECATED: use selectOptions
	 */
	static public function arrayToSelectOptions($array,$keyText,$keyValue,$selected=NULL)
	{
		return self::selectOptions($array,$selected,$keyText,$keyValue);
	}

	/**
	 * 	method:	selectRadioButton
	 *
	 * 	todo: write documentation
	 *
	 * 	DEPRECATED: use isChecked
	 */
	static public function selectRadioButton($value,$compare)
	{
		return self::isChecked($value,$compare);
	}

	/**
	 * 	method:	selectCheckbox
	 *
	 * 	todo: write documentation
	 *
	 * 	DEPRECATED: use isChecked
	 */
	static public function selectCheckbox($value,$compare)
	{
		return self::isChecked($value,$compare);
	}
}