<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?>
<?php echo $titulo; ?>
<?= $this->endSection() ?>


<?= $this->section('estilos') ?>
<!-- Aqui será colocado estilos especificos -->
<?= $this->endSection() ?>


<?= $this->section('conteudo') ?>
<div class="row">
  <div class="col-6">
    <div class="block">
      
      <div class="block-body">

        <!-- Exibirá os retornos do back-end -->
        <div id="response"></div>

        <?php echo form_open('/', ['id' => 'form'], ['id' => "$usuario->id"]); ?>
        <?php echo $this->include('Usuarios/_form'); ?>

        <div class="form-group mt-5 mb-4">

          <input type="submit" value="Salvar" id="btn-salvar" class="btn btn-danger w-100 mt-2" />
          <a href="<?php echo site_url("usuarios/exibir/$usuario->id"); ?>" class="btn btn-secondary w-100 mt-2">Voltar</a>
        </div>

        <?php echo form_close(); ?>
      </div>
    
    </div> <!-- end div block -->
  </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<!-- Aqui será colocado scripts especificos -->
<script>
  $(document).ready(function() {
    $('#form').on('submit', function(e) {
      e.preventDefault()
      
      $.ajax({
        type: 'POST', 
        url: '<?php echo site_url('usuarios/atualizar'); ?>', 
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
          if (!response.erro && !response.erros_model) {
            
            if (response.info) {
              $('#response').html(`<div class="alert alert-primary">${response.info}</div>`)
            } else {
              window.location.href = `<?php echo site_url("usuarios/exibir/$usuario->id"); ?>`
            }
          }

          if (response.erro)
            $('#response').html(`<div class="alert alert-danger">${response.erro}</div>`)
            
          if (response.erros_model) {
              $.each(response.erros_model, function (key, value) {
                // $('#response').append(`<ul class="list-unstyled"><li class="text-danger">value</li></ul>`)
                $('#response').append(`<div class="alert alert-danger mt-0">${value}</div>`)
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

    // $("#form").submit(function() {
    //   $(this).find(":submit").attr('disable', 'disabled')
    // })
  })

</script>
<?= $this->endSection() ?>