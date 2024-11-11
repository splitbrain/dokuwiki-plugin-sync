<?php

$lang['menu'] = 'Sincronizar Wikis';

$lang['syncstart'] = 'Iniciar Sincronização';
$lang['lastsync']  = 'Última Sincronização:';
$lang['remotever'] = 'Versão do Wiki Remoto:';

$lang['autherr']  = 'O usuário fornecido não tem permissão para acessar a API XMLRPC no wiki remoto.';
$lang['loginerr'] = 'Falha ao fazer login no wiki remoto. Certifique-se de que as credenciais fornecidas sejam válidas no wiki remoto.';
$lang['versionerr'] = 'A versão da API XMLRPC do wiki remoto é muito antiga. Você precisa atualizar o wiki remoto para usar o plugin de sincronização.';

$lang['neversync'] = 'Este perfil nunca foi sincronizado antes.';
$lang['profile'] = 'Sincronizar Perfil';
$lang['newprofile'] = 'Novo Perfil...';
$lang['select'] = 'Selecionar';
$lang['create'] = 'Criar novo Perfil de Sincronização';
$lang['edit'] = 'Editar Perfil de Sincronização';
$lang['delete'] = 'Excluir Perfil de Sincronização';

$lang['server'] = 'URL do XMLRPC';
$lang['user']   = 'Nome de usuário';
$lang['pass']   = 'Senha';
$lang['ns']     = 'Namespace';
$lang['depth']  = 'Profundidade de Sincronização';
$lang['level0'] = 'Todos os subnamespaces';
$lang['level1'] = 'Nenhum subnamespace';
$lang['level2'] = 'Namespace + 1 subnamespace';
$lang['level3'] = 'Namespace + 2 subnamespaces';
$lang['type']   = 'O que sincronizar';
$lang['type0']  = 'Tudo';
$lang['type1']  = 'Apenas páginas';
$lang['type2']  = 'Arquivos de mídia';


$lang['save'] = 'Salvar';
$lang['changewarn'] = 'Salvar novamente este perfil redefinirá os tempos de sincronização. Você precisará escolher manualmente as direções de sincronização para todos os arquivos na próxima sincronização.';

$lang['noconnect']     = 'Não foi possível conectar-se ao wiki remoto:';
$lang['lockfail']      = 'não foi possível bloquear e irá pular:';
$lang['localdelfail']  = 'falha na exclusão local:';

$lang['js']['list']   = 'Uma lista de arquivos que diferem entre o wiki local e o remoto é mostrada abaixo. Você precisa decidir quais revisões deseja manter.'; #from list.txt

$lang['js']['file']   = 'Página ou Arquivo de Mídia';
$lang['js']['local']  = 'Wiki Local';
$lang['js']['remote'] = 'Wiki Remoto';
$lang['js']['diff']   = 'Diff';
$lang['js']['dir']    = 'Direção de Sincronização';

$lang['js']['push']    = 'Envie a revisão local para o wiki remoto.';
$lang['js']['pushdel'] = 'Exclua a revisão no wiki remoto.';
$lang['js']['pull']    = 'Extraia a revisão remota para o wiki local.';
$lang['js']['pulldel'] = 'Exclua a revisão local.';
$lang['js']['keep']    = 'Ignore este arquivo e mantenha ambas as revisões como estão.';

$lang['js']['insync']    = 'Não foram encontradas diferenças entre o seu wiki local e o wiki remoto. Não há necessidade de sincronizar.'; #from nochange.txt
$lang['js']['tosync']    = 'Sincronizando %d arquivos…';
$lang['js']['btn_done']  = 'Feito';
$lang['js']['btn_start'] = 'Iniciar';
$lang['js']['syncdone']  = 'Sincronização concluída.';
$lang['js']['loading']   = 'Recuperando lista de arquivos…';
$lang['js']['summary']   = 'Resumo';

$lang['timeout'] = 'Tempo limite';


