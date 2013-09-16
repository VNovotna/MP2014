<?php //netteCache[01]000384a:2:{s:4:"time";s:21:"0.40715000 1379333390";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:62:"/home/viky/NetBeansProjects/MP2014/app/templates/@layout.latte";i:2;i:1379333386;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:30:"80a7e46 released on 2013-08-08";}}}?><?php

// source file: /home/viky/NetBeansProjects/MP2014/app/templates/@layout.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'ayvmk6sfwa')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block title
//
if (!function_exists($_l->blocks['title'][] = '_lbcefe523612_title')) { function _lbcefe523612_title($_l, $_args) { extract($_args)
?>Should be overwriten<?php
}}

//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb51b66bafd3_head')) { function _lb51b66bafd3_head($_l, $_args) { extract($_args)
;
}}

//
// block _flashMessages
//
if (!function_exists($_l->blocks['_flashMessages'][] = '_lb15dc00cbce__flashMessages')) { function _lb15dc00cbce__flashMessages($_l, $_args) { extract($_args); $_control->validateControl('flashMessages')
;$iterations = 0; foreach ($flashes as $flash): ?>                <div class="flash <?php echo htmlSpecialChars($flash->type) ?>
"><?php echo Nette\Templating\Helpers::escapeHtml($flash->message, ENT_NOQUOTES) ?></div>
<?php $iterations++; endforeach ;
}}

//
// block scripts
//
if (!function_exists($_l->blocks['scripts'][] = '_lbe0ab7979e4_scripts')) { function _lbe0ab7979e4_scripts($_l, $_args) { extract($_args)
?>        <script src="<?php echo htmlSpecialChars($basePath) ?>/js/jquery.js"></script>
        <script src="<?php echo htmlSpecialChars($basePath) ?>/js/netteForms.js"></script>
        <script src="<?php echo htmlSpecialChars($basePath) ?>/js/nette.ajax.js"></script>
        <script src="<?php echo htmlSpecialChars($basePath) ?>/js/main.js"></script>
        <script>
        $(function () {
            $.nette.init();
        });
        </script>
<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="description" content="" />
<?php if (isset($robots)): ?>        <meta name="robots" content="<?php echo htmlSpecialChars($robots) ?>" />
<?php endif ?>

        <title><?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
ob_start(); call_user_func(reset($_l->blocks['title']), $_l, get_defined_vars()); echo $template->striptags(ob_get_clean())  ?></title>

        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo htmlSpecialChars($basePath) ?>/css/screen.css" />
        <link rel="stylesheet" media="print" href="<?php echo htmlSpecialChars($basePath) ?>/css/print.css" />
        <link rel="shortcut icon" href="<?php echo htmlSpecialChars($basePath) ?>/favicon.ico" />
	<?php call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars())  ?>

    </head>

    <body>
        <script> document.documentElement.className+=' js' </script>

        <div id="header">
            <div id="header-inner">
                <div class="title"><a href="<?php echo htmlSpecialChars($_control->link("Homepage:")) ?>
">Home</a></div>
<?php if ($user->isLoggedIn()): ?>                <div class="icon user">
                    <?php echo Nette\Templating\Helpers::escapeHtml($user->getIdentity()->jmeno, ENT_NOQUOTES) ?>
 <?php echo Nette\Templating\Helpers::escapeHtml($user->getIdentity()->prijmeni, ENT_NOQUOTES) ?>
 | <a href="<?php echo htmlSpecialChars($_control->link("logOut!")) ?>">Odhlásit</a>
                </div>
<?php endif ?>
            </div>
        </div>
        <div id="container">
<?php if ($user->isLoggedIn()): ?>            <div id="sidebar">
                <h2>Menu</h2>
                <div class="task-lists">
                    <ul>
<?php if ($user->isInRole('merch')): ?>                        <li><a href="<?php echo htmlSpecialChars($_control->link("Visit:create")) ?>
">Vyplnit návštěvu</a></li>
<?php endif ?>
                        <li><a href="<?php echo htmlSpecialChars($_control->link("Visit:view")) ?>
">Zobrazit návštěvy</a></li>
<?php if ($user->isInRole('admin')): ?>                        <li><a href="<?php echo htmlSpecialChars($_control->link("Summary:decide")) ?>
">Týdenní přehled</a></li>                        
<?php endif ;if ($user->isInRole('admin')): ?>
                        Seznamy
                        <ul>
<?php if ($user->isInRole('admin')): ?>                            <li><a href="<?php echo htmlSpecialChars($_control->link("Store:")) ?>
">- prodejen</a></li>
<?php endif ;if ($user->isInRole('admin')): ?>                            <li><a href="<?php echo htmlSpecialChars($_control->link("Product:")) ?>
">- výrobků</a></li>
<?php endif ;if ($user->isInRole('admin')): ?>                            <li><a href="<?php echo htmlSpecialChars($_control->link("User:")) ?>
">- uživatelů</a></li>
<?php endif ?>
                        </ul>
<?php endif ?>
                    </ul>
                </div>
            </div>
<?php endif ?>
            <div id="content">
<div id="<?php echo $_control->getSnippetId('flashMessages') ?>"><?php call_user_func(reset($_l->blocks['_flashMessages']), $_l, $template->getParameters()) ?>
</div><?php Nette\Latte\Macros\UIMacros::callBlock($_l, 'content', $template->getParameters()) ?>
            </div>

            <div id="footer">
                PHP <?php echo Nette\Templating\Helpers::escapeHtml(PHP_VERSION, ENT_NOQUOTES) ?>
 |<?php if (isset($_SERVER['SERVER_SOFTWARE'])): ?>Server <?php echo Nette\Templating\Helpers::escapeHtml($_SERVER['SERVER_SOFTWARE'], ENT_NOQUOTES) ;endif ?>
 | Nette Framework <?php echo Nette\Templating\Helpers::escapeHtml(Nette\Framework::VERSION, ENT_NOQUOTES) ?>

            </div>
        </div>
<?php call_user_func(reset($_l->blocks['scripts']), $_l, get_defined_vars())  ?>
    </body>
</html>