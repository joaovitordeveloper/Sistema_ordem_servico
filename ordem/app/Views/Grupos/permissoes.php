<?php $this->extend('Layout/principal');?>
<!-- Extendendo o layout principal -->

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

    <div class="col-lg-8">
        <div class="user-block block">
            <?php if(empty($permissoesDisponiveis)): ?>

                <p class="contributions mt-0">Esse grupo já possui todas as permissões disponiveis!</p>

            <?php else: ?>
                <div id="response">
                        
                </div>
            
                <?php echo form_open('/', ['id' => 'form'], ['id' => "$grupo->id"]); ?>

                <div class="form-group">
                    <label class="form-control-label">Escolha uma ou mais permissões</label>
                    <select name="permissao_id[]" class="form-control" multiple>
                        <option value="">Selecione...</option>
                        <?php foreach($permissoesDisponiveis as $permissao): ?>
                            <option value="<?php echo $permissao->id ?>"> <?php echo esc($permissao->nome) ?> </option>
                        <?php endforeach; ?>    
                    </select>
                </div>
            
                <div class="form-group mt-5 mb-2">
            
                    <input id="btn-salvar" type="submit" name="" value="salvar" class="btn btn-danger btn-sm mr-2">
                    <a href="<?php echo site_url("grupos/exibir/$grupo->id"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>
                </div>
            
                <?php echo form_close(); ?>

                <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="user-block block">
            <?php if (empty($grupo->permissoes)): ?>

                <p class="contributions text-warning mt-0">Esse grupo ainda não possui permissões de acesso!</p>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Permissão</th>
                                <th>Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($grupo->permissoes as $permissao): ?>
                                <tr>
                                    <td><?php echo esc($permissao->nome) ?></td>
                                    <td><a href="#" class="btn btn-sm btn-danger">Excluir</a></td>
                                </tr>
                            <?php endforeach; ?>    
                        </tbody>
                    </table>
                    <hr class="border-secondary">
                    <div class="mt-3 ml-2">
                        <?php echo $grupo->pager->links(); ?>
                    </div>
                </div>

            <?php endif;?>

        </div><!-- final do block -->
    </div>
</div>

<?php $this->endSection();?>

<!-- Scripts da pagina -->
<?php $this->section('scripts');?>



<?php $this->endSection();?>