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

<h1>Estendendo o layout principal</h1>

<?php $this->endSection(); ?>

<!-- Scripts da pagina -->
<?php $this->section('scripts'); ?>



<?php $this->endSection(); ?>