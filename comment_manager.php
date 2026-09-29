<?php

if (! defined('IN_ADMIN'))
{
    exit;
}

$stylee     = 'comment_manager';
$styleePath = __DIR__;

kj_comment_olang_fallback();

$no_results = false;
$comments   = [];

$comments_query =
    [
        'SELECT'  => 'c.id , c.user , c.comment , c.file_id , c.time , u.name , f.real_filename , f.name AS file_name',
        'FROM'    => $dbprefix . 'comments c',
        'JOINS'   =>
        [
            [
                'INNER JOIN' => "{$dbprefix}users u",
                'ON'         => 'c.user = u.id'
            ],
            [
                'INNER JOIN' => "{$dbprefix}files f",
                'ON'         => 'c.file_id = f.id'
            ]
        ],
        'ORDER BY' => 'c.id DESC'
    ];

$all_comments = $SQL->build($comments_query);

if ($num_rows = $SQL->num_rows($all_comments))
{
    $perpage                    = 21;
    $currentPage                = ig('page') ? g('page', 'int') : 1;
    $Pager                      = new Pagination($perpage, $num_rows, $currentPage);
    $start                      = $Pager->getStartRow();
    $linkgoto                   = basename(ADMIN_PATH) . '?cp=comment_manager';
    $page_nums                  = $Pager->print_nums($linkgoto);
    $comments_query['LIMIT']    = "$start, $perpage";
    $all_comments               = $SQL->build($comments_query);

    while ($cmnt = $SQL->fetch($all_comments))
    {
        [$cmnt['initial'], $cmnt['avatar_style']] = kj_comment_avatar($cmnt['name'], (int) $cmnt['user']);

        $cmnt['full_time'] = kleeja_date($cmnt['time'], false);
        $cmnt['time']      = kleeja_date($cmnt['time']);
        $cmnt['user_link'] = kj_comment_user_link((int) $cmnt['user']);
        $cmnt['file_link'] = $config['siteurl'] . 'do.php?id=' . $cmnt['file_id'] . '#comment-' . $cmnt['id'];
        $cmnt['file_name'] = $cmnt['real_filename'] != '' ? $cmnt['real_filename'] : $cmnt['file_name'];
        $cmnt['excerpt']   = kj_comment_excerpt($cmnt['comment']);
        $comments[]        = $cmnt;
    }
}
else
{
    $no_results = true;
}

$delFormAction = $config['siteurl'] . 'ucp.php?go=comment&amp;action=del';
$form_key      = kleeja_add_form_key(kj_comment_form_name());
