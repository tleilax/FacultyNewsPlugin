<?php
/**
 * facultryNews.php -> FacultyNews Model
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

class FacultyNews
{
     /**
     * Get a file object for the given id. May be file or directory.
     * If the file does not exist, a new (virtual) RootDirectory is
     * created for this id. TODO Is this a good idea?
     *
     *
     * @return array 
     */
    public static function getFacultyNews(){
        $institutes = Institute::findBySql('Institut_id IN (SELECT Institut_id FROM user_inst WHERE user_id = :user_id) ORDER BY Name ASC',
                array(':user_id' => $GLOBALS['user']->id));
        $result = array();
        foreach($institutes as $institut){
            $tmp['name'] = $institut->name;
            $tmp['institut'] = $institut;
            $tmp['isAdmin']  = FacultyNews::editableForUser($institut->Institut_id);
            $tmp['news'] = StudipNews::GetNewsByRange($institut->getId(), true);
            $result[$institut->Institut_id] = $tmp;    
        }
        return $result;
    }
    
     /**
     * Get a file object for the given id. May be file or directory.
     * If the file does not exist, a new (virtual) RootDirectory is
     * created for this id. TODO Is this a good idea?
     *
     * @param string $inst_id  Institut_id
     *
     * @return bool
     */
    public static function editableForUser($inst_id)
    {
        $db = DBManager::get();
        $query = 'SELECT 1 FROM user_inst WHERE Institut_id = :inst_id AND user_id = :user_id AND inst_perms = :perm';
        $stm = $db->prepare($query);
        $stm->execute(array(':inst_id' => $inst_id, ':user_id' => $GLOBALS['user']->id, ':perm' => 'admin'));
        return $stm->fetchColumn();
    }
}

