<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 *
 * @author Myeongjin <aranet100@gmail.com>
 */
$lang['menu']                  = '위키 동기화';
$lang['syncstart']             = '동기화 시작';
$lang['lastsync']              = '마지막 동기화:';
$lang['remotever']             = '원격 위키 버전:';
$lang['loginerr']              = '원격 위키에 로그인하는 데 실패했습니다. 공급된 자격 증명이 원격 위키에서 올바른지 확인해야 합니다.';
$lang['versionerr']            = '원격 위키 XMLRPC API 버전이 너무 오래되었습니다. 원격 플러그인을 사용하려면 원격 위키를 업그레이드해야 합니다.';
$lang['neversync']             = '이 프로필은 이전에 동기화되지 않았습니다.';
$lang['profile']               = '동기화 프로필';
$lang['newprofile']            = '새 프로필...';
$lang['select']                = '선택';
$lang['create']                = '새 동기화 프로필 만들기';
$lang['edit']                  = '동기화 프로필 편집';
$lang['delete']                = '동기화 프로필 삭제';
$lang['server']                = 'XMLRPC URL';
$lang['user']                  = '사용자 이름';
$lang['pass']                  = '비밀번호';
$lang['ns']                    = '이름공간';
$lang['depth']                 = '동기화 깊이';
$lang['level0']                = '모든 하위 이름공간';
$lang['level1']                = '하위 이름공간 제외';
$lang['level2']                = '이름공간 + 1개 하위 이름공간';
$lang['level3']                = '이름공간 + 2개 하위 이름공간';
$lang['type']                  = '동기화할 내용';
$lang['type0']                 = '모두';
$lang['type1']                 = '문서만';
$lang['type2']                 = '미디어 파일';
$lang['save']                  = '저장';
$lang['changewarn']            = '이 프로필을 다시 저장하면 동기화 시간이 재설정됩니다. 다음 동기화에 있는 모든 파일에 대한 동기화 방향을 수동으로 선택할 필요가 있습니다.';
$lang['lockfail']              = '잠글 수 없고 건너뜁니다:';
$lang['localdelfail']          = '로컬 삭제 실패:';
$lang['js']['file']                  = '문서 또는 미디어 파일';
$lang['js']['local']                 = '로컬 위키';
$lang['js']['remote']                = '원격 위키';
$lang['js']['diff']                  = '차이';
$lang['js']['push']                  = '원격 위키로 로컬 판을 밉니다.';
$lang['js']['pushdel']               = '원격 위키에서 판을 삭제합니다.';
$lang['js']['pull']                  = '로컬 위키에서 원격 판을 당깁니다.';
$lang['js']['pulldel']               = '로컬 판을 삭제합니다.';
$lang['js']['keep']                  = '이 파일을 건너뛰고 그대로 두 판을 유지합니다.';
$lang['js']['syncdone']              = '동기화를 마쳤습니다.';
$lang['timeout']               = '시간 초과';

$lang['js']['list'] = '로컬 위키와 원격 위키 사이에 차이가 있는 파일의 목록이 아래에 나와 있습니다. 유지하고 싶은 판을 결정해야 합니다.';
$lang['js']['insync'] = '로컬 위키와 원격 위키 사이에 찾은 차이가 없습니다. 동기화할 필요가 없습니다.';
