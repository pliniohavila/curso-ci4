<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?>
<?php echo $titulo; ?>
<?= $this->endSection() ?>


<?= $this->section('estilos') ?>
<!-- Aqui será colocado estilos especificos -->
<link href="<?php echo site_url('recursos/vendor/selectize/selectize.bootstrap4.css') ?>" rel="stylesheet">
<style>
  /* Estilizando o select para acompanhar a formatação do template */

  .selectize-input,
  .selectize-control.single .selectize-input.input-active {
    background: #2d3035 !important;
  }

  .selectize-dropdown,
  .selectize-input,
  .selectize-input input {
    color: #777;
  }

  .selectize-input {
    /*        height: calc(2.4rem + 2px);*/
    border: 1px solid #444951;
    border-radius: 0;
  }
</style>
<?= $this->endSection() ?>


<?= $this->section('conteudo') ?>
<div class="row">
  <?php if ($usuario->id == 1): ?>
    <div class="col-md-12 alert alert-info" role="alert">
      <h4 class="alert-heading">Importante!</h4>
      <p>Os grupos do usuario <strong><?php echo $usuario->nome; ?></strong> não pode ser editado ou excluído.</p>
    </div>
  <?php endif; ?>

  <div class="col-6">
    <!-- Exibirá os retornos do back-end -->
    <div id="response"></div>
    <div class="block">
      <div class="card-title">
        <h5>Adicionar Permissões ao Grupo</h5>
      </div>

      <div class="card-body">
        <?php if (empty($gruposDisponiveis)): ?>
          <div class="contributions text-info">
            Este usuário faz parte do grupo de ADMINISTRADORES com acesso completo sobre todos os outros grupos de permissões!
          </div>
        <?php else: ?>

          <?php echo form_open('/', ['id' => 'form'], ['id' => "$usuario->id"]); ?>

          <div class="form-group">
            <label class="form-control-label">Escolha uma ou mais grupos de acesso</label>
            <select name="grupos_id[]" class="form-control" id="input-beast" multiple>
              <option value="">Escolha...</option>
              <?php foreach ($gruposDisponiveis as $grupo): ?>
                <option value="<?php echo $grupo->id ?>"><?php echo esc($grupo->nome) ?></option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="form-group mt-5 mb-4">
            <input type="submit" value="Salvar" id="btn-salvar" class="btn btn-danger w-100 mt-2" />
          </div>

          <?php echo form_close(); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-6">
    <div class="block">
      <div class="card-title">
        <h5>Permissões do Grupo</h5>
      </div>
      <div class="card-body">
        <?php if (empty($usuario->grupos)): ?>
          <div class="contributions text-warning">
            Este usuário não faz parte de nenhum grupo de acesso!
          </div>
        <?php else: ?>
          <table class="table table-dark table-hover">
            <thead>
              <tr>
                <th>Grupo de Acesso</th>
                <th>Descrição</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuario->grupos as $grupo): ?>
                <tr>
                  <td>
                    <?php echo esc($grupo->nome); ?>
                  </td>
                  <td>
                    <?php echo ellipsize(esc($grupo->descricao), 16, .5); ?>
                  </td>
                  <td>
                    <?php
                      $attr = ['onSubmit' => "return confirm('Tem certeza da exclusão do grupo de acesso?')"];
                    ?>

                    <?php echo form_open("/usuarios/removeGrupo/$grupo->principal_id", $attr); ?>
                      <input type="submit" value="Excluir" id="btn btn-sm btn-danger" class="btn btn-danger w-100 mt-2" />
                    <?php echo form_close(); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <?php echo $usuario->pager->links(); ?>
        <?php endif; ?>
        <a href="<?php echo site_url("usuarios/exibir/$usuario->id"); ?>" class="btn btn-secondary w-100 mt-2">Voltar</a>
      </div>
    </div> <!-- end div block -->
  </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<!-- Aqui será colocado scripts especificos -->
<script src="<?php echo site_url('recursos/vendor/selectize/selectize.min.js') ?>"></script>
<script>
  $(document).ready(function() {
    $("#input-beast").selectize({
      create: true,
      sortField: "text"
    });

    $('#form').on('submit', function(e) {
      e.preventDefault()

      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('usuarios/salvarGrupos'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $('#response').html('')
          $('#btn-salvar').html('Aguarde...')
          $('#btn-salvar').html('Aguarde...')
        },
        success: function(response) {
          $('#btn-salvar').html('Salvar')
          $('#btn-salvar').removeAttr('disabled')

          $('[name=csrf_ordem]').val(response.token)
          if (!response.erro && !response.erros_model)
            window.location.href = `<?php echo site_url("usuarios/grupos/$usuario->id"); ?>`

          if (response.erro)
            $('#response').html(`<div class="alert alert-danger">${response.erro}</div>`)

          if (response.erros_model) {
            $.each(response.erros_model, function(key, value) {
              $('#response').append(`<p class="alert-danger mt-0">${value}</p>`)
            })
          }
        },
        error: function() {
          alert('Ocorreu um erro no back-end')
          $('#btn-salvar').html('Salvar')
          $('#btn-salvar').removeAttr('disabled')
        }
      })
    })
  })
</script>
<?= $this->endSection() ?>