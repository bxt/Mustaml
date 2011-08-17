/**
 * Parser for tempates attribute strings
 */
(function(undefined) {
	var WS=' ',A='A-Za-z',GT='>',ANUM='0-9A-Za-z_-',EQ='=',DYNEQ='=',SEP=' ,',Q='"';
	var self=function (ast){
		ast=mustaml.ast||require('./ast');

		var attrParser={};
		
		function parserAttr(scanner) {
			var attrs=[];
			while(scanner.is()) {
				scanner.get(WS);
				if(scanner.is(A)) {
					//identifier
					var key=ast.tagnode('attr');
					key.name=scanner.get(ANUM);
					scanner.get(WS);
					if(scanner.getOne(EQ)) {
						//value
						scanner.getOne(GT);// accept ruby-like hash (k=>v)
						scanner.get(WS);
						
						if(scanner.getOne(Q)) {
							// quoted sting
							key.children.push(textnode(scanner.getUnless(Q)));
							scanner.getOne(Q);
						} else {
							// unquoted value list
							while(scanner.is()&&!scanner.is(SEP)) {
								var dynval=parseDynval(scanner);
								if(dynval) {
									// var "val" value
									key.children.push(dynval);
								} else {
									// unquoted text value
									key.children.push(textnode(scanner.getUnless(SEP,DYNEQ)));
								}
							}
						}
					}
					scanner.get(SEP);
					attrs.push(key);
				}
				var dynval2=parseDynval(scanner);
				if(dynval2) attrs.push(dynval2);
			}
			return attrs;
		}
		
		function textnode(contents) {
			var t=ast.textnode();
			t.contents=contents;
			return t;
		}
		
		function parseDynval(scanner) {
			if(scanner.getOne(DYNEQ)) {
				if(scanner.is(SEP)||!scanner.is()) throw ('Syntax Error: No varname');
				var varname=scanner.getUnless(SEP,DYNEQ);
				scanner.getOne(DYNEQ);
				
				var node=ast.datanode();
				node.varname=varname;
				return node;
			}
			return false;
		}
		
		attrParser.parserAttr=parserAttr;
		return attrParser;
	};
	var mustaml={};
	if(this.mustaml!==undefined) {
		this.mustaml.attrParser=self;
		mustaml=this.mustaml;
	} else if (typeof module!=='undefined' && module.exports) {
		module.exports = self;
	}
})();

