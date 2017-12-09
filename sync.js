/* global SYNC_DATA */

jQuery(function () {
    var row = 0;
    var $output = jQuery('#sync__plugin');
    var $progress = jQuery('#sync__progress').progressbar({value: false});
    var $progress_label = $progress.find('.label');


    function displayTable(data) {
        console.log(data);

        SYNC_DATA.times = data.times;

        if(data.count === 0) {
            $output.append('<p>No data to sync</p>');
            $output.append(finishbutton());
            return;
        }

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
            '<td class="file"></td>' +
            '<td class="local"></td>' +
            '<td class="dir"></td>' +
            '<td class="remote"></td>' +
            '<td class="diff"></td>'
        );
        $tr.addClass('type' + type);
        $tr.data('type', type);
        $tr.find('.file').text(id);

        if (item.local) {
            $tr.find('.local').html(item.local.info);
        } else {
            $tr.find('.local').text('-');
        }

        if (item.remote) {
            $tr.find('.remote').html(item.remote.info);
        } else {
            $tr.find('.remote').text('-');
        }

        $tr.find('.dir').append(dir(item));

        return $tr;
    }

    function dir(item) {
        row++;

        var $html = jQuery(
            '<label class="push"><input /></label>' +
            '<label class="skip"><input /></label>' +
            '<label class="pull"><input /></label>'
        );
        var $radios = $html.find('input');
        $radios.attr({
            type: 'radio',
            name: 'dir' + row,
            value: 0,
            title: LANG.plugins.sync.keep
        });

        if (item.local) {
            $radios.first().attr({
                value: 1,
                title: LANG.plugins.sync.push
            });
        } else {
            $radios.first().attr({
                value: 2,
                title: LANG.plugins.sync.pushdel
            });
        }
        if (item.remote) {
            $radios.last().attr({
                value: -1,
                title: LANG.plugins.sync.pull
            });
        } else {
            $radios.last().attr({
                value: -2,
                title: LANG.plugins.sync.pulldel
            });
        }

        if (item.dir === -1 || item.dir === 1) {
            $radios.val([item.dir]);
        } else {
            $radios.val([0]);
        }

        return $html;
    }

    function beginsync() {
        SYNC_DATA.items = [];

        $output.find('tr').each(function (idx, tr) {
            var $tr = jQuery(tr);
            var id = $tr.find('td').first().text();
            var dir = parseInt($tr.find('input:checked').val(), 10);
            var type = parseInt($tr.data('type'), 10);

            if (dir !== 0) {
                SYNC_DATA.items.push([id, type, dir]);
            }
        });

        $output.html('');
        $progress.progressbar('option', 'value', 0);
        $progress.progressbar('option', 'max', SYNC_DATA.items.length);
        $progress.show();

        $output.append('<p>Syncing ' + SYNC_DATA.items.length + ' files</p>'); // FIXME localize

        sync();
    }

    function finishbutton() {
        $progress.hide();
        var link = document.createElement('a');
        link.href = DOKU_BASE + '?do=admin&page=sync';
        link.text = 'Okay'; // FIXME localize
        link.className = 'button';
        return link;
    }

    function endsync() {
        jQuery.ajax(
            DOKU_BASE + 'lib/exe/ajax.php',
            {
                method: 'POST',
                data: {
                    call: 'sync_finish',
                    no: SYNC_DATA.profile,
                    ltime: SYNC_DATA.times.ltime,
                    rtime: SYNC_DATA.times.rtime
                },
                complete: function () {
                    $output.append(finishbutton());
                },
                error: error
            }
        );
    }

    function sync() {
        var cur = $progress.progressbar('option', 'max') - SYNC_DATA.items.length;
        $progress.progressbar('option', 'value', cur);
        $progress_label.text = '';

        var item = SYNC_DATA.items.pop();
        if (!item) {
            endsync();
            return;
        }

        $progress_label.text = item[0];
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
        );
    }


    function error(data) {
        var $err = jQuery('<div class="error">');
        $err.text(data);
        $output.append($err);
    }

    // main
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
