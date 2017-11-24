var domains_edit=document.getElementById('domains_edit');
var edit_inputs=domains_edit.getElementsByTagName('input');
var edit_arr=new Array();
for(var i=0;i<edit_inputs.length;i++){
	if(edit_inputs[i].type=='text'){
		edit_arr.push(edit_inputs[i]);
	}
	if(edit_inputs[i].type=='submit'){
		var edit_inputs_submit=edit_inputs[i];
	}
}
var base_n=3;
for(var i=0;i<edit_arr.length;i++){
	edit_arr[0].onblur=function(){
		var n=parseFloat(this.value);
		edit_arr[1].value=n;
		edit_arr[2].value=n;
		for(var j=1;j<edit_arr.length;j++){
			edit_arr[j].value=Math.ceil((j+1)/3)*n;
		}
		//edit_inputs_submit.click();
	}
}
var next_id=document.getElementById('next_id');
next_id.onclick=function(){
	var href=window.location.href;
	var str_arr=href.split('id=');
	var str_n=str_arr[1].split('&')[0].length;
	var n=parseInt(str_arr[1].split('&')[0]);
	n++;
	var t=str_arr[0]+'id='+n+str_arr[1].substring(str_n);
	window.location.href=t;
	//console.log(t);
}
//next_id.click();