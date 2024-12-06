<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?>
<?php echo $titulo; ?>
<?= $this->endSection() ?>


<?= $this->section('estilos') ?>
<!-- Aqui será colocado estilos especificos -->
<link href="https://cdn.datatables.net/v/bs4/dt-2.1.8/r-3.0.3/datatables.min.css" rel="stylesheet">

<?= $this->endSection() ?>


<?= $this->section('conteudo') ?>
<div class="col-lg-12">
  <div class="block">

    <div class="d-flex justify-content-end">
      <a href="<?php echo site_url('grupos/criar'); ?>" class="btn btn-danger mb-4">Criar Novo Grupo</a>
    </div>

    <div class="title"><strong>Relação de Grupos</strong></div>
    <div class="table-responsive">
      <table class="table table-striped table-sm" id="ajaxTable" style="width: 100%;">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Exibir</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<!-- Aqui será colocado scripts especificos -->
<script src="https://cdn.datatables.net/v/bs4/dt-2.1.8/r-3.0.3/datatables.min.js"></script>
<script>
  $(document).ready(function() {
    const DATATABLE_PTBR = {
      "sEmptyTable": "Nenhum registro encontrado",
      "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
      "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
      "sInfoFiltered": "(Filtrados de _MAX_ registros)",
      "sInfoPostFix": "",
      "sInfoThousands": ".",
      "sLengthMenu": "_MENU_ resultados por página",
      "sLoadingRecords": "Carregando...",
      "sProcessing": "Processando...",
      "sZeroRecords": "Nenhum registro encontrado",
      "sSearch": "Pesquisar",
      "oPaginate": {
        "sNext": "Próximo",
        "sPrevious": "Anterior",
        "sFirst": "Primeiro",
        "sLast": "Último"
      },
      "oAria": {
        "sSortAscending": ": Ordenar colunas de forma ascendente",
        "sSortDescending": ": Ordenar colunas de forma descendente"
      },
      "select": {
        "rows": {
          "_": "Selecionado %d linhas",
          "0": "Nenhuma linha selecionada",
          "1": "Selecionado 1 linha"
        }
      }
    }
    $('#ajaxTable').DataTable({
      "oLanguage": DATATABLE_PTBR,
      "ajax": "<?php echo site_url('grupos/recuperaGrupos'); ?>",
      "columns": [
        {
          data: 'nome'
        },
        {
          data: 'descricao'
        },
        {
          data: 'exibir'
        },
      ],
      "deferRender": true, 
      "processing": true, 
      "language": {
        processing: '<i class="fa fa-spin fa-3x fa-fw"></i>'
      }, 
      "responsive": true, 
    });
  })
</script>
<?= $this->endSection() ?>