<?php
if (!defined('IN_SAESPOT')) {
    $dir_arr = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
    array_pop(array_pop($dir_arr));
    define('ROOT', implode(DIRECTORY_SEPARATOR, $dir_arr));
    include_once(ROOT . '/error/403.php');
    exit;
};

if($userdb || $userdb2){
if($userdb){
echo '
<a name="1"></a>
<div class="title"><a href="/">',$options['name'],'</a> &raquo; 最近等待审核的用户</div>

<div class="main-box">';
if($tip1){
    echo ' <p class="red">',$tip1,'</p>';
}
echo '
<ul class="user-list">';
foreach($userdb as $user){
    echo '<li><strong>',$user['name'],'</strong> • 于',$user['regtime'],'注册  • <a href="/admin-user-pass-',$user['id'],'#1">通过注册</a></li>';
}

echo '</ul>
</div>';
}

if($userdb2){
echo '
<a name="2"></a>
<div class="title"><a href="/">',$options['name'],'</a> &raquo; - 最近被禁用的用户</div>

<div class="main-box">';
if($tip2){
    echo ' <p class="red">',$tip2,'</p>';
}
echo '
<ul class="user-list">';
foreach($userdb2 as $user){
    echo '<li><strong>',$user['name'],'</strong> • 于',$user['regtime'],'注册  • <a href="/admin-user-active-',$user['id'],'#2">解禁</a></li>';
}

echo '</ul>
</div>';
}

}else{
echo '
<div class="title"><a href="/">',$options['name'],'</a> &raquo; 用户管理</div>

<div class="main-box">

<p>目前尚无 等待审核的用户 或 被禁用的用户</p>

</div>';

}

?>
