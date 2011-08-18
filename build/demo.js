
function id(x) {
	return document.getElementById(x);
}

function refresh() {
	try {
		var data=eval('('+id('jsonInput').value+')');
		var tmpl=id('mustamlInput').value;
		mustaml.htmlCompiler().render(null,tmpl,data,function(err,html){
			if(err) {
				id('resultOutput').innerHTML='<b>Error: </b>'+err+'';
			} else {
				id('htmlOutput').firstChild.nodeValue=pp(html);
				id('resultOutput').innerHTML=html;
			}
		});
	} catch (e) {
		id('resultOutput').innerHTML='<b>Invalid JSON: </b>'+e;
	}
}

function pp(html) {
	var pphtml='',pad='  ',lvl=0,i=0,inEndtag=false,wasTagEnd=true,wasTagEndNeu;
	for (;i<html.length;i++) {
		wasTagEndNeu=false;
		if(html.charAt(i)=='>') {
			wasTagEndNeu=true;
			pphtml+=html.charAt(i);
			if(inEndtag) {
				inEndtag=false;
				wasTagEndNeu=true;
				if (html.charAt(i+1)!='<') {
					pphtml+='\n'+times(pad,lvl);
				}
			} else {
				lvl++;
				pphtml+='\n'+times(pad,lvl);
			}
		} else if(html.charAt(i)=='<'&&html.charAt(i+1)=='/') {
			inEndtag=true;
			lvl--;
			pphtml+='\n'+times(pad,lvl);
			pphtml+=html.charAt(i);
		} else if(html.charAt(i)=='<'&&!wasTagEnd) {
			pphtml+='\n'+times(pad,lvl);
			pphtml+=html.charAt(i);
		} else {
			pphtml+=html.charAt(i);
		}
		wasTagEnd=wasTagEndNeu;
	}
	return pphtml;
}

refresh();
id('jsonInput').onkeyup=id('mustamlInput').onkeyup=refresh;

function times(input,multiplier) {
	return new Array(multiplier+1).join(input);
}