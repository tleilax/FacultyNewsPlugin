<?php
/**
 * facultryNews.php -> FacultyNewsConroller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Stefan Osterloh <s.osterloh@uni-oldenburg.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP Core Plugin
 */
class FacultyNewsController extends StudipController {

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args) {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
    }
 
    public function setVisit_action($news_id)
    {
        object_set_visit($news_id, 'news', $GLOBALS['user']->id);
        //$this->render_nothing();
        $this->redirect(URLHelper::getLink('dispatch.php/start'));
    }
    
    public function setRead_action($news_id, $all = false)
    {
        if(!$all && $all != 'true'){
            object_add_view($news_id);
            //$this->render_nothing();
        } else {
            $facultynews = StudipNews::GetNewsByRange($news_id, false);
            foreach($facultynews as $news){
                object_set_visit($news['news_id'], 'news', $GLOBALS['user']->id);
            }
            //$this->redirect(URLHelper::getLink('dispatch.php/start'));
        }
        $this->redirect(URLHelper::getLink('dispatch.php/start'));
    }
    
    public function display_action() 
    {
        $openNews = $news_id;
        $news_id = Request::get('news_id_open');
        if($news_id != ''){
            object_set_visit($news_id, 'news', $GLOBALS['user']->id);
            object_add_view($news_id);
        }
        $news = FacultyNews::getFacultyNews();
        foreach ($news as $entry) {
            $iNews_new = 0;
            foreach ($entry['news'] as $news) {
                $last_visit = object_get_visit($news['news_id'], "news", false, false);
                if ($last_visit === false || $news['chdate'] >= $last_visit) {
                    $iNews_new++;
                }
            }
            $entry['newNews'] =  $iNews_new;
            $this->news[] = $entry;
        }
    }

// customized #url_for for plugins
    function url_for($to)
    {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join('/', $args));
    } 
}