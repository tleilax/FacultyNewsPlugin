<?php

require_once 'lib/classes/Institute.class.php';

class FacultyNewsPlugin extends StudIPPlugin implements SystemPlugin, PortalPlugin
{
    function __construct()
    {
        if (basename($_SERVER['PHP_SELF']) == 'index.php') {
            $script = <<<EOT
STUDIP.FacultyNews = {
  openclose: function (id, admin_link) {
    if (jQuery("#news_item_" + id + "_content").is(':visible')) {
      STUDIP.FacultyNews.close(id);
    } else {
      STUDIP.FacultyNews.open(id, admin_link);
    }
  },

  open: function (id, admin_link) {
    jQuery("#news_item_" + id + "_content").load(
      STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/facultynewsplugin/get_news/' + id,
      {admin_link: admin_link},
      function () {
        jQuery("#news_item_" + id + "_content").slideDown(400);
        jQuery("#news_item_" + id + " .printhead2 img")
            .attr('src', STUDIP.ASSETS_URL + "images/forumgraurunt2.png");
        jQuery("#news_item_" + id + " .printhead2")
            .removeClass("printhead2")
            .addClass("printhead3");
        jQuery("#news_item_" + id + " .printhead b").css("font-weight", "bold");
        jQuery("#news_item_" + id + " .printhead a.tree").css("font-weight", "bold");
      });
  },

  close: function (id) {
    jQuery("#news_item_" + id + "_content").slideUp(400);
    jQuery("#news_item_" + id + " .printhead3 img")
        .attr('src', STUDIP.ASSETS_URL + "images/forumgrau2.png");
    jQuery("#news_item_" + id + " .printhead3")
        .removeClass("printhead3")
        .addClass("printhead2");
    jQuery("#news_item_" + id + " .printhead b").css("font-weight", "normal");
    jQuery("#news_item_" + id + " .printhead a.tree").css("font-weight", "normal");
  }
};
jQuery('document').ready(function(){
    var position = jQuery('img[src$="breaking-news.png"]').size() - 1;
    jQuery(jQuery('.index_box')[position]).after(jQuery('#facultynewsplugin').parents('.index_box'));
});
EOT;
            PageLayout::addHeadElement('script', array('type'=>'text/javascript'), $script);
        }
    }

    function get_news_action($id = null)
    {

        require_once 'lib/showNews.inc.php';

        if (!$id || preg_match('/[^\\w,-]/', $id)) {
            throw new Exception('wrong parameter');
        }

        $news = StudipNews::find($id);

        if (is_null($news)) {
            throw new Exception('wrong parameter');
        }
        $permitted = $show_admin = false;
        foreach ($news->getRanges() as $range) {
            if (get_object_type($range, array('inst'))) {
                $permitted = true;
                $show_admin = $GLOBALS['perm']->have_studip_perm('tutor', $range);
            }
        }
        if (!$permitted) {
            throw new AccessDeniedException();
        }

        $newscontent = $news->toArray();
        // use the same logic here as in show_news_item()
        if ($newscontent['user_id'] != $GLOBALS['auth']->auth['uid']) {
            object_add_view($id);
        }

        object_set_visit($id, "news");
        $content = show_news_item_content($newscontent,
                                          array(),
                                          $show_admin,
                                          Request::get('admin_link')
                                          );
        echo studip_utf8encode($content);
    }

    function getPortalTemplate()
    {
        $last_inst_news_open =& $GLOBALS['user']->user_vars['last_inst_news_open'];
        if (Request::get('faculty_news_toggle')) {
            if (Request::get('faculty_news_toggle') == $last_inst_news_open) $last_inst_news_open = null;
            else $last_inst_news_open = Request::get('faculty_news_toggle');
        }
        //$institutes = Institute::findBySql('Institut_id = fakultaets_id ORDER BY Name ASC');
        $institutes = Institute::findBySql("Institut_id IN (SELECT Institut_id FROM user_inst WHERE user_id='".$GLOBALS['user']->id."' AND inst_perms ='user') ORDER BY Name ASC");
        if (count($institutes)) {
            ob_start();

            foreach($institutes as $inst_item) {
                $news = StudipNews::GetNewsByRange($inst_item->getId(), true);
                if (count($news)) {
                    echo '<div id="facultynewsplugin"></div>';
                    $iNews_new = 0;
                    $newest = current($news);
                    foreach ($news as $news_id => $news_item) {
                        // wann wurde die news zuletzt geöffnet
                        $last_visit = object_get_visit($news_item['news_id'], "news",false,false);
                        if ($last_visit === false){
                            $last_visit = 0;
                        }
                        if ($news_item['chdate'] >= $last_visit) $iNews_new++;
                    }
                    // anzeige von fakultät und anzahl gefundener news
                    $tmp_titel  = htmlReady(mila($inst_item['Name']));
                    $tmp_titel .=" (" . count($news) . " News, " . $iNews_new. " neue)";
                    $link = UrlHelper::getLink('?faculty_news_toggle=' . $inst_item->getId() . '&foo=' . rand() .'#anker');
                    $titel = "<a href=\"$link\" class=\"tree\" >".$tmp_titel."</a>";
                    if ($last_inst_news_open == $inst_item->getId()) {
                        $open = 'open';
                        $anker = '<a name="anker"> </a>';
                    } else {
                        $open = 'close';
                        $anker = '';
                    }
                    $icon = count($news) ? Assets::img('icons/16/blue/folder-full.png') : Assets::img('icons/16/blue/folder-empty.png');
                    echo '<div id="news_inst_item_'.$inst_item->getId().'">';
                    echo $anker;
                    echo '<table style="width:100%;" border="0" cellpadding="0" cellspacing="0"><tr>';
                    printhead(0, 0, $link, $open, false, $icon, $titel, '', $newest['chdate']);
                    if ($open == 'open') {
                        $cmd_data = array();
                        process_news_commands($cmd_data);
                        echo '</tr><tr>';
                        echo '<td colspan="4" >';
                        foreach ($news as $id => $news_item) {
                            $news_item['open'] = ($id == $cmd_data["nopen"]);
                            echo '<div id="news_item_'.$id.'" style="padding-left:5px;">';
                            echo str_replace('STUDIP.News.openclose', 'STUDIP.FacultyNews.openclose', show_news_item($news_item, $cmd_data, $GLOBALS['perm']->have_studip_perm('tutor', $inst_item->getId()), 'new_inst=TRUE&view=news_inst&range_id='.$inst_item->getId()));
                            echo '</div>';
                        }
                        echo '</td>';
                    }
                    echo '</tr></table>';
                    echo '</div>';
                }
            }

            $template = $GLOBALS['template_factory']->open('shared/string.php');
            $template->set_attribute('content', ob_get_clean());
            $template->set_attribute('title', _("Ankündigungen der Fakultäten"));
            $template->set_attribute('icon_url',Assets::image_path('icons/16/white/breaking-news.png'));
            return $template;
        }
    }
}