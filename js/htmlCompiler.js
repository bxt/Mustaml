(function(undefined) {
	var self=function (parser,config){
		var config=config||{
			"htmlArrayAttrs":{"class":true,"rel":true,"rev":true},
			"htmlSelfclosingTags":{"br":true,"img":true,"input":true,"meta":true,"link":true,"hr":true,"frame":true,"param":true}
		};
		parser=parser||(mustaml.parser&&mustaml.parser())||require('./parser')();
		htmlCompilerAttrs=(mustaml.htmlCompilerAttrs&&mustaml.htmlCompilerAttrs())||require('./htmlCompilerAttrs')(config);
		
		var htmlCompiler={};
		
		function render(err,tmpl,data,cb) {
			if(err) cb(err);
			if(arguments.length==2) return render(err,tmpl,{},data);
			if(typeof(tmpl)=='string') tmpl=parser.parseString(tmpl);
			
			renderer[tmpl.type](err,tmpl,data,cb);
			
		}
		
		var renderer={};
		renderer.root=function(err,tmpl,data,cb) {
			renderChildren(err,tmpl,data,function(err,childrenHtml){
				cb(err,childrenHtml);
			})
		}
		renderer.htag=function(err,tmpl,data,cb) {
			var aHtml=false;
			var cHtml=false;
			htmlCompilerAttrs.render(err,tmpl,data,function(err,attrsHtml){
				if(err) return cb(err);
				aHtml=attrsHtml;
				callCb();
			});
			renderChildren(err,tmpl,data,function(err,childrenHtml){
				if(err) return cb(err);
				cHtml=childrenHtml;
				callCb();
			});
			function callCb() {
				if(aHtml!==false&&cHtml!==false) {
					var myhtml='';
					var selfClose=(cHtml===''&&config.htmlSelfclosingTags[tmpl.name]);
					myhtml+='<'+htmlspecialchars(tmpl.name)+aHtml;
					if(selfClose) {
						myhtml+=' />';
					} else {
						myhtml+='>';
						myhtml+=cHtml;
						myhtml+='</'+htmlspecialchars(tmpl.name)+'>';
					}
					cb(err,myhtml);
				}
			}
		}
		renderer.hecho=function(err,tmpl,data,cb) {
			cb(err,htmlspecialchars(data[tmpl.varname]));
		}
		renderer.notval=function(err,tmpl,data,cb) {
			var myval=data[tmpl.varname];
			if( !myval || (is_vector(myval) && !myval.length)) {
				renderChildren(err,tmpl,data,function(err,childrenHtml){
					cb(err,childrenHtml);
				});
			} else {
				cb(err,'');
			}
		}
		renderer.notnotval=function(err,tmpl,data,cb) {
			var myval=data[tmpl.varname];
			if( myval && (!is_vector(myval) || is_vector(myval) && myval.length)) {
				renderChildren(err,tmpl,data,function(err,childrenHtml){
					cb(err,childrenHtml);
				});
			} else {
				cb(err,'');
			}
		}
		renderer.val=function(err,tmpl,data,cb) {
			var myval=data[tmpl.varname];
			if(typeof myval==='function') { // summon black magic
				myval(err,data,cb)
			} else if(is_vector(myval)) { // register current (or subobj) and call block
				var noJobs=true;
				var anzChildren=0;
				var htmlChildren=[];
				if(myval.length) {
					noJobs=false;
					for(var i=0;i<myval.length;i++) {
						var baseData=extended(is_map(myval[i])?myval[i]:{'.':myval[i]},data);
						//console.log(baseData,);
						renderChildren(err,tmpl,baseData,function(err,html){
							if(err) return cb(err);
							htmlChildren[i]=html;
							anzChildren++;
							if((anzChildren==myval.length)) {
								cb(err,htmlChildren.join(''));
							}
						});
					}
				} else {
					cb(err,'');
				}
			} else if(myval && typeof myval==='object') { // switch scope
				var newdata=extended(myval,data);
				renderChildren(err,tmpl,newdata,function(err,childrenHtml){
					cb(err,childrenHtml);
				});
			} else if(myval===false) { //nothing
				cb(err,'');
			} else if(myval===true) { // render children
				renderChildren(err,tmpl,data,function(err,childrenHtml){
					cb(err,childrenHtml);
				});
			} else if(tmpl.children.length) { // whatever, register as . and call block
				var newdata=extended({'.':myval},data);
				renderChildren(err,tmpl,newdata,function(err,childrenHtml){
					cb(err,childrenHtml);
				});
			} else { // toString
				cb(err,myval.toString());
			}
		}
		renderer.text=function(err,tmpl,data,cb) {
			renderChildren(err,tmpl,data,function(err,childrenHtml){
				cb(err,htmlspecialchars(tmpl.contents)+childrenHtml);
			});
		}
		renderer.doctype=function(err,tmpl,data,cb) {
			cb(err,'<!DOCTYPE html>');
		}
		renderer.comment=function(err,tmpl,data,cb) {
			cb(err,'');
		}
		renderer.hcomment=function(err,tmpl,data,cb) {
			renderChildren(err,tmpl,data,function(err,childrenHtml){
				cb(err,'<!-- '+htmlcommentescape(childrenHtml)+' -->');
			})
		}
		
		
		function renderChildren(err,tmpl,data,cb) {
			var noJobs=true;
			var anzChildren=0;
			var htmlChildren=[];
			if(tmpl.children.length) {
				noJobs=false;
				for(var i=0;i<tmpl.children.length;i++) {
					render(err,tmpl.children[i],data,function(err,html){
						if(err) return cb(err);
						htmlChildren[i]=html;
						anzChildren++;
						if((anzChildren==tmpl.children.length)) {
							cb(err,htmlChildren.join(''));
						}
					});
				}
			} else {
				cb(err,'');
			}
		}
		
		function htmlcommentescape(str) {
			return str.replace(/--/g,'&#x2d;&#x2d;').replace(/>/g,'&gt;');
		}
		
		function htmlspecialchars(str) {
			return str;
		}
		
		function is_vector(a) {
			return Object.prototype.toString.call(a) === '[object Array]';
		}
		
		function is_map(a) {
			return a&&typeof a==='object';
		}
		
		function extended(a,b) {
			var name,e={};
			for (name in a) {
				if(a[name]!==undefined) {
					e[name]=a[name];
				}
			}
			for (name in b) {
				if(a[name]===undefined&&b[name]!==undefined) {
					e[name]=b[name];
				}
			}
			return e;
		}
		
		htmlCompiler.render=render;
		return htmlCompiler;
	};
	
	var mustaml={};
	if(this.mustaml!==undefined) {
		this.mustaml.htmlCompiler=self;
		mustaml=this.mustaml;
	} else if (typeof module!=='undefined' && module.exports) {
		module.exports = self;
	}
})();