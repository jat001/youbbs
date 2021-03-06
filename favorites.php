<?php
define('IN_SAESPOT', 1);
define('ROOT', dirname(__FILE__));

include_once(ROOT . '/config.php');
include_once(ROOT . '/common.php');

if (!$cur_user) {
    include_once(ROOT . '/error/401.php');
    exit;
} else {
    if ($cur_user['flag'] == 0){
        $error_code = 4032;
        include_once(ROOT . '/error/403.php');
        exit;
    }
    if ($cur_user['flag'] == 1){
        $error_code = 4011;
        include_once(ROOT . '/error/403.php');
        exit;
    }
}

$act = $_GET['act'];
$tid = $_GET['id'];
$page = intval($_GET['page']);

// 获取收藏数据
$user_fav = $MMC->get('favorites_'.$cur_uid);
if(!$user_fav){
    $user_fav = $DBS->fetch_one_array("SELECT * FROM yunbbs_favorites WHERE uid='".$cur_uid."'");
    $MMC->set('favorites_'.$cur_uid, $user_fav, 0, 300);
}

// 处理收藏操作
if($act && $tid){
    switch ($act) {
        case 'add':
            // 添加
            if($user_fav){
                if($user_fav['content']){
                    $ids_arr = explode(",", $user_fav['content']);
                    if(!in_array($tid, $ids_arr)){
                        array_unshift($ids_arr, $tid);
                        $articles = count($ids_arr);
                        $content = implode(',', $ids_arr);
                        $user_fav['content'] = $content;
                        $user_fav['articles'] = $articles;

                        $DBS->unbuffered_query("UPDATE yunbbs_favorites SET articles='$articles',content='$content' WHERE uid='$cur_uid'");
                        $DBS->unbuffered_query("UPDATE yunbbs_articles SET favorites=favorites+1 WHERE id='$tid'");
                        $MMC->delete('favorites_'.$cur_uid);
                        $MMC->delete('t-'.$tid);
                        $MMC->delete('t-'.$tid.'_ios');
                    }
                    unset($ids_arr);
                }else{
                    $user_fav['content'] = $tid;
                    $user_fav['articles'] = 1;

                    $DBS->unbuffered_query("UPDATE yunbbs_favorites SET articles='1',content='$tid' WHERE uid='$cur_uid'");
                    $DBS->unbuffered_query("UPDATE yunbbs_articles SET favorites=favorites+1 WHERE id='$tid'");
                    $MMC->delete('favorites_'.$cur_uid);
                    $MMC->delete('t-'.$tid);
                    $MMC->delete('t-'.$tid.'_ios');
                }
            }else{

                $user_fav= array('id'=>'','uid'=>$cur_uid, 'articles'=>1, 'content' => $tid);
                $DBS->query("INSERT INTO yunbbs_favorites (uid, articles, content) VALUES ($cur_uid, 1, $tid)");
                $DBS->unbuffered_query("UPDATE yunbbs_articles SET favorites=favorites+1 WHERE id='$tid'");
                $MMC->delete('favorites_'.$cur_uid);
                $MMC->delete('t-'.$tid);
                $MMC->delete('t-'.$tid.'_ios');
            }
            break;

        case 'del':
            // 删除
            if($user_fav){
                if($user_fav['content']){
                    $ids_arr = explode(",", $user_fav['content']);
                    if(in_array($tid, $ids_arr)){
                        foreach($ids_arr as $k=>$v){
                            if($v == $tid){
                                unset($ids_arr[$k]);
                                break;
                            }
                        }
                        $articles = count($ids_arr);
                        $content = implode(',', $ids_arr);
                        $user_fav['content'] = $content;
                        $user_fav['articles'] = $articles;

                        $DBS->unbuffered_query("UPDATE yunbbs_favorites SET articles='$articles',content='$content' WHERE uid='$cur_uid'");
                        $DBS->unbuffered_query("UPDATE yunbbs_articles SET favorites=favorites-1 WHERE id='$tid'");
                        $MMC->delete('favorites_'.$cur_uid);
                        $MMC->delete('t-'.$tid);
                        $MMC->delete('t-'.$tid.'_ios');
                    }
                    unset($ids_arr);
                }
            }
            break;

        default:
            break;
    }
}

// 处理正确的页数
// 第一页是1
if($user_fav && $user_fav['articles']){
    $taltol_page = ceil($user_fav['articles']/$options['list_shownum']);
    if($page<=0){
        header('Location: /favorites?page=1');
        exit;
    }
    if($page!=1 && $page>$taltol_page){
        header('Location: /favorites?page='.$taltol_page);
        exit;
    }
}elseif($page) {
    header('Location: /favorites');
    exit;
}

// 获取收藏文章列表
if($user_fav['articles']){
    if($page == 0) $page = 1;
    $from_i = $options['list_shownum']*($page-1);
    $to_i = $from_i + $options['list_shownum'];

    if($user_fav['articles'] > 1){
        $id_arr = array_slice( explode(',', $user_fav['content']), $from_i, $to_i);
    }else{
        $id_arr = array($user_fav['content']);
    }
    $ids = implode(',', $id_arr);

    $query_sql = "SELECT a.id,a.uid,a.cid,a.ruid,a.title,a.addtime,a.edittime,a.comments,c.name as cname,u.avatar as uavatar,u.name as author,ru.name as rauthor
        FROM yunbbs_articles a
        LEFT JOIN yunbbs_categories c ON c.id=a.cid
        LEFT JOIN yunbbs_users u ON a.uid=u.id
        LEFT JOIN yunbbs_users ru ON a.ruid=ru.id
        WHERE a.id in(".$ids.")";
    $query = $DBS->query($query_sql);
    $articledb=array();
    // 按收藏顺序排列
    foreach($id_arr as $aid){
        $articledb[$aid] = '';
    }

    while ($article = $DBS->fetch_array($query)) {
        // 格式化内容
        $article['addtime'] = showtime($article['addtime']);
        $article['edittime'] = showtime($article['edittime']);
        $articledb[$article['id']] = $article;
    }
    unset($article);
    $DBS->free_result($query);
}

// 页面变量
$title = '收藏的帖子 - '.$options['name'];

$pagefile = ROOT . '/templates/default/'.$tpl.'favorites.php';

include_once(ROOT . '/templates/default/'.$tpl.'layout.php');

?>
