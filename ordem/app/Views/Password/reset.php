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
        <p>Crie a sua senha.</p>
      </div>
    </div>
  </div>
  <!-- Form Panel    -->
  <div class="col-lg-6 bg-white">
    <div class="form d-flex align-items-center">
      <div class="content">

        <div id="response">

        </div>

        <?php echo form_open('/', ['id' => 'form', 'class' => 'form-validate'], ['token' => $token]); ?>
        <div class="form-group">
          <input id="login-password" type="password" name="password" required data-msg="Por favor informe sua nova senha" class="input-material">
          <label for="login-password" class="label-material">Sua nova senha</label>
        </div>

        <div class="form-group">
          <input id="login-password" type="password_confirmation" name="password" required data-msg="Por favor Confirme sua nova senha" class="input-material">
          <label for="login-password" class="label-material">Confirme a sua nova senha</label>
        </div>
        <input id="btn-reset" type="submit" class="btn btn-primary" value="Salvar">

        <?php echo form_close(); ?>
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
        url: '<?php echo site_url('password/processaReset'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $("#response").html('');
          $("#btn-reset").val('Por favor aguarde...');
        },
        success: function(response) {
          $("#btn-reset").val('Salvar');
          $("#btn-reset").removeAttr("disabled");
          $('[name=csrf_ordem]').val(response.token);

          if (!response.erro) {
            window.location.href = "<?php echo site_url('login') ?>";
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
          $("#btn-reset").val('Salvar');
          $("#btn-reset").removeAttr("disabled");
        }
      });
    })

    $("#form").submit(function() {
      $(this).find(":submit").attr('disabled', 'disabled');
    })
  });
</script>

<?php $this->endSection(); ?>