(function(undefined) {
	var self={};
	self.node=function(type) {
		var node={};
		node.type=type;
		node.children=[];
		return node;
	}
	self.datanode=function(type) {
		var node=self.node(type||'val');
		node.varname='';
		return node;
	}
	self.tagnode=function(type) {
		var node=self.node(type||'htag');
		node.attributes=[];
		node.name='div';
		return node;
	}
	self.textnode=function(type) {
		var node=self.node(type||'text');
		node.contents='';
		return node;
	}
	var mustaml={};
	if(this.mustaml!==undefined) {
		this.mustaml.ast=self;
		mustaml=this.mustaml;
	} else if (typeof module!=='undefined' && module.exports) {
		module.exports = self;
	}
})();