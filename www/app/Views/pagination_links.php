<?php if ($pagination["limit"] < $pagination["total"]) { ?>
<nav aria-label="Navegação da página">
    <ul class="pagination">
        <?php if ($show_btn_inicio && $pagination["limit"] < $pagination["total"]) { ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pagination["url_first_page"]; ?>" aria-label="Início">
                <span aria-hidden="true">Início</span>
                <span class="sr-only">Início</span>
            </a>
        </li>
        <?php } ?>
        <?php if ($show_btn_prev && $pagination["current_page"] > $pagination["first_page"]) { ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pagination["url_prev_page"]; ?>" aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Anterior</span>
            </a>
        </li>
        <?php } ?>
        <?php foreach ($pagination["url_pages"] as $key => $url_page) { ?>
            <li class="page-item">
                <li class="page-item<?= $pagination["current_page"] === $url_page["page"] ? " active" : ""; ?>"><a class="page-link" href="<?= $url_page["url"]; ?>"><?= $url_page["page"]; ?></a></li>
            </li>
        <?php } ?>
        <?php if ($show_btn_next && $pagination["current_page"] < $pagination["last_page"]) { ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pagination["url_next_page"]; ?>" aria-label="Próximo">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Próximo</span>
            </a>
        </li>
        <?php } ?>
        <?php if ($show_btn_fim && $pagination["limit"] < $pagination["total"]) { ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pagination["url_last_page"]; ?>" aria-label="Fim">
                <span aria-hidden="true">Fim</span>
                <span class="sr-only">Fim</span>
            </a>
        </li>
        <?php } ?>
        <?php if ($show_btn_all && $pagination["limit"] < $pagination["total"]) { ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pagination["url_all_single_page"]; ?>" aria-label="Mostrar todos">
                <span aria-hidden="true">Mostrar todos</span>
                <span class="sr-only">Mostrar todos</span>
            </a>
        </li>
        <?php } ?>
    </ul>
</nav>
<?php } ?>
<?php if ($debug) { dumpl($pagination); } ?>
