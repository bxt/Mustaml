$(function(){
	
	var comp=mustaml.htmlCompiler()
	var tmpls={};
	function render(name,data,cb) {
		if(typeof tmpls[name]=='undefined') {
			tmpls[name]=[];
			jQuery.get('tmpl/'+name,'text').success(function(tmplText){
				var ast,e;
				try {
					ast=mustaml.parser().parseString(tmplText);
				} catch (e2) {
					e=e2;
				}
				comp.render(e,ast,data,cb);
				for (var i=0;i<tmpls[name].length;i++) {
					tmpls[name][i](e,ast);
				}
				tmpls[name]=ast;
			}).error(function(e){
				for (var i=0;i<tmpls[name].length;i++) {
					tmpls[name][i](e);
				}
				cb(e);
			});
		} else if ($.isArray(tmpls[name])) {
			tmpls[name].push(function(e,ast){
				comp.render(e,ast,data,cb);
			});
		} else {
			comp.render(null,tmpls[name],data,cb);
		}
	}
	
	var currentId=$('li').length-1;
	$('body').addClass('jsenabled');
	$('input[name=ajax]').val('true');
	$('input[type=checkbox]').change(function(){
	$(this).parents('form').eq(0).submit();
	});
	$('form.checkbox').submit(function(){
		console.log($(this).serialize(),$(this).attr('action'));
		$.post('', $(this).serialize(), function(data){
			if(data!='OK') alert('Error: '+data);
		},"text");
		return false;
	});
	 $('form#adder').submit(function(){
		var model={text:$('input[name=text]',this).val(),state:false,id:currentId++};
		$.post('', $(this).serialize(), function(data){
			if(data!='OK') alert('Error: '+data); else {
				console.log(model);
				render('todo.mustaml',model,function(err,html){
					if(err) return console.log(err);
					$(html).insertBefore('#adderli');
				});
			}
		},"text");
		return false;
	});
});