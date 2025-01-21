<?= $this->extend('Layout/Autenticacao/principal_autenticacao') ?>

<?= $this->section('titulo') ?>
<?php echo $titulo; ?>
<?= $this->endSection() ?>


<?= $this->section('estilos') ?>
<!-- Aqui será colocado estilos especificos -->
<?= $this->endSection() ?>


<?= $this->section('conteudo') ?>

<div class="row">
  <!-- Logo & Information Panel-->
  <div class="col-lg-6">
    <div class="info d-flex align-items-center">
      <div class="content">
        <div class="logo">
          <h1>Esqueci a Senha</h1>
        </div>
        <p>Informe o seu e-mail de acesso para recuperar a senha, seu esquecido.</p>
      </div>
    </div>
  </div>
  <!-- Form Panel    -->
  <div class="col-lg-6 bg-white">
    <div class="form d-flex align-items-center">
      <div class="content">
        <?php echo form_open('/', ['id' => 'form', 'class' => 'form-validate']); ?>
        <?php echo $this->include('Layout/_mensagens') ?>
        <div id="response"></div>
        
        <div class="form-group">
          <input id="email" type="text" name="email" required data-msg="Seu e-mail" class="input-material">
          <label for="email" class="label-material">Informe o seu E-mail</label>
        </div>
      
        <input type="submit" id="btn-login" class="btn btn-primary" value="Enviar E-mail de Recuperar Senha">
        <?php echo form_close(); ?>

      </div>
    </div>
  </div>
</div>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
  $(document).ready(function() {
    $('#form').on('submit', function(e) {
      e.preventDefault()

      $.ajax({
        type: 'POST',
        url: '<?php echo site_url('Password/processaEsqueci'); ?>',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
          $('#response').html('')
          $('#btn-login').html('Aguarde...')
          $('#btn-login').html('Aguarde...')
        },
        success: function(response) {
          $('#btn-login').html('Salvar')
          $('#btn-login').removeAttr('disabled')

          $('[name=csrf_ordem]').val(response.token)
          if (!response.erro)
            window.location.href = `<?php echo site_url('password/resetEnviado'); ?>`

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
          $('#btn-login').html('Salvar')
          $('#btn-login').removeAttr('disabled')
        }
      })

    })
  })
</script>
<?= $this->endSection() ?>