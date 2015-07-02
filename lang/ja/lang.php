<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * 
 * @author Hideaki SAWADA <chuno@live.jp>
 */
$lang['menu']                  = 'Wiki の同期';
$lang['syncstart']             = '同期開始';
$lang['lastsync']              = '最終同期：';
$lang['remotever']             = 'Remote Wiki Version:';
$lang['xmlerr']                = 'remote Wiki とのアクセスに失敗しました。remote Wiki の XMLRPC 接続許可、設定情報の XMLRPC URL 設定を確認して下さい。';
$lang['loginerr']              = 'remote Wiki のログインに失敗しました。remote Wiki の認証情報が有効かを確認して下さい。';
$lang['versionerr']            = 'remote Wiki の XMLRPC API バージョンが古すぎます。同期プラグインを使用するには remote Wiki をアップグレードする必要があります。';
$lang['neversync']             = 'この設定情報は一度も同期していません。';
$lang['profile']               = '同期の設定情報';
$lang['newprofile']            = '新規の設定情報…';
$lang['select']                = '選択';
$lang['create']                = '新規の同期の設定情報を作成する';
$lang['edit']                  = '同期の設定情報を編集する';
$lang['delete']                = '同期の設定情報を削除する';
$lang['server']                = 'XMLRPC URL';
$lang['user']                  = 'ユーザー名';
$lang['pass']                  = 'パスワード';
$lang['ns']                    = '名前空間';
$lang['depth']                 = '同期範囲';
$lang['level0']                = '全てのサブ名前空間';
$lang['level1']                = 'サブ名前空間なし';
$lang['level2']                = '名前空間 + 1 階層のサブ名前空間';
$lang['level3']                = '名前空間 + 2 階層のサブ名前空間';
$lang['type']                  = '同期内容';
$lang['type0']                 = '全て';
$lang['type1']                 = 'ページのみ';
$lang['type2']                 = 'メディアファイルのみ';
$lang['save']                  = '保存';
$lang['changewarn']            = 'この設定情報を再保存すると、同期時間が初期化されます。次回の同期時に、すべてのファイルの同期方向を手動で選択する必要があります。';
$lang['lockfail']              = 'ロックできなかったのでスキップします：';
$lang['pullfail']              = 'pull 失敗：';
$lang['pullok']                = 'pull 成功：';
$lang['localdelok']            = 'local 削除成功：';
$lang['localdelfail']          = 'local 削除失敗：';
$lang['pushfail']              = 'push 失敗：';
$lang['pushok']                = 'push 成功：';
$lang['remotedelok']           = 'remote 削除成功：';
$lang['remotedelfail']         = 'remote 削除失敗：';
$lang['skipped']               = 'スキップしました：';
$lang['file']                  = 'ページ・メディアファイル';
$lang['local']                 = 'Local Wiki';
$lang['remote']                = 'Remote Wiki';
$lang['diff']                  = '差分';
$lang['push']                  = 'local リビジョンを remote Wiki へ push する。';
$lang['pushdel']               = 'remote Wiki のリビジョンを削除する。';
$lang['pull']                  = 'remote リビジョンを local Wiki へ pull する。';
$lang['pulldel']               = 'local リビジョンを削除する。';
$lang['keep']                  = 'このファイルをスキップして、両方のリビジョンをそのままにする。';
$lang['syncdone']              = '同期は終了しました。';
$lang['timeout']               = 'タイムアウト';
