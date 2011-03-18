<?php

$lang['menu'] = '同步 Wiki';

$lang['syncstart'] = '开始同步';
$lang['lastsync']  = '最后一次同步：';
$lang['remotever'] = '远端 Wiki 版本：';

$lang['xmlerr']   = '无法连接到远端 Wiki。请确保远端 Wiki 允许XMLRPC请求，并且您正确设置了URL。';
$lang['loginerr'] = '无法登录到远端 Wiki。请确保提供了有效的验证信息。';
$lang['versionerr'] = '远端 Wiki 的XMLRPC API版本过于陈旧。您需要升级远端 Wiki 以使用同步插件。';

$lang['neversync'] = '这个配置文件从未同步过。';
$lang['profile'] = '配置文件';
$lang['newprofile'] = '新配置文件...';
$lang['select'] = '选择';
$lang['delete'] = '删除';
$lang['create'] = '创建新配置文件';
$lang['edit'] = '编辑配置文件';

$lang['name']   = '配置文件名称';
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
$lang['pullfail']      = '拉取失败：';
$lang['pullok']        = '拉取成功：';
$lang['localdelok']    = '本地删除成功：';
$lang['localdelfail']  = '本地删除失败：';
$lang['pushfail']      = '推送失败：';
$lang['pushok']        = '推送成功：';
$lang['remotedelok']   = '远端删除成功：';
$lang['remotedelfail'] = '远端删除失败：';
$lang['skipped']       = '跳过：';

$lang['file']   = '页面或媒体文件';
$lang['local']  = '本地 Wiki';
$lang['remote'] = '远端 Wiki';
$lang['diff']   = '差异';

$lang['push']    = '推送本地版本到远端 Wiki。';
$lang['pushdel'] = '删除远端 Wiki 的版本。';
$lang['pull']    = '拉取远端版本到本地。';
$lang['pulldel'] = '删除本地版本。';
$lang['keep']    = '跳过这个文件，维持两边的版本。';

$lang['syncdone'] = '同步结束。';
