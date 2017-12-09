/* global SYNC_DATA */

jQuery(function () {
    var row = 0;
    var $output = jQuery('#sync__plugin');
    var $progress = jQuery('#sync__progress').progressbar({value: false});


    function displayTable(data) {
        console.log('yeah');

        console.log(data);


        var $table = jQuery('<table>');


        jQuery.each(data.list, function (type, items) {
            jQuery.each(items, function (id, item) {
                $table.append(tr(type, id, item));
            });
        });
        $output.append($table);

        var $button = jQuery('<button>sync</button>');
        $button.click(beginsync);
        $output.append($button);

        $progress.hide();
    }

    function tr(type, id, item) {
        var $tr = jQuery('<tr>');
        $tr.html(
            '<td class="sync__file"></td>' +
            '<td class="sync__local"></td>' +
            '<td class="sync__dir"></td>' +
            '<td class="sync__remote"></td>' +
            '<td class="sync__diff"></td>'
        );
        $tr.addClass('type' + type);
        $tr.data('type', type);
        $tr.find('.sync__file').text(id);

        if (item.local) {
            $tr.find('.sync__local').html(item.local.info);
        } else {
            $tr.find('.sync__local').text('-');
        }

        if (item.remote) {
            $tr.find('.sync__remote').html(item.remote.info);
        } else {
            $tr.find('.sync__remote').text('-');
        }

        $tr.find('.sync__dir').append(dir(item));

        return $tr;
    }

    function dir(item) {
        row++;

        $radios = jQuery('<input /><input /><input />');
        $radios.attr('type', 'radio');
        $radios.attr('name', 'dir' + row);
        $radios.attr('value', 0);

        if (item.local) {
            $radios.first().attr('value', 1);
        } else {
            $radios.first().attr('value', 2);
        }
        if (item.remote) {
            $radios.last().attr('value', -1);
        } else {
            $radios.last().attr('value', -2);
        }

        if (item.dir === -1 || item.dir === 1) {
            $radios.val([item.dir]);
        } else {
            $radios.val([0]);
        }

        return $radios;
    }

    function beginsync() {
        SYNC_DATA.items = [];

        $output.find('tr').each(function (idx, tr) {
            var $tr = jQuery(tr);
            var id = $tr.find('td').first().text();
            var dir = parseInt($tr.find('input').val(), 10);
            var type = parseInt($tr.data('type'), 10);

            if (dir !== 0) {
                SYNC_DATA.items.push([id, type, dir]);
            }
        });

        $output.html('');
        $progress.progressbar('option', 'value', 0);
        $progress.progressbar('option', 'max', SYNC_DATA.items.length);
        $progress.show();

        $output.append('<div class="info">Syncing ' + SYNC_DATA.items.length + ' files'); // FIXME localize

        sync();
    }


    function endsync() {
        $progress.hide();
        var link = document.createElement('a');
        link.href = DOKU_BASE + '?do=admin&page=sync';
        link.text = 'Okay'; // FIXME localize
        link.className = 'button';
        $output.append(link);
    }

    function sync() {
        var cur = $progress.progressbar('option', 'max') - SYNC_DATA.items.length;
        $progress.progressbar('option', 'value', cur);

        var item = SYNC_DATA.items.pop();
        if (!item) {
            endsync();
            return;
        }


        jQuery.ajax(
            DOKU_BASE + 'lib/exe/ajax.php',
            {
                method: 'POST',
                data: {
                    call: 'sync_file',
                    no: SYNC_DATA.profile,
                    id: item[0],
                    type: item[1],
                    dir: item[2]
                },
                complete: sync,
                error: error
            }
        )
    }


    function error(data) {
        console.log(data);


        $err = jQuery('<div class="error">');
        $err.text(data);

        $output.append($err);
    }


    jQuery.ajax(
        DOKU_BASE + 'lib/exe/ajax.php',
        {
            method: 'POST',
            data: {
                call: 'sync_init',
                no: SYNC_DATA.profile
            },
            success: displayTable,
            error: error
        }
    )

});
