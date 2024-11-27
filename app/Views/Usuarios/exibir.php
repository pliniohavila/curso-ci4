<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?>
<?php echo $titulo; ?>
<?= $this->endSection() ?>


<?= $this->section('estilos') ?>
<!-- Aqui será colocado estilos especificos -->
<?= $this->endSection() ?>


<?= $this->section('conteudo') ?>
<div class="row">
  <div class="col-lg-6">
    <div class="block">
      
    <div class="text-center">
        <?php if ($usuario->imagem == null): ?>
          <img src="<?php echo site_url('recursos/img/user-icon.png'); ?>" alt="Usuário Sem Imagem" class="card-img-top" style="width: 90%;" />
        <?php else: ?>
          <img src="<?php echo site_url("usuario/imagem/$usuario->imagem"); ?>" alt="<?php echo esc($usuario->nome); ?>" class="card-img-top" style="width: 90%;" />
        <?php endif; ?>
        <a href="<?php echo site_url("usuarios/editarImagem/$usuario->id"); ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
      </div>

      <hr class="border-secondary" />
      
      <div class="card-title">
        <h5><?php echo esc($usuario->nome); ?></h5>
        <div class="card-text">
          <p>E-mail: <?php echo esc($usuario->email); ?></p>
          <p>Criado em: <?php echo $usuario->criado_em->humanize(); ?></p>
          <p>Atualizado em: <?php echo $usuario->atualizado_em; ?></p>
        </div>

        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Ações
          </button>
          <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item w-100" href="<?php echo site_url("usuarios/editar/$usuario->id"); ?>">Editar Usuário</a>
            <a class="dropdown-item w-100" href="<?php echo site_url("usuarios/excluir/$usuario->id"); ?>">Excluir Usuário</a>
            <a class="dropdown-item w-100" href="<?php echo site_url("usuarios/ativarDesativar/$usuario->id"); ?>"><?php echo (($usuario->ativo) ? 'Desativar' : 'Desativar'); ?></a>
          </div>
        </div>

        <a href="<?php echo site_url("usuarios"); ?>" class="btn btn-secondary w-100 mt-2">Voltar</a>
      </div>
    
    </div> <!-- end div block -->
  </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<!-- Aqui será colocado scripts especificos -->
</script>
<?= $this->endSection() ?>