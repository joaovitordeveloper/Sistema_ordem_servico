<?php $this->extend('Layout/principal'); ?><!-- Extendendo o layout principal -->

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
    <div class="col-lg-6">
        <div class="block">
            <div class="block-body">
                <!--Exibirá od retornos do back-end-->
                <div id="response">
                    
                </div>

                <?php echo form_open('/', ['id' => 'form'], ['id' => "$grupo->id"]); ?>

                <?php echo $this->include('grupos/_form');?>

                <div class="form-group mt-5 mb-2">

                    <input id="btn-salvar" type="submit" name="" value="salvar" class="btn btn-danger btn-sm mr-2">
                    <a href="<?php echo site_url("grupos/"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div><!-- final do block -->
    </div>
</div>

<?php $this->endSection(); ?>

<!-- Scripts da pagina -->
<?php $this->section('scripts'); ?>

<script>
    $(document).ready(function(){
        $("#form").on('submit', function(e){
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('grupos/cadastrar'); ?>',
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function(){
                    $("#response").html('');
                    $("#btn-salvar").val('Por favor aguarde...');
                },
                success: function(response){
                    $("#btn-salvar").val('Salvar');
                    $("#btn-salvar").removeAttr("disabled");
                    $('[name=csrf_ordem]').val(response.token);

                    if(!response.erro){
                        //tudo certo na atualização do grupo.
                        window.location.href = "<?php echo site_url("grupos/exibir/")?>" + response.id;
                    }

                    if(response.erro){
                        $("#response").html('<div class="alert alert-danger">' + response.erro + '</div>');

                        if(response.erros_model){
                            $.each(response.erros_model, function(key, value){
                                $("#response").append('<ul class="list-unstyled"><li class="text-danger">'+ value +'</li></ul>')
                            });
                        }
                    }
                },
                error: function(){
                    alert('Não foi possivel processar a solicitação. Por favor entre em contato com o suporte técnico.');
                    $("#btn-salvar").val('Salvar');
                    $("#btn-salvar").removeAttr("disabled");
                }
            });
        })
        
        $("#form").submit(function(){
            $(this).find(":submit").attr('disabled', 'disabled');
        })
    });
</script>

<?php $this->endSection(); ?>