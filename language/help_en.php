<?php
//
// kj_comment, its guide on the help page of the control panel
// English
//
// The guide is built by the admin_help_guides hook in init.php, the texts here can have HTML.
//  - a list is numbered: KJ_COMMENT_HELP_TIP_1, KJ_COMMENT_HELP_TIP_2 ... it ends at the first missing number,
//    so an item can be added or removed without touching the code
//  - KJ_COMMENT_HELP_{LIST}_TITLE is the title of the list, a list without it takes the title that Kleeja gives its type
//  - questions and answers are KJ_COMMENT_HELP_FAQ_Q_1 and KJ_COMMENT_HELP_FAQ_A_1
//  - names in <strong> are written as the control panel and the download page show them: the words of Kleeja
//    are in lang/en/, the words of the plugin in kj_comment_translations() of functions.php
//

return [
    'KJ_COMMENT_HELP_TITLE' => 'KJ Comment',
    'KJ_COMMENT_HELP_INTRO' =>
        'Let your members talk about the files of your site. The plugin adds a comment box to the download page of every file, and a <strong>Comment Manager</strong> page to the control panel, where you read and delete the comments of the whole site.',

    //
    // what the plugin does
    //
    'KJ_COMMENT_HELP_FEATURE_1' =>
        'A <strong>Comments</strong> box on the download page of every file. Visitors read the comments, and signed-in members post their own.',
    'KJ_COMMENT_HELP_FEATURE_2' =>
        'Comments are posted and deleted without reloading the page, and they appear at once, newest first.',
    'KJ_COMMENT_HELP_FEATURE_3' =>
        'A comment is plain text of up to 1000 characters, in any language and with emoji, and its line breaks are kept. A counter under the box shows how many characters are written.',
    'KJ_COMMENT_HELP_FEATURE_4' => 'The comments of the uploader of a file carry an <strong>Uploader</strong> badge.',
    'KJ_COMMENT_HELP_FEATURE_5' =>
        'Members delete their own comments. Members of groups with the <strong>Access Admin control panel</strong> permission delete any comment, on the download page too.',
    'KJ_COMMENT_HELP_FEATURE_6' =>
        '<strong>Comment Manager</strong> lists every comment of the site, newest first and 21 per page, with its author, its file and its time.',
    'KJ_COMMENT_HELP_FEATURE_7' =>
        'When a file is deleted, its comments are deleted with it: from the control panel, by its uploader, by its deletion link, or by the cleanup of old files.',

    //
    // how to use it
    //
    'KJ_COMMENT_HELP_STEP_TITLE' => 'Manage the comments',
    'KJ_COMMENT_HELP_STEP_1' => 'Open <strong>Comment Manager</strong> from the menu of the control panel.',
    'KJ_COMMENT_HELP_STEP_2' =>
        'Hold the pointer over a comment to read all of it, the list shows only its first 90 characters. Hold it over the time to see the full date.',
    'KJ_COMMENT_HELP_STEP_3' =>
        'Click the name of a file to open the comment on its download page in a new tab, or the name of a member to see their files.',
    'KJ_COMMENT_HELP_STEP_4' => 'To remove a comment, click <strong>Delete</strong> and confirm.',

    //
    // common questions
    //
    'KJ_COMMENT_HELP_FAQ_Q_1' => 'Who can post comments?',
    'KJ_COMMENT_HELP_FAQ_A_1' =>
        'Every signed-in member, whatever their group. Guests read the comments and see a <strong>Login</strong> button instead of the form. The plugin has no permission of its own, so it can\'t turn comments off for one group.',
    'KJ_COMMENT_HELP_FAQ_Q_2' => 'Are comments checked before they appear?',
    'KJ_COMMENT_HELP_FAQ_A_2' =>
        'No. A comment appears as soon as it is posted. Open <strong>Comment Manager</strong> now and then, and delete the comments that break the terms of your site.',
    'KJ_COMMENT_HELP_FAQ_Q_3' => 'Why do some files have no comment box?',
    'KJ_COMMENT_HELP_FAQ_A_3' =>
        'The box is on the download page only. Files whose extension is in <strong>Settings → Upload settings → Live Extensions (No waiting page)</strong> skip that page, and so do the direct links of files and images.',
    'KJ_COMMENT_HELP_FAQ_Q_4' => 'Can the uploader of a file delete the comments on it?',
    'KJ_COMMENT_HELP_FAQ_A_4' =>
        'Only their own. A comment can be deleted by its author and by the members of groups with the <strong>Access Admin control panel</strong> permission.',
    'KJ_COMMENT_HELP_FAQ_Q_5' => 'Why does the comment box look different from the rest of my style?',
    'KJ_COMMENT_HELP_FAQ_A_5' =>
        'The plugin has comment boxes for the Bootstrap, Default and Kleeja Return styles, and for the styles built on them. Any other style gets the Bootstrap box. A style can bring its own box: a <code>comment.html</code> file in its folder, which the plugin shows instead of its own. The variables it can use are listed above <code>kj_comment_box_html()</code> in <code>plugins/kj_comment/functions.php</code>.',
    'KJ_COMMENT_HELP_FAQ_Q_6' => 'What happens to the comments when the plugin is disabled or uninstalled?',
    'KJ_COMMENT_HELP_FAQ_A_6' =>
        'They are kept. Disabling the plugin hides the comment box and <strong>Comment Manager</strong>. Uninstalling it leaves the table of the comments in the database, so they come back when you install it again.',

    //
    // beside the guide
    //
    'KJ_COMMENT_HELP_TIP_1' =>
        'Tell your members what they may write in comments: add it to the terms of your site in <a href="./?cp=l_rules">Terms</a>.',
    'KJ_COMMENT_HELP_TIP_2' =>
        'You can delete a comment on the download page of its file too, with the trash button beside it.',
    'KJ_COMMENT_HELP_TIP_3' => 'Members post a comment with Ctrl + Enter, or ⌘ + Enter on a Mac.',

    'KJ_COMMENT_HELP_WARNING_1' =>
        'A deleted comment can\'t be restored, whether it was deleted in <strong>Comment Manager</strong> or on the download page.',
    'KJ_COMMENT_HELP_WARNING_2' =>
        'Comments are public: everyone who can open the download page of a file reads its comments, guests and search engines too.',
];
