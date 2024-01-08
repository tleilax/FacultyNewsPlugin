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
        $condition = "1 ORDER BY Name";
        $parameters = [];
        if (!$GLOBALS['perm']->have_perm('root')) {
            $condition = "Institut_id IN (
                              SELECT Institut_id
                              FROM user_inst
                              WHERE user_id = ?
                          ) ORDER BY Name";
            $parameters[] = User::findCurrent()->id;
        }
        return Institute::findAndMapBySQL(
            function (Institute $institute): array {
                return [
                    'name'     => $institute->name,
                    'institut' => $institute,
                    'isAdmin'  => self::editableForUser($institute->id),
                    'news'     => StudipNews::GetNewsByRange($institute->id, true, true),
                ];
            },
            $condition,
            $parameters
        );
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
