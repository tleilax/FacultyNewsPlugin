
<form class="">
    <? if (isset($news)) : ?>
        <? foreach ($news as $new) : ?>
            <? if (!empty($new['news']) || $new['isAdmin']) : ?>
                <? $newContent = $new['newNews'] > 0 ? true : false ?>
                <section class="contentbox">
                    <article class="<?= ContentBoxHelper::classes($new['institut']->institut_id, $newContent) ?>" id="<?= $new['institut']->institut_id ?>"
                             data-visiturl="">
                        <header>
                            <h1>
                                <a href="<?= ContentBoxHelper::href($new['institut']->institut_id, array('contentbox_type' => 'news')) ?>">
                                    <?= htmlReady($new['institut']->name) ?>
                                    <? !isset($new['newNews']) ? $new['newNews'] = 0 : '' ?>
                                    <?= count($new['news']) == 0 ? '(keine News)' : sprintf('(%s News, %s neue)', count($new['news']), $new['newNews']) ?>
                                </a>
                                </h1>
                                <div style="float:right;">
                                <? if($new['newNews']> 0 ) : ?>
                                    <a href="<?= $controller->url_for('facultyNews/setRead/'.$new['institut']->institut_id.'/true') ?>">
                                        <?= Icon::create('refresh','clickable',
                                            array('style' => 'vertical-align:middle', 'title' => $_('News dieser Einrichtung als gelesen markieren'))) ?>
                                    </a>
                                <? endif; ?>
                                <? if ($new['isAdmin']) : ?>
                                    <a href="<?= URLHelper::getURL('dispatch.php/news/edit_news/new/' . $new['institut']->institut_id) ?>" rel="get_dialog">
                                        <?= Icon::create('add', 'clickable',
                                                array('style' => 'vertical-align:middle', 'title' => $_('News erstellen'))) ?>
                                    </a>
                                <? endif; ?>
                                </div>
                        </header>
                        <section>

                            <? if (!empty($new['news'])) : ?>
                                <table class="default nohover collapsable" style="width:99%; margin-top: 10px;">
                                    <? foreach ($new['news'] as $entry) : ?>
                                        <tbody class="<?= $entry['news_id'] != Request::get('news_id_open') ? 'collapsed' : '' ?>">
                                            <? $user = new User($entry['user_id']); ?>
                                            <tr class="header-row">

                                                <th class="toggle-indicator" style="white-space:nowrap;"
                                                    onclick="STUDIP.FACULTYNEWS.showNews('<?= $entry['news_id'] ?>')">

                                                    <a href="<?= URLHelper::getURL('dispatch.php/start',
                                                            array('contentbox_open' => Request::get('contentbox_open'),
                                                                'news_id_open' => $entry['news_id']))?>"
                                                            name="<?= $entry['news_id'] ?>" class="toggler">
                                                        <!--NEWS ICON-->
                                                        <? if (!object_get_visit($entry['news_id'], "news", false, false)
                                                                || $entry['chdate'] >= object_get_visit($entry['news_id'], "news", false, false)) :?>

                                                            <?= Icon::create('news+new','clickable',
                                                                    array('style' => 'vertical-align:middle')) ?>
                                                        <? else : ?>
                                                            <?= Icon::create('news', 'clickable',
                                                                    array('style' => 'vertical-align:middle')) ?>
                                                        <? endif; ?>
                                                        <?= htmlReady($entry['topic']) ?>
                                                    </a>

                                                </th>

                                                <th class="dont-hide">
                                                    <a href="<?=$user->user_id != $GLOBALS['user']->user_id ?
                                                               URLHelper::getURL('dispatch.php/profile?username=' . $user->username) :
                                                               URLHelper::getURL('dispatch.php/profile?')?>">
                                                        <?= htmlReady($entry['author']) ?>
                                                    </a>
                                                </th>
                                                <th class="dont-hide">
                                                    <?= strftime('%x', $entry['date']) ?>
                                                </th>
                                                <th class="dont-hide" style="white-space:nowrap;">
                                                    | <span style="color: #050"><?= object_return_views($entry['news_id']) ?></span> |
                                                </th>
                                                <th class="dont-hide actions" style="white-space:nowrap;">
                                                    <? if ($new['isAdmin']) : ?>
                                                        <?= ActionMenu::get()
                                                            ->addLink(
                                                                URLHelper::getLink('dispatch.php/news/edit_news/' . $entry['news_id']),
                                                                $_('Bearbeiten'),
                                                                    Icon::create('admin', 'clickable',
                                                                        ['title' => sprintf($_('Bearbeiten von %s'), htmlReady($entry['topic'])),]),
                                                                    ['data-dialog' => '']
                                                            )
                                                            ->addLink(
                                                                URLHelper::getLink('', array('delete_news' => $entry['news_id'])),
                                                                $_('Löschen'),
                                                                    Icon::create('trash', 'clickable',
                                                                        ['title' => sprintf($_('Löschen von %s'), htmlReady($entry['topic']))])
                                                                ) ?>

                                                    <? endif; ?>
                                                </th>
                                            </tr>
                                            <tr style="border: 1px solid graytext;margin-bottom: 5em;">
                                                <td colspan="5"><?= formatReady($entry['body']) ?></td>
                                            </tr>
                                        </tbody>
                                <? endforeach; ?>
                                    <tr><td colspan="5"></td></tr>
                                </table>
                                <? else : ?>
                                    <span><?= $_('Keine Ankündigungen vorhanden') ?></span>
                                <? endif; ?>
                            </section>
                        </article>
                    </section>
            <? endif; ?>
        <? endforeach; ?>
    <? else : ?>
        <span style="margin-left: 10px;"><?= $_('Ihr Nutzerkonto ist keiner Fakultät zugeordnet') ?></span>
    <? endif; ?>
</form>
