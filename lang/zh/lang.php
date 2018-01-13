<?php

$lang['menu'] = '同步 Wiki';

$lang['syncstart'] = '开始同步';
$lang['lastsync']  = '最后一次同步：';
$lang['remotever'] = '远端 Wiki 版本：';

$lang['loginerr'] = '无法登录到远端 Wiki。请确保提供了有效的验证信息。';
$lang['versionerr'] = '远端 Wiki 的XMLRPC API版本过于陈旧。您需要升级远端 Wiki 以使用同步插件。';

$lang['neversync'] = '这个配置文件从未同步过。';
$lang['profile'] = '配置文件';
$lang['newprofile'] = '新配置文件...';
$lang['select'] = '选择';
$lang['delete'] = '删除';
$lang['create'] = '创建新配置文件';
$lang['edit'] = '编辑配置文件';

$lang['server'] = 'XMLRPC地址';
$lang['user']   = '用户名';
$lang['pass']   = '密码';
$lang['ns']     = '命名空间';
$lang['depth']  = '同步深度';
$lang['level0'] = '所有子命名空间';
$lang['level1'] = '不包括子命名空间';
$lang['level2'] = '命名空间+1个子命名空间';
$lang['level3'] = '命名空间+2个子命名空间';
$lang['type']   = '同步内容';
$lang['type0']  = '所有';
$lang['type1']  = '仅页面';
$lang['type2']  = '媒体文件';


$lang['save'] = '保存';
$lang['changewarn'] = '重新保存这个配置文件将会重置同步时间。您需要在下次同步中手动为所有文件选择同步的方向。';

$lang['lockfail']      = '无法锁定并跳过：';
$lang['localdelfail']  = '本地删除失败：';


$lang['js']['file']   = '页面或媒体文件';
$lang['js']['local']  = '本地 Wiki';
$lang['js']['remote'] = '远端 Wiki';
$lang['js']['diff']   = '差异';

$lang['js']['push']    = '推送本地版本到远端 Wiki。';
$lang['js']['pushdel'] = '删除远端 Wiki 的版本。';
$lang['js']['pull']    = '拉取远端版本到本地。';
$lang['js']['pulldel'] = '删除本地版本。';
$lang['js']['keep']    = '跳过这个文件，维持两边的版本。';

$lang['js']['syncdone'] = '同步结束。';

$lang['js']['list'] = '下面列出了两个 Wiki 中不同的文件。您需要决定保存哪个版本。';
$lang['js']['insync'] = '在您的本地 Wiki 和远端 Wiki 间没有发现差异。不需要同步。';
