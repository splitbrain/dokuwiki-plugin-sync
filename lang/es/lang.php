<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * 
 * @author r0sk <r0sk10@gmail.com>
 */
$lang['menu']                  = 'Sincronizar Wikis';
$lang['syncstart']             = 'Comenzar Sincronización';
$lang['lastsync']              = 'Última Sincronización:';
$lang['remotever']             = 'Versión de la Wiki Remota:';
$lang['xmlerr']                = 'Fallo al conectar con wiki remota. Asegúrese que la wiki remota permite solicitudes XMLRPC y que ha configurado correctamente la URL.';
$lang['loginerr']              = 'Fallo al iniciar sesión en la wiki remota. Asegúrese que las credenciales provistas son válidas en la wiki remota.';
$lang['versionerr']            = 'La versión XMLRPC API de la wiki remota es demasiado antigua. Necesita actualizar la wiki remota para usar este plugin.';
$lang['neversync']             = 'Este perfil nunca ha sido sincronizado antes.';
$lang['profile']               = 'Prerfil de Sincronización';
$lang['newprofile']            = 'Nuevo Perfil...';
$lang['select']                = 'Seleccionar';
$lang['create']                = 'Crear nuevo Perfil de Sincronización';
$lang['edit']                  = 'Editar Perfil de Sincronización';
$lang['delete']                = 'Eliminar Perfil de sincronización';
$lang['server']                = 'XMLRPC URL';
$lang['user']                  = 'Usuario';
$lang['pass']                  = 'Contraseña';
$lang['ns']                    = 'Namespace';
$lang['depth']                 = 'Profundidad de Sincronización';
$lang['level0']                = 'Todos los sub-namespaces';
$lang['level1']                = 'No sub-namespaces';
$lang['level2']                = 'Namespace + 1 sub namespace';
$lang['level3']                = 'Namespace + 2 sub namespaces';
$lang['type']                  = 'Qué sincronizar';
$lang['type0']                 = 'Todo';
$lang['type1']                 = 'Sólo Páginas';
$lang['type2']                 = 'Archivo Multimedia';
$lang['save']                  = 'Guardar';
$lang['changewarn']            = 'Guardar este perfil borrará el registro de sincronización existente. Usted tendrá que elegir manualmente la dirección de sincronización para todos los archivos la próxima vez que sincronice.';
$lang['lockfail']              = 'no se pudo bloquear y se omitirá:';
$lang['pullfail']              = 'fallo al obtener:';
$lang['pullok']                = 'éxito al obtener:';
$lang['localdelok']            = 'eliminación local exitosa:';
$lang['localdelfail']          = 'eliminación local fallida:';
$lang['pushfail']              = 'fallo al enviar:';
$lang['pushok']                = 'éxito al enviar:';
$lang['remotedelok']           = 'eliminación remota exitosa:';
$lang['remotedelfail']         = 'eliminación remota fallida:';
$lang['skipped']               = 'omitido:';
$lang['file']                  = 'Página o Archivo Multimedia';
$lang['local']                 = 'Wiki Local';
$lang['remote']                = 'Wiki Remote';
$lang['diff']                  = 'Diferencias';
$lang['push']                  = 'Enviar revisión local a la wiki remota.';
$lang['pushdel']               = 'Eliminar revisión en la wiki remota.';
$lang['pull']                  = 'Obtener revisión remota en la wiki local.';
$lang['pulldel']               = 'Eliminar revisión local.';
$lang['keep']                  = 'Omitir este archivo y mantener ambas revisiones tal como están.';
$lang['syncdone']              = 'Sincronización finalizada.';
$lang['timeout']               = 'Timeout';
