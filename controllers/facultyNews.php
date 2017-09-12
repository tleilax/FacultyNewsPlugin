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
class FacultyNewsController extends PluginController
{
    /**
     * Constructs the controller and provide translations methods.
     *
     * @param object $dispatcher
     * @see https://stackoverflow.com/a/12583603/982902 if you need to overwrite
     *      the constructor of the controller
     */
    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);

        $this->plugin = $dispatcher->current_plugin;

        // Localization
        $this->_ = function ($string) use ($dispatcher) {
            return call_user_func_array(
                [$dispatcher->current_plugin, '_'],
                func_get_args()
            );
        };

        $this->_n = function ($string0, $tring1, $n) use ($dispatcher) {
            return call_user_func_array(
                [$dispatcher->current_plugin, '_n'],
                func_get_args()
            );
        };
    }

    /**
     * Intercepts all non-resolvable method calls in order to correctly handle
     * calls to _ and _n.
     *
     * @param string $method
     * @param array  $arguments
     * @return mixed
     * @throws RuntimeException when method is not found
     */
    public function __call($method, $arguments)
    {
        $variables = get_object_vars($this);
        if (isset($variables[$method]) && is_callable($variables[$method])) {
            return call_user_func_array($variables[$method], $arguments);
        }
        throw new RuntimeException("Method {$method} does not exist");
    }

    public function setVisit_action($news_id)
    {
        object_set_visit($news_id, 'news', $GLOBALS['user']->id);
        $this->redirect(URLHelper::getLink('dispatch.php/start'));
    }

    public function setRead_action($news_id, $all = false)
    {
        if (!$all && $all != 'true') {
            object_add_view($news_id);
        } else {
            $facultynews = StudipNews::GetNewsByRange($news_id, false);
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
    public function url_for($to = '')
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

        return PluginEngine::getURL($this->plugin, $params, join('/', $args));
    }
}
