<?php
// Kleeja Plugin
// KJ_COMMENT
// Developer: Kleeja Team

// Prevent illegal run
if (! defined('IN_PLUGINS_SYSTEM'))
{
    exit();
}

// version of the plugin, also added to the css and js urls to refresh the browser cache
define('KJ_COMMENT_VERSION', '1.1.0');

// the longest comment that can be posted, in characters
define('KJ_COMMENT_MAX_LENGTH', 1000);

// seconds a comment form stays valid, the page can be open for a while before a comment is written
define('KJ_COMMENT_FORM_TIME', 3600);


/**
 * words of the plugin, added to $olang at install and update
 * @return array
 */
function kj_comment_translations(): array
{
    return [
        'en' => [
            'NO_4_GUEST'         => 'Log in to join the conversation.',
            'COMNTS'             => 'Comments',
            'COMNT'              => 'Comment',
            'ADD_COMNT'          => 'Add a comment',
            'R_COMMENT_MANAGER'  => 'Comment Manager',
            'KJC_PLACEHOLDER'    => 'Write a comment…',
            'KJC_SEND'           => 'Post comment',
            'KJC_SHORTCUT'       => 'Ctrl + Enter to post',
            'KJC_EMPTY_LIST'     => 'No comments yet. Be the first to comment.',
            'KJC_EMPTY'          => 'Write something before posting.',
            'KJC_TOO_LONG'       => 'A comment can be %s characters at most.',
            'KJC_ADDED'          => 'Your comment was posted.',
            'KJC_DELETED'        => 'The comment was deleted.',
            'KJC_DELETE_CONFIRM' => 'Delete this comment? This can\'t be undone.',
            'KJC_NOT_FOUND'      => 'This comment no longer exists, or you can\'t delete it.',
            'KJC_UPLOADER'       => 'Uploader',
            'KJC_ADMIN_HINT'     => 'Comments posted on the files of the site, newest first.',
        ],
        'ar' => [
            'NO_4_GUEST'         => 'سجّل الدخول لتشارك في النقاش.',
            'COMNTS'             => 'التعليقات',
            'COMNT'              => 'تعليق',
            'ADD_COMNT'          => 'أضف تعليقًا',
            'R_COMMENT_MANAGER'  => 'مدير التعليقات',
            'KJC_PLACEHOLDER'    => 'اكتب تعليقًا…',
            'KJC_SEND'           => 'نشر التعليق',
            'KJC_SHORTCUT'       => 'Ctrl + Enter للنشر',
            'KJC_EMPTY_LIST'     => 'لا توجد تعليقات بعد، كن أول من يعلّق.',
            'KJC_EMPTY'          => 'اكتب شيئًا قبل النشر.',
            'KJC_TOO_LONG'       => 'لا يمكن أن يزيد التعليق عن %s حرف.',
            'KJC_ADDED'          => 'تم نشر تعليقك.',
            'KJC_DELETED'        => 'تم حذف التعليق.',
            'KJC_DELETE_CONFIRM' => 'هل تريد حذف هذا التعليق؟ لا يمكن التراجع عن ذلك.',
            'KJC_NOT_FOUND'      => 'التعليق لم يعد موجودًا، أو لا تملك صلاحية حذفه.',
            'KJC_UPLOADER'       => 'صاحب الملف',
            'KJC_ADMIN_HINT'     => 'التعليقات المنشورة على ملفات الموقع، الأحدث أولًا.',
        ],
    ];
}

/**
 * English words for a language without translation, so a template never shows {olang.KEY}
 */
function kj_comment_olang_fallback(): void
{
    global $olang;

    $olang = (array) $olang + kj_comment_translations()['en'];
}

/**
 * id of the plugin in the plugins table, the update callback is not given it
 * @return int
 */
function kj_comment_plugin_id(): int
{
    global $SQL, $dbprefix;

    $query = $SQL->build([
        'SELECT' => 'plg_id',
        'FROM'   => "{$dbprefix}plugins",
        'WHERE'  => "plg_name = 'kj_comment'",
        'LIMIT'  => '1',
    ]);

    $row = $SQL->fetch($query);

    return $row ? (int) $row['plg_id'] : 0;
}

/**
 * url of a file in the plugin folder, the version busts the browser cache after an update
 * @param  string $file
 * @return string
 */
function kj_comment_asset(string $file): string
{
    global $config;

    return $config['siteurl'] . KLEEJA_PLUGINS_FOLDER . '/kj_comment/' . $file . '?v=' . KJ_COMMENT_VERSION;
}

/**
 * name of the csrf form key, one for every member
 * @return string
 */
function kj_comment_form_name(): string
{
    global $usrcp;

    return 'comment_for_' . $usrcp->name();
}

/**
 * can the current user delete a comment of this author
 * @param  int  $author_id
 * @return bool
 */
function kj_comment_can_delete(int $author_id): bool
{
    global $usrcp;

    return $usrcp->name() && ($author_id == $usrcp->id() || user_can('enter_acp'));
}

/**
 * first letter of a name and a color from the user id, for the avatar circle
 * @param  string $name html encoded name
 * @param  int    $user_id
 * @return array  [initial, style]
 */
function kj_comment_avatar(string $name, int $user_id): array
{
    $plain   = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
    $initial = mb_strtoupper(mb_substr($plain, 0, 1));

    // a light tint and a dark text of the same hue, readable for every hue
    $hue = ($user_id * 137) % 360;

    return [
        htmlspecialchars($initial, ENT_QUOTES),
        "background-color: hsl({$hue}, 70%, 92%); color: hsl({$hue}, 55%, 28%);",
    ];
}

/**
 * link to the files page of a user
 * @param  int    $user_id
 * @return string
 */
function kj_comment_user_link(int $user_id): string
{
    global $config;

    return $config['siteurl'] . ($config['mod_writer'] ? 'fileuser-' . $user_id . '.html' : 'ucp.php?go=fileuser&amp;id=' . $user_id);
}

/**
 * comments of a file, newest first, ready for comment_list.html
 * @param  int   $file_id
 * @return array
 */
function kj_comment_fetch(int $file_id): array
{
    global $SQL, $dbprefix;

    $query = $SQL->build([
        'SELECT'   => 'c.id, c.user, c.comment, c.file_id, c.time, u.name, f.user AS file_user',
        'FROM'     => "{$dbprefix}comments c",
        'JOINS'    => [
            [
                'INNER JOIN' => "{$dbprefix}users u",
                'ON'         => 'c.user = u.id',
            ],
            [
                'LEFT JOIN' => "{$dbprefix}files f",
                'ON'        => 'c.file_id = f.id',
            ],
        ],
        'WHERE'    => 'c.file_id = ' . $file_id,
        'ORDER BY' => 'c.id DESC',
    ]);

    $comments = [];

    while ($row = $SQL->fetch($query))
    {
        [$initial, $avatar_style] = kj_comment_avatar($row['name'], (int) $row['user']);

        $comments[] = [
            'id'           => (int) $row['id'],
            'file_id'      => (int) $row['file_id'],
            'name'         => $row['name'],
            'comment'      => $row['comment'],
            'initial'      => $initial,
            'avatar_style' => $avatar_style,
            'user_link'    => kj_comment_user_link((int) $row['user']),
            'time'         => kleeja_date((int) $row['time']),
            'full_time'    => kleeja_date((int) $row['time'], false),
            'iso_time'     => date('c', (int) $row['time']),
            'is_uploader'  => (int) $row['file_user'] > 0 && (int) $row['file_user'] == (int) $row['user'],
            'can_delete'   => kj_comment_can_delete((int) $row['user']),
        ];
    }

    return $comments;
}

/**
 * form actions and a fresh csrf key, used by comment.html and comment_list.html
 */
function kj_comment_assign_forms(): void
{
    global $tpl, $config, $usrcp;

    $tpl->assign('kj_comment_add_action', $config['siteurl'] . 'ucp.php?go=comment&amp;action=add');
    $tpl->assign('kj_comment_del_action', $config['siteurl'] . 'ucp.php?go=comment&amp;action=del');
    $tpl->assign('kj_comment_form_key', $usrcp->name() ? kleeja_add_form_key(kj_comment_form_name()) : '');
}

/**
 * the list items of the comments, the same html for the page and the ajax answers
 * @param  array  $comments
 * @return string
 */
function kj_comment_list_html(array $comments): string
{
    global $tpl;

    $tpl->assign('kj_comments', $comments);
    $tpl->assign('kj_comments_count', count($comments));

    return $tpl->display('comment_list', __DIR__);
}

/**
 * texts of utf8mb4 (emoji) can not be saved in the utf8 tables of kleeja,
 * the comment is kept html encoded, so they are saved as html entities and shown as they were
 * @param  string $text
 * @return string
 */
function kj_comment_encode_text(string $text): string
{
    return preg_replace_callback(
        '/[\x{10000}-\x{10FFFF}]/u',
        function (array $m): string {
            return '&#' . mb_ord($m[0], 'UTF-8') . ';';
        },
        $text
    ) ?? $text;
}

/**
 * a short plain part of a comment, for the admin list
 * @param  string $comment html encoded comment
 * @param  int    $length
 * @return string
 */
function kj_comment_excerpt(string $comment, int $length = 90): string
{
    $plain = preg_replace('/\s+/u', ' ', html_entity_decode($comment, ENT_QUOTES, 'UTF-8'));

    if (mb_strlen($plain) > $length)
    {
        $plain = rtrim(mb_substr($plain, 0, $length)) . '…';
    }

    return htmlspecialchars($plain, ENT_QUOTES);
}

/**
 * is the request sent by comment.js
 * @return bool
 */
function kj_comment_is_ajax(): bool
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
}

/**
 * answer comment.js and stop
 * @param array $data
 * @param int   $status
 */
function kj_comment_json(array $data, int $status = 200): void
{
    global $usrcp;

    // a new csrf key with every answer, so the forms of the page keep working
    if ($usrcp->name())
    {
        $data['form_key'] = kleeja_add_form_key(kj_comment_form_name());
    }

    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');

    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    exit;
}

/**
 * an error as json for comment.js, or as the error page of kleeja for a normal post
 * @param string $message
 * @param int    $status
 * @param array  $data    more for comment.js
 */
function kj_comment_error(string $message, int $status, array $data = []): void
{
    if (kj_comment_is_ajax())
    {
        kj_comment_json(['ok' => false, 'message' => $message] + $data, $status);
    }

    kleeja_err($message);

    exit;
}
