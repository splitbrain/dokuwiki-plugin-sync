/* global SYNC_DATA */

jQuery(function () {
    var row = 0;
    var $output = jQuery('#sync__plugin');
    var $progress = jQuery('#sync__progress').progressbar({value: false});
    var $progress_label = $progress.find('.label');
    var $sum;

    /**
     * Show the table of items to sync
     *
     * @param {object} data
     */
    function displayTable(data) {
        console.log(data);

        SYNC_DATA.times = data.times;

        if (data.count === 0) {
            log(LANG.plugins.sync.insync);
            finishbutton();
            return;
        }
        log(LANG.plugins.sync.list);

        var $table = jQuery('<table>');
        $table.append(headers());
        jQuery.each(data.list, function (type, items) {
            jQuery.each(items, function (id, item) {
                $table.append(tr(parseInt(type, 10), id, item));
            });
        });
        $output.append($table);
        jQuery('input[name="dir"]').click(function(e){ sync_select(this.value); this.value = 0; });

        var $lbl = jQuery('<label>');
        $lbl.text(LANG.plugins.sync.summary + ': ');
        $sum = jQuery('<input>');
        $sum.attr('type', 'text');
        $sum.addClass('edit');
        $lbl.append($sum);
        $output.append($lbl);

        var $button = jQuery('<button>');
        $button.click(beginsync);
        $button.text(LANG.plugins.sync.btn_start);
        $output.append($button);

        $progress.hide();
    }

    /**
     * Get the table headers
     */
    function headers() {
        var $tr = jQuery('<tr>');

        ['file', 'local', 'dir', 'remote', 'diff'].map(function (l) {
            var $th = jQuery('<th>');
            $th.addClass(l);
            $th.text(LANG.plugins.sync[l]);
            if(l == 'dir') {
                $th.append('<br>');
                $th.append('<label class="push"><input type="radio" name="dir" value="1" title="' + LANG.plugins.sync['push'] + '"></label>');
                $th.append('<label class="skip"><input type="radio" name="dir" value="0" title="' + LANG.plugins.sync['skip'] + '"></label>');
                $th.append('<label class="pull"><input type="radio" name="dir" value="-1" title="' + LANG.plugins.sync['pull'] + '"></label>');
            }
            $tr.append($th);
        });
        return $tr;
    }

    /**
     * Get one table row for the given item
     *
     * @param {int} type
     * @param {string} id
     * @param {object} item
     */
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

        console.log('type', type);
        if (type === 1) {
            var url = DOKU_BASE + 'lib/plugins/sync/diff.php?no=' + SYNC_DATA.profile + '&id=' + encodeURIComponent(id);
            var a = jQuery('<a>');
            a.attr('href', url);
            a.attr('target', '_blank');
            a.text(LANG.plugins.sync.diff);
            a.click(diffclick);
            $tr.find('.diff').append(a);
        }

        return $tr;
    }

    /**
     * Open a diff in a popup
     *
     * @param {Event} e
     */
    function diffclick(e) {
        e.preventDefault();
        e.stopPropagation();
        window.open(this.href, 'diff', 'height=600,width=800');

    }

    /**
     * Get the direction buttons for the given Item
     *
     * @param {object} item
     */
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

    function sync_select(type) {
        switch(type) {
            case "1":
                type = "push";
                break;
            case "0":
                type = "skip";
                break;
            case "-1":
                type = "pull";
                break;
        }
        const types = ["push", "skip", "pull"];
        if(types.includes(type)) {
            jQuery('label[class='+type+'] input').prop('checked',true);
        }
    }

    /**
     * Start the sync process
     */
    function beginsync() {
        SYNC_DATA.items = [];

        $output.find('tr[class^="type"]').each(function (idx, tr) {
            var $tr = jQuery(tr);
            var id = $tr.find('td').first().text();
            var dir = parseInt($tr.find('input:checked').val(), 10);
            var type = parseInt($tr.data('type'), 10);

            if (dir !== 0) {
                SYNC_DATA.items.push([id, type, dir]);
            }
        });
        SYNC_DATA.items = SYNC_DATA.items.reverse();

        SYNC_DATA.summary = $sum.val();

        $output.html('');
        $progress.progressbar('option', 'value', 0);
        $progress.progressbar('option', 'max', SYNC_DATA.items.length);
        $progress.show();

        log(LANG.plugins.sync.tosync.replace(/%d/, SYNC_DATA.items.length));
        sync();
    }

    /**
     * Hide the progressbar and output a button for ending the sync
     */
    function finishbutton() {
        $progress.hide();
        var link = document.createElement('a');
        link.href = DOKU_BASE + '?do=admin&page=sync';
        link.text = LANG.plugins.sync.btn_done;
        link.className = 'button';
        $output.append(link);
    }

    /**
     * Finalize the sync process
     */
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
                complete: finishbutton(),
                error: error
            }
        );
    }

    /**
     * Sync the next file from the sync list
     */
    function sync() {
        var cur = $progress.progressbar('option', 'max') - SYNC_DATA.items.length;
        $progress.progressbar('option', 'value', cur);
        $progress_label.text('');

        var item = SYNC_DATA.items.pop();
        if (!item) {
            log(LANG.plugins.sync.syncdone);
            endsync();
            return;
        }

        $progress_label.text(item[0]);
        jQuery.ajax(
            DOKU_BASE + 'lib/exe/ajax.php',
            {
                method: 'POST',
                data: {
                    call: 'sync_file',
                    no: SYNC_DATA.profile,
                    id: item[0],
                    type: item[1],
                    dir: item[2],
                    sum: SYNC_DATA.summary
                },
                complete: sync,
                error: error
            }
        );
    }

    /**
     * Add the given error to the output
     *
     * @param {string} error
     */
    function error(error) {
        var $err = jQuery('<div class="error">');
        $err.text(error.responseText);
        $output.append($err);
    }

    /**
     * Output a given log message
     *
     * @param {string} log
     */
    function log(log) {
        var $p = jQuery('<p>');
        $p.text(log);
        $output.append($p);
    }

    // main
    $progress_label.text(LANG.plugins.sync.loading);
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
    );

});
