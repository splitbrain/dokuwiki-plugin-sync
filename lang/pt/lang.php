<?php

/**
 * @license    GPL 2 (https://www.gnu.org/licenses/gpl.html)
 *
 * @author Eduardo Mozart de Oliveira <eduardomozart182@gmail.com>
 */
$lang['menu']                  = 'Sincronizar Wikis';
$lang['syncstart']             = 'Iniciar Sincronização';
$lang['lastsync']              = 'Última Sincronização:';
$lang['remotever']             = 'Versão do Wiki Remoto:';
$lang['xmlerr']                = 'Falha ao falar com o wiki remoto. Verifique se o wiki remoto permite solicitações XMLRPC e se você configurou a URL do ponto de extremidade corretamente.	';
$lang['loginerr']              = 'Falha ao efetuar login no wiki remoto. Verifique se as credenciais fornecidas são válidas no wiki remoto.';
$lang['versionerr']            = 'A versão da API XMLRPC do wiki remoto é muito antiga. Você precisa atualizar o wiki remoto para usar o plug-in de sincronização.';
$lang['neversync']             = 'Esse perfil nunca foi sincronizado antes.';
$lang['profile']               = 'Perfil de Sincronização';
$lang['newprofile']            = 'Novo Perfil...';
$lang['select']                = 'Selecionar';
$lang['create']                = 'Criar novo Perfil de Sincronização';
$lang['edit']                  = 'Editar Perfil de Sincronização';
$lang['delete']                = 'Excluir Perfil de Sincronização';
$lang['server']                = 'URL XMLRPC';
$lang['user']                  = 'Nome de usuário';
$lang['pass']                  = 'Senha';
$lang['ns']                    = 'Namespace';
$lang['depth']                 = 'Profundidade de Sincronização';
$lang['level0']                = 'Todos os sub namespaces';
$lang['level1']                = 'Nenhum sub namespace';
$lang['level2']                = 'Namespace + 1 sub namespace';
$lang['level3']                = 'Namespace + 2 sub namespaces';
$lang['type']                  = 'O que sincronizar';
$lang['type0']                 = 'Tudo';
$lang['type1']                 = 'Somente páginas';
$lang['type2']                 = 'Arquivos de mídia';
$lang['save']                  = 'Salvar';
$lang['changewarn']            = 'Salvar novamente esse perfil redefinirá os tempos de sincronização. Você precisará escolher manualmente as direções de sincronização para todos os arquivos na próxima sincronização.';
$lang['lockfail']              = 'não foi possível bloquear e vai pular:';
$lang['pullfail']              = 'falha ao puxar:';
$lang['pullok']                = 'puxar bem-sucedido:';
$lang['localdelok']            = 'exclusão local foi bem-sucedida:';
$lang['localdelfail']          = 'falha na exclusão local:';
$lang['pushfail']              = 'falha no push:';
$lang['pushok']                = 'push bem-sucedido:';
$lang['remotedelok']           = 'exclusão remota foi bem-sucedida:	';
$lang['remotedelfail']         = 'falha na exclusão remota:';
$lang['skipped']               = 'ignorado:';
$lang['file']                  = 'Página ou Arquivo de Mídia';
$lang['local']                 = 'Wiki Local';
$lang['remote']                = 'Wiki Remoto';
$lang['diff']                  = 'Diferença';
$lang['push']                  = 'Envie a revisão local para o wiki remoto.';
$lang['pushdel']               = 'Excluir revisão no wiki remoto.';
$lang['pull']                  = 'Puxe a revisão remota para o wiki local.';
$lang['pulldel']               = 'Excluir revisão local.';
$lang['keep']                  = 'Ignore este arquivo e mantenha ambas as revisões como estão.';
$lang['syncdone']              = 'Sincronização concluída.';
$lang['timeout']               = 'Tempo limite';
