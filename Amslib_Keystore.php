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
 * 	class:	Amslib_Keystore
 *
 *	group:	core
 *
 *	file:	Amslib_Keystore.php
 *
 *	description: Amslib key/value storage mechanism
 *
 * 	todo: write documentation
 *
 */
class Amslib_Keystore
{
	protected static $store = array();

	/**
	 * 	method:	set
	 *
	 * 	todo: write documentation
	 */
	static public function set($name,$value)
	{
		if(!is_string($name)) return NULL;

		self::$store[$name] = $value;

		return $value;
	}

	/**
	 * 	method:	setBoolean
	 *
	 * 	todo: write documentation
	 */
	static public function setBoolean($name,$value)
	{
		return self::set($name,is_bool($value)?$value:NULL);
	}

	/**
	 * 	method:	has
	 *
	 * 	todo: write documentation
	 */
	static public function has($name)
	{
		return (isset(self::$store[$name])) ? true : false;
	}

	/**
	 * 	method:	get
	 *
	 * 	todo: write documentation
	 */
	static public function add($name,$value)
	{
		if(!isset(self::$store[$name])){
			self::$store[$name] = array();
		}
		
		if(!is_array(self::$store[$name])){
			return false;
		}
		
		if(!is_string($name)){
			return false;
		}

		self::$store[$name][] = $value;

		return self::get($name);
	}

	/**
	 * 	method:	get
	 *
	 * 	todo: write documentation
	 */
	static public function get($name,$default=NULL)
	{
		return (isset(self::$store[$name])) ? self::$store[$name] : $default;
	}

	/**
	 * 	method:	getAll
	 *
	 * 	todo: write documentation
	 */
	static public function getAll()
	{
		return self::$store;
	}
}