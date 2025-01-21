<?= $this->extend('Layout/Autenticacao/principal_autenticacao') ?>

<?= $this->section('titulo') ?>
<?php echo $titulo; ?>
<?= $this->endSection() ?>


<?= $this->section('estilos') ?>
<!-- Aqui será colocado estilos especificos -->
<?= $this->endSection() ?>


<?= $this->section('conteudo') ?>

<div class="row">
  <div class="col-lg-12">
    <div class="info d-flex align-items-center">
      <div class="content">
        <div class="logo">
          <h1><?= $titulo ?></h1>
        </div>
        <p>Verifique o seu e-mail para iniciar o procedimento de recuperação de senha</p>
        <a href="<?php echo site_url('login'); ?>" class="btn btn-info">Login</a>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>