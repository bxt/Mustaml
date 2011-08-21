/**
 * Demosite-Javascript
 * 
 * Since it would be pretty boring to render
 * templates client-side w/o "AJAX", we realize
 * adding and checking items via XHR
 */
$(function(){
	
	// First of all a function which loads template files
	//  since we don not (yet) have neat autoloaders for JS
	var comp=mustaml.htmlCompiler()
	var tmpls={}; // will hold the ASTs
	
	/**
	 * Render a template
	 * @param String Template filename
	 * @param mixed The Data
	 * @param function(err,ast) The callback to continue
	 * @param bool Set to true if you only want to load the file
	 */
	function render(name,data,cb,noRender) {
		if(typeof tmpls[name]=='undefined') { // not yet loaded
			// Make a queue while tmpl is loading, so that we don't
			// load the template twice
			tmpls[name]=[];
			// Fetch template with AJAX
			jQuery.get('tmpl/'+name,'text').success(function(tmplText){
				var ast,e;
				try {
					// Now that we have our template string parse it into an ast
					ast=mustaml.parser().parseString(tmplText);
				} catch (e2) {
					e=e2;
				}
				if(noRender) cb(null);
				else comp.render(e,ast,data,cb);
				// Empty the queue
				for (var i=0;i<tmpls[name].length;i++) {
					tmpls[name][i](e,ast);
				}
				tmpls[name]=ast;
			}).error(function(e){
				cb(e);
				// Empty the queue (with errors)
				for (var i=0;i<tmpls[name].length;i++) {
					tmpls[name][i](e);
				}
			});
		} else if ($.isArray(tmpls[name])) {
			if(noRender) return cb(null);
			// If there is a queue, append to it
			tmpls[name].push(function(e,ast){
				comp.render(e,ast,data,cb);
			});
		} else {
			if(noRender) return cb(null);
			// we already have the AST, go for it
			comp.render(null,tmpls[name],data,cb);
		}
	}
	
	// Holds the (well kinda guessed) id of a new item
	var currentId=$('li').length-1;
	// To hide our submit buttons
	$('body').addClass('jsenabled');
	
	/**
	 * Re-attach our event handlers etc.
	 * @param $ Only set handlers here
	 */
	function initialize(context) {
		$('input[name=ajax]',context).val('true'); // To tell that we want only short AJAXish answers
		$('input[type=checkbox]',context).change(function(){ // Make checkboxes submit on toggle
			$(this).parents('form').eq(0).submit();
		});
		$('form.checkbox',context).submit(function(){ // Handler for checkbox
			$.post('', $(this).serialize(), function(data){ // Just send the form
				if(data!='OK') alert('Error: '+data);
			},"text");
			return false;
		});
	}
	initialize();
	
	$('form#adder').submit(function(){ // Handler for adding
		// Build our todo-item:
		var model={text:$('input[name=text]',this).val(),state:false,id:currentId++};
		// Start fetching template file:
		render('todo.mustaml',null,$.noop,true);
		// Now submit the form
		$.post('', $(this).serialize(), function(data){
			if(data!='OK') alert('Error: '+data); else {
				// When finished, render the item
				render('todo.mustaml',model,function(err,html){
					if(err) return console.log(err);
					// When compiled, add the item to our todo-list
					var item=$(html);
					item.insertBefore('#adderli');
					initialize(item);
				});
			}
		},"text");
		return false;
	});
});