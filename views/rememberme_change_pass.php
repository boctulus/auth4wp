<!-- Rememberme => change password -->

<?php
	global $config;
?>

<div>
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="password" placeholder="Password" required="required"></input></div>
	
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="passwordconfirmation" placeholder="Password confirmación" required="required" name="passwordconfirmation"></input></div>
	
	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="change_pass()">Login</button>
	</div>

	<div id="error_box" style="font-size:125%;"></div>
</div>

<script>
	function password_show_hide_pc(){
		password_show_hide(); 
		password_show_hide('passwordconfirmation')
	}

	function change_pass(){
		var obj ={};
		
		if (jQuery('#password').val() != jQuery('#passwordconfirmation').val()){
			addNotice('Contraseñas no coinciden', 'warning', 'error_box', true);
			return;
		}else {
			hideNotice('error_box');
		}

		obj['email'] = jQuery('#email').val();

		const url = base_url + '/wp-json/auth/v1/change_pass_process';

		const data = Object.keys( obj)
		.map((key) => `${key}=${encodeURIComponent( obj[key])}`)
		.join('&');

		axios
		.post(url, data, 
		{
			headers: {
				Accept: "application/json",
				"Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
			},
		})
		.then(({data}) => {
			console.log(data);
			// ....
				
		})
		.catch(function (error) {
			if (error.response) {
				// The request was made and the server responded with a status code
				// that falls out of the range of 2xx
				//console.log(error.response.data);  ///  <--- mensaje de error
				// console.log(error.response.status);
				// console.log(error.response.headers);

				addNotice(error.response.data.message, 'warning', 'error_box', true);
			} else if (error.request) {
				// The request was made but no response was received
				// `error.request` is an instance of XMLHttpRequest in the browser and an instance of
				// http.ClientRequest in node.js
				
				console.log(error.request);
				addNotice('El servidor no responde. Intente maś tarde.', 'danger', 'error_box', true);
			} else {
				// Something happened in setting up the request that triggered an Error
				
				console.log('Error', error.message);
				addNotice('Algo salió mal.', 'danger', 'error_box', true);
			}

			//console.log(error.config);
		});

		return false;
	}
</script>
		
	
		
	