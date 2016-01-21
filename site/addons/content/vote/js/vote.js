/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
-------------------------------------------------------------------------*/

function JVXVote(obj,id,i,total,total_count,counter,style){
	var currentURL = window.location;
	var live_site = currentURL.protocol+'//'+currentURL.host+tzPortfolioPlusBase;
	var lsXmlHttp = '';
	
	var div = document.getElementById('TzVote_'+id);
	if (div.className != 'TzVote-count voted') {
		div.innerHTML='<img src="'+live_site+'/images/loading.gif" border="0" align="absmiddle" /> '+'<small>'+TzPortfolioPlusVote_text[1]+'</small>';
		try	{
			lsXmlHttp=new XMLHttpRequest();
		} catch (e) {
			try	{ lsXmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try { lsXmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					alert(TzPortfolioPlusVote_text[0]);
					return false;
				}
			}
		}
	}
	div.className = 'TzVote-count voted';
	if ( lsXmlHttp != '' ) {
		lsXmlHttp.onreadystatechange=function() {
			var response;
			if(lsXmlHttp.readyState==4){
				setTimeout(function(){ 
					response = lsXmlHttp.responseText; 
					if(response=='thanks') div.innerHTML='<small>'+TzPortfolioPlusVote_text[2]+'</small>';
					if(response=='login') div.innerHTML='<small>'+TzPortfolioPlusVote_text[3]+'</small>';
					if(response=='voted') div.innerHTML='<small>'+TzPortfolioPlusVote_text[4]+'</small>';
				},500);
				setTimeout(function(){
					if(response=='thanks'){
						var newtotal = total_count+1;
						var percentage = ((total + i)/(newtotal));


                        if(style == 1){ // For bootstrap style
                            var elements    = obj.parentNode.getElementsByTagName('a');
                            for(var k=0;k<5;k++){
                                elements[k].innerHTML = '';
                                var className = elements[k].className;
                                if(className.indexOf('voted') != -1){
                                    className   = className.replace('voted','');
                                    elements[k].setAttribute('class',className);
                                }
                            }
                            for(var j = 1; j <= Math.ceil(percentage); j++){
                                if(elements[5-j]){
                                    elements[5-j].setAttribute('class',elements[5-j].className.toString()+' voted');
                                    if(j == Math.ceil(percentage) && (percentage - parseInt(percentage)) > 0 ){
                                        elements[5-j].innerHTML = '<span class="icon-star" style="width:'+ ((100 - Math.round((percentage - parseInt(percentage))*100) )) +'%"></span>';
                                    }
                                }
                            }
                        }

                        if(style == 2){ // For classic style
                            document.getElementById('rating_'+id).style.width=parseInt(percentage*20)+'%';
                        }
					}
					if(counter!=0){
						if(response=='thanks'){
							if(newtotal!=1)
								var newvotes=TzPortfolioPlusVote_text[5].replace('%d', newtotal );
							else
								var newvotes=TzPortfolioPlusVote_text[6].replace('%d', newtotal );
							div.innerHTML='<small>( '+newvotes+' )</small>';
						} else {
							if(total_count!=0 || counter!=-1) {
								if(total_count > 1)
									var votes = TzPortfolioPlusVote_text[5].replace('%d', total_count);
								else
									var votes=TzPortfolioPlusVote_text[6].replace('%d', total_count );
								div.innerHTML='<small>( '+votes+' )</small>';
							} else {
								div.innerHTML='';
							}
						}
					} else {
						div.innerHTML='';
					}
				},2000);
			}
		}
		lsXmlHttp.open("GET",live_site+"/assets/ajax.php?task=vote&user_rating="+i+"&cid="+id,true);
		lsXmlHttp.send(null);
	}
}