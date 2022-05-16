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
}
