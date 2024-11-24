<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo')?>
  <?php echo $titulo; ?>
<?=  $this->endSection() ?>


<?= $this->section('estilos')?>
  <!-- Aqui será colocado estilos especificos -->
<?=  $this->endSection() ?>


<?= $this->section('conteudo')?>
  <?php echo 'Conteúdo'; ?>
<?=  $this->endSection() ?>


<?= $this->section('scripts')?>
  <!-- Aqui será colocado scripts especificos -->
<?=  $this->endSection() ?>