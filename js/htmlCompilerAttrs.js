(function(undefined) {
	var self=function (config){
		var htmlCompilerAttrs={};
		
		function render(err,tmpl,data,cb) {
			if(err) cb(err);
			var noJobs=true;
			var anzAttributes=0;
			var rawAttributes={};
			if(tmpl.attributes&&tmpl.attributes.length) {
				noJobs=false;
				for(var i=0;i<tmpl.attributes.length;i++) {
					renderer[tmpl.attributes[i].type](err,tmpl.attributes[i],data,function(err,attrName,attrVal,dontcount){
						if(err) return cb(err);
						if(attrName) rawAttributes[attrName]=(rawAttributes[attrName]||[]).concat(attrVal);
						anzAttributes+=(dontcount?0:1);
						if(anzAttributes==tmpl.attributes.length) {
							finilaze(err,rawAttributes,cb);
						}
					});
				}
			}
			if(noJobs) {
				cb(err,'');
			}
		}
		
		function finilaze(err,rawAttributes,cb) {
			if(err) return cb(err);
			var html='';
			for(var name in rawAttributes) {
				switch(rawAttributes[name].length) {
					case 0: html+=' '+htmlspecialchars(name)+'="'+htmlspecialchars(name)+'"';continue;
					case 1: html+=' '+htmlspecialchars(name)+'="'+htmlspecialchars(rawAttributes[name][0])+'"';continue;
					default: html+=' '+htmlspecialchars(name)+'="'+htmlspecialchars((config.htmlArrayAttrs[name]?rawAttributes[name].join(' '):rawAttributes[name][rawAttributes[name].length-1]))+'"';continue;
				}
			}
			cb(err,html);
		}
		
		var renderer={};
		renderer.attr=function(err,tmpl,data,cb) {
			var noJobs=true;
			var name=tmpl.name;
			var anzSubnodes=0;
			var cntSubnodes=[];
			if(tmpl.children&&tmpl.children.length) {
				noJobs=false;
				for(var i=0;i<tmpl.children.length;i++) {
					renderer['inner'+tmpl.children[i].type](err,tmpl.children[i],data,function(err,content){
						if(err) return cb(err);
						cntSubnodes[i]=content;
						anzSubnodes++;
						if(anzSubnodes==tmpl.children.length) {
							cb(err,name,[cntSubnodes.join('')]);
						}
					});
				}
			}
			if(noJobs) {
				cb(err,name,[]);
			}
		}
		renderer.val=function(err,tmpl,data,cb) {
			if(!data[tmpl.varname]) return cb(err);
			var counted=false;
			for(var name in data[tmpl.varname]) {
				if(counted) {
					cb(err,name,data[tmpl.varname][name],true);
				} else {
					counted=true;
					cb(err,name,data[tmpl.varname][name]);
				}
			}
		}
		renderer.innerval=function(err,tmpl,data,cb) {
			if(data[tmpl.varname]) {
				cb(err,data[tmpl.varname]);
			} else {
				cb(err,'');
			}
		}
		renderer.innertext=function(err,tmpl,data,cb) {
			cb(err,tmpl.contents);
		}
		
		function htmlspecialchars(str) {
			return str;
		}
		
		htmlCompilerAttrs.render=render;
		return htmlCompilerAttrs;
	};
	
	var mustaml={};
	if(this.mustaml!==undefined) {
		this.mustaml.htmlCompilerAttrs=self;
		mustaml=this.mustaml;
	} else if (typeof module!=='undefined' && module.exports) {
		module.exports = self;
	}
})();