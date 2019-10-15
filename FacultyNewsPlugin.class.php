<?php
require __DIR__ . '/bootstrap.php';

/**
 * FacultyNewsPlugin.class.php
 *
 * @author  Stefan Osterloh <s.osterloh@uni-oldenburg.de>
 * @license GPL2 or any later version
 */
class FacultyNewsPlugin extends StudIPPlugin implements PortalPlugin
{
    const GETTEXT_DOMAIN = 'facultynews';

    public function __construct()
    {
        parent::__construct();

        bindtextdomain(static::GETTEXT_DOMAIN, $this->getPluginPath() . '/locale');
        bind_textdomain_codeset(static::GETTEXT_DOMAIN, 'UTF-8');
    }

    public function getPortalTemplate()
    {
        $trails_root = $this->getPluginPath();
        $dispatcher = new Trails_Dispatcher(
            $trails_root,
            rtrim(PluginEngine::getURL($this, [], '', true), '/'),
            'display'
        );
        $dispatcher->current_plugin = $this;
        $controller = new FacultyNewsController($dispatcher);

        $response = $controller->relay('facultyNews/display');
        $template = $GLOBALS['template_factory']->open('shared/string');
        $template->content = $response->body;

        return $template;
    }

    public function getPluginName()
    {
        return $this->_('Ankündigung der Einrichtungen');
    }

    /**
     * Plugin localization for a single string.
     * This method supports sprintf()-like execution if you pass additional
     * parameters.
     *
     * @param String $string String to translate
     * @return translated string
     */
    public function _($string)
    {
        $result = static::GETTEXT_DOMAIN === null
                ? $string
                : dcgettext(static::GETTEXT_DOMAIN, $string, LC_MESSAGES);
        if ($result === $string) {
            $result = _($string);
        }

        if (func_num_args() > 1) {
            $arguments = array_slice(func_get_args(), 1);
            $result = vsprintf($result, $arguments);
        }

        return $result;
    }

    /**
     * Plugin localization for plural strings.
     * This method supports sprintf()-like execution if you pass additional
     * parameters.
     *
     * @param String $string0 String to translate (singular)
     * @param String $string1 String to translate (plural)
     * @param mixed  $n       Quantity factor (may be an array or array-like)
     * @return translated string
     */
    public function _n($string0, $string1, $n)
    {
        if (is_array($n)) {
            $n = count($n);
        }

        $result = static::GETTEXT_DOMAIN === null
                ? $string0
                : dngettext(static::GETTEXT_DOMAIN, $string0, $string1, $n);
        if ($result === $string0 || $result === $string1) {
            $result = ngettext($string0, $string1, $n);
        }

        if (func_num_args() > 3) {
            $arguments = array_slice(func_get_args(), 3);
            $result = vsprintf($result, $arguments);
        }

        return $result;
    }

}
