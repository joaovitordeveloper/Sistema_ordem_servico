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
    <div class="col-lg-3">
        <div class="user-block block">

            <h5 class="card-title mt-2"><?php echo esc($grupo->nome); ?></h5>
            <p class="contributions mt-0"> <?php echo $grupo->exibeSituacao(); ?>

                <?php if($grupo->deletado_em == null): ?>
                    <a tabindex="0" style="text-decoration:none;" role="button" data-toggle="popover" data-trigger="focus" title="Importante" data-content="Esse grupo <?php echo ($grupo->exibir == true ? 'será' : 'não será') ?> exibido como opção na hora de definir um <b>Responsavel técnico </b>pela ordem de serviço">&nbsp;&nbsp;<i class="fa fa-question-circle fa-lg text-danger"></i></a>
                <?php endif; ?>  

            </p>
            <p class="card-text"> <?php echo esc($grupo->descricao); ?></p>
            <p class="card-text">Criado <?php echo $grupo->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado <?php echo $grupo->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("grupos/editar/$grupo->id"); ?>">Editar grupo de acesso</a>
                    <div class="dropdown-divider"></div>

                    <?php if ($grupo->deletado_em == null): ?>
                        <a class="dropdown-item" href="<?php echo site_url("grupos/excluir/$grupo->id"); ?>">Excluir grupo de acesso</a>
                    <?php else: ?>
                        <a class="dropdown-item" href="<?php echo site_url("grupos/restaurargrupo/$grupo->id"); ?>">Restaurar grupo de acesso</a>
                    <?php endif;?>

                </div>
            </div>
            <a href="<?php echo site_url("grupos"); ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div><!-- final do block -->
    </div>
</div>

<?php $this->endSection();?>

<!-- Scripts da pagina -->
<?php $this->section('scripts');?>



<?php $this->endSection();?>