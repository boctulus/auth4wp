
<!-- Sign up -->
    
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

		echo tag('card')
		->header(tag('cardTitle')->text('Registro')->style('font-size: 300%;'))
		->body([
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-user"></i>', [
					'class' => 'input-group-text'
				]),
	
				Bt5Form::inputText(
					id:"email",
					placeholder:"E-mail",
					required:"required"
				)
			])->class("mb-3"),
	
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-user"></i>', [
					'class' => 'input-group-text'
				]),
	
				Bt5Form::inputText(
					id:"username",
					placeholder:"Nombre de usuario",
					required:"required"
				)
			])->class("mb-3"),
			
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-key"></i>', [
					'class' => 'input-group-text'
				]),
	
				Bt5Form::password(
					id:"password", 
					placeholder:"Password",
					required:"required"
				)
			])->class("mb-3"),
	
			tag('inputGroup')->content([
				Bt5Form::span('<i class="fas fa-key"></i>', [
					'class' => 'input-group-text'
				]),
	
				Bt5Form::password(
					id:"password_confirmation", 
					placeholder:"Password confirmación",
					required:"required",
					name:"passwordconfirmation"
				)
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
