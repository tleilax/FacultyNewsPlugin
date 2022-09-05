<?php
/**
 * @author   Stefan Osterloh <s.osterloh@uni-oldenburg.de>
 * @license  GPL2 or any later version
 */
class FacultyNewsController extends PluginController
{
    public function visit_action($id, $all = false)
    {
        if (!$all) {
            object_set_visit($id, 'news', $GLOBALS['user']->id);
            object_add_view($id);
        } else {
            $facultynews = StudipNews::GetNewsByRange($id, false);
            foreach ($facultynews as $news) {
                object_set_visit($news['news_id'], 'news', $GLOBALS['user']->id);
            }
        }

        $this->redirect(URLHelper::getLink('dispatch.php/start'));
    }

    public function display_action()
    {
        $news_id = Request::get('news_id_open');
        if ($news_id) {
            object_set_visit($news_id, 'news', $GLOBALS['user']->id);
            object_add_view($news_id);
        }

        $this->news = [];
        foreach (FacultyNews::getFacultyNews() as $entry) {
            $iNews_new = 0;
            foreach ($entry['news'] as $news) {
                $last_visit = object_get_visit($news['news_id'], 'news', false, false);
                if ($last_visit === false || $news['chdate'] >= $last_visit) {
                    $iNews_new += 1;
                }
            }
            $entry['newNews'] = $iNews_new;

            if (count($entry['news']) > 0) {
                $this->news[] = $entry;
            }
        }

        if (!$this->news) {
            $this->render_template('faculty_news/no-news.php');
        }
    }
}
