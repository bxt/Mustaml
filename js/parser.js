(function(undefined) {
	var self=function (ast){
		ast=mustaml.ast||require('./ast');
		scanner=mustaml.scanner||require('./scanner');
		attrParser=mustaml.attrParser||require('./attrParser');
		
		var parser={};
		
		var LINE_REGEX=/^([\t ]*)(.*)/;
		var MULTINODE_REGEX=/^(.+?)( (.*))?$/;
		var TAGNODE_REGEX=/^(%(.+?))?(\#(.+?))?((\..+?)*)(\((.*?)\))?(\ (.*))?$/;
		
		var restNodecode=false;
		
		function array_sum (array) {
			var sum=0;
			for(var i=0;i<array.length;i++) {
				sum+=array[i];
			}
			return sum;
		}
		
		function parseString(templateString) {
			var rootnode=ast.node('root');
			var lines=templateString.split('\n');
			var indentLevels=[];
			var parentBlocks=[];
			parentBlocks[-1]=rootnode;
			for(var i=0;i<lines.length;i++) {
				var startWs=lines[i].match(LINE_REGEX);
				var indent=startWs[1].replace('\t','        ').length;
				var nodecode=startWs[2];
				if(nodecode=='') continue; // ignore empty lines
				var preIndent=array_sum(indentLevels);
				if(indent>preIndent) {
					indentLevels.push(indent-preIndent);
				}
				if(indent<preIndent) {
					for(var isum=0,change=preIndent-indent;isum<change;isum+=indentLevels.pop());
					if(isum>change) throw "Syntax Error: Indent-error";
				}
				var level=indentLevels.length;
				restNodecode=nodecode;
				var parentNode=parentBlocks[level-1];
				while (restNodecode) {
					var node=parseNode(restNodecode);
					parentNode.children.push(node);
					parentNode=node;
				}
				parentBlocks[level]=parentNode;
			}
			return rootnode;
		}
		
		function parseNode(nodecode) {
			restNodecode=false; // usualy we don't expect more nodes in this line
			switch(true) {
				case (nodecode[0]=='/'): return parse.hcomment(nodecode);
				case (!nodecode[1]): return parse.text(nodecode);// all following need at least 2 chars
				case (nodecode[0]=='-'): switch(true) {
					case (nodecode[1]=='/'): return parse.comment(nodecode);
					case (nodecode[1]=='^'): if(nodecode[2]=='^') {
							return parse.notnotval(nodecode);
						} else { return parse.notval(nodecode); }
					default: return parse.val(nodecode);
				}
				case (nodecode[0]=='='): return parse.hecho(nodecode);
				case (nodecode[0]=='\\'): return parse.escapedText(nodecode);
				case (nodecode[0]=='%'):
				case (nodecode[0]=='.'):
				case (nodecode[0]=='#'): return parse.htag(nodecode);
				case (nodecode[0]=='!'&&nodecode[1]=='!'&&nodecode[2]=='!'): return parse.doctype(nodecode);
				default:return parse.text(nodecode);
			}
		}
		
		var parse={};
		(function(){
			function firstNodecode(nodecode) {
				var m=nodecode.match(MULTINODE_REGEX);
				if(m[3]) {
					restNodecode=m[3];
				}
				return m[1];
			}
			function attrNode(key,val) {
				var attr=ast.tagnode('attr');
				attr.name=key;
				var t=ast.textnode();
				t.contents=val;
				attr.children.push(t);
				return attr;
			}
			parse.text=function(contents) {
				var node=ast.textnode();
				node.contents=contents;
				return node;
			}
			parse.escapedText=function(nodecode) {
				
				return parse.text(nodecode.substr(1));
			}
			parse.hcomment=function(nodecode) {
				var node=ast.node('hcomment');
				restNodecode=nodecode.substr(1);
				return node;
			}
			parse.hecho=function(nodecode) {
				var node=ast.datanode('hecho');
				node.varname=nodecode.substr(1);
				return node;
			}
			parse.notval=function(nodecode) {
				var node=ast.datanode('notval');
				node.varname=firstNodecode(nodecode.substr(2));
				return node;
			}
			parse.notnotval=function(nodecode) {
				var node=ast.datanode('notnotval');
				node.varname=firstNodecode(nodecode.substr(3));
				return node;
			}
			parse.val=function(nodecode) {
				var node=ast.datanode();
				node.varname=firstNodecode(nodecode.substr(1));
				return node;
			}
			parse.doctype=function() {
				var node=ast.node('doctype');
				return node;
			}
			parse.comment=function() {
				var node=ast.node('comment');
				return node;
			}
			parse.htag=function(nodecode) {
				var node=ast.tagnode();
				var m=nodecode.match(TAGNODE_REGEX);
				if(m[2]!==undefined) {
					node.name=m[2];
				} else {
					node.name='div';
				}
				if(m[4]!==undefined) {
					node.attributes.push(attrNode('id',m[4]));
				}
				if(m[5]!==undefined) {
					var classes=m[5].split('.');
					for(var i=1;i<classes.length;i++) {
						node.attributes.push(attrNode('class',classes[i]));
					}
				}
				if(m[8]!==undefined) {
					node.attributes=node.attributes.concat( attrParser().parserAttr(scanner(m[8])) );
				}
				if(m[10]!==undefined) {
					restNodecode=m[10];
				}
				return node;
			}
		})();
		
		parser.parseString=parseString;
		return parser;
	};
	
	var mustaml={};
	if(this.mustaml!==undefined) {
		this.mustaml.parser=self;
		mustaml=this.mustaml;
	} else if (typeof module!=='undefined' && module.exports) {
		module.exports = self;
	}
})();