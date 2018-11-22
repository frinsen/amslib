<?php
class Amslib_REQUEST extends Amslib_GLOBAL
{
	static public function has($key)
	{
		return self::hasIndex($_REQUEST,$key);
	}

	/**
	 * 	method:	set
	 *
	 * 	todo: write documentation
	 */
	static public function set($key,$value)
	{
		return self::setIndex($_REQUEST,$key,$value);
	}

	/**
	 * 	function:	get
	 *
	 * 	Obtain a parameter from the REQUEST global array
	 *
	 * 	parameters:
	 * 		$value		-	The value requested
	 * 		$default	-	The value to return if the value does not exist
	 * 		$erase		-	Whether or not to erase the value after it's been read
	 *
	 * 	returns:
	 * 		-	The value from the REQUEST global array, if not exists, the value of the parameter return
	 */
	static public function get($key,$default=NULL,$erase=false)
	{
		return self::getIndex($_REQUEST,$key,$default,$erase);
	}

	static public function delete($key)
	{
		return self::deleteIndex($_REQUEST,$key);
	}
	
	static public function dump()
	{
		return parent::dump($_REQUEST);
	}
}