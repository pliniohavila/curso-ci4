<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?>
<?php echo $titulo; ?>
<?= $this->endSection() ?>


<?= $this->section('estilos') ?>
<!-- Aqui será colocado estilos especificos -->
<?= $this->endSection() ?>


<?= $this->section('conteudo') ?>
<div class="row">
  <?php if ($grupo->id == 1): ?>
    <div class="col-md-12 alert alert-info" role="alert">
      <h4 class="alert-heading">Importante!</h4>
      <p>O grupo <strong><?php echo $grupo->nome; ?></strong> não pode ser editado ou excluído.</p>
    </div>
  <?php endif; ?>
  
  <div class="col-4">
    <div class="block">
      
      <div class="card-title">
        <h5><?php echo esc($grupo->nome); ?></h5>
        <div class="card-text">
          <p><strong>Descrição:</strong> <?php echo esc($grupo->descricao); ?></p>
          <p><strong>Criado em:</strong> <?php echo $grupo->criado_em->humanize(); ?></p>
          <p><strong>Atualizado em:</strong><?php echo $grupo->atualizado_em; ?></p>
        </div>

        <?php if ($grupo->id > 1): ?>
          <div class="dropdown">
            <button class="btn btn-danger dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Ações
            </button>
            <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item w-100" href="<?php echo site_url("grupos/editar/$grupo->id"); ?>">Editar Grupo</a>
              <a class="dropdown-item w-100" href="<?php echo site_url("grupos/excluir/$grupo->id"); ?>">Excluir Grupo</a>
              <a class="dropdown-item w-100" href="<?php echo site_url("grupos/permissoes/$grupo->id"); ?>">Editar Permissões do Grupo</a>
            </div>
          </div>
        <?php endif; ?>

        <a href="<?php echo site_url("grupos"); ?>" class="btn btn-secondary w-100 mt-2">Voltar</a>
      </div>

    </div> <!-- end div block -->
  </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<!-- Aqui será colocado scripts especificos -->
</script>
<?= $this->endSection() ?>