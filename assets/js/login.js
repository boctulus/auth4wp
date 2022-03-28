/*
	Pablo Bozzolo
	boctulus@gmail.com
*/

function logout(){
	localStorage.removeItem('access_token');
	localStorage.removeItem('refresh_token');
	window.location.href = login_redirection;
}

function checkpoint()
{
	if (typeof login_redirection === 'undefined' || typeof localStorage === 'undefined'){
		console.log('Error');
		return;
	}

	const expired = ((localStorage.getItem('exp')!=null) && ((localStorage.getItem('exp')*1000) - (new Date()).getTime())<0);
	
	if (expired)
		console.log('expired');

	if ((localStorage.getItem('access_token') == null) || expired){
		if (localStorage.getItem('refresh_token')){
			renew();	
		}else{
			window.location = login_redirection; 
		} 
			
	}		 
}

function renew(){
	if (typeof localStorage === 'undefined' || localStorage.getItem('refresh_token') === null){
		console.log('Error');
		return;
	}

	console.log('Renewing token at ...'+(new Date()).toString());

	jQuery.ajax({
		type: "POST",
		url: token_renewal,
		dataType: 'json',
		headers: {"Authorization": 'Bearer ' + localStorage.getItem('refresh_token')}, 
		success: function(res){
			let data = res.data;

			if (typeof data.access_token != 'undefined'){
				localStorage.setItem('access_token',data.access_token);
				localStorage.setItem('expires_in',data.expires_in);
				localStorage.setItem('exp', parseInt((new Date).getTime() / 1000) + data.expires_in);
				
				//console.log(data.access_token);
			}else{
				console.log('Error en la renovación del token');
				////////window.location = login_redirection;
			}
		},
		error: function(data){
			console.log('Error en la renovación del token!!!!!!!!!!!!');
			console.log(data);
			/////////window.location = login_redirection;
		}
	});		
}
