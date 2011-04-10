(function(undefined) {
	var self=function (input){
		input=input||'';
		
		var scanner={};
		
		function get() {
			var a=Array.prototype.slice.call(arguments);
			if(a.length==0) {
				var c=input[0]||false;
				input=input.substr(1);
				return c;
			}
			var eaten=is.apply(null,a);
			if(eaten) {
				input=input.substr(eaten.length);
			}
			return eaten;
		}
		function getUnless() {
			var a=Array.prototype.slice.call(arguments);
			if(a.length==0) {
				var c=input;
				input='';
				return c;
			}
			a[0]='^'+a[0];// not
			var eaten=is.apply(null,a);
			if(eaten) {
				input=input.substr(eaten.length);
			}
			return eaten;
		}
		function getOne() {
			var a=Array.prototype.slice.call(arguments);
			if(a.length==0) { // the next char:
				return get.apply(null,a);
			}
			var eaten=is.apply(null,a);
			if(eaten) {
				eaten=eaten[0];
				input=input.substr(eaten.length);
			}
			return eaten;
		}
		
		function is() {
			var a=Array.prototype.slice.call(arguments);
			if(input.length==0) return false;
			if(a.length==0) return true;
			var m=input.match(new RegExp('^['+a.join('')+']+'));
			if(m) {
				return m[0];
			}
			return false;
		}
		
		scanner.get=get;
		scanner.getUnless=getUnless;
		scanner.getOne=getOne;
		scanner.is=is;
		return scanner;
	};
	var mustaml={};
	if(this.mustaml!==undefined) {
		this.mustaml.scanner=self;
		mustaml=this.mustaml;
	} else if (typeof module!=='undefined' && module.exports) {
		module.exports = self;
	}
})();