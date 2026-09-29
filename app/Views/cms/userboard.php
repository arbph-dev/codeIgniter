<?= $this->extend('layouts/cms_w3c') ?> 

<?= $this->section('title') ?>
    User Board
<?= $this->endSection() ?>


<?= $this->section('topbar') ?>
    <div class="w3-bar w3-black">
        <button class="w3-bar-item w3-button" onclick="openTabs('tab1')">tab1</button>
        <button class="w3-bar-item w3-button" onclick="openTabs('tab2')">tab2</button>
        <button class="w3-bar-item w3-button" onclick="openTabs('tab3')">tab3</button>
    </div>
<?= $this->endSection() ?>


<?= $this->section('main') ?>
    <div id="main" class="w3-content">

        <div class="w3-container w3-indigo">
            <p><a href="/">Accueil</a> | <a href="/logout">logout</a></p>
        </div>

        <h1>Userboard</h1>

        <p>Bienvenue <?= auth()->user()->username ?></p>

    </div>        
<?= $this->endSection() ?>




<?= $this->section('tab1') ?>
    <p>
        Board user
    </p>
<?= $this->endSection() ?>    


<?= $this->section('tab2') ?>
    <p>tab2</p>
<?= $this->endSection() ?>

<?= $this->section('tab3') ?>
    <p>tab3</p>
<?= $this->endSection() ?>    