<?php
define("THEME_URL", str_replace('//usr', '/usr', str_replace(Helper::options()->siteUrl, Helper::options()->rootUrl . '/', Helper::options()->themeUrl)));
$str1 = explode('/themes/', (THEME_URL . '/'));
$str2 = explode('/', $str1[1]);
define("THEME_NAME", $str2[0]);
require 'ipdata.class.php';

/* 插件方法 */
require_once('factory.php');

/* 获取模板版本号 */
function JoeVersion()
{
    return "1.1.9";
}

function autoCdnUrl($path) {
    if (Helper::options()->JCDNUrl) {
        $url = Helper::options()->JCDNUrl;
        if (substr($url, strlen($url) - 1) != "/") {
            $url = $url . '/';
        }
        if (substr($path, 0, 1) == "/") {
            $path = substr($path, 1);
        }
        return  $url . $path;
    } else {
        Helper::options()->themeUrl($path);
    }
}

/* 获取懒加载图片 */
function GetLazyLoad()
{
    if (Helper::options()->JLazyLoad) {
        return Helper::options()->JLazyLoad;
    } else {
        return autoCdnUrl("/assets/img/lazyload.jpg");
    }
}

/* 获取模板内置播放器 */
function GetDplayer()
{
    return THEME_URL . '/player.php';
}

/** 获取评论者归属地信息 */
function convertip($ip){  
echo convertips($ip);
}

function GetPlyr()
{
    return THEME_URL . '/plyr.php';
}

function imgNum($content){
$output = preg_match_all('#<img(.*?) src="([^"]*/)?(([^"/]*)\.[^"]*)"(.*?)>#', $content,$s);
$cnt = count( $s[1] );
return $cnt;
}

/* 作者认证等级 */
function dengji($id){
    $db=Typecho_Db::get();
    $mail=$db->fetchRow($db->select(array('COUNT(authorId)'=>'rbq'))->from ('table.contents')->where ('table.contents.authorId=?',$id)->where('table.contents.type=?', 'post'));
    $rbq=$mail['rbq'];
    if ($id == 1){
         echo '<span style="background-color:#000; color:#ffe000;" class="dengji"><b>博 主</b></span>';
    }
    if($rbq<1){
    echo '<span class="dengji">小白</span>';
    }elseif ($rbq<10 && $rbq>0) {
    echo '<span class="dengji">大白</span>';
    }elseif ($rbq<30 && $rbq>=10) {
    echo '<span class="dengji">小黑</span>';
    }elseif ($rbq<50 && $rbq>=30) {
    echo '<span class="dengji">大黑</span>';
    }elseif ($rbq<80 && $rbq>=50) {
    echo '<span class="dengji">大佬</span>';
    }elseif ($rbq<150 && $rbq>=90) {
    echo '<span class="dengji">神仙</span>';
    }elseif ($rbq>=200) {
    echo '<span class="dengji">归隐</span>';
    }
}

/* 评论者认证等级 */
function dengji1($i){
    $db=Typecho_Db::get();
    $mail=$db->fetchAll($db->select(array('COUNT(cid)'=>'rbq'))->from('table.comments')->where('mail = ?', $i));
    foreach ($mail as $sl){
    $rbq=$sl['rbq'];
    }
    if ($i == '1401668510@qq.com'){
         echo '<span style="background-color:#000; color:#ffe000;" class="dengji"><b>博 主</b></span>';
    }
    if($rbq<10){
    echo '<span class="dengji">打酱油</span>';
    }elseif ($rbq<20 && $rbq>10) {
    echo '<span class="dengji">初入江湖</span>';
    }elseif ($rbq<40 && $rbq>=20) {
    echo '<span class="dengji">小有名气</span>';
    }elseif ($rbq<80 && $rbq>=40) {
    echo '<span class="dengji">江湖大侠</span>';
    }elseif ($rbq<120 && $rbq>=80) {
    echo '<span class="dengji">武林盟主</span>';
    }elseif ($rbq<180 && $rbq>=120) {
    echo '<span class="dengji">笑傲江湖</span>';
    }elseif ($rbq>=999) {
    echo '<span class="dengji">独孤求败</span>';
    }
}
        //  echo '<span style="background-color:#fff0;" class="dengji"><img style="width: 30px;" src="https://xggm.top/dengji/v1.png"></span>';

/* 用户主页链接*/
function getUserPermalink($uid){
    return Helper::options()->index.'/author/'.$uid;
}

//调用博主最近登录时间
function get_last_login($user){
    $user   = '1';
    $now = time();
    $db     = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $row = $db->fetchRow($db->select('activated')->from('table.users')->where('uid = ?', $user));
    echo Typecho_I18n::dateWord($row['activated'], $now);
}

//在线人数
function online_users() {
    $filename='online.txt'; //数据文件
    $cookiename='Nanlon_OnLineCount'; //Cookie名称
    $onlinetime=30; //在线有效时间
    $online=file($filename); 
    $nowtime=$_SERVER['REQUEST_TIME']; 
    $nowonline=array(); 
    foreach($online as $line){ 
        $row=explode('|',$line); 
        $sesstime=trim($row[1]); 
        if(($nowtime - $sesstime)<=$onlinetime){
            $nowonline[$row[0]]=$sesstime;
        } 
    } 
    if(isset($_COOKIE[$cookiename])){
        $uid=$_COOKIE[$cookiename]; 
    }else{
        $vid=0;
        do{
            $vid++; 
            $uid='U'.$vid; 
        }while(array_key_exists($uid,$nowonline)); 
        setcookie($cookiename,$uid); 
    } 
    $nowonline[$uid]=$nowtime;
    $total_online=count($nowonline); 
    if($fp=@fopen($filename,'w')){ 
        if(flock($fp,LOCK_EX)){ 
            rewind($fp); 
            foreach($nowonline as $fuid=>$ftime){ 
                $fline=$fuid.'|'.$ftime."\n"; 
                @fputs($fp,$fline); 
            } 
            flock($fp,LOCK_UN); 
            fclose($fp); 
        } 
    } 
    echo "$total_online"; 
} 

/*百度收录*/
function baidu_record() {
$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if(checkBaidu($url)==1)
{echo "百度已收录";
}
else
{echo "<a style=\"color:red;\" rel=\"external nofollow\" title=\"点击提交收录！\" target=\"_blank\" href=\"http://zhanzhang.baidu.com/sitesubmit/index?sitename=$url\">百度未收录</a>";}
}
function checkBaidu($url) {
$url = 'http://www.baidu.com/s?wd=' . urlencode($url);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$rs = curl_exec($curl);
curl_close($curl);
if (!strpos($rs, '没有找到')) { //没有找到说明已被百度收录
return 1;
} else {
return -1;
}
}

/*文章字数统计*/
function art_count ($cid){
$db=Typecho_Db::get ();
$rs=$db->fetchRow ($db->select ('table.contents.text')->from ('table.contents')->where ('table.contents.cid=?',$cid)->order ('table.contents.cid',Typecho_Db::SORT_ASC)->limit (1));
$text = preg_replace("/[^\x{4e00}-\x{9fa5}]/u", "", $rs['text']);
echo mb_strlen($text,'UTF-8');
}

/*会员页判断是否会员id*/
function userok($id){
$db = Typecho_Db::get();
$userinfo=$db->fetchRow($db->select()->from ('table.users')->where ('table.users.uid=?',$id));
return $userinfo;
}
/*输出作者人气*/
function allviewnum($id){
    $db = Typecho_Db::get();
    $postnum=$db->fetchRow($db->select(array('Sum(views)'=>'allviewnum'))->from ('table.contents')->where ('table.contents.authorId=?',$id)->where('table.contents.type=?', 'post'));
    $postnum = $postnum['allviewnum'];
    if($postnum=='0')
    {
        return '暂无访问';
    }
    elseif ($postnum>=1000000) {
        return '访问 100万+℃';
    }   
    else{
        return '访问 '.$postnum.' ℃ ';
    }

}

/*当前作者文章数*/
function allpostnum($id){
    $db = Typecho_Db::get();
    $postnum=$db->fetchRow($db->select(array('COUNT(authorId)'=>'allpostnum'))->from ('table.contents')->where ('table.contents.authorId=?',$id)->where('table.contents.type=?', 'post'));
    $postnum = $postnum['allpostnum'];
    return $postnum;    
}
/*当前作者评论总数*/
function commentnum($id){
    $db = Typecho_Db::get();
    $commentnum=$db->fetchRow($db->select(array('COUNT(authorId)'=>'commentnum'))->from ('table.comments')->where ('table.comments.authorId=?',$id)->where('table.comments.type=?', 'comment'));
    $commentnum = $commentnum['commentnum'];
    return $commentnum;
}

/* 生成目录树 */
function CreateCatalog($obj)
{
    global $catalog;
    global $catalog_count;
    $catalog = array();
    $catalog_count = 0;
    $obj = preg_replace_callback('/<h([1-6])(.*?)>(.*?)<\/h\1>/i', function ($obj) {
        global $catalog;
        global $catalog_count;
        $catalog_count++;
        $catalog[] = array('text' => trim(strip_tags($obj[3])), 'depth' => $obj[1], 'count' => $catalog_count);
        return '<h' . $obj[1] . $obj[2] . ' id="cl-' . $catalog_count . '"><span>' . $obj[3] . '</span></h' . $obj[1] . '>';
    }, $obj);
    return $obj;
}
function GetCatalog()
{
    global $catalog;
    $index = '';
    if ($catalog) {
        $index = '<ul>';
        $prev_depth = '';
        $to_depth = 0;
        foreach ($catalog as $catalog_item) {
            $catalog_depth = $catalog_item['depth'];
            if ($prev_depth) {
                if ($catalog_depth == $prev_depth) {
                    $index .= '</li>';
                } elseif ($catalog_depth > $prev_depth) {
                    $to_depth++;
                    $index .= '<ul>';
                } else {
                    $to_depth2 = ($to_depth > ($prev_depth - $catalog_depth)) ? ($prev_depth - $catalog_depth) : $to_depth;
                    if ($to_depth2) {
                        for ($i = 0; $i < $to_depth2; $i++) {
                            $index .= '</li></ul>';
                            $to_depth--;
                        }
                    }
                    $index .= '</li>';
                }
            }
            $index .= '<li><a href="javascript: void(0)" data-href="#cl-' . $catalog_item['count'] . '">' . $catalog_item['text'] . '</a>';
            $prev_depth = $catalog_item['depth'];
        }
        for ($i = 0; $i <= $to_depth; $i++) {

            $index .= '</li></ul>';
        }
        $index = '<div class="j-floor"><div class="contain" id="jFloor"><div class="title">文章目录</div>' . $index . '<svg class="toc-marker" xmlns="http://www.w3.org/2000/svg"><path stroke="var(--theme)" stroke-width="3" fill="transparent" stroke-dasharray="0, 0, 0, 1000" stroke-linecap="round" stroke-linejoin="round" transform="translate(-0.5, -0.5)" /></svg></div></div>';
    }
    echo $index;
}

/* 格式化标签 */
function ParseCode($text)
{

    /* 初始化图片为懒加载 */
    $text = Short_Lazyload($text);
    /* 图片短代码 */
    $text = Short_Photo($text);
    /* tag标签短代码 */
    $text = Short_Tag($text);
    /* 按钮短代码 */
    $text = Short_Button($text);
    /* 提示短代码 */
    $text = Short_Alt($text);
    /* 线短代码 */
    $text = Short_Line($text);
    /* tabs短代码 */
    $text = Short_Tabs($text);
    /* 默认卡片短代码 */
    $text = Short_Card_default($text);
    /* 展开隐藏短代码 */
    $text = Short_Collapse($text);
    /* 时间线短代码 */
    $text = Short_Time_line($text);
    /* 复制短代码 */
    $text = Short_Copy($text);
    /* 打字机短代码 */
    $text = Short_Typing($text);
    /* 链接卡片短代码 */
    $text = Short_Card_Nav($text);
    /* dplayer短代码 */
    $text = Short_Dplayer($text);
    /* plyr短代码 */
    $text = Short_Plyr($text);
    /* 音乐短代码 */
    $text = Short_Music($text);
    /* 音乐列表短代码 */
    $text = Short_Music_List($text);
    /* 视频列表短代码 */
    $text = Short_Video_List($text);
    return $text;
}

function Short_Lazyload($text)
{
    $text = preg_replace_callback('/<img src=\"(.*?)\".*?>/ism', function ($text) {
        return '<img class="lazyload" data-original="' . $text[1] . '" src="' . GetLazyLoad() . '" />';
    }, $text);
    return $text;
}

function Short_Photo($text)
{
    $text = preg_replace_callback('/<p>\[photo\](.*?)\[\/photo\]<\/p>/ism', function ($text) {
        return '[photo]' . $text[1] . '[/photo]';
    }, $text);
    $text = preg_replace_callback('/\[photo\](.*?)\[\/photo\]/ism', function ($text) {
        return preg_replace('~<br.*?>~', '', $text[0]);
    }, $text);
    $text = preg_replace_callback('/\[photo\](.*?)\[\/photo\]/ism', function ($text) {
        return '<div class="j-photos">' . $text[1] . '</div>';
    }, $text);
    return $text;
}

function Short_Tag($text)
{
    $text = preg_replace_callback('/\[tag type=\"(.*?)\".*?\](.*?)\[\/tag\]/ism', function ($text) {
        return '<span class="j-tag ' . $text[1] . '">' . $text[2] . '</span>';
    }, $text);

    return $text;
}

function Short_Button($text)
{
    $text = preg_replace_callback('/\[btn href=\"(.*?)\" type=\"(.*?)\".*?\](.*?)\[\/btn\]/ism', function ($text) {
        return '<a href="' . $text[1] . '" class="j-btn ' . $text[2] . '">' . $text[3] . '</a>';
    }, $text);
    return $text;
}


function Short_Alt($text)
{
    $text = preg_replace_callback('/<p>\[alt type=\"(.*?)\".*?\](.*?)\[\/alt\]<\/p>/ism', function ($text) {
        return '[alt type="' . $text[1] . '"]' . $text[2] . '[/alt]';
    }, $text);
    $text = preg_replace_callback('/\[alt type=\"(.*?)\".*?\](.*?)\[\/alt\]/ism', function ($text) {
        return '<div class="j-alt ' . $text[1] . '">' . $text[2] . '</div>';
    }, $text);
    return $text;
}

function Short_Line($text)
{
    $text = preg_replace_callback('/<p>\[line\](.*?)\[\/line\]<\/p>/ism', function ($text) {
        return '[line]' . $text[1] . '[/line]';
    }, $text);
    $text = preg_replace_callback('/\[line\](.*?)\[\/line\]/ism', function ($text) {
        return '<div class="j-line"><span>' . $text[1] . '</span></div>';
    }, $text);
    return $text;
}

function Short_Tabs($text)
{
    $text = preg_replace_callback('/<p>\[tabs\](.*?)\[\/tabs\]<\/p>/ism', function ($text) {
        return '[tabs]' . $text[1] . '[/tabs]';
    }, $text);
    $text = preg_replace_callback('/\[tabs\](.*?)\[\/tabs\]/ism', function ($text) {
        return preg_replace('~<br.*?>~', '', $text[0]);
    }, $text);
    $text = preg_replace_callback('/\[tabs\](.*?)\[\/tabs\]/ism', function ($text) {
        $tabname = '';
        preg_match_all('/label=\"(.*?)\"\]/i', $text[1], $tabnamearr);
        for ($i = 0; $i < count($tabnamearr[1]); $i++) {
            if ($i === 0) {
                $tabname .= '<span class="active" data-panel="' . $i . '">' . $tabnamearr[1][$i] . '</span>';
            } else {
                $tabname .= '<span data-panel="' . $i . '">' . $tabnamearr[1][$i] . '</span>';
            }
        }
        $tabcon = '';
        preg_match_all('/"\](.*?)\[\//i', $text[1], $tabconarr);
        for ($i = 0; $i < count($tabconarr[1]); $i++) {
            if ($i === 0) {
                $tabcon .= '<div class="active" data-panel="' . $i . '">' . $tabconarr[1][$i] . '</div>';
            } else {
                $tabcon .= '<div data-panel="' . $i . '">' . $tabconarr[1][$i] . '</div>';
            }
        }
        return '<div class="j-tabs"><div class="nav">' . $tabname . '</div><div class="content">' . $tabcon . '</div></div>';
    }, $text);
    return $text;
}

function Short_Card_default($text)
{
    $text = preg_replace_callback('/<p>\[card-default width=\"(.*?)\" label=\"(.*?)\".*?\](.*?)\[\/card-default\]<\/p>/ism', function ($text) {
        return '[card-default width="' . $text[1] . '" label="' . $text[2] . '"]' . $text[3] . '[/card-default]';
    }, $text);
    $text = preg_replace_callback('/<p>\[card-default width=\"(.*?)\" label=\"(.*?)\".*?\](.*?)\[\/card-default\]<\/p>/ism', function ($text) {
        return '[card-default width="' . $text[1] . '" label="' . $text[2] . '"]' . $text[3] . '[/card-default]';
    }, $text);
    $text = preg_replace_callback('/\[card-default width=\"(.*?)\" label=\"(.*?)\".*?\](.*?)\[\/card-default\]/ism', function ($text) {
        return '<div class="j-card-default" style="width: ' . $text[1] . '">
                <div class="head">' . $text[2] . '</div>
                <div class="content">' . $text[3] . '</div>
            </div>';
    }, $text);
    return $text;
}


function Short_Collapse($text)
{
    $text = preg_replace_callback('/<p>\[collapse\](.*?)\[\/collapse\]<\/p>/ism', function ($text) {
        return '[collapse]' . $text[1] . '[/collapse]';
    }, $text);
    $text = preg_replace_callback('/\[collapse\](.*?)\[\/collapse\]/ism', function ($text) {
        return preg_replace('~<br.*?>~', '', $text[0]);
    }, $text);
    $text = preg_replace_callback('/\[collapse\](.*?)\[\/collapse\]/ism', function ($text) {
        return '<div class="j-collapse">' . $text[1] . '</div>';
    }, $text);
    $text = preg_replace_callback('/\<p>\[collapse-item label=\"(.*?)\".*?\](.*?)\[\/collapse-item\]<\/p>/ism', function ($text) {
        return '[collapse-item label="' . $text[1] . '"]' . $text[2] . '[/collapse-item]';
    }, $text);
    $text = preg_replace_callback('/\[collapse-item label=\"(.*?)\".*?\](.*?)\[\/collapse-item\]/ism', function ($text) {
        return '<div class="collapse-head"><span>' . $text[1] . '</span><svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M21.6 772.8c28.8 28.8 74.4 28.8 103.2 0L512 385.6 899.2 772.8c28.8 28.8 74.4 28.8 103.2 0 28.8-28.8 28.8-74.4 0-103.2l-387.2-387.2-77.6-77.6c-14.4-14.4-37.6-14.4-51.2 0l-77.6 77.6-387.2 387.2c-28.8 28.8-28.8 75.2 0 103.2z"></path></svg></div><div class="collapse-body">' . $text[2] . '</div>';
    }, $text);
    return $text;
}

function Short_Time_line($text)
{
    $text = preg_replace_callback('/<p>\[timeline\](.*?)\[\/timeline\]<\/p>/ism', function ($text) {
        return '[timeline]' . $text[1] . '[/timeline]';
    }, $text);
    $text = preg_replace_callback('/\[timeline\](.*?)\[\/timeline\]/ism', function ($text) {
        return preg_replace('~<br.*?>~', '', $text[0]);
    }, $text);
    $text = preg_replace_callback('/\[timeline\](.*?)\[\/timeline\]/ism', function ($text) {
        return '<div class="j-timeline">' . $text[1] . '</div>';
    }, $text);
    $text = preg_replace_callback('/<p>\[timeline-item\](.*?)\[\/timeline-item\]<\/p>/ism', function ($text) {
        return '[timeline-item]' . $text[1] . '[/timeline-item]';
    }, $text);
    $text = preg_replace_callback('/\[timeline-item\](.*?)\[\/timeline-item\]/ism', function ($text) {
        return '<div class="item">' . $text[1] . '</div>';
    }, $text);
    return $text;
}

function Short_Copy($text)
{
    $text = preg_replace_callback('/\[copy\](.*?)\[\/copy\]/ism', function ($text) {
        return '<span class="j-copy" data-copy="' . $text[1] . '">' . $text[1] . '</span>';
    }, $text);
    return $text;
}

function Short_Typing($text)
{
    $text = preg_replace_callback('/\[typing\](.*?)\[\/typing\]/ism', function ($text) {
        return '<span class="j-typing">' . $text[1] . '</span>';
    }, $text);
    return $text;
}

function Short_Card_Nav($text)
{
    $text = preg_replace_callback('/<p>\[card-nav\](.*?)\[\/card-nav\]<\/p>/ism', function ($text) {
        return '[card-nav]' . $text[1] . '[/card-nav]';
    }, $text);
    $text = preg_replace_callback('/\[card-nav\](.*?)\[\/card-nav\]/ism', function ($text) {
        return preg_replace('~<br.*?>~', '', $text[0]);
    }, $text);
    $text = preg_replace_callback('/\[card-nav\](.*?)\[\/card-nav\]/ism', function ($text) {
        return '<div class="j-card-nav">' . $text[1] . '</div>';
    }, $text);
    $text = preg_replace_callback('/\[card-nav-item src=\"(.*?)\" title=\"(.*?)\" img=\"(.*?)\".*?\/\]/ism', function ($text) {
        $img = $text[3] === "auto" ? $text[1] . '/favicon.ico' : $text[3];
        $arr = array(
            0 => "linear-gradient(to right, #6DE195, #C4E759)",
            1 => "linear-gradient(to right, #41C7AF, #54E38E)",
            2 => "linear-gradient(to right, #99E5A2, #D4FC78)",
            3 => "linear-gradient(to right, #ABC7FF, #C1E3FF)",
            4 => "linear-gradient(to right, #6CACFF, #8DEBFF)",
            5 => "linear-gradient(to right, #5583EE, #41D8DD)",
            6 => "linear-gradient(to right, #A16BFE, #DEB0DF)",
            6 => "linear-gradient(to right, #D279EE, #F8C390)",
            7 => "linear-gradient(to right, #F78FAD, #FDEB82)",
            8 => "linear-gradient(to right, #A43AB2, #E13680)",
        );
        return '<div class="item">
                    <a href="' . $text[1] . '" class="nav" style="background-image: ' . $arr[rand(0, 8)] . '">
                        <span class="avatar" style="background-image: url(' . $img . ')"></span>
                        <span class="content">' . $text[2] . '</span>
                        <svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M428.8 928h-60.8v-282.24l280.96-284.16 46.08 44.8-263.04 265.6v164.48l137.6-131.2 174.08 80 98.56-568.32-604.8 334.72 96 44.16-26.88 58.24L96 556.8l832-460.8-135.68 782.72-209.92-97.28z" p-id="5322"></path></svg>
                    </a>
                </div>';
    }, $text);
    return $text;
}

function Short_Dplayer($text)
{
    $text = preg_replace_callback('/<p>\[dplayer src="(.*?)".*?\/]<\/p>/ism', function ($text) {
        return '[dplayer src="' . $text[1] . '" /]';
    }, $text);

    $text = preg_replace_callback('/\[dplayer src="(.*?)".*?\/]/ism', function ($text) {
        return '<iframe scrolling="no" allowfullscreen="allowfullscreen" frameborder="0" width="100%" class="iframe-dplayer" src="' . GetDplayer() . '?url=' . $text[1] . '"></iframe>';
    }, $text);

    return $text;
}

function Short_Plyr($text)
{
    $text = preg_replace_callback('/<p>\[plyr src="(.*?)".*?\/]<\/p>/ism', function ($text) {
        return '[plyr src="' . $text[1] . '" /]';
    }, $text);

    $text = preg_replace_callback('/\[plyr src="(.*?)".*?\/]/ism', function ($text) {
        return '<iframe scrolling="no" allowfullscreen="allowfullscreen" frameborder="0" width="100%" class="iframe-dplayer" src="' . GetPlyr() . '?url=' . $text[1] . '"></iframe>';
    }, $text);

    return $text;
}


function Short_Music($text)
{
    $text = preg_replace_callback('/<p>\[music id="(.*?)".*?\/]<\/p>/ism', function ($text) {
        return '[music id="' . $text[1] . '" /]';
    }, $text);

    $text = preg_replace_callback('/\[music id="(.*?)".*?\/]/ism', function ($text) {
        return '<iframe class="iframe-music" frameborder="no" border="0" width="330" height="86" src="//music.163.com/outchain/player?type=2&id=' . $text[1] . '&auto=1&height=66"></iframe>';
    }, $text);

    return $text;
}

function Short_Music_List($text)
{
    $text = preg_replace_callback('/<p>\[music-list id="(.*?)".*?\/]<\/p>/ism', function ($text) {
        return '[music-list id="' . $text[1] . '" /]';
    }, $text);

    $text = preg_replace_callback('/\[music-list id="(.*?)".*?\/]/ism', function ($text) {
        return '<iframe class="iframe-music" frameborder="no" border="0" width="330" height="450" src="//music.163.com/outchain/player?type=0&id=' . $text[1] . '&auto=1&height=430"></iframe>';
    }, $text);

    return $text;
}

function Short_Video_List($text)
{
    $text = preg_replace_callback('/<p>\[video](.*?)\[\/video]<\/p>/ism', function ($text) {
        return '[video]' . $text[1] . '[/video]';
    }, $text);
    $text = preg_replace_callback('/\[video](.*?)\[\/video]/ism', function ($text) {
        return preg_replace('~<br.*?>~', '', $text[0]);
    }, $text);
    $text = preg_replace_callback('/\[video](.*?)\[\/video]/ism', function ($text) {
        return '<div class="j-short-video">' . $text[1] . '</div>';
    }, $text);
    $text = preg_replace_callback('/\[video-item src="(.*?)" poster="(.*?)".*?\/]/ism', function ($text) {
        return '<div class="item">
                    <div class="inner" data-poster="' . $text[2] . '" data-src="' . $text[1] . '" style="background-image: url(' . GetLazyLoad() . ')">
                        <svg t="1607510948740" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="19996" width="80" height="80"><path d="M512 65c247.424 0 448 200.576 448 448S759.424 961 512 961 64 760.424 64 513 264.576 65 512 65z m0 64c-212.077 0-384 171.923-384 384s171.923 384 384 384 384-171.923 384-384-171.923-384-384-384z m-63 214.657a64 64 0 0 1 33.593 9.525L655.857 460.03c30.086 18.552 39.435 57.982 20.882 88.067a64 64 0 0 1-21.324 21.152L482.151 674.17c-30.235 18.308-69.587 8.64-87.896-21.594A64 64 0 0 1 385 619.425V407.657c0-35.346 28.654-64 64-64z m1.196 74.49a8 8 0 0 0-1.196 4.207v183.432a8 8 0 0 0 12.15 6.84l149.688-90.851a8 8 0 0 0 0.057-13.643L461.208 415.55a8 8 0 0 0-11.012 2.595z" p-id="19997"></path></svg>
                    </div>
                </div>';
    }, $text);

    return $text;
}



function themeInit($archive)
{
    /* 强奸用户关闭反垃圾保护 */
    Helper::options()->commentsAntiSpam = false;
    /* 强奸用户关闭检查来源URL */
    Helper::options()->commentsCheckReferer = false;
    /* 强奸用户强制要求填写邮箱 */
    Helper::options()->commentsRequireMail = true;
    /* 强奸用户强制要求无需填写url */
    Helper::options()->commentsRequireURL = false;
    /* 强奸用户强制开启评论回复 */
    Helper::options()->commentsThreaded = true;

    if ($archive->is('single')) {
        $archive->content = ParseReply($archive->content);
        $archive->content = CreateCatalog($archive->content);
        $archive->content = ParseCode($archive->content);
    }
    if ($archive->request->isPost() && $archive->request->likeup) {
        commentLike($archive->request->likeup);
        exit;
    }
}



/* 请求 */
function GetRequest($curl, $method = 'post', $data = null, $https = true)
{
    $ch = curl_init(); //初始化
    curl_setopt($ch, CURLOPT_URL, $curl); //设置访问的URL
    curl_setopt($ch, CURLOPT_HEADER, false); //设置不需要头信息
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //只获取页面内容，但不输出
    if ($https) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不做服务器认证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不做客户端认证
    }
    if ($method == 'post') {
        curl_setopt($ch, CURLOPT_POST, true); //设置请求是POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置POST请求的数据
    }
    $str = curl_exec($ch); //执行访问，返回结果
    curl_close($ch); //关闭curl，释放资源
    return $str;
}


/* 解析头像 */
function ParseAvatar($mail, $re = 0, $id = 0)
{
    $a = Typecho_Widget::widget('Widget_Options')->JGravatars;
    $b = 'https://' . $a . '/';
    $c = strtolower($mail);
    $d = md5($c);
    $f = str_replace('@qq.com', '', $c);
    if (strstr($c, "qq.com") && is_numeric($f) && strlen($f) < 11 && strlen($f) > 4) {
        $g = '//thirdqq.qlogo.cn/g?b=qq&nk=' . $f . '&s=100';
        if ($id > 0) {
            $g = Helper::options()->rootUrl . '?id=' . $id . '" data-type="qqtx';
        }
    } else {
        $g = $b . $d . '?d=mm';
    }
    if ($re == 1) {
        return $g;
    } else {
        echo $g;
    }
}

/* 获取父级评论 */
function GetParentReply($parent)
{
    if ($parent == 0) {
        return '';
    }
    $db = Typecho_Db::get();
    $commentInfo = $db->fetchRow($db->select('author,status,mail')->from('table.comments')->where('coid = ?', $parent));
    $link = '<div class="parent">@' . $commentInfo['author'] .  '</div>';
    return $link;
}


function ParsePaopaoBiaoqingCallback($match)
{
    return '<img class="owo" src="' . autoCdnUrl('assets/owo/paopao/') . str_replace('%', '', urlencode($match[1])) . '_2x.png">';
}

function ParseAruBiaoqingCallback($match)
{
    return '<img class="owo" src="' . autoCdnUrl('assets/owo/aru/') . str_replace('%', '', urlencode($match[1])) . '_2x.png">';
}
function ParseBiliBiaoqingCallback($match)
{
    return '<img class="owo" src="' . autoCdnUrl('assets/owo/bili/') . str_replace('%', '', urlencode($match[1])) . '_2x.png">';
}

/* 格式化 */
function ParseReply($content)
{
    $content = preg_replace_callback(
        '/\:\:\(\s*(呵呵|哈哈|吐舌|太开心|笑眼|花心|小乖|乖|捂嘴笑|滑稽|你懂的|不高兴|怒|汗|黑线|泪|真棒|喷|惊哭|阴险|鄙视|酷|啊|狂汗|what|疑问|酸爽|呀咩爹|委屈|惊讶|睡觉|笑尿|挖鼻|吐|犀利|小红脸|懒得理|勉强|爱心|心碎|玫瑰|礼物|彩虹|太阳|星星月亮|钱币|茶杯|蛋糕|大拇指|胜利|haha|OK|沙发|手纸|香蕉|便便|药丸|红领巾|蜡烛|音乐|灯泡|开心|钱|咦|呼|冷|生气|弱|吐血)\s*\)/is',
        'ParsePaopaoBiaoqingCallback',
        $content
    );
    $content = preg_replace_callback(
        '/\:\@\(\s*(高兴|小怒|脸红|内伤|装大款|赞一个|害羞|汗|吐血倒地|深思|不高兴|无语|亲亲|口水|尴尬|中指|想一想|哭泣|便便|献花|皱眉|傻笑|狂汗|吐|喷水|看不见|鼓掌|阴暗|长草|献黄瓜|邪恶|期待|得意|吐舌|喷血|无所谓|观察|暗地观察|肿包|中枪|大囧|呲牙|抠鼻|不说话|咽气|欢呼|锁眉|蜡烛|坐等|击掌|惊喜|喜极而泣|抽烟|不出所料|愤怒|无奈|黑线|投降|看热闹|扇耳光|小眼睛|中刀)\s*\)/is',
        'ParseAruBiaoqingCallback',
        $content
    );
    $content = preg_replace_callback(
        '/\:\%\(\s*(脱单doge|热|微笑|口罩|doge|妙啊|OK|星星眼|辣眼睛|吃瓜|滑稽|呲牙|打call|歪嘴|调皮|虎年|豹富|嗑瓜子|笑哭|藏狐|脸红|给心心|嘟嘟|哦呼|喜欢|酸了|嫌弃|大哭|害羞|疑惑|喜极而泣|奸笑|笑|偷笑|惊讶|捂脸|阴险|囧|呆|抠鼻|大笑|惊喜|无语|点赞|鼓掌|尴尬|灵魂出窍|委屈|傲娇|疼|冷|生病|吓|吐|捂眼|嘘声|思考|再见|翻白眼|哈欠|奋斗|墨镜|难过|撇嘴|抓狂|生气|奶茶干杯|汤圆|锦鲤|福到了|鸡腿|雪花|干杯|黑洞|爱心|胜利|加油|抱拳|响指|保佑|支持|拥抱|跪了|怪我咯|老鼠|牛年|洛天依|坎公骑冠剑_吃鸡|坎公骑冠剑_钻石|坎公骑冠剑_无语|来古-沉思|来古-呆滞|来古-疑问|来古-震撼|来古-注意|原神_哇|原神_哼|原神_嗯|原神_欸嘿|原神_喝茶|原神_生气|保卫萝卜_白眼|保卫萝卜_笔芯|保卫萝卜_哭哭|保卫萝卜_哇|保卫萝卜_问号|无悔华夏_不愧是你|无悔华夏_吃瓜|无悔华夏_达咩|无悔华夏_点赞|无悔华夏_好耶|奥比岛_搬砖|奥比岛_点赞|奥比岛_击爪|奥比岛_委屈|奥比岛_喜欢)\s*\)/is',
        'ParseBiliBiaoqingCallback',
        $content
    );    
    return $content;
}




/* 判断是否是移动端 */
function isMobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;
    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/* 页面加载计时 */
timerStart();
function timerStart()
{
    global $timestart;
    $mtime     = explode(' ', microtime());
    $timestart = $mtime[1] + $mtime[0];
    return true;
}
function timerStop($display = 0, $precision = 3)
{
    global $timestart, $timeend;
    $mtime     = explode(' ', microtime());
    $timeend   = $mtime[1] + $mtime[0];
    $timetotal = number_format($timeend - $timestart, $precision);
    $r         = $timetotal < 1 ? $timetotal * 1000 . "ms" : $timetotal . "s";
    if ($display) {
        echo $r;
    }
    return $r;
}


/* 热门文章 */
class Widget_Post_hot extends Widget_Abstract_Contents
{
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault(array('pageSize' => $this->options->commentsListSize, 'parentId' => 0, 'ignoreAuthor' => false));
    }
    public function execute()
    {
        $select  = $this->select()->from('table.contents')
            ->where("table.contents.password IS NULL OR table.contents.password = ''")
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.created <= ?', time())
            ->where('table.contents.type = ?', 'post')
            ->limit($this->parameter->pageSize)
            ->order('table.contents.views', Typecho_Db::SORT_DESC);
        $this->db->fetchAll($select, array($this, 'push'));
    }
}

/* 随机图片 */
function GetRandomThumbnail($widget)
{
    $random = 'https://api.btstu.cn/sjbz/api.php?lx=dongman&format=images' . rand(1, 25);
    if (Helper::options()->Jmos) {
        $moszu = explode("\r\n", Helper::options()->Jmos);
        $random = $moszu[array_rand($moszu, 1)] . "?jrandom=" . mt_rand(0, 1000000);
    }
    $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
    $patternMD = '/\!\[.*?\]\((http(s)?:\/\/.*?(jpg|jpeg|gif|png|webp))/i';
    $patternMDfoot = '/\[.*?\]:\s*(http(s)?:\/\/.*?(jpg|jpeg|gif|png|webp))/i';
    $t = preg_match_all($pattern, $widget->content, $thumbUrl);
    $img = $random;
    if ($widget->fields->thumb) {
        $img = $widget->fields->thumb;
    } elseif ($t) {
        $img = $thumbUrl[1][0];
    } elseif (preg_match_all($patternMD, $widget->content, $thumbUrl)) {
        $img = $thumbUrl[1][0];
    } elseif (preg_match_all($patternMDfoot, $widget->content, $thumbUrl)) {
        $img = $thumbUrl[1][0];
    }
    echo $img;
}


/* 获取浏览量 */
function GetPostViews($archive)
{
    $db = Typecho_Db::get();
    $cid = $archive->cid;
    $exist = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid))['views'];
    if ($archive->is('single')) {
        $cookie = Typecho_Cookie::get('contents_views');
        $cookie = $cookie ? explode(',', $cookie) : array();
        if (!in_array($cid, $cookie)) {
            $db->query($db->update('table.contents')
                ->rows(array('views' => (int)$exist + 1))
                ->where('cid = ?', $cid));
            $exist = (int)$exist + 1;
            array_push($cookie, $cid);
            $cookie = implode(',', $cookie);
            Typecho_Cookie::set('contents_views', $cookie);
        }
    }
    echo number_format($exist);
}

/* 随机一言 */
function GetRandomMotto()
{
    if (Helper::options()->JMotto) {
        $JMottoRandom = explode("\r\n", Helper::options()->JMotto);
        $random = $JMottoRandom[array_rand($JMottoRandom, 1)];
        echo $random;
    }
}


/* 点赞数 */
function agreeNum($cid)
{
    $db = Typecho_Db::get();
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $AgreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($AgreeRecording)) {
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array(0)));
    }
    return array(
        'agree' => $agree['agree'],
        'recording' => in_array($cid, json_decode(Typecho_Cookie::get('typechoAgreeRecording'))) ? true : false
    );
}
/* 点赞 */
function agree($cid)
{
    $db = Typecho_Db::get();
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $agreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($agreeRecording)) {
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array($cid)));
    } else {
        $agreeRecording = json_decode($agreeRecording);
        if (in_array($cid, $agreeRecording)) {
            return $agree['agree'];
        }
        array_push($agreeRecording, $cid);
        Typecho_Cookie::set('typechoAgreeRecording', json_encode($agreeRecording));
    }
    $db->query($db->update('table.contents')->rows(array('agree' => (int)$agree['agree'] + 1))->where('cid = ?', $cid));
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    return $agree['agree'];
}

/* 评论like数 */
function commentLikeNum($coid)
{
    $db = Typecho_Db::get();
    $likes = $db->fetchRow($db->select('table.comments.likes')->from('table.comments')->where('coid = ?', $coid));
    $LikesRecording = Typecho_Cookie::get('typechoLikesRecording');
    if (empty($LikesRecording)) {
        Typecho_Cookie::set('typechoLikesRecording', json_encode(array(0)));
    }
    return array(
        'likes' => $likes['likes'],
        'recording' => in_array($coid, json_decode(Typecho_Cookie::get('typechoLikesRecording'))) ? true : false
    );
}

/* 评论like */
function commentLike($likeup)
{
    $db = Typecho_Db::get();
    $likes = $db->fetchRow($db->select('table.comments.likes')->from('table.comments')->where('coid = ?', $likeup));
    $likesRecording = Typecho_Cookie::get('typechoLikesRecording');
    if (empty($likesRecording)) {
        Typecho_Cookie::set('typechoLikesRecording', json_encode(array($likeup)));
    } else {
        $likesRecording = json_decode($likesRecording);
        if (in_array($likeup, $likesRecording)) {
            echo $likes['likes'];
            return;
        }
        array_push($likesRecording, $likeup);
        Typecho_Cookie::set('typechoLikesRecording', json_encode($likesRecording));
    }
    $db->query($db->update('table.comments')->rows(array('likes' => (int)$likes['likes'] + 1))->where('coid = ?', $likeup));
    $likes = $db->fetchRow($db->select('table.comments.likes')->from('table.comments')->where('coid = ?', $likeup));
    echo $likes['likes'];
}


/* 获取浏览器信息 */
function GetBrowser($agent)
{
if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
        $outputer = 'IE浏览器';
    } else if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Firefox/', $regs[0]);
        $FireFox_vern = explode('.', $str1[1]);
        $outputer = '火狐浏览器 '. $FireFox_vern[0];
    } else if (preg_match('/Maxthon([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Maxthon/', $agent);
        $Maxthon_vern = explode('.', $str1[1]);
        $outputer = '傲游浏览器 '.$Maxthon_vern[0];
    } else if (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $agent, $regs)) {
        $outputer = '搜狗浏览器';
    } else if (preg_match('#360([a-zA-Z0-9.]+)#i', $agent, $regs)) {
        $outputer = '360浏览器';
    } else if (preg_match('/Edg([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Edg/', $regs[0]);
        $Edge_vern = explode('.', $str1[1]);
        $outputer = 'Edge浏览器 Chrome'.$Edge_vern[0];
    } else if (preg_match('/EdgiOS([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('EdgiOS/', $regs[0]);
        $outputer = 'Edge浏览器';
    } else if (preg_match('/UC/i', $agent)) {
        $str1 = explode('rowser/',  $agent);
        $UCBrowser_vern = explode('.', $str1[1]);
        $outputer = 'UC浏览器 '.$UCBrowser_vern[0];
    }else if (preg_match('/OPR/i', $agent)) {
        $str1 = explode('OPR/',  $agent);
        $opr_vern = explode('.', $str1[1]);
        $outputer = '欧朋浏览器 '.$opr_vern[0];
    } else if (preg_match('/MicroMesseng/i', $agent, $regs)) {
        $outputer = '微信内嵌浏览器';
    }  else if (preg_match('/WeiBo/i', $agent, $regs)) {
        $outputer = '微博内嵌浏览器';
    }  else if (preg_match('/QQ/i', $agent, $regs)||preg_match('/QQBrowser\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('rowser/',  $agent);
        $QQ_vern = explode('.', $str1[1]);
        $outputer = 'QQ内嵌浏览器 '.$QQ_vern[0];
    } else if (preg_match('/MQBHD/i', $agent, $regs)) {
        $str1 = explode('MQBHD/',  $agent);
        $QQ_vern = explode('.', $str1[1]);
        $outputer = '<i class= "ua-icon icon-qq"></i>&nbsp;&nbsp;QQ浏览器 '.$QQ_vern[0];
    } else if (preg_match('/BIDU/i', $agent, $regs)) {
        $outputer = '百度浏览器';
    } else if (preg_match('/LBBROWSER/i', $agent, $regs)) {
        $outputer = '猎豹浏览器';
    } else if (preg_match('/TheWorld/i', $agent, $regs)) {
        $outputer = '世界之窗浏览器';
    } else if (preg_match('/XiaoMi/i', $agent, $regs)) {
        $outputer = '小米浏览器';
    } else if (preg_match('/UBrowser/i', $agent, $regs)) {
        $str1 = explode('rowser/',  $agent);
        $UCBrowser_vern = explode('.', $str1[1]);
        $outputer = 'UC浏览器 '.$UCBrowser_vern[0];
    } else if (preg_match('/mailapp/i', $agent, $regs)) {
        $outputer = 'email内嵌浏览器';
    } else if (preg_match('/2345Explorer/i', $agent, $regs)) {
        $outputer = '2345浏览器';
    } else if (preg_match('/Sleipnir/i', $agent, $regs)) {
        $outputer = '神马浏览器';
    } else if (preg_match('/YaBrowser/i', $agent, $regs)) {
        $outputer = 'Yandex浏览器';
    }  else if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
        $outputer = 'Opera浏览器';
    } else if (preg_match('/MZBrowser/i', $agent, $regs)) {
        $outputer = '魅族浏览器';
    } else if (preg_match('/VivoBrowser/i', $agent, $regs)) {
        $outputer = 'vivo浏览器';
    } else if (preg_match('/Quark/i', $agent, $regs)) {
        $outputer = '夸克浏览器';
    } else if (preg_match('/mixia/i', $agent, $regs)) {
        $outputer = '米侠浏览器';
    }else if (preg_match('/fusion/i', $agent, $regs)) {
        $outputer = '客户端';
    } else if (preg_match('/CoolMarket/i', $agent, $regs)) {
        $outputer = '基安内置浏览器';
    } else if (preg_match('/Thunder/i', $agent, $regs)) {
        $outputer = '迅雷内置浏览器';
    } else if (preg_match('/Chrome([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $str1 = explode('Chrome/', $agent);
        $chrome_vern = explode('.', $str1[1]);
        $outputer = '谷歌浏览器 Chrome'.$chrome_vern[0];
    } else if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
         $str1 = explode('Version/',  $agent);
    $safari_vern = explode('.', $str1[1]);
        $outputer = 'Safari '.$safari_vern[0];
    } else {
        $outputer = 'Chrome';
    }
    echo $outputer;
}

// 获取操作系统信息
function GetOs($agent)
{
    $os = false;
    if (preg_match('/win/i', $agent)) {
        if (preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } else if (preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } else if (preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } else if (preg_match('/nt 6.3/i', $agent)) {
            $os = 'Windows 8.1';
        } else if (preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } else if (preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';
        } else {
            $os = 'Windows X64';
        }
    } else if (preg_match('/android/i', $agent)) {
        if (preg_match('/android 9/i', $agent)) {
            $os = 'Android Pie';
        } else if (preg_match('/android 8/i', $agent)) {
            $os = 'Android Oreo';
        } else {
            $os = 'Android';
        }
    } else if (preg_match('/ubuntu/i', $agent)) {
        $os = 'Ubuntu';
    } else if (preg_match('/linux/i', $agent)) {
        $os = 'Linux';
    } else if (preg_match('/iPhone/i', $agent)) {
        $os = 'iPhone';
    } else if (preg_match('/mac/i', $agent)) {
        $os = 'MacOS';
    } else if (preg_match('/fusion/i', $agent)) {
        $os = 'Android';
    } else {
        $os = 'Linux';
    }
    echo $os;
}


/* 自定义字段 */
function themeFields($layout)
{
    $thumb = new Typecho_Widget_Helper_Form_Element_Text(
        'thumb',
        NULL,
        NULL,
        '自定义文章缩略图',
        '填写时：将会显示填写的文章缩略图 <br>
         不填写时：如果文章内有图片则取文章图片，否则取模板自带的随机缩略图'
    );
    $layout->addItem($thumb);

    $desc = new Typecho_Widget_Helper_Form_Element_Text(
        'desc',
        NULL,
        NULL,
        'SEO描述',
        '用于填写文章或独立页面的SEO描述，如果不填写则显示默认描述'
    );
    $layout->addItem($desc);

    $keywords = new Typecho_Widget_Helper_Form_Element_Text(
        'keywords',
        NULL,
        NULL,
        'SEO关键词',
        '用于填写文章或独立页面的SEO关键词，如果不填写则显示默认关键词'
    );
    $layout->addItem($keywords);

    $keywords = new Typecho_Widget_Helper_Form_Element_Text(
        'keywords',
        NULL,
        NULL,
        'SEO关键词',
        '用于填写文章或独立页面的SEO关键词，如果不填写则显示默认关键词'
    );
    $layout->addItem($keywords);

    $video = new Typecho_Widget_Helper_Form_Element_Textarea(
        'video',
        NULL,
        NULL,
        'M3U8或MP4地址（仅限文章和自定义页面使用）',
        '填写则会显示视频模板，不填写则显示默认文章模板 <br>
         格式：视频名称&视频地址。如果有多个，换行写即可 <br>
         例如：<br>
            第01集$https://iqiyi.cdn9-okzy.com/20201104/17638_8f3022ce/index.m3u8 <br>
            第02集$https://iqiyi.cdn9-okzy.com/20201104/17639_5dcb8a3b/index.m3u8 
        '
    );
    $layout->addItem($video);

    $sharePic = new Typecho_Widget_Helper_Form_Element_Textarea(
        'sharePic',
        NULL,
        NULL,
        'QQ里分享链接时的缩略图',
        '填写则会优先使用此缩略图，不填写则随机取网站中图片 <br>
         格式：图片URL 或 BASE64地址'
    );
    $layout->addItem($sharePic);

    $aside = new Typecho_Widget_Helper_Form_Element_Select(
        'aside',
        array(
            'on' => '开启（默认）',
            'off' => '关闭'
        ),
        'on',
        '是否开启当前页面的侧边栏',
        '用于单独设置当前页面侧边栏的开启状态 <br /> 
         只有在外观设置侧边栏开启状态下生效'
    );
    $layout->addItem($aside);
}

function GetQQSharePic($widget)
{
    if ($widget->fields->sharePic) {
        return $widget->fields->sharePic;
    } else {
        return Helper::options()->JQQSharePic;
    }
}

/* 评论回复 */
Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('myyodux', 'one');
Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('myyodux', 'one');
class myyodux
{
    public static function one($con, $obj, $text)
    {
        $text = empty($text) ? $con : $text;
        if (!$obj->is('single')) {
            $text = preg_replace("/\[hide\](.*?)\[\/hide\]/sm", '', $text);
        }
        return $text;
    }
}


function check_in($words_str, $str)
{
    $words = explode("||", $words_str);
    if (empty($words)) {
        return false;
    }
    foreach ($words as $word) {
        if (false !== strpos($str, trim($word))) {
            return true;
        }
    }
    return false;
}

Typecho_Plugin::factory('Widget_Feedback')->comment = array('plgl', 'one');
class plgl
{
    public static function one($comment, $post)
    {
        $options = Helper::options();
        $action = "";
        $msg = "";

        /* 脚本回复 */
        if ($options->JProhibitScript === "on") {
            if (preg_match("/<a(.*?)href=\"javascript:(.*?)>(.*?)<\/a>/u", $comment['text']) == 1) {
                $msg = "检测到脚本回复，已禁止！";
                $action = 'abandon';
            }
        }

        /* 空格回复 */
        if ($options->JProhibitEmsp === "on") {
            if (ctype_space($comment['text'])) {
                $msg = "请不要使用空格评论！";
                $action = 'abandon';
            }
        }

        /* 非中文评论 */
        if ($options->JProhibitChinese === "on") {
            if (!preg_match("/{\!\{(.*?)/", $comment['text']) && preg_match("/[\x{4e00}-\x{9fa5}]/u", $comment['text']) == 0) {
                $msg = "评论至少包含一个中文！";
                $action = 'abandon';
            }
        }


        /* 敏感词 */
        if (!empty($options->JProhibitWords)) {
            if (check_in($options->JProhibitWords, $comment['text'])) {
                $msg = "评论内容中包含敏感词汇";
                $action = "abandon";
            }
        }

        if ($action == "abandon") {
            Typecho_Cookie::set('__typecho_remember_text', $comment['text']);
            throw new Typecho_Widget_Exception(_t($msg), 403);
        }

        Typecho_Cookie::delete('__typecho_remember_text');
        return $comment;
    }
}
