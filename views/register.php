<!-- Sign up -->

<?php
	global $url_pages;
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
		<a href="<?= $url_pages['rememberme'] ?>">Recordar contraseña</a>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="login()">Login</button>
	</div>

	<div class="mt-3" style="text-align:right;">
		Ya registrado? <a href="<?= $url_pages['login'] ?>">Ingrese</a>
	</div>
</div>

<script>
	function password_show_hide_pc(){
		password_show_hide(); 
		password_show_hide('passwordconfirmation')
	}
</script>
		
	