<?php
//
// kj_comment, its guide on the help page of the control panel
// Arabic
//
// The guide is built by the admin_help_guides hook in init.php, the texts here can have HTML.
// The keys are the ones of help_en.php, a key that is missing here is shown in English.
//  - names in <strong> are written as the control panel and the download page show them: the words of Kleeja
//    are in lang/ar/, the words of the plugin in kj_comment_translations() of functions.php
//

return [
    'KJ_COMMENT_HELP_TITLE' => 'تعليقات كليجا',
    'KJ_COMMENT_HELP_INTRO' =>
        'دع أعضاءك يتحدثون عن ملفات موقعك. تضيف الإضافة صندوق تعليقات إلى صفحة تحميل كل ملف، وصفحة <strong>مدير التعليقات</strong> إلى لوحة التحكم، تقرأ فيها تعليقات الموقع كله وتحذف منها ما تريد.',

    //
    // what the plugin does
    //
    'KJ_COMMENT_HELP_FEATURE_1' =>
        'صندوق <strong>التعليقات</strong> في صفحة تحميل كل ملف: يقرأ الزوار التعليقات، وينشر الأعضاء المسجّلون تعليقاتهم.',
    'KJ_COMMENT_HELP_FEATURE_2' => 'تُنشر التعليقات وتُحذف دون إعادة تحميل الصفحة، وتظهر فورًا، الأحدث أولًا.',
    'KJ_COMMENT_HELP_FEATURE_3' =>
        'التعليق نص عادي يصل إلى 1000 حرف، بأي لغة ومع الرموز التعبيرية، وتبقى أسطره كما كُتبت. ويعرض عدّاد أسفل الصندوق عدد الأحرف المكتوبة.',
    'KJ_COMMENT_HELP_FEATURE_4' => 'تحمل تعليقات صاحب الملف شارة <strong>صاحب الملف</strong>.',
    'KJ_COMMENT_HELP_FEATURE_5' =>
        'يحذف كل عضو تعليقاته، ويحذف أعضاء المجموعات التي لها صلاحية <strong>دخول لوحة التحكم</strong> أي تعليق، من صفحة التحميل أيضًا.',
    'KJ_COMMENT_HELP_FEATURE_6' =>
        'تعرض صفحة <strong>مدير التعليقات</strong> كل تعليقات الموقع، الأحدث أولًا، 21 تعليقًا في كل صفحة، مع كاتب التعليق وملفه ووقته.',
    'KJ_COMMENT_HELP_FEATURE_7' =>
        'عند حذف ملف تُحذف تعليقاته معه، سواء حُذف من لوحة التحكم، أو حذفه صاحبه، أو حُذف برابط الحذف، أو بالتنظيف التلقائي للملفات القديمة.',

    //
    // how to use it
    //
    'KJ_COMMENT_HELP_STEP_TITLE' => 'إدارة التعليقات',
    'KJ_COMMENT_HELP_STEP_1' => 'افتح <strong>مدير التعليقات</strong> من قائمة لوحة التحكم.',
    'KJ_COMMENT_HELP_STEP_2' =>
        'مرّر المؤشر فوق التعليق لتقرأه كاملًا، فالقائمة تعرض أول 90 حرفًا منه فقط. ومرّره فوق الوقت لترى التاريخ كاملًا.',
    'KJ_COMMENT_HELP_STEP_3' => 'اضغط اسم الملف لتفتح التعليق في صفحة تحميله في تبويب جديد، أو اسم العضو لترى ملفاته.',
    'KJ_COMMENT_HELP_STEP_4' => 'لحذف تعليق، اضغط <strong>حذف</strong> ثم أكّد الحذف.',

    //
    // common questions
    //
    'KJ_COMMENT_HELP_FAQ_Q_1' => 'من يستطيع نشر التعليقات؟',
    'KJ_COMMENT_HELP_FAQ_A_1' =>
        'كل عضو سجّل دخوله، أيًّا كانت مجموعته. أما الزوار فيقرؤون التعليقات ويرون زر <strong>دخول</strong> بدل نموذج التعليق. وليس للإضافة صلاحية خاصة بها، فلا يمكن منع مجموعة بعينها من التعليق.',
    'KJ_COMMENT_HELP_FAQ_Q_2' => 'هل تُراجَع التعليقات قبل ظهورها؟',
    'KJ_COMMENT_HELP_FAQ_A_2' =>
        'لا، يظهر التعليق فور نشره. افتح <strong>مدير التعليقات</strong> من حين لآخر، واحذف التعليقات التي تخالف شروط موقعك.',
    'KJ_COMMENT_HELP_FAQ_Q_3' => 'لماذا لا يظهر صندوق التعليقات لبعض الملفات؟',
    'KJ_COMMENT_HELP_FAQ_A_3' =>
        'يظهر الصندوق في صفحة التحميل فقط. والملفات التي امتدادها في <strong>إعدادات ← اعدادات التحميل ← الامتدادات التي لاتمر على صفحة الانتظار</strong> لا تمر بهذه الصفحة، وكذلك الروابط المباشرة للملفات والصور.',
    'KJ_COMMENT_HELP_FAQ_Q_4' => 'هل يستطيع صاحب الملف حذف التعليقات على ملفه؟',
    'KJ_COMMENT_HELP_FAQ_A_4' =>
        'تعليقاته فقط. لا يحذف التعليق إلا كاتبه وأعضاء المجموعات التي لها صلاحية <strong>دخول لوحة التحكم</strong>.',
    'KJ_COMMENT_HELP_FAQ_Q_5' => 'لماذا يختلف شكل صندوق التعليقات عن بقية الستايل؟',
    'KJ_COMMENT_HELP_FAQ_A_5' =>
        'للإضافة صناديق تعليقات لستايلات Bootstrap وDefault وKleeja Return وللستايلات المبنية عليها، وأي ستايل آخر يأخذ صندوق Bootstrap. ويمكن للستايل أن يأتي بصندوقه الخاص: ملف <code>comment.html</code> في مجلده، تعرضه الإضافة بدل صندوقها. والمتغيرات المتاحة له مشروحة فوق <code dir="ltr">kj_comment_box_html()</code> في الملف <code>plugins/kj_comment/functions.php</code>.',
    'KJ_COMMENT_HELP_FAQ_Q_6' => 'ماذا يحدث للتعليقات عند تعطيل الإضافة أو إلغاء تثبيتها؟',
    'KJ_COMMENT_HELP_FAQ_A_6' =>
        'تبقى محفوظة. تعطيل الإضافة يخفي صندوق التعليقات وصفحة <strong>مدير التعليقات</strong>، وإلغاء تثبيتها يُبقي جدول التعليقات في قاعدة البيانات، فتعود التعليقات عند تثبيتها من جديد.',

    //
    // beside the guide
    //
    'KJ_COMMENT_HELP_TIP_1' =>
        'وضّح لأعضائك ما يجوز كتابته في التعليقات، بإضافته إلى شروط موقعك في <a href="./?cp=l_rules">شروط الخدمة</a>.',
    'KJ_COMMENT_HELP_TIP_2' => 'يمكنك أيضًا حذف التعليق من صفحة تحميل ملفه، بزر سلة المهملات بجانبه.',
    'KJ_COMMENT_HELP_TIP_3' =>
        'ينشر الأعضاء التعليق بالضغط على <span dir="ltr">Ctrl + Enter</span>، أو <span dir="ltr">⌘ + Enter</span> على أجهزة Mac.',

    'KJ_COMMENT_HELP_WARNING_1' =>
        'لا يمكن استعادة التعليق المحذوف، سواء حُذف من <strong>مدير التعليقات</strong> أو من صفحة التحميل.',
    'KJ_COMMENT_HELP_WARNING_2' =>
        'التعليقات علنية: يقرؤها كل من يستطيع فتح صفحة تحميل الملف، ومنهم الزوار ومحركات البحث.',
];
