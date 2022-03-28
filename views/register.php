<!-- Sign up -->

<?php
	global $config;
?>

<div>
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-user"></i></span><input class="form-control" type="text" id="email" placeholder="E-mail" required="required"></input></div>
	
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-user"></i></span><input class="form-control" type="text" id="username" placeholder="Nombre de usuario" required="required"></input></div>
	
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="password" placeholder="Password" required="required"></input><span class="input-group-text" onclick="password_show_hide_pc();">
			<i class="fas fa-eye" id="show_eye"></i>
			<i class="fas fa-eye-slash d-none" id="hide_eye"></i>
		</span>
	</div>

	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="passwordconfirmation" placeholder="Password confirmación" required="required" name="passwordconfirmation"></input></div>
	
	<div style="margin-bottom:1em;">
		<a href="<?= $config['url_pages']['rememberme'] ?>">Recordar contraseña</a>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="register();">Registrarse</button>
	</div>

	<div class="mt-3" style="text-align:right;">
		Ya registrado? <a href="<?= $config['url_pages']['login'] ?>">Ingrese</a>
	</div>

	<div id="error_box">
	</div>
</div>

<script>
	function password_show_hide_pc(){
		password_show_hide(); 
		password_show_hide('passwordconfirmation')
	}

	function register(){
		var obj ={};
		
		if (jQuery('#password').val() != jQuery('#passwordconfirmation').val()){
			addNotice('Contraseñas no coinciden', 'warning', 'error_box', true);
			return;
		}else jQuery('#registerError').text('');
		
		obj['email']    = jQuery('#email').val();
		obj['username'] = jQuery('#username').val();
		obj['password'] = jQuery('#password').val();

		const url = base_url + '/wp-json/auth/v1/register';

		axios
		.post(url, obj, {
			headers: {
			Accept: "application/json",
			"Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
			},
		})
		.then(({data}) => {
			console.log(data);
			if (typeof data.access_token != 'undefined' && typeof data.refresh_token != 'undefined'){
				localStorage.setItem('access_token',data.access_token);
				localStorage.setItem('refresh_token',data.refresh_token);
				localStorage.setItem('expires_in',data.expires_in);
				localStorage.setItem('exp', parseInt((new Date).getTime() / 1000) + data.expires_in);
				console.log('Tokens obtenidos');

				if (typeof register_redirection != 'undefined' && register_redirection!== null){
					window.location = register_redirection;
				}
				
			}else{	
				console.log('Error (success)',data);	
				jQuery('#loginError').text(data.responseJSON.error);
			}
		})
		.catch(function (error) {
			if (error.response) {
				// The request was made and the server responded with a status code
				// that falls out of the range of 2xx
				console.log(error.response.data);  ///  <--- mensaje de error
				console.log(error.response.status);
				console.log(error.response.headers);
			} else if (error.request) {
				// The request was made but no response was received
				// `error.request` is an instance of XMLHttpRequest in the browser and an instance of
				// http.ClientRequest in node.js
				console.log(error.request);
			} else {
				// Something happened in setting up the request that triggered an Error
				console.log('Error', error.message);
			}

			console.log(error.config);
		});

		return false;
	}
</script>
		
	