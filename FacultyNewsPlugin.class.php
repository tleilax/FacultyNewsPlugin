<?php
/**
 * FacultyNewsPlugin.class.php
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

require_once 'controllers/facultyNews.php';

class FacultyNewsPlugin extends StudIPPlugin implements PortalPlugin 
{
    function getPortalTemplate()
    {
        $trails_root = $this->getPluginPath();
        $dispatcher = new Trails_Dispatcher($trails_root, "plugins.php", 'display');
        $controller = new FacultyNewsController($dispatcher); 

        $response = $controller->relay('facultyNews/display');
        $template = $GLOBALS['template_factory']->open('shared/string');
        $template->content = $response->body;

        $script_attributes = array(
            'src' => $GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP']
            . $this->getPluginPath()
            . '/assets/application.js'
        );
        PageLayout::addHeadElement('script', $script_attributes, '');

        $ajaxURL = PluginEngine::getURL('FacultyNewsPlugin/facultyNews');
        $init_js = 'STUDIP.FACULTYNEWS.setAjaxURL(\'' . $ajaxURL . '\');';
        PageLayout::addHeadElement('script', array(), $init_js);

        return $template;
    }

    function getPluginName()
    {
        return _('Ankündigung der Einrichtungen');
    }

    function getHeaderOptions()
    {
        $options = array();
        
        return $options;
    }
}

