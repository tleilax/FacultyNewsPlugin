<article class="studip">
<? foreach ($news as $new) : ?>
    <? $newContent = $new['newNews'] > 0; ?>
    <article class="studip toggle <?= ContentBoxHelper::classes($new['institut']->institut_id, $newContent) ?>" id="<?= $new['institut']->institut_id ?>" data-visiturl="">
        <header>
            <h1>
                <a href="<?= ContentBoxHelper::href($new['institut']->institut_id, ['contentbox_type' => 'news']) ?>">
                    <?= htmlReady($new['institut']->name) ?>
                <? if (count($new['news']) == 0): ?>
                    <?= $_('(keine News)') ?>
                <? else: ?>
                    <?= sprintf($_('(%s News, %s neue)'), count($new['news']), $new['newNews']) ?>
                <? endif; ?>
                </a>
            </h1>
        <? if ($new['newNews'] > 0 || $new['isAdmin']): ?>
            <nav>
            <? if ($new['newNews'] > 0 ) : ?>
                <a href="<?= $controller->link_for("facultyNews/visit/{$new['institut']->institut_id}/1") ?>">
                    <?= Icon::create('refresh')->asImg([
                        'title' => $_('News dieser Einrichtung als gelesen markieren'),
                    ]) ?>
                </a>
            <? endif; ?>
            <? if ($new['isAdmin']) : ?>
                <a href="<?= URLHelper::getLink('dispatch.php/news/edit_news/new/' . $new['institut']->institut_id) ?>" rel="get_dialog">
                    <?= Icon::create('add')->asImg([
                        'title' => $_('News erstellen'),
                    ]) ?>
                </a>
            <? endif; ?>
            </nav>
        <? endif; ?>
        </header>
        <section>
        <? if (!$new['news']) : ?>
            <span><?= $_('Keine Ankündigungen vorhanden') ?></span>
        <? else: ?>
            <? foreach ($new['news'] as $entry) : ?>
                <? $is_new = $entry['chdate'] >= object_get_visit($entry['news_id'], 'news', false, false); ?>
                <? $user = new User($entry['user_id']); ?>
                <article class="studip toggle <?= ContentBoxHelper::classes($new['institut']->institut_id, $is_new) ?>" data-visiturl="<?= $controller->link_for('facultyNews/visit/' . $entry['news_id'], ['dummy' => '']) ?>">
                    <header>
                        <h1>
                            <a href="<?= URLHelper::getLink('dispatch.php/start', [
                                'contentbox_open' => Request::get('contentbox_open'),
                                'news_id_open' => $entry['news_id'],
                            ]) ?>">
                                <?= Icon::create($is_new ? 'news+new' : 'news') ?>
                                <?= htmlReady($entry['topic']) ?>
                            </a>
                        </h1>
                        <nav>
                            <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $user->username]) ?>">
                                <?= htmlReady($entry['author']) ?>
                            </a>
                            <span><?= strftime('%x', $entry['date']) ?></span>
                            <span style="color: #050"><?= object_return_views($entry['news_id']) ?></span>
                        <? if ($new['isAdmin']) : ?>
                            <?= ActionMenu::get()->addLink(
                                URLHelper::getLink('dispatch.php/news/edit_news/' . $entry['news_id']),
                                $_('Bearbeiten'),
                                Icon::create('admin'),
                                [
                                    'title'       => sprintf($_('Bearbeiten von %s'), htmlReady($entry['topic'])),
                                    'data-dialog' => '',
                                ]
                            )->addLink(
                                URLHelper::getLink('', array('delete_news' => $entry['news_id'])),
                                $_('Löschen'),
                                Icon::create('trash'),
                                ['title' => sprintf($_('Löschen von %s'), htmlReady($entry['topic']))]
                            )->render() ?>
                        <? endif; ?>
                        </nav>
                    </header>
                    <section><?= formatReady($entry['body']) ?></section>
                </article>
            <? endforeach; ?>
        <? endif; ?>
        </section>
    </article>
<? endforeach; ?>
</article>
