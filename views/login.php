<!-- Login -->

<script>
	const login_redirection = '<?= $login_redirection ?? null ?>'	
</script>

<div>
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-user"></i></span><input class="form-control" type="text" id="email_username" placeholder="email o username" required="required"></input></div>
	
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="password" placeholder="Password" required="required"></input><span class="input-group-text" onclick="password_show_hide();">
			<i class="fas fa-eye" id="show_eye"></i>
			<i class="fas fa-eye-slash d-none" id="hide_eye"></i>
		</span>
	</div>
	
		<div style="margin-bottom:1em;">
		<a href="login/rememberme">Recordar contraseña</a>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="login()">Login</button>
	</div>

	<div class="mt-3" style="text-align:right;">
		No registrado? <a href="auth/register">Regístrese</a>
	</div>
</div>
		
<script>
	function login(){
		var obj ={};
		
		if (jQuery('#email_username').val().match(/@/) != null)
			obj['email']    = jQuery('#email_username').val();	
		else
			obj['username'] = jQuery('#email_username').val();
		
		obj['password'] = encodeURIComponent(jQuery('#password').val());

		var formBody = [];

		for (var property in obj) {
			var encodedKey = encodeURIComponent(property);
			var encodedValue = encodeURIComponent(obj[property]);
			formBody.push(encodedKey + "=" + encodedValue);
		}
		
		formBody = formBody.join("&");

		fetch(base_url + '/wp-json/auth/v1/login', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
			},
			body: formBody
		})
		.then(function(response) {
			return response.json();
		})
		.then(function(data) {
			//console.log(data);

			if (typeof data.access_token != 'undefined' && typeof data.refresh_token != 'undefined'){
				localStorage.setItem('access_token',data.access_token);
				localStorage.setItem('refresh_token',data.refresh_token);
				localStorage.setItem('expires_in',data.expires_in);
				localStorage.setItem('exp', parseInt((new Date).getTime() / 1000) + data.expires_in);
				console.log('Tokens obtenidos');

				if (login_redirection !== null){
					window.location = base_url;
				}
				
			}else{	
				console.log('Error (success)',data);	
				$('#loginError').text(data.responseJSON.error);
			}

		})
		.catch((error) => {
			console.log(error)
		});

		return false;
	}
</script>