<!-- Rememberme -->
    
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
        ->header(tag('cardTitle')->text('Recordar contraseña')->style('font-size: 300%;'))
        ->body([
            '<div style="text-align:right; margin-bottom:1em;">
                Tiene cuenta? <a href="login">Ingresar</a>
            </div>',

            tag('inputGroup')->content([
                Bt5Form::span('<i class="fas fa-envelope"></i>', [
                    'class' => 'input-group-text'
                ]),

                Bt5Form::email(
                    id:"email",
                    placeholder:"E-mail",
                    required:"required"
                )
            ])->class("input-group mb-3"),		
        

            '<div class="form-group mb-3">
                <button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="rememberme()">Recuérdame</button>
            </div>

            No registrado? <a href="login/register">regístrese</a>'
            

        ])->class('card-primary card-outline');
        
        ?>
        
        
	</div>
</div>
