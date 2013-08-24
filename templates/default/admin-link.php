<?php
if (!defined('IN_SAESPOT')) {
    $dir_arr = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
    array_pop(array_pop($dir_arr));
    define('ROOT', implode(DIRECTORY_SEPARATOR, $dir_arr));
    include_once(ROOT . '/error/403.php');
    exit;
};

echo '
<a name="add"></a>
<div class="title">
    <a href="/">',$options['name'],'</a> &raquo; 添加链接
</div>

<div class="main-box">';
if($tip1){
    echo '<p class="red">',$tip1,'</p>';
}

echo '
<form action="',$_SERVER["REQUEST_URI"],'#add" method="post">
<input type="hidden" name="action" value="add"/>
<p>链接名： <input type="text" class="sl w100" name="name" value="" />
网址： <input type="text" class="sl w200" name="url" value="" />
<input type="submit" value=" 添 加 " name="submit" class="textbtn" /></p>
</form>
</div>';


if($l_obj){
echo '
<a name="edit"></a>
<div class="title">修改链接</div>

<div class="main-box">';
if($tip2){
    echo '<p class="red">',$tip2,'</p>';
}

echo '
<form action="',$_SERVER["REQUEST_URI"],'#edit" method="post">';

echo '
<input type="hidden" name="action" value="edit"/>
<p>链接名： <input type="text" class="sl w100" name="name" value="',htmlspecialchars($l_obj['name']),'" />
网址： <input type="text" class="sl w200" name="url" value="',htmlspecialchars($l_obj['url']),'" />
<input type="submit" value=" 保 存 " name="submit" class="textbtn" /></p>
</form>
</div>';
}

if($linkdb){
echo '
<a name="list"></a>
<div class="title">链接列表</div>

<div class="main-box">';
echo '
<ul class="user-list">';
foreach($linkdb as $link){
    echo '<li><a href="',$link['url'],'" target="_blank">',$link['name'],'</a>&nbsp;&nbsp;&nbsp;',$link['url'],'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin-link-edit-',$link['id'],'#1">编辑</a> | <a href="/admin-link-del-',$link['id'],'#list">删除</a></li>';
}

echo '</ul>
</div>';
}

?>
