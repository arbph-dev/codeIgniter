<!-- app/Views/admin/component_types/create.php -->
<h1>Nouveau type de composant</h1>

<form method="post" action="<?= site_url('/admin/component-types/insert') ?>">

    <?= csrf_field() ?>

    <?= $this->include('admin/component_types/form') ?>

    <button type="submit" class="btn btn-primary">
        Créer
    </button>

    <a href="<?= site_url('/admin/component-types') ?>" class="btn btn-secondary">
        Annuler
    </a>

</form>