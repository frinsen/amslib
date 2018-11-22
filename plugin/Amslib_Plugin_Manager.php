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
 * 	class:	Amslib_Plugin_Manager
 *
 *	group:	plugin
 *
 *	file:	Amslib_Plugin_Manager.php
 *
 *	description:
 *		An object to store all the plugins and provide a
 *		central method to access them all
 *
 * 	todo:
 * 		write documentation
 *
 */
class Amslib_Plugin_Manager
{
	static protected $plugins	=	array();
	static protected $api		=	array();
	static protected $location	=	array();
	static protected $replace	=	array();
	static protected $alias		=	array();
	static protected $block		=	array();
	static protected $import	=	array();
	static protected $export	=	array();

	const ERROR_PLUGIN_INVALID = "plugin was invalid";

	/**
	 * 	method:	findPlugin
	 *
	 * 	todo: write documentation
	 */
	static protected function findPlugin($name,$location=NULL)
	{
		$search = array_merge(array($location),self::$location);

		foreach($search as $location)
		{
			//	TODO: this obviously hardcodes the xml configuration filename, perhaps this is not ideal
			if(file_exists("$location/$name/package.xml")){
				//	double check that the location starts and ends with a slash
				//	something this isn't the case and the programmer forgets
				//	then the plugin doesnt load, all because of a simple missing slash
				return Amslib_File::reduceSlashes("/$location/");
			}
		}

		return false;
	}

	/**
	 * 	method:	setPluginReplace
	 *
	 * 	todo: write documentation
	 *
	 * 	note:
	 * 	-	This function will map plugin_ref => replace_by, so any attempt to load plugin_ref will
	 * 		result in loading replace_by
	 * 	-	This means you need to be able to load replace_by AT THE POINT IN TIME that plugin_ref
	 * 		is loaded, if you require something more, you're going to find trouble.
	 */
	static public function setPluginReplace($plugin_ref,$replace_by)
	{
		if(!is_string($plugin_ref) && strlen($plugin_ref) == 0) return false;
		if(!is_string($replace_by) && strlen($replace_by) == 0) return false;

		self::$replace[$plugin_ref] = $replace_by;
	}

	/**
	 * 	method:	setupPluginAlias
	 *
	 *	Setup a plugin to be an alias of another, allowing you to query plugins by another, possibly more common name
	 */
	static public function setPluginAlias($plugin_ref,$alias_with)
	{
		if(!is_string($plugin_ref) && strlen($plugin_ref) == 0) return false;

		self::$alias[$alias_with] = $plugin_ref;
	}

	/**
	 * 	method:	setPluginBlock
	 *
	 * 	Block a plugin from being loaded into the system
	 */
	static public function setPluginBlock($plugin)
	{
		if(!is_string($plugin) && strlen($plugin) == 0) return false;

		self::$block[$plugin] = true;
	}

	/**
	 * 	method:	config
	 *
	 * 	todo: write documentation
	 */
	static public function config($name,$location)
	{
		if(!is_string($name) || is_bool($name)){
			Amslib_Debug::log("stack_trace","Error attempting to configure plugin, invalid name",$name);

			return false;
		}

		//	If this plugin is configured to not load, return false
		if(isset(self::$block[$name])) return false;

		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name])) $name = self::$replace[$name];

		//	Plugin was already loaded, so return it's Plugin Object directly
		if(self::isLoaded($name)) return self::$plugins[$name];

		if($location = self::findPlugin($name,$location)){
			//	Plugin was not present, so create it, load everything required and return it's API
			self::$plugins[$name] = new Amslib_Plugin();
			self::$plugins[$name]->setLocation($location.$name);
			//	NOTE: This is hardcoding it to only work with an XML config source
			//	NOTE: The reason for this is because I Don't really have an alternative plan yet
			self::$plugins[$name]->setConfigSource(new Amslib_Plugin_Config_XML());
			self::$plugins[$name]->config($name);

			return self::$plugins[$name];
		}

		//	Plugin was not found
		return false;
	}

	/**
	 * 	method:	load
	 *
	 * 	todo: write documentation
	 */
	static public function load($name,$location=NULL)
	{
		//	If this plugin is configured to not load, return false
		if(isset(self::$block[$name])) return false;

		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name])) $name = self::$replace[$name];

		$p = self::config($name,$location);

		//	If the plugin failed to load, you need to return false to indicate an error
		if(!$p) return false;

		//	Process any import/export directives
		$p->transfer();

		//	Load the plugin and all it's children and resources
		$p->load();

		//	Insert the plugin, or remove it if something has failed
		if(self::insert($name,$p) == false){
			//	TODO: a plugin failed to insert, we should trigger some kind of error to be logged
			self::remove($name);
		}

		//	Obtain the API object, or false if it doesn't exist
		return self::getAPI($name);
	}

	/**
	 * 	method:	insert
	 *
	 * 	todo: write documentation
	 */
	static public function insert($name,$plugin)
	{
		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name])) $name = self::$replace[$name];

		if($name && $plugin){
			self::$plugins[$name] = $plugin;

			$api = $plugin->getAPI();

			if($api){
				self::$api[$name] = $api;

				return true;
			}
		}

		return false;
	}

	/**
	 * 	method:	remove
	 *
	 * 	todo: write documentation
	 */
	static public function remove($name)
	{
		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name])) $name = self::$replace[$name];

		$r = self::$plugins[$name];

		unset(self::$plugins[$name],self::$api[$name]);

		return $r;
	}

	/**
	 * 	method:	getAPI
	 *
	 * 	todo: write documentation
	 */
	static public function getAPI($name)
	{
		if($name instanceof Amslib_MVC) return $name;

		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name]))	$name = self::$replace[$name];
		if(isset(self::$alias[$name]))		$name = self::$alias[$name];

		return is_string($name) && isset(self::$api[$name]) ? self::$api[$name] : false;
	}

	/**
	 * 	method:	setAPI
	 *
	 * 	todo: write documentation
	 */
	static public function setAPI($name,$api)
	{
		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name])) $name = self::$replace[$name];

		self::$api[$name] = $api;
	}

	/**
	 * 	method:	getPlugin
	 *
	 * 	todo: write documentation
	 */
	static public function getPlugin($name)
	{
		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name])) $name = self::$replace[$name];

		return is_string($name) && isset(self::$plugins[$name]) ? self::$plugins[$name] : false;
	}

	/**
	 * 	method:	listPlugin
	 *
	 * 	todo: write documentation
	 */
	static public function listPlugin($testObject=false)
	{
		return $testObject
			? array_map("get_class",self::$plugins)
			: array_keys(self::$plugins);
	}

	/**
	 * 	method:	listAPI
	 *
	 * 	todo: write documentation
	 */
	static public function listAPI($testObject=false)
	{
		return $testObject
			? array_map("get_class",self::$api)
			: array_keys(self::$api);
	}

	/**
	 * 	method:	isLoaded
	 *
	 * 	todo: write documentation
	 */
	static public function isLoaded($name)
	{
		//	If this plugin is configured to be replaced with another, use the replacement
		if(isset(self::$replace[$name])) $name = self::$replace[$name];

		return isset(self::$plugins[$name]) ? true : false;
	}

	/**
	 * 	method:	addLocation
	 *
	 * 	todo: write documentation
	 */
	static public function addLocation($location)
	{
		self::$location[] = Amslib_File::absolute($location);
	}

	/**
	 * 	method:	getLocation
	 *
	 * 	todo: write documentation
	 */
	static public function getLocation()
	{
		return self::$location;
	}

	/**
	 *	method: addImport
	 *
	 *	Add a new piece of data to be imported from one plugin to another
	 *
	 *	parameters:
	 *		$source			-	the source plugin to import from
	 *		$destination	-	the destination plugin to import to
	 *		$key			-	the data key to import
	 *		$value			-	the value within that data key
	 */
	static public function addImport($source,$destination,$key,$value)
	{
		$queue = !is_string($source) && is_object($source) ? $source->getName() : $source;

		self::$import[$queue][] = array("src"=>$source,"dst"=>$destination,"key"=>$key,"val"=>$value);
	}

	/**
	 * 	method:	addExport
	 *
	 * 	Add a new piece of data to be exported from one plugin to another
	 *
	 * 	parameters:
	 * 		$source			-	the source plugin to export from
	 * 		$destination	-	the destination plugin to export to
	 * 		$key			-	the data key to export
	 * 		$value			-	the value within that data key
	 */
	static public function addExport($source,$destination,$key,$value)
	{
		$queue = !is_string($source) && is_object($source) ? $source->getName() : $source;

		self::$export[$queue][] = array("src"=>$source,"dst"=>$destination,"key"=>$key,"val"=>$value);
	}

	static public function processImport()
	{
		foreach(self::$import as $key=>$list) foreach($list as $value){
			$src	= self::getPlugin($value["src"]);
			$dst	= is_string($value["dst"]) ? self::getPlugin($value["dst"]) : $value["dst"];
			$data	= false;

			if(!$src || !$dst){
				$sname = ($ts=is_object($src)) ? $src->getName() : "searched: {$value["src"]}";
				$dname = ($td=is_object($dst)) ? $dst->getName() : "searched: {$value["dst"]}";

				Amslib_Debug::log(self::PLUGIN_INVALID,intval($ts),intval($ts),$sname,$dname);
				continue;
			}

			switch($value["key"]){
				//	The new package format has upgraded a lot of things, these are the non-so-used
				//	upgrades which havent needed upgrading yet, so I've left them until I have more time.
				case "view":
				case "stylesheet":
				case "javascript":
				case "font":{
					$message = "[DIE]IMPORT[$key] => ".Amslib_Debug::pdump(true,array(
							$src->getName(),
							$dst->getName(),
							$value["key"],
							$value["val"]
					));
					Amslib_Debug::log($message);
					die($message);
				}break;

				case "translator":{
					$data = $src->getValue($value["key"],$value["val"]["name"]);
					$dst->setValue($value["key"],$data);
				}break;

				case "service":{
					/*
					 //	TODO	we are processing an xml block named "service" but allowing the service to be a route????
					//	FIXME:	this is obviously fucking stupid....perhaps the block should be called "router" or done
					//			a different way, because this is obviously not the right way
					$r = $item["service"] == "true"
					? Amslib_Router::getService($item["name"],$item["plugin"])
					: Amslib_Router::getRoute($item["name"],$item["plugin"]);

					Amslib_Router::setRoute($item["rename"],$this->getName(),NULL,$r,false);

					//	the following parameters are available
					//
					//	plugin, name, service, rename
					//		plugin => src plugin to obtain service from
					//		name => the service to obtain
					//		service => whether it's a service or path route
					//		rename => the local name to store the copy in
					*/
				}break;

				//	We do nothing special with these entries, we simply pass them
				case "value":
				case "image":
				//case "model":
				default:{
					//	NOTE: why do I not use $value["val"]["name"] here? like I do with translators?
					$data = $src->getValue($value["key"]);
					$dst->setValue($value["key"],$data);
				}break;

				case "model":{
					//	NOTE: why do I not use $value["val"]["name"] here? like I do with translators?
					$data = $src->getValue($value["key"]);
					$dst->setValue($value["key"],$data);
				}break;
			}
		}

		self::$import = NULL;
	}

	static public function processExport()
	{
		foreach(self::$export as $key=>$list) foreach($list as $name=>$value){
			$src	= is_string($value["src"]) ? self::getPlugin($value["src"]) : $value["src"];
			$dst	= is_string($value["dst"]) ? self::getPlugin($value["dst"]) : $value["dst"];
			$data	=	false;

			if(!$src || !$dst){
				$sname = is_object($src) ? $src->getName() : "searched: {$value["src"]}";
				$dname = is_object($dst) ? $dst->getName() : "searched: {$value["dst"]}";

				Amslib_Debug::log("plugin list",Amslib_Plugin_Manager::listPlugin());
				Amslib_Debug::log("plugin invalid",intval(is_object($src)).", ".intval(is_object($dst)),$sname,$dname,Amslib_Router::getPath());
				continue;
			}

			switch($value["key"]){
				case "stylesheet":
				case "javascript":
				case "font":{
					die("[DIE]EXPORT[$key] => ".Amslib_Debug::pdump(true,array(
							$src->getName(),
							$dst->getName(),
							$value["key"],
							$value["val"]
					)));
				}break;

				case "view":
				case "value":{
					//	NOTE:	if I could change getValue to this, I could refactor all of these branches
					//			together maybe into something very generic
					//	NOTE:	the new import/export system works slightly differently from the old one,
					//			we push directly into the import/export queues the information that we
					//			want to pass and it doesn't enter the host plugin, this way, we can skip
					//			a lot of bullshit with regard to internal data and data which is destined
					//			for other plugins, the getValue method should in this case, circumstantially
					//			create objects or just parse the data out of the structure, but it's not
					//			about "getting" the value from the pluing, the $value variable already has
					//			it and in many cases we don't need to do anything except return a particular
					//			key depending on the stucture or type of that data, but in the case of
					//			translators, objects or models, we need to ask the host plugin to create
					//			the object on our behalf and then return and use it, because it might be
					//			that the host plugin is the only plugin which has the correct functionality
					//			necessary to create that object, in these cases getValue will do more than
					//			just return a particular key, but will actually process the input data into
					//			an "output data" to use
					//$data = $src->getValue($value);
					$dst->setValue($value["key"],$value["val"]);
				}break;

				case "service":{
					//Amslib_FirePHP::output("export",$item);
					//	Hmmm, I need a test case cause otherwise I won't know if this works
				}break;

				case "image":{
					$dst->setValue($value["key"],$value["val"]);
				}break;

				//	We do nothing special with these entries, we simply pass them
				case "model":
				case "translator":
				default:{
					//	NOTE: I should change $value["key"] here to $value and make "key" something getValue uses internally
					$data = $src->getValue($value["key"]);
					$dst->setValue($value["key"],$data);
				}break;
			}
		}

		self::$export = NULL;
	}

	/*************** DEPRECATED METHODS BELOW *******************/
	private function _____DEPRECATED_METHODS_BELOW(){}

	static public function render($plugin,$view="default",$parameters=array())
	{
		Amslib_Debug::log("DEPRECATED METHOD","stack_trace");
		return Amslib_Plugin::render($plugin,$view,$parameters);
	}

	static public function renderView($plugin,$view="default",$parameters=array())
	{
		Amslib_Debug::log("DEPRECATED METHOD");
		return Amslib_Plugin::renderView($plugin,$view,$parameters);
	}

	static public function getObject($plugin,$id,$singleton=false)
	{
		Amslib_Debug::log("DEPRECATED METHOD");
		return Amslib_Plugin::getObject($plugin,$id,$singleton);
	}

	static public function setStylesheet($plugin,$id,$file,$conditional=NULL)
	{
		Amslib_Debug::log("DEPRECATED METHOD");
		return Amslib_Plugin::setStylesheet($plugin,$id,$file,$conditional);
	}

	static public function addStylesheet($plugin,$stylesheet)
	{
		Amslib_Debug::log("DEPRECATED METHOD");
		return Amslib_Plugin::addStylesheet($plugin,$stylesheet);
	}

	static public function setJavascript($plugin,$id,$file,$conditional=NULL)
	{
		Amslib_Debug::log("DEPRECATED METHOD");
		return Amslib_Plugin::setJavascript($plugin,$id,$file,$conditional);
	}

	static public function addJavascript($plugin,$javascript)
	{
		Amslib_Debug::log("DEPRECATED METHOD");
		return Amslib_Plugin::addJavascript($plugin,$javascript);
	}

	static public function preload($name,$plugin)
	{
		Amslib_Debug::log("DEPRECATED METHOD");
		return self::insert($name,$plugin);
	}

	static public function listPlugins()
	{
		Amslib_Debug::log("DEPRECATED METHOD","stack_trace");
		return self::listPlugin();
	}
}
