<div class="page-header">
    <h2><?= t('Remove a tag') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this tag: "%s"?', $tag['name']) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Yes'), 'ProjectTagController', 'remove', ['tag_id' => $tag['id'], 'project_id' => $project['id']], true, 'btn btn-danger popover-link') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'ProjectTagController', 'index', ['project_id' => $project['id']], false, 'close-popover') ?>
    </div>
</div>
