<?php
// Kleeja Plugin
// KJ_COMMENT
// Version: 1.1.0
// Developer: Kleeja Team

// Prevent illegal run
if (! defined('IN_PLUGINS_SYSTEM'))
{
    exit();
}

require_once __DIR__ . '/functions.php';


// Plugin Basic Information
$kleeja_plugin['kj_comment']['information'] = [
    // The casual name of this plugin, anything can a human being understands
    'plugin_title' => [
        'en' => 'KJ Comment',
        'ar' => 'تعليقات كليجا'
    ],
    // Who wrote this plugin?
    'plugin_developer' => 'Kleeja Team',
    // This plugin version
    'plugin_version' => KJ_COMMENT_VERSION,
    // Explain what is this plugin, why should I use it?
    'plugin_description' => [
        'en' => 'Add Comments To Files',
        'ar' => 'إضافة تعليقات على الملفات'
    ],
    // Min version of Kleeja that's requiered to run this plugin
    'plugin_kleeja_version_min' => '4.0.0',
    // Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '4.9.9',
    // Should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0
];

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['kj_comment']['first_run']['ar'] = "
أضافت هذه الإضافة صندوق تعليقات إلى صفحة تحميل كل ملف، ينشر فيه الأعضاء المسجّلون تعليقاتهم،
وصفحة «مدير التعليقات» إلى لوحة التحكم، تقرأ فيها تعليقات الموقع كله وتحذف منها ما تريد.
<br>
<a href='./index.php?cp=comment_manager'>مدير التعليقات</a> -
<a href='./index.php?cp=s_help#help-kj_comment'>دليل الإضافة</a> -
<a href='https://github.com/kleeja/kj_comment/issues' target='_blank' rel='noopener'>الإبلاغ عن مشكلة</a>
";
$kleeja_plugin['kj_comment']['first_run']['en'] = "
This plugin added a comment box to the download page of every file, where signed-in members post their comments,
and the Comment Manager page to the control panel, where you read and delete the comments of the whole site.
<br>
<a href='./index.php?cp=comment_manager'>Comment Manager</a> -
<a href='./index.php?cp=s_help#help-kj_comment'>Plugin guide</a> -
<a href='https://github.com/kleeja/kj_comment/issues' target='_blank' rel='noopener'>Report a problem</a>
";


// Plugin Installation function
$kleeja_plugin['kj_comment']['install'] = function ($plg_id) {
    global $SQL , $dbprefix;
    $SQL->query(
        "CREATE TABLE IF NOT EXISTS `{$dbprefix}comments` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `user` int(11) NOT NULL,
            `file_id` int(11) NOT NULL,
            `comment` TEXT NOT NULL,
            `time` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
    );

    foreach (kj_comment_translations() as $lang_id => $words)
    {
        add_olang($words, $lang_id, $plg_id);
    }
};


//Plugin update function, called if plugin is already installed but version is different than current
$kleeja_plugin['kj_comment']['update'] = function ($old_version, $new_version) {
    //a plg_id of 0 makes delete_olang() drop every translation for the language
    if (! ($plg_id = kj_comment_plugin_id()))
    {
        return;
    }

    // the words are added again, so the new ones are there and the changed ones are updated
    foreach (kj_comment_translations() as $lang_id => $words)
    {
        delete_olang(null, $lang_id, $plg_id);
        add_olang($words, $lang_id, $plg_id);
    }
};


// Plugin Uninstallation, function to be called at unistalling
$kleeja_plugin['kj_comment']['uninstall'] = function ($plg_id) {
    //a plg_id of 0 makes delete_olang() drop every translation for the language
    if ((int) $plg_id > 0)
    {
        delete_olang(null, array_keys(kj_comment_translations()), (int) $plg_id);
    }
};


// Plugin functions
$kleeja_plugin['kj_comment']['functions'] = [
    // comments of the file of the download page
    'b4_showsty_downlaod_id_filename' => function ($args) {
        $file_info = $args['file_info'];

        if (empty($file_info['id']))
        {
            return;
        }

        define('KJ_COMMENT_DISPLAY', true);

        $kj_comment_file_id = (int) $file_info['id'];
        $kj_comments        = kj_comment_fetch($kj_comment_file_id);

        return compact('kj_comments', 'kj_comment_file_id');
    },

    // style and script of the comment box, only on the download page
    'Saaheader_links_func' => function ($args) {
        if (! defined('KJ_COMMENT_DISPLAY'))
        {
            return;
        }

        $extra = $args['extra'] . kj_comment_head_code();

        return compact('extra');
    },

    // the comment box, comment.html of the style or the box of the plugin, before the footer of the download page
    'print_Saafooter_func' => function ($args) {
        global $kj_comments , $kj_comment_file_id;

        if (! defined('IN_DOWNLOAD') || ! defined('KJ_COMMENT_DISPLAY'))
        {
            return;
        }

        $footer = kj_comment_box_html((int) $kj_comment_file_id, $kj_comments) . $args['footer'];

        return compact('footer');
    } ,

    // ucp.php?go=comment&action=add|del, answers comment.js with json and the box rendered again, or redirects after a normal post
    'default_usrcp_page' => function ($args) {
        global $usrcp , $lang , $olang , $SQL , $dbprefix , $config;

        if (g('go') !== 'comment')
        {
            return;
        }

        kj_comment_olang_fallback();

        // all actions in this hook is only for members
        if (! $usrcp->name())
        {
            kj_comment_error($olang['NO_4_GUEST'], 403);
        }

        // comment.js sends the form again with the new key of the answer
        if (! kleeja_check_form_key(kj_comment_form_name(), KJ_COMMENT_FORM_TIME))
        {
            kj_comment_error($lang['INVALID_FORM_KEY'], 403, ['expired_key' => true]);
        }

        $file_id = p('file_id', 'int');

        if (g('action') === 'add')
        {
            $comment = p('comment');
            $length  = mb_strlen(html_entity_decode($comment, ENT_QUOTES, 'UTF-8'));

            if ($length === 0)
            {
                kj_comment_error($olang['KJC_EMPTY'], 422);
            }

            if ($length > KJ_COMMENT_MAX_LENGTH)
            {
                kj_comment_error(sprintf($olang['KJC_TOO_LONG'], KJ_COMMENT_MAX_LENGTH), 422);
            }

            $file_query = $SQL->build([
                'SELECT' => 'id',
                'FROM'   => "{$dbprefix}files",
                'WHERE'  => 'id = ' . $file_id,
                'LIMIT'  => '1',
            ]);

            if (! $file_id || ! $SQL->num_rows($file_query))
            {
                kj_comment_error($lang['FILE_NO_FOUNDED'], 404);
            }

            $SQL->build([
                'INSERT' => 'user , file_id , comment , time',
                'INTO'   => "{$dbprefix}comments",
                'VALUES' => implode(' , ', [
                    (int) $usrcp->id(),
                    $file_id,
                    "'" . $SQL->real_escape(kj_comment_encode_text($comment)) . "'",
                    time(),
                ]),
            ]);

            $comment_id = (int) $SQL->insert_id();

            if (kj_comment_is_ajax())
            {
                $comments = kj_comment_fetch($file_id);

                kj_comment_json([
                    'ok'      => true,
                    'message' => $olang['KJC_ADDED'],
                    'id'      => $comment_id,
                    'count'   => count($comments),
                    'box'     => kj_comment_box_html($file_id, $comments),
                ]);
            }

            redirect($config['siteurl'] . 'do.php?id=' . $file_id . '#comment-' . $comment_id);

            exit;
        }
        elseif (g('action') === 'del')
        {
            $comment_id = p('comment_id', 'int');

            $SQL->build([
                'DELETE' => "{$dbprefix}comments",
                'WHERE'  => 'file_id = ' . $file_id . ' AND id = ' . $comment_id
                    . (user_can('enter_acp') ? '' : ' AND user = ' . (int) $usrcp->id()),
            ]);

            if (! $SQL->affected())
            {
                kj_comment_error($olang['KJC_NOT_FOUND'], 404);
            }

            if (kj_comment_is_ajax())
            {
                $comments = kj_comment_fetch($file_id);

                kj_comment_json([
                    'ok'      => true,
                    'message' => $olang['KJC_DELETED'],
                    'count'   => count($comments),
                    'box'     => kj_comment_box_html($file_id, $comments),
                ]);
            }

            if (ip('admin_form'))
            {
                redirect($config['siteurl'] . 'admin/index.php?cp=comment_manager');

                exit;
            }

            redirect($config['siteurl'] . 'do.php?id=' . $file_id . '#kj-comments');

            exit;
        }

        kj_comment_error($lang['ERROR_NAVIGATATION'], 400);
    } ,

    'begin_admin_page' => function ($args) {
        $adm_extensions = $args['adm_extensions'];
        $ext_icons = $args['ext_icons'];
        $adm_extensions[] = 'comment_manager';
        $ext_icons['comment_manager'] = 'comment';

        //the menu shows the title on every admin page, English is used for a language without translation
        $olang = (array) ($args['olang'] ?? []) + kj_comment_translations()['en'];

        return compact('adm_extensions', 'ext_icons', 'olang');
    },

    'not_exists_comment_manager' => function() {
        $include_alternative = __DIR__ . '/comment_manager.php';

        return compact('include_alternative');
    },

    //
    // the comments of a deleted file are deleted with it, see kj_comment_delete_for_files(),
    // Kleeja deletes files in seven places, each hook below runs before the files are deleted
    //

    // Files Control, the selected files
    'submit_files_admin' => function ($args) {
        kj_comment_delete_for_files((array) ($args['ids'] ?? []));
    },

    // Images Control, the selected images
    'submit_imgctrl_admin' => function ($args) {
        kj_comment_delete_for_files((array) ($args['ids'] ?? []));
    },

    // the files page of a member, the selected files or all of them
    'qr_del_files_in_filecp' => function ($args) {
        kj_comment_delete_for_files((array) ($args['ids'] ?? []));
    },

    // the deletion link of a file, go.php?go=del
    'qr_del_file_with_code_del' => function ($args) {
        kj_comment_delete_for_files([$args['row']['id'] ?? 0]);
    },

    // the files that nobody downloaded for a while, deleted by the cleanup of Kleeja
    'qr_del_delf_old_files' => function ($args) {
        kj_comment_delete_for_files((array) ($args['ids'] ?? []));
    },

    // Files Control, every file of a search: this has no hook of its own,
    // so the files are found as c_files.php finds them, before the page deletes them
    'require_admin_page_begin_c_files' => function ($args) {
        if (ip('submit') || ! ig('deletefiles'))
        {
            return;
        }

        $filter = get_filter(g('search_id'), 'file_search', false, 'filter_uid');

        if (! $filter)
        {
            return;
        }

        [$where, $bind] = build_search_query(
            unserialize(htmlspecialchars_decode($filter['filter_value']), ['allowed_classes' => false])
        );

        if ($where !== '')
        {
            kj_comment_delete_for_files(kj_comment_file_ids($where, $bind));
        }
    },

    // Users & Groups, every file of a member: this has no hook of its own either,
    // so the checks of g_users.php are made again, before the page deletes the files
    'require_admin_page_begin_g_users' => function ($args) {
        global $SQL, $dbprefix;

        if (! ig('deleteuserfile') || ! kleeja_check_form_key_get('adm_users'))
        {
            return;
        }

        $user_id = g('deleteuserfile', 'int');

        if (! $SQL->num_rows($SQL->query("SELECT id FROM {$dbprefix}users WHERE id = :id", ['id' => $user_id])))
        {
            return;
        }

        kj_comment_delete_for_files(kj_comment_file_ids('f.user = :user', ['user' => $user_id]));
    },

    // the guide of the plugin on the help page of the control panel, its words are in language/help_{code}.php
    'admin_help_guides' => function ($args) {
        $help_guides = $args['help_guides'];
        $words       = kj_comment_help_words();

        // the name of the plugin is the key, so Kleeja knows that the plugin has its guide, and shows its icon,
        // and the help button of Comment Manager opens it
        $help_guides['kj_comment'] = [
            'group'    => 'plugins',
            'title'    => $words['KJ_COMMENT_HELP_TITLE'],
            'intro'    => $words['KJ_COMMENT_HELP_INTRO'],
            'page'     => 'comment_manager',
            'link'     => './?cp=comment_manager',
            // tips and warnings are shown beside the others on wide screens
            'sections' => [
                kj_comment_help_section($words, 'features', 'FEATURE'),
                kj_comment_help_section($words, 'steps', 'STEP'),
                kj_comment_help_section($words, 'faq', 'FAQ'),
                kj_comment_help_section($words, 'tips', 'TIP'),
                kj_comment_help_section($words, 'warnings', 'WARNING'),
            ],
        ];

        return compact('help_guides');
    },
];
