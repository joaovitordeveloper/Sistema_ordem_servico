<?php $this->extend('Layout/Autenticacao/principal_autenticacao'); ?><!-- Extendendo o layout principal -->

<!-- Titulo vindo da home -->
<?php $this->section('titulo'); ?>

<?php echo $titulo; ?>

<?php $this->endSection(); ?>

<!-- Estilos da pagina -->
<?php $this->section('estilos'); ?>



<?php $this->endSection(); ?>

<!-- Conteudos da pagina -->
<?php $this->section('conteudo'); ?>

<div class="row">
  <!-- Logo & Information Panel-->
  <div class="col-lg-6">
    <div class="info d-flex align-items-center">
      <div class="content">
        <div class="logo">
          <h1><?php echo $titulo; ?></h1>
        </div>
        <p>Informe seu e-mail de acesso para iniciar a recuperação de senha.</p>
      </div>
    </div>
  </div>
  <!-- Form Panel    -->
  <div class="col-lg-6 bg-white">
    <div class="form d-flex align-items-center">
      <div class="content">
        
        <div id="response">

        </div>

        <?php echo form_open('/', ['id' => 'form', 'class' => 'form-validate']); ?>

        <div class="form-group">
          <input id="login-username" type="email" name="email" required data-msg="Por favor informe seu e-mail" class="input-material">
          <label for="login-username" class="label-material">Informe seu e-mail de acesso</label>
        </div>

        <input id="btn-esqueci" type="submit" class="btn btn-primary" value="Enviar">

        <?php echo form_close(); ?>
        <a href="<?php echo site_url('login'); ?>" class="forgot-pass mt-3">Lembrou a sua senha de acesso?</a>
      </div>
    </div>
  </div>
</div>

<?php $this->endSection(); ?>

<!-- Scripts da pagina -->
<?php $this->section('scripts'); ?>

<script>
  $(document).ready(function() {
    $("#form").on('submit', function(e) {
      e.preventDefault();

      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('password/processaEsqueci'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $("#response").html('');
          $("#btn-esqueci").val('Por favor aguarde...');
        },
        success: function(response) {
          $("#btn-esqueci").val('Enviar');
          $("#btn-esqueci").removeAttr("disabled");
          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {
            //tudo certo na atualização do usuario.
            window.location.href = "<?php echo site_url("password/resetEnviado") ?>";
          }

          if (response.erro) {
            $("#response").html('<div class="alert alert-danger">' + response.erro + '</div>');

            if (response.erros_model) {
              $.each(response.erros_model, function(key, value) {
                $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>')
              });
            }
          }
        },
        error: function() {
          alert('Não foi possivel processar a solicitação. Por favor entre em contato com o suporte técnico.');
          $("#btn-esqueci").val('Enviar');
          $("#btn-esqueci").removeAttr("disabled");
        }
      });
    })

    $("#form").submit(function() {
      $(this).find(":submit").attr('disabled', 'disabled');
    })
  });
</script>

<?php $this->endSection(); ?>