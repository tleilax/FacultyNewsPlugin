<?php
/**
 * @author   Stefan Osterloh <s.osterloh@uni-oldenburg.de>
 * @license  GPL2 or any later version
 * @category Stud.IP Core Plugin
 */
class FacultyNews
{
    public static function getFacultyNews(): array
    {
        $condition = 'Institut_id IN (
                        SELECT Institut_id
                        FROM user_inst
                        WHERE user_id = :user_id
                     ) ORDER BY Name ASC';
        $institutes = Institute::findBySql($condition, [
            ':user_id' => $GLOBALS['user']->id,
        ]);

        $result = [];
        foreach ($institutes as $institut) {
            $result[$institut->id] = [
                'name'     => $institut->name,
                'institut' => $institut,
                'isAdmin'  => self::editableForUser($institut->Institut_id),
                'news'     => StudipNews::GetNewsByRange($institut->id, true, true),
            ];
        }
        return $result;
    }

    public static function editableForUser(string $institute_id): bool
    {
        $query = "SELECT 1
                  FROM user_inst
                  WHERE Institut_id = :inst_id
                    AND user_id = :user_id
                    AND inst_perms = :perm";
        return (bool) DBManager::get()->fetchColumn($query, [
            ':inst_id' => $institute_id,
            ':user_id' => User::findCurrent()->id,
            ':perm' => 'admin',
        ]);
    }
}
