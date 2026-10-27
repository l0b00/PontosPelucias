<?php
/**
 * admin/includes/footer.php - Fim de todas as paginas do painel
 * Fecha as tags abertas no header do painel e carrega o JavaScript.
 */
?>
  </main>

  <footer class="painel-rodape">
    Painel do <?= e(NOME_SITE) ?> &bull; projeto de <?= e(EQUIPE) ?>
  </footer>
</div>

<script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
