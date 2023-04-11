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
  <div class="col-lg-8 mx-auto">
    <div class="info d-flex align-items-center">
      <div class="content">
        <div class="logo">
          <h1><?php echo $titulo; ?></h1>
        </div>
        <p>Não deixe de conferir a caixa de span.</p>
      </div>
    </div>
  </div>
  <!-- Form Panel    -->
  <div class="col-lg-6 bg-white d-none">
    <div class="form d-flex align-items-center">
      <div class="content">
        
      </div>
    </div>
  </div>
</div>

<?php $this->endSection(); ?>

<!-- Scripts da pagina -->
<?php $this->section('scripts'); ?>



<?php $this->endSection(); ?>