<!-- Login -->

<form>
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-user"></i></span><input class="form-control" type="text" id="email_username" placeholder="email o username" required="required"></input></div>
	<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="password" placeholder="Password" required="required"></input><span class="input-group-text" onclick="password_show_hide();">
			<i class="fas fa-eye" id="show_eye"></i>
			<i class="fas fa-eye-slash d-none" id="hide_eye"></i>
		</span></div>
	<div style="margin-bottom:1em;">
		<a href="login/rememberme">Recordar contraseña</a>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="login()">Login</button>
	</div>

	<div class="mt-3" style="text-align:right;">
		No registrado? <a href="login/register">regístrese</a>
	</div>
</form>
		
