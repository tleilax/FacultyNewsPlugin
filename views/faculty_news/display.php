
<form class="studip_form">
    <? if (isset($news)) : ?>
        <? foreach ($news as $new) : ?>
            <? if (!empty($new['news']) || $new['isAdmin']) : ?>
                <? $newContent = $new['newNews'] > 0 ? true : false ?> 
                <section class="contentbox">
                    <article class="<?= ContentBoxHelper::classes($new['institut']->institut_id, $newContent) ?>" id="<?= $new->id ?>">
                        <header>
                            <h1>
                                <a href="<?= ContentBoxHelper::href($new['institut']->institut_id) ?>">
                                    <?= htmlReady($new['institut']->name) ?>
                                    <? !isset($new['newNews']) ? $new['newNews'] = 0 : '' ?>
                                    <?= count($new['news']) == 0 ? '(keine News)' : sprintf('(%s News, %s neue)', count($new['news']), $new['newNews']) ?>
                                </a>
                                </h1>
                                <div style="float:right;">
                                <? if($new['newNews']> 0 ) : ?>    
                                    <a href="<?= PluginEngine::getURL('FacultyNewsPlugin/facultyNews').'/setRead/'.$new['institut']->institut_id.'/true'?>">
                                        <?= Assets::img('icons/16/blue/refresh.png', 
                                            array('style' => 'vertical-align:middle', 'title' => _('News dieser Einrichtung als gelesen markieren'))) ?>
                                    </a>
                                <? endif; ?>
                                <? if ($new['isAdmin']) : ?>
                                    <a href="<?= URLHelper::getURL('dispatch.php/news/edit_news/new/' . $new['institut']->institut_id) ?>" rel="get_dialog">
                                        <?= Assets::img('icons/16/blue/add.png', 
                                                array('style' => 'vertical-align:middle', 'title' => _('News erstellen'))) ?>
                                    </a>
                                <? endif; ?>    
                                </div>
                                
                            
                        </header>
                        <section>
                            <? if (!empty($new['news'])) : ?>
                                <table class="collapsable default" style='width:100%' >
                                    <? foreach ($new['news'] as $entry) : ?>
                                        <tbody class="<?= $entry['news_id'] != Request::get('news_id_open') ? 'collapsed' : '' ?>">
                                            <? $user = new User($entry['user_id']); ?>
                                            <tr class="table_header header-row">
                                                <td class="toggle-indicator" style="width: 50%" 
                                                    onclick="STUDIP.FACULTYNEWS.showNews('<?= $entry['news_id'] ?>')">
                                                    <a href="<?= URLHelper::getURL('', 
                                                            array('contentbox_open' => Request::get('contentbox_open'),
                                                                'news_id_open' => $entry['news_id']))?>"
                                                            name="<?= $entry['news_id'] ?>" class="toggler">
                                                        <!--NEWS ICON-->
                                                        <? if (!object_get_visit($entry['news_id'], "news", false, false) 
                                                                || $entry['chdate'] >= object_get_visit($entry['news_id'], "news", false, false)) :?> 

                                                            <?= Assets::img('icons/16/red/news.png', 
                                                                    array('style' => 'vertical-align:middle')) ?>
                                                        <? else : ?>
                                                            <?= Assets::img('icons/16/grey/news.png', 
                                                                    array('style' => 'vertical-align:middle')) ?>
                                                        <? endif; ?>
                                                        <?= htmlReady($entry['topic']) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="<?=$user->user_id != $GLOBALS['user']->user_id ?
                                                               URLHelper::getURL('dispatch.php/profile?username=' . $user->username) :
                                                               URLHelper::getURL('dispatch.php/profile?')?>">
                                                        <?= htmlReady($entry['author']) ?>
                                                    </a>
                                                </td>
                                                <td><?= date("d.m.Y", $entry['date']) ?></td>
                                                <td>| <span style="color: #050"><?= object_return_views($entry['news_id']) ?></span> |</td>
                                                <td>
                                                    <? if ($new['isAdmin']) : ?>
                                                        <a href=" <?= URLHelper::getLink('dispatch.php/news/edit_news/' . $entry['news_id']) ?>" rel='get_dialog' >
                                                            <?= Assets::img('icons/16/blue/admin.png'); ?>
                                                        </a>
                                                        <a href=" <?= URLHelper::getLink('', array('delete_news' => $entry['news_id'])) ?>" >
                                                            <?= Assets::img('icons/16/blue/trash.png'); ?>
                                                        </a>
                                                    <? endif; ?>
                                                </td>
                                            </tr>
                                            <tr style="border: 1px solid graytext;margin-bottom: 5em;">
                                                <td colspan="4"><?= formatReady($entry['body']) ?></td>
                                            </tr>
                                        </tbody>
                                <? endforeach; ?>
                                    <tr><td colspan="4"></td></tr>
                                </table>
                                <? else : ?>
                                    <span><?= _('Keine Ankündigungen vorhanden') ?></span>
                                <? endif; ?>
                            </section>
                        </article>
                    </section>
            <? endif; ?>
        <? endforeach; ?>
<? else : ?>
        <span style="margin-left: 10px;"><?= _('Ihr Nutzerkonto ist keiner Fakultät zugeordnet') ?></span>
<? endif; ?>
</form>
