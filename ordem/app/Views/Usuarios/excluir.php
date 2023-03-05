<?php $this->extend('Layout/principal');?><!-- Extendendo o layout principal -->

<!-- Titulo vindo da home -->
<?php $this->section('titulo');?>

<?php echo $titulo; ?>

<?php $this->endSection();?>

<!-- Estilos da pagina -->
<?php $this->section('estilos');?>



<?php $this->endSection();?>

<!-- Conteudos da pagina -->
<?php $this->section('conteudo');?>

<div class="row">
    <div class="col-lg-6">
        <div class="block">
            <div class="block-body">

                <?php echo form_open("usuarios/excluir/$usuario->id"); ?>

                <div class="alert alert-warning" role="alert">
                    Tem certeza da exclusão do registro?
                </div>

                <div class="form-group mt-5 mb-2">

                    <input id="btn-salvar" type="submit" name="" value="Sim, pode excluir" class="btn btn-danger btn-sm mr-2">
                    <a href="<?php echo site_url("usuarios/exibir/$usuario->id"); ?>" class="btn btn-secondary btn-sm ml-2">Cancelar</a>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div><!-- final do block -->
    </div>
</div>

<?php $this->endSection();?>

<!-- Scripts da pagina -->
<?php $this->section('scripts');?>


</script>

<?php $this->endSection();?>