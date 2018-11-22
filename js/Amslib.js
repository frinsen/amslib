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
 * file: Amslib.js
 * title: Antimatter core javascript
 *
 * Contributors/Author:
 *    {Christopher Thomas} - Creator - chris.thomas@antimatter-studios.com
 *******************************************************************************/
if(typeof(my) == "undefined" || typeof(my.Class) == "undefined"){
	 throw("Amslib.js: requires my.[class/common] to be loaded first");
}

;(function(window,jQuery){
	var waiting 	=	{},
		completed	=	{},
		undef		=	"undefined";
	
	//	Protect against jQuery not being installed before the wait api, but allow the code to run, not causing fatal errors
	if(typeof(jQuery) == undef){
		var error = function(){
			throw("Amslib.js: jQuery.Deferred is required for the wait API");
		};
		
		window.wait = {until:error,resolve:error};
		
		return error();
	}
	
	window.wait = {
		until: function()
		{
			var def = [], done = false, fail = false, list = [].slice.call(arguments); 
			
			for(i=0,l=list.length;i<l;i++){
				var arg = list[i];
				
				switch(typeof(arg)){
					case "string":{
						if(typeof(waiting[arg]) == undef){
							waiting[arg] = jQuery.Deferred();
						}
						
						if(typeof(completed[arg]) != undef){
							waiting[arg].resolve.apply(waiting[arg],completed[arg]);
						}
						
						def.push(waiting[arg]);
					}break;
					
					case "function":{
						if(!done) done = arg;
						if(!fail) fail = arg;
					}
				}
			}

			var w = jQuery.when.apply(jQuery,def);

			if(done) w.done(done);
			if(fail) w.fail(fail);
			
			return w;
		},
		
		resolve: function(name)
		{
			var a = [].slice.call(arguments,1), 
				w = waiting[name];

			if(typeof(w) != undef){
				w.resolve.apply(w,a);
			}
			
			completed[name] = a;
		}
	};
})(window,jQuery);

// make it safe to use console.log always
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());

var Amslib = my.Amslib = my.Class({
	parent:					false,
	__controller:			false,
	//	we "abuse" jquery dom-data functionality to store groups of data
	__value:				false,
	__services:				false,
	__translation:			false,
	__images:				false,
	//	now we're "abusing" it all over the place, here we use it to store custom events
	__events:				false,
	
	STATIC: {
		autoload: function()
		{		
			wait.resolve("Amslib",Amslib);
		},
		
		options: {
			controller: "Default_Controller"
		},
		
		getController: function(amslib_object)
		{
			console.log("POSSIBLY FAULTY CODE DETECTED","getController executed: I don't think this function works");
			if(typeof(amslib_object.instances) != "undefined" && amslib_object.instances != false){
				return amslib_object.instances.data(amslib_object.options.amslibName);
			}
			
			return false;
		},
		
		firebug: function()
		{
			console.log("DEPRECATED, STOP USING THIS METHOD (Amslib.firebug)");
			//	NOTE: This is suspiciously similar to paulirishes window.log method shown above in the autoload method
			//	NOTE: apparently some people found this function would cause an error in google chrome, dunno why.
			if(console && console.log) console.log.apply(console,arguments);
		},
		
		//	DEPRECATED getPath, use Amslib.locate() instead, it does exactly what I was supposed to do here
		getPath: function(file)
		{
			console.log("DEPRECATED, STOP USING THIS METHOD (Amslib.getPath)");
			//	Copied from how scriptaculous does it's "query string" thing
			var re 		=	new RegExp("^(.*?)"+file.replace(/[-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")+"$","g");
			var path	=	false;
			
			$("script[src]").each(function(){
				var matches = re.exec(this.src);
				
				if(matches){
					path = matches[1]; 
					return false;
				}
			});
			
			if(!path) Amslib.firebug("requested path["+file+"] using regexp["+re+"] was not found");
				
			return path;
		},
		
		locate: function()
		{
			Amslib.__location = Amslib.js.find("/js/Amslib.js",true);
			
			return Amslib.__location || false;
		},

		getQuery: function()
		{
			console.log("DEPRECATED, STOP USING THIS METHOD (Amslib.getQuery)");
			var p = function(s){
				var e,
		        a = /\+/g,  // Regex for replacing addition symbol with a space
		        r = /([^&=]+)=?([^&]*)/g,
		        d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
		        f = {};
		        
		        if(i=s.indexOf("?")) s = s.substring(i+1);
		        
		        while(e = r.exec(s)){
		        	var k = d(e[1]), v = d(e[2]);
		        	
		        	//	This works with arrays of url params[]
		        	if(k.indexOf("[]")>=0){
		        		if(!f[k]) f[k] = new Array();
		        		f[k].push(v);
		        	}else{
		        		f[k] = v;
		        	}
		        }
		        
		        return f;
			};
			
			if(arguments.length == 1) return p(arguments[0]);
			if(arguments.length >= 2) return (f=p(arguments[1])) && (arguments[0] in f) ? f[arguments[0]] : f;
			
			return false;
		},
		
		//	DEPRECATED API, use Amslib.[js,css].* instead
		loadJS: function(name,file,callback){	Amslib.js.load(name,file,callback);	},
		hasJS: function(name,callback){			Amslib.js.has(name,callback);		},
		getJSPath: function(search,path){		return Amslib.js.find(search,path);	},
		loadCSS: function(file){				Amslib.css.load(file);				},
		
		__urlParams:	[],
		__location:		false
	},	
	
	constructor: function(parent,name)
	{
		this.parent = $(parent) || false;
		if(!this.parent) return false;
		
		this.__controller = name || Amslib.options.controller;
		
		//	Setup the amslib_controller to make this object available on the node it was associated with
		Amslib.controller.set(this.parent,this.__controller,this);
		
		this.__value		= $("<div/>");
		this.__services		= $("<div/>");
		this.__translation	= $("<div/>");
		this.__images		= $("<div/>");
		this.__events		= this.parent;
		
		this.readMVC();

		return this;
	},
	
	readMVC: function()
	{
		try{
			var mvc	=	this.parent.find(".__amslib_mvc_values");
			
			if(mvc.length == 0) return;

			var input	=	mvc.find("input[type='hidden']");
			var data	=	{};
			
			if(input.length){
				//	interpret input values
				input.each(function(){
					data[$(this).attr("name")] = $(this).val();
				});
			}else{
				data = $.parseJSON(mvc.text());
			}
			
			for(k in data){
				if(k.indexOf("service:") >=0){
					this.setService(k.replace("service:",""),data[k]);
				}else if(k.indexOf("translation:") >=0){
					this.setTranslation(k.replace("translation:",""),data[k]);
				}else if(k.indexOf("image:") >=0){
					this.setImage(k.replace("image:",""),data[k]);
				}else{
					this.setValue(k,data[k]);
				}
			}	
		}catch(e){
			console.log("Exception caused whilst reading Amslib.readMVC");
			console.log(e);
		}
	},
	
	getAmslibName: function()
	{
		return this.__controller;
	},
	
	getParentNode: function()
	{
		return this.parent;
	},
	
	bind: function(event,callback,live)
	{
		console.log("DEPRECATED, STOP USING THIS METHOD (Amslib.bind)");
		this.__events.bind(event,callback);
	},
	
	on: function(event,callback)
	{
		console.log("DEPRECATED, STOP USING THIS METHOD (Amslib.on)");
		this.__events.on(event,callback);
	},
	
	live: function(event,callback)
	{
		console.log("DEPRECATED, STOP USING THIS METHOD (Amslib.live)");
		this.on(event,callback);
	},
	
	trigger: function(event,data)
	{
		console.log("DEPRECATED, STOP USING THIS METHOD (Amslib.trigger)");
		this.__events.trigger(event,Array.prototype.slice.call(arguments,1));
	},
	
	//	Getter/Setter for the object values
	setValue: function(name,value){			this.__value.data(name,value);			},
	getValue: function(name){				return this.__value.data(name);			},
	
	//	Getter/Setter for web services
	setService: function(name,value){		this.__services.data(name,value);		},
	getService: function(name){				return this.__services.data(name);		},
	
	//	Getter/Setter for text translations
	setTranslation: function(name,value){	this.__translation.data(name,value);	},
	getTranslation: function(name){			return this.__translation.data(name);	},
	
	//	Getter/Setter for images
	setImage: function(name,value){			this.__images.data(name,value);			},
	getImage: function(name){				return this.__images.data(name);		}
});

Amslib.controller = {
	set: function(node,name,controller)
	{
		node = $(node);
		
		if(name != false){
			name = "Amslib.controller."+name;
		}
		
		return node && node.length ? node.data(name,controller) : controller;
	},
	
	get: function(node,name)
	{
		node = $(node);
		
		if(node && node.length){
			return node.data("Amslib.controller."+name);
		}
		
		console.log("the requested controller was not found",name);
		
		return $();
	},
	
	remove: function(node,name)
	{
		if(!Amslib.controller.set(node,false)){
			console.log("the requested controller could not be removed",name);
		}
	}
};

Amslib.wait = {
	until:		window.wait.until,
	resolve:	window.wait.resolve
};

////////////////////////////////////////////////////////////////////
//The JS API
//
//This API will allow you to quickly and simply load a javascript
//and apply a function that will wait until it's loaded to execute
//
//NOTE: refactor this against the wait API, it's cleaner
//NOTE: if I can refactor it, I'm having second thoughts
////////////////////////////////////////////////////////////////////

Amslib.js = {
	handles: {},
	
	load: function(name,filename,callback)
	{	
		if(typeof(require) != "function" || typeof(scope) != "function"){
			console.log("ERROR[Amslib.js.load]: Functions 'require' or 'scope' are not available");
			console.log("ERROR[Amslib.js.load]: Did you load my.common into your website?");
			return;
		}
		
		if(typeof(Amslib.js.handles[name]) == "undefined"){
			Amslib.js.handles[name] = require(filename);
		}
		
		scope(function(){
			wait.resolve(name);
			
			if(typeof(callback) == "function"){
				callback();
			}
		},Amslib.js.handles[name]);
	},
	
	loadSeq: function()
	{
		var args = [].slice.call(arguments);
		
		if(args.length < 2) return;
		
		var trigger = args.shift();
		//	NOTE: what does "prev" mean? previous?
		var prev = args.shift();
		
		//	If you don't have this, lets be harsh and cancel the whole thing
		if(typeof(prev) != "object" || prev.length < 2) return;
		
		Amslib.js.load.apply(null,prev);
		
		var list = [prev[0]];
		
		for(a in args){
			var item = args[a];
			
			//	if this item doesn't have the right parameters, skip it
			if(typeof(item) != "object" || item.length < 2){
				continue;
			}

			Amslib.js.has(prev[0],function(){
				Amslib.js.load.apply(null,item);
			});
			
			prev = item;
			
			list.push(prev[0]);
		}
		
		list.push(function(){
			wait.resolve.resolve(trigger);
		});
		
		wait.until.apply(null,list);
	},
	
	//	a variable number of names and possible two callbacks for success and failure
	//	if you pass has(a,b,c,d,function(){}) it'll treat a,b,c,d like names to succeed 
	//	together and run the function as a success callback
	has: function(name,callback)
	{
		return wait.until.apply(null,arguments);
	},
	
	find: function(search,path)
	{
		var s = $("script[src*='"+search+"']").attr("src");
		
		return s && path ? s.split(search)[0] : s;
	}
};

Amslib.css = {
	load: function(file)
	{
		$("head").append($("<link/>").attr({rel:"stylesheet",type:"text/css",href: file}));
	}		
};

$(document).ready(Amslib.autoload);