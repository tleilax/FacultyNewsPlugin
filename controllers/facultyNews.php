<?php
/**
 * @author   Stefan Osterloh <s.osterloh@uni-oldenburg.de>
 * @license  GPL2 or any later version
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
        return parent::__call($method, $arguments);
    }

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

            if (!empty($entry['news']) || $entry['isAdmin']) {
                $this->news[] = $entry;
            }
        }

        if (!$this->news) {
            $this->render_template('faculty_news/no-news.php');
        }
    }
}
