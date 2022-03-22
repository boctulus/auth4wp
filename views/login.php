<!-- Login -->
    
<?php
	include_css(WIDGETS_PATH . 'login/login.css');
?>

<div class="row vh-100 d-flex  align-items-center">
	<div class="col-xs-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">

		<?php

		use simplerest\core\libs\HtmlBuilder\Bt5Form;
		use simplerest\core\libs\HtmlBuilder\Tag;

		Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

		Bt5Form::setIdAsName();

		include_css(ASSETS_PATH . 'adminlte/css/adminlte.css');
	
		echo tag('card')
		->header(tag('cardTitle')->text('Login')->style('font-size: 300%;'))
		->body([

			tag('div')->content('
				<a href="facebook/login" class="btn btn-primary w-100"><i class="fa fa-facebook"></i> Sign in with <b>Facebook</b></a>
				<a href="google/login" class="btn btn-danger w-100"><i class="fa fa-google"></i> Sign in with <b>Google</b></a>'
			)->class("social-btn"),

			'<div class="or-seperator"><i>or</i></div>',

			tag('div')->content(
				Bt5Form::span('<i class="fas fa-user"></i>', [
					'class' => 'input-group-text'
				]) .

				Bt5Form::inputText(
					id:"email_username",
					placeholder:"email o username",
					required:"required"
				)
			)->class("input-group mb-3"),

			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-key"></i>', [
					'class' => 'input-group-text'
				]),

				Bt5Form::password(
					id:"password", 
					placeholder:"Password",
					required:"required"
				),

				'<span class="input-group-text" onclick="password_show_hide();">
				<i class="fas fa-eye" id="show_eye"></i>
				<i class="fas fa-eye-slash d-none" id="hide_eye"></i>
				</span>'	
			])->class("mb-3"),


			'<div style="margin-bottom:1em;">
				<a href="login/rememberme">Recordar contraseña</a>
			</div>	

			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="login()">Login</button>
			</div>
			
			<div class="mt-3" style="text-align:right;">
				No registrado? <a href="login/register">regístrese</a>
			</div>'
		])->class('card-primary card-outline');
	?>

	</div>
</div>
