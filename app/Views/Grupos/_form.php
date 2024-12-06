<div class="form-group">
  <label class="form-control-label">Nome do grupo:</label>
  <input type="text" name="nome" placeholder="Insira o nome do grupo de acesso" class="form-control" value="<?php echo esc($grupo->nome); ?>" />
</div>

<div class="form-group">
  <label class="form-control-label">Descrição:</label>
  <textarea name="descricao" placeholder="Descrição do grupo" class="form-control" >
    <?php echo esc($grupo->descricao); ?>
  </textarea>
</div>

<div class="custom-control custom-checkbox">
  <input type="hidden" name="exibir" value="0" />
  <input type="checkbox" name="exibir" value="1" 
    class="custom-control-input" id="exibir" 
    <?php if ($grupo->exibir == true): ?> checked <?php endif; ?>
  />
  <label class="custom-control-label" for="exibir">Exibir grupo de acesso</label>
</div>