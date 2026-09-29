<div>

    <section>
        <h3>L'adoucisseur</h3>
        <div>
            <div>
                <p><?= esc($data['principe']) ?></p>
                <h4>Choix et dimensionnement</h4>
                <ul>
                    <?php foreach ($data['dimensionnement'] as $d): ?>
                        <li><?= esc($d) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <aside>
                <p class="cp_callout"><?= esc($data['perso']) ?></p>
            </aside>
        </div>
    </section>

    <?php foreach ($data['types'] as $type): ?>
    <section>
        <h3><?= esc($type['nom']) ?></h3>
        <div>
            <div>
                <p><?= esc($type['description']) ?></p>
            </div>
            <aside>
                <ul>
                    <?php foreach ($type['specs'] as $spec): ?>
                        <li><?= esc($spec) ?></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </div>
    </section>
    <?php endforeach; ?>

</div>
