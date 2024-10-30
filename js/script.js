/**
 * seotoolset: v0.8.0
 */

var SEOToolSet = SEOToolSet || function () {
    (window.SEOToolSet.q=window.SEOToolSet.q||[]).push(arguments)};window.SEOToolSet.l=window.SEOToolSet.l||1*new Date();

// Mimic wp.i18n.__() if it doesn't exist.
var wp = wp || {};
wp.i18n = wp.i18n || {};
wp.i18n.__ = wp.i18n.__ || function (str, dom) {
    return str; }

var SEOTOOLSET_TEXTDOMAIN = SEOTOOLSET_TEXTDOMAIN || 'seotoolset';

/**
 * Define logging class.
 */
SEOToolSet.log = SEOToolSet.log || (function (console) {
    var ll = 0; //default log level
    var production = function () {
        ll=0; };

    var log = function (level, fn, label, message, trace) {
        if (!console) {
            return;
        }
        if (ll>level) {
            return;
        }
        var lapse = (new Date().getTime() - SEOToolSet.l) / 1000;
        var a = Math.floor(lapse);
        var b = (lapse - a).toPrecision(4);
        lapse = String("   " + a).slice(-(Math.max(3,String(a).length))) + String(b).slice(1,5);
        message = '[SEOToolSet:' + label + ':' + lapse + '] ' + message;
        if (typeof console[fn] == 'function') {
            console[fn](message);
        } else if (typeof console.log == 'function' || typeof console.log == 'object') {
            console.log(message);
        }
        if (trace && console.trace) {
            console.trace();
        }
    };

    var fn    = function (m) {
        log(0, 'debug','FN   ', m);};
    var debug = function (m) {
        log(1, 'debug','DEBUG', m);};
    var info  = function (m) {
        log(2, 'info', 'INFO ', m);};
    var warn  = function (m) {
        log(3, 'warn', 'WARN ', m, false);};
    var error = function (m) {
        log(4, 'warn', 'ERROR', m, false);};

    return {
        debug: debug,
        fn: fn,
        info: info,
        warn: warn,
        error: error,
        production: production
    };

})(window.console);


/**
 * Define analytics class for analytics-specific things.
 */
SEOToolSet.analytics = SEOToolSet.analytics || (function ($) {

    /***
     * Private variables.
     ***/


    var redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
    var client_id = '382467566954-vqqe2fup5q4vmcgphauib4hej2j88med.apps.googleusercontent.com';
    var client_secret = '5r3YnXeqPSTosbRgmD86FFAB';
    var scopes = [
        'https://www.googleapis.com/auth/analytics.readonly',
        'https://www.googleapis.com/auth/webmasters.readonly'
    ];


    /***
     * Public methods.
     ***/


    /**
     *
     */
    var getAnalyticsSites = function (auth_code) {

        if (!auth_code) {
            auth_code = $('input[name="google[auth]"]').val();
        }

        SEOToolSet.log.fn('getAnalyticsSites("' + auth_code + '")');


        getAccessToken(auth_code)
            .done(
                function (response) {
                    SEOToolSet.log.fn('getAnalyticsSites.getAccessToken.done');
                    var access_token = response.access_token;

                    SEOToolSet.log.debug('Token:' + JSON.stringify(response));

                    $('input[name="google[access_token]"]').val(access_token);

                    if (response.refresh_token) {
                        $('input[name="google[refresh_token]"]').val(response.refresh_token);
                    }

                    getAnalyticsSummary(access_token)
                    .done(
                        function (response) {
                            SEOToolSet.log.fn('getAnalyticsSites.getAccessToken.done.getAnalyticsSummary.done');
                            var email = response.username,
                                accounts = [];

                            SEOToolSet.log.debug('Analytics:' + JSON.stringify(response));
                            $('input[name="google[username]"]').val(email);

                            $.each(
                                response.items,
                                function (i, item) {
                                    $.each(
                                        item.webProperties,
                                        function (j, prop) {
                                            accounts.push(
                                                {
                                                    id: prop.id,
                                                    name: prop.name,
                                                    url: prop.websiteUrl,
                                                    email: email
                                                }
                                            );
                                        }
                                    );
                                }
                            );

                            // I'd like to return accounts and build the frontend
                            // part elsewhere but I don't know how since it's all up in
                            // nested async requests...
                            SEOToolSet.log.debug(JSON.stringify(accounts));
                            buildSiteList(accounts);
                        }
                    )
                    .fail(
                        function () {
                            SEOToolSet.log.fn('getAnalyticsSites.getAccessToken.done.getAnalyticsSummary.fail');
                            alert(wp.i18n.__('Error authorizing your analytics account. Please try again.', SEOTOOLSET_TEXTDOMAIN));
                        }
                    );

                    testWebmasterThing(access_token)
                    .done(
                        function (response) {
                            SEOToolSet.log.fn('getAnalyticsSites.getAccessToken.done.testWebmasterThing.done');
                            var getAnalyticsSitesClicked = true;
                            SEOToolSet.log.debug('Webmaster:' + JSON.stringify(response));
                        }
                    )
                    .fail(
                        function () {
                            SEOToolSet.log.fn('getAnalyticsSites.getAccessToken.done.testWebmasterThing.fail');
                            alert(wp.i18n.__('Terrible failure in the testWebmasterThing!', SEOTOOLSET_TEXTDOMAIN));
                        }
                    );
                }
            )
            .fail(
                function (jqXHR, textStatus, errorThrown) {
                    SEOToolSet.log.error('getAnalyticsSites.getAccessToken.fail jqXHR.responseText = "' + jqXHR.responseText + '"');

                    //jqXHR.responseText = '{ "error": "invalid_grant", "error_description": "Bad Request" }';

                    var obj = JSON.parse(jqXHR.responseText);
                    if (obj && obj.error) {
                        if (obj.error == 'invalid_grant') {
                            if (confirm(wp.i18n.__('Google responded with "invalid_grant". You must re-link your account. Do this now?', SEOTOOLSET_TEXTDOMAIN))) {
                                SEOToolSet.events.xhrLoadInside($('.postbox.google'), 'analytics-form');
                            }
                        } else {
                            alert(wp.i18n.__('Google responded with', SEOTOOLSET_TEXTDOMAIN) + ' "' + obj.error + '".');
                        }
                    } else {
                        alert(wp.i18n.__('An error occurred.', SEOTOOLSET_TEXTDOMAIN) + ' ' + textStatus);
                    }
                }
            );
    };


    /**
     *
     */
    var getAuthCode = function () {
        var url = 'https://accounts.google.com/o/oauth2/auth?',
            id = 'seotoolset-google-auth-window';

        popupWindow(
            url + [
            'response_type=code',
            'redirect_uri=' + redirect_uri,
            'client_id=' + client_id,
            'scope=' + scopes.join(' '),
            'access_type=offline',
            'approval_prompt=auto'
            ].join('&'),
            id,
            500,
            500
        );

        $('#google-auth').slideDown();
    };


    /***
     * Private methods.
     ***/


    /**
     *
     */
    function popupWindowClose()
    {
        if (SEOToolSet.google_auth_window) {
            SEOToolSet.google_auth_window.close();
        }
    }


    /**
     *
     */
    function popupWindow(url, id, width, height)
    {
        var left = screen.availWidth / 2 - width / 2,
            top = screen.availHeight / 2 - height / 2;

        popupWindowClose();
        SEOToolSet.google_auth_window = window.open(url, id, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);
        if (SEOToolSet.google_auth_window == null) {
            alert(wp.i18n.__('Authentication pop-up appears to have been blocked. Please disable any pop-up blockers and try again.', SEOTOOLSET_TEXTDOMAIN));
        }
    }


    /**
     * Exchange auth code for access token. Or, if not given an auth code,
     * use the saved refresh token to regrant the access token.
     */
    function getAccessToken(auth_code)
    {
        if (auth_code) {
            return $.post(
                'https://www.googleapis.com/oauth2/v4/token',
                {
                    code: auth_code,
                    client_id: client_id,
                    client_secret: client_secret,
                    redirect_uri: redirect_uri,
                    grant_type: 'authorization_code'
                }
            );
        }

        var refresh_token = $('input[name="google[refresh_token]"]').val();

        return $.post(
            'https://www.googleapis.com/oauth2/v4/token',
            {
                client_id: client_id,
                client_secret: client_secret,
                refresh_token: refresh_token,
                grant_type: 'refresh_token'
            }
        );
    }


    /**
     *
     */
    function getAnalyticsSummary(access_token)
    {
        return $.get(
            'https://www.googleapis.com/analytics/v3/management/accountSummaries',
            {
                access_token: access_token
            }
        );
    }


    /**
     *
     */
    function testWebmasterThing(access_token)
    {
        return $.get(
            'https://www.googleapis.com/webmasters/v3/sites',
            {
                access_token: access_token
            }
        );
    }


    /**
     *
     */
    function buildSiteList(accounts)
    {
        var $div = $('.postbox.google .sites'),
            $ul = $div.find('ul');

        $ul.html('');
        $.each(
            accounts,
            function (i, account) {
                var $li = $('<li />'),
                val = [
                    account.id,
                    account.name,
                    encodeURIComponent(account.url)
                ].join('|');

                $li.append('<input id="' + account.id + '" type="radio" name="google[analytics_id]" value="' + val + '">');
                $li.append(' <label for="' + account.id + '">' + account.name + ' &bull; ' + account.url + '/</label>');

                $ul.append($li);
            }
        );

        $div.slideDown();
    };

    // Expose public methods.
    return {
        getAnalyticsSites: getAnalyticsSites,
        getAuthCode: getAuthCode
    };

})(jQuery);


/**
 * Define posts class for post editor-related things.
 */
SEOToolSet.posts = SEOToolSet.posts || (function ($) {

    /***
     * Private variables.
     ***/

    var keywordColors = [
        // main
        '#ff225e',
        '#ff745e',
        '#f6bd60',
        '#5bdbab',
        '#00b8e2',
        '#806da0',
        // mixes
        '#ff22be',
        '#00e2be',
        // grays
        '#9099a5',
        '#676c73',
        '#3d3f40',
        '#000000'
    ];

    // To be ++'d and %'d to wrap around the array above.
    var lastColorUsed = 0;


    /***
     * Public methods.
     ***/

    var initEditorCallbacks = function () {
        if (isGutenbergActive()) {
            $('#wpbody-content').off('DOMSubtreeModified.bcseo').on(
                'DOMSubtreeModified.bcseo',
                '.edit-post-visual-editor',
                function () {
                    SEOToolSet.posts.updateKeywordCounts();
                }
            );
        }

        $('#wpbody-content').off('change.bcseo').on(
            'change.bcseo',
            'input#title,textarea.wp-editor-area,input[name="seotoolset_meta_title"],textarea[name="seotoolset_meta_description"]',
            function () {
                SEOToolSet.posts.updateKeywordCounts();
            }
        );

        // Add event listeners to Yoast section too
        $('#wpseo_meta').off('DOMSubtreeModified.bcseo').on(
            'DOMSubtreeModified.bcseo',
            ':input',
            function () {
                SEOToolSet.posts.updateKeywordCounts();
            }
        );

        var addhook = function () {
            if (SEOToolSet.posts.isGutenbergActive()) {
                // check for Gutenberg loaded or return/setTimeout
                var gtnb = wp.data.select('core/block-editor');
                if (!gtnb) {
                    setTimeout(
                        function () {
                            addhook(); },
                        1000
                    );
                    return;
                }
                // hook into Gutenberg callbacks
                gtnb.editPost = function () {
                    SEOToolSet.posts.updateKeywordCounts();
                };

                $(document).ready(
                    function () {
                        setTimeout(
                            function () {
                                SEOToolSet.posts.updateKeywordCounts();
                            },
                            500
                        );
                    }
                );
            } else {
                var tiny = window.tinyMCE ? window.tinyMCE.activeEditor : false;
                if (!tiny) {
                    setTimeout(
                        function () {
                            addhook(); },
                        1000
                    );
                    return;
                }
                tiny.onChange.add(
                    function () {
                        SEOToolSet.posts.updateKeywordCounts();
                    }
                );
            }

            SEOToolSet.posts.updateKeywordCounts();
        };

        // Don't add editor hooks if it's already been done.
        if (this.init === true) {
            return;
        }
        this.init = true;

        addhook();
    };

    /**
     * Highlight instances of the given word in the editor. Successive calls to
     * this will stack up `<mark>` tags around that word. We can stop this by
     * calling `unhighlightKeyword()` first, but for now I'll leave it be. The
     * way the API/UX works may never reach a state for multiple highlights.
     * (Stack issue is fixed. see below. -RB)
     */
    var highlightKeyword = function (word, color) {
        SEOToolSet.log.fn('SEOToolSet.posts.highlightKeyword(word=' + word + ', color=' + color + ')');
        color = color || keywordColors[lastColorUsed++ % keywordColors.length];
        SEOToolSet.log.fn('SEOToolSet.posts.highlightKeyword color=' + color + ')');

        regexAndUpdateContent(
            '\\b(' + word + ')(?![^<]*>)\\b',
            '<mark class="seotoolset" style="background-color: ' + color + '">$1</mark>'
        );

        // Undo any double tagging.
        regexAndUpdateContent(
            '<mark class="seotoolset" [^>]*>(<mark class="seotoolset" [^>]*>[^<>]+</mark>)</mark>',
            '$1'
        );
    };


    /**
     * Remove all highlighted words.
     */
    var removeAllHighlights = function () {
        SEOToolSet.log.fn('SEOToolSet.posts.removeAllhighlights()');
        regexAndUpdateContent(
            '<mark class="seotoolset"[^>]+?>(.*?)</mark>',
            '$1'
        );
    };

    var updateKeywordCounts = function () {
        SEOToolSet.log.fn('SEOToolSet.posts.updateKeywordCounts()');

        var keywordCounts = {};

        $('.keywords-table .keyword_row').each(
            function () {
                var keyword = $(this).children('.keyword').html(),
                    counts = keywordCount(keyword);

                keywordCounts[keyword] = counts;
            }
        );

        var updateCount = function (cell, count) {
            var goal = $(cell).find('.goal').removeClass('met'),
                re = new RegExp(/([0-9]+)-([0-9]+)/, 'gi');
                
            try {
                var m = re.exec(goal.html());
                if (count >= m[1] && count <= m[2]) {
                    goal.addClass('met');
                }
            } catch {
                //do nothing
            }

            $(cell).find('.have').html(count);
        };

        var updateCols = function (selector, cols) {
            $(selector + ' .keyword_row').each(
                function () {
                    var keyword = $(this).children('.keyword').html(),
                        counts = keywordCounts[keyword];

                    for (var idx in cols) {
                        var col = cols[idx],
                            count = (!col || typeof counts == 'undefined' || typeof counts[col] == 'undefined') ? '-' : counts[col];
                        updateCount($(this).find('.keyword_' + col), count);
                    }
                }
            );
        };

        updateCols('.keywords-table', ['title', 'meta_description', 'content']);

        updateCols('.meta-keywords-table', ['title', 'meta_description']);
    };



    /***
     * Private methods.
     ***/

    function isGutenbergActive()
    {
        return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';
    }

    function getTinyEditor()
    {
        return window.tinyMCE ? window.tinyMCE.activeEditor : false;
    }

    function getPostTitle()
    {
        SEOToolSet.log.fn('SEOToolSet.posts.getPostTitle()');

        var igba = isGutenbergActive();
        var title = $('input[name="seotoolset_meta_title"]').val();

        if (title == null) {
            title = $('.screen-reader-text:contains(SEO title preview:) + div span').text();
        }

        if (title == null && igba) {
            title = wp.data.select('core/editor').getEditedPostAttribute('title');
        }

        SEOToolSet.log.debug('SEOToolSet.posts.getPostTitle igba=' + igba);

        return title;
    }

    function getPostAuthorId()
    {
        SEOToolSet.log.fn('SEOToolSet.posts.getPostAuthorId()');

        var igba = isGutenbergActive();
        var authorId = $('.editor-post-author__select').val();

        if (authorId == null) {
            authorId = $('#post_author_override').val();
        }

        if (authorId == null && igba) {
            authorId = wp.data.select('core/editor').getEditedPostAttribute('author');
        }

        SEOToolSet.log.debug('SEOToolSet.posts.getAuthorId igba=' + igba);

        return authorId;
    }

    function getPostDescription()
    {
        SEOToolSet.log.fn('SEOToolSet.posts.getPostDescription()');

        var igba = isGutenbergActive();
        var desc = $('textarea[name="seotoolset_meta_description"]').val();

        if (desc == null) {
            desc = $('.screen-reader-text:contains(Meta description preview:) + div').text();
        }

        if (desc == null && igba) {
            desc = wp.data.select('core/editor').getEditedPostAttribute('excerpt');
        }

        SEOToolSet.log.debug('SEOToolSet.posts.getPostDescription igba=' + igba);

        return desc;
    }

    function getPostContent()
    {
        SEOToolSet.log.fn('SEOToolSet.posts.getPostContent()');

        var igba = isGutenbergActive();
        var text = '';

        if (igba) {
            text = wp.data.select('core/editor').getEditedPostContent();
        } else {
            var tiny = getTinyEditor();
            text = $('textarea.wp-editor-area[aria-hidden="false"]').val();
            if ((typeof text == 'undefined' || text == '') && tiny) {
                text = tiny.getContent();
            }
            else if ($('textarea.wp-editor-area').length) {
                text = $('textarea.wp-editor-area').val();
            }
            else {
                text = '';
            }
        }

        SEOToolSet.log.debug('SEOToolSet.posts.getPostContent igba=' + igba);

        return text;
    }

    function setPostContent(text)
    {
        SEOToolSet.log.fn('SEOToolSet.posts.setPostContent(...)');

        var igba = isGutenbergActive();

        if (igba) {
            //FIXME pushes all content into active block... wp.data.dispatch('core/block-editor').editPost({content: text});
        } else {
            var tiny = getTinyEditor();
            if (tiny) {
                tiny.setContent(text);
            }
        }

        SEOToolSet.log.debug('SEOToolSet.posts.setPostContent igba=' + igba);
    }

    /**
     * A support function to handle replacing certain pieces of editor content
     * with something else. `regex` doesn't have to be a string but I'm using it
     * as such due to variable concatenation.
     */
    function regexAndUpdateContent(regex, replace)
    {
        var re = new RegExp(regex, 'gi'),
            text = (getPostContent()+'').replace(re, replace);

        if (isGutenbergActive()) {
            // Iterate blocks and replace content.
            var blocks = wp.data.select('core/block-editor').getBlocks();
            for (var i in blocks) {
                var block = blocks[i],
                    clientId = block.clientId;
                wp.data.dispatch('core/block-editor').selectBlock(clientId);
                text = wp.data.select('core/editor').getEditedPostContent();
                text = (text+'').replace(re, replace);
                wp.data.dispatch('core/block-editor').resetBlocks(wp.blocks.parse(text));
            }
            wp.data.dispatch('core/block-editor').clearSelectedBlock();
        } else {
            setPostContent(text);
        }

        return text;
    }


    /**
     * Remove the highlight from the given word.
     */
    function unhighlightKeyword(word)
    {
        SEOToolSet.log.fn('SEOToolSet.posts.unhighlightKeyword(word=' + word + ')');
        regexAndUpdateContent(
            '<mark class="seotoolset"[^>]+?>(' + word + ')</mark>',
            '$1'
        );
    }


    function keywordCount(keyword)
    {
        SEOToolSet.log.fn('SEOToolSet.posts.keywordCount(keyword=' + keyword + ')');
        var title = getPostTitle(),
            meta_description = getPostDescription(),
            content = getPostContent(),
            count = { title: null, meta_description: null, content: null };

        keyword = (keyword + '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        title += '';
        meta_description += '';
        content += '';

        SEOToolSet.log.fn('SEOToolSet.posts.keywordCount(keyword=' + keyword + ') title=' + title + ' meta_description=' + meta_description + '');

        if (keyword.length <= 0) {
            count.title = title.length + 1;
            count.meta_description = meta_description.length + 1;
            count.content = content.length + 1;
        } else {
            var re = new RegExp('\\b(' + keyword + ')\\b', 'gi');
            count.title = (title.match(re) || []).length;
            count.meta_description = (meta_description.match(re) || []).length;
            count.content = (content.match(re) || []).length;
        }
        return count;
    }


    // Expose public methods.
    return {
        initEditorCallbacks: initEditorCallbacks,
        highlightKeyword: highlightKeyword,
        removeAllHighlights: removeAllHighlights,
        updateKeywordCounts: updateKeywordCounts,

        getPostContent: getPostContent,
        getPostTitle: getPostTitle,
        getPostAuthorId: getPostAuthorId,
        getPostDescription: getPostDescription,

        isGutenbergActive: isGutenbergActive,

        getTinyEditor: getTinyEditor,
    };

})(jQuery);

SEOToolSet.draw = SEOToolSet.draw || (function ($) {

    /***
     * Private variables.
     ***/

    var dataTrafficChart = null;
    var dataTrafficChartInitialized = false;


    /***
     * Private methods.
     ***/

    function initTrafficChart()
    {
        if (dataTrafficChartInitialized) {
            return;
        }
        SEOToolSet.log.fn('SEOToolSet.draw.initTrafficChart()');

        if (typeof google == 'undefined'
            || typeof google.charts == 'undefined'
            || typeof google.charts.load == 'undefined'
        ) {
            SEOToolSet.log.debug('SEOToolSet.draw.initTrafficChart google.charts not loaded yet, loading');
            
            var script = document.createElement("script");
            script.setAttribute("type", "text/javascript");
            script.setAttribute("src", "//www.gstatic.com/charts/loader.js");
            document.getElementsByTagName("head")[0].appendChild(script);

            setTimeout(
                function () {
                    loadGoogleCharts();
                },
                200
            );
            return;
        } else {
            loadGoogleCharts();
        }

        dataTrafficChartInitialized = true;
    }

    function loadGoogleCharts() {
        if (typeof google != 'undefined'
            && typeof google.charts != 'undefined'
            && typeof google.charts.load != 'undefined'
        ) {
            google.charts.load('current', { packages: ['corechart'] });
            google.charts.setOnLoadCallback(drawTrafficChart);
        }
    }

    /***
     * Public methods.
     ***/

    var drawTrafficChart = function (newData) {
        SEOToolSet.log.fn('SEOToolSet.draw.drawTrafficChart(' + JSON.stringify(newData) + ')');

        var $ = $ || jQuery;

        if (newData != null && newData !== '') {
            SEOToolSet.log.debug('SEOToolSet.draw.drawTrafficChart Setting dataTrafficChart=' + JSON.stringify(newData) + '');
            dataTrafficChart = newData;
        }

        initTrafficChart();

        if (typeof google.visualization == 'undefined'
            || typeof google.visualization.DataTable == 'undefined'
            || typeof google.visualization.AreaChart == 'undefined'
        ) {
            SEOToolSet.log.debug('SEOToolSet.draw.drawTrafficChart Calling setTimeout 2');
            setTimeout(
                function () {
                    drawTrafficChart(); },
                200
            );
            return;
        }

        var dataTable = new google.visualization.DataTable();

        var headers = [ 'desktop', 'mobile', 'organic', 'paid', 'direct', 'social' ];
        dataTable.addColumn('string', wp.i18n.__('Date', SEOTOOLSET_TEXTDOMAIN));
        for (var h in headers) {
            var k = headers[h],
                k2 = k.replace(
                    /^./,
                    function (chr) {
                        return (''+chr).toUpperCase(); }
                );
            dataTable.addColumn('number', wp.i18n.__(k2, SEOTOOLSET_TEXTDOMAIN));
        }

        if (dataTrafficChart != null) {
            for (var h in headers) {
                var k = headers[h];
                if (typeof dataTrafficChart[k] != 'undefined') {
                    dataTrafficChart[k].forEach(
                        function (row, idx) {
                            var arr = [ row['date'] ];
                            for (var h2 in headers) {
                                //key = direct|mobile|desktop|paid|social|organic
                                arr.push(dataTrafficChart[headers[h2]][idx]['views']);
                            }
                            dataTable.addRow(arr);
                        }
                    );
                    break;
                }
            }
        }

        var colors = ['#00b8e2', '#5bdbab', '#f6bd60', '#ff745e', '#ff225e', '#806da0'];
        colors.forEach(
            function (color, index) {
                dataTable.setColumnProperty(index + 1, 'color', color);
            }
        );
      
        var options = {
            legend: {
                position: 'top'
            },
            lineWidth: 2,
            areaOpacity: 1,
            pointSize: 8,
            colors: colors
        };
      
        var _drawChart = function () {
            var chartColors = [];
            var chartColumns = [0];
            var view = new google.visualization.DataView(dataTable);
            var el = document.getElementById('chart-traffic');

            if (!el) {
                return;
            }

            $.each(
                $('.traffic-check'),
                function (index, checkbox) {
                    var seriesColumn = parseInt(checkbox.value);
                    if (checkbox.checked) {
                        chartColumns.push(seriesColumn);
                        chartColors.push(dataTable.getColumnProperty(seriesColumn, 'color'));
                    }
                }
            );
            view.setColumns(chartColumns);
            options.colors = chartColors;
            var chart = new google.visualization.AreaChart(el);
            chart.draw(view, options);
        };

        $('.traffic-check').off('change.bcseo').on('change.bcseo', _drawChart);
        $(window).off('resize.bcseo').on('resize.bcseo', _drawChart);
        _drawChart();
    };

    return {
        drawTrafficChart: drawTrafficChart
    }
})(jQuery);

SEOToolSet.events = SEOToolSet.events || (function ($) {

    /***
     * Private variables.
     ***/


    var $dashWidget;
    var $postWidget;
    

    /***
     * Public methods.
     ***/


    /***
     * Private methods.
     ***/


    /**
     * Initialize functionality after document ready.
     */
    function bind(page)
    {
        SEOToolSet.log.fn('SEOToolSet.events.bind(page=' + page + ')');

        setupGenericEvents();

        switch (page) {
            case 'dashboard-widget':
                $dashWidget = $('#seotoolset_dashboard_widget');
            break;

            case 'page-widget':
            case 'post-widget':
                $postWidget = $('#seotoolset_post_widget');
                SEOToolSet.posts.initEditorCallbacks();
                setupPostEvents();
                //          setTimeout(function(){
                //              SEOToolSet.posts.updateKeywordCounts();
                //          }, 1);
            break;

            case 'page-dashboard':
            case 'page-activity':
            case 'page-content':
            case 'page-keywords':
            case 'page-authors':
                setupPageEvents();
            break;

            case 'page-settings':
                setupSettingsEvents();
            break;

            default:
            break;
        }
    }

    /**
     * Get cross-platform viewport dimensions.
     */
    function viewportDimensions()
    {
        var s = window,
            e = document,
            o = e.documentElement,
            t = e.getElementsByTagName('body')[0],
            x = s.innerWidth || o.clientWidth || t.clientWidth,
            y = s.innerHeight || o.clientHeight || t.clientHeight;

        return { width: x, height: y };
    }


    /**
     * It's basically the opposite of WP's `sanitize_title()`.
     */
    function toTitleCase(str, glue)
    {
        var words = str.split(' '),
            glue = (glue === undefined) ? ' ' : glue;

        words = words.map(
            function (word) {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            }
        );

        return words.join(glue);
    }


    /**
     * Switch tabs in a `.postbox` based on the `<a>` clicked.
     */
    function switchTab($link)
    {
        var $postbox = $link.closest('.postbox'),
            $tabs = $link.closest('ul.tabs'),
            target = $link.data('tab-target');

        if (typeof target != 'string') {
            return;
        }

        // Find and show the relevant `div.tab`.
        var $newtab = $postbox.find('div.tab.' + target);

        $postbox.find('div.tab').hide();
        $newtab.show();

        // Swap the active `ul.tabs li`.
        if ($tabs.length) {
            $tabs.find('li').removeClass('active');
            $link.parent('li').addClass('active');
        }

        // Google Charts are (still) not responsive. You can force them to draw
        // on window resize, and this will make them adjust to their new parent
        // width. Since tabs start out `display: none`, the Charts will draw
        // themselves based on the width of their `.postbox` rather than the
        // column they're in. This makes them far too wide and render behind or
        // over other elements. Triggering the resize event on tab switch will
        // fix this by redrawing once the Chart's actual column is in view.
        if ($newtab.find('svg').length) {
            window.dispatchEvent(new Event('resize'));
        }

        // Call a function to handle specific tabs if necessary.
        target = toTitleCase(target.replace('-', ' '), '');

        if (this['handle' + target + 'TabSwitch']) {
            this['handle' + target + 'TabSwitch']();
        }
    }


    // ---------------------------------------------------------------------- //


    /**
     * Keyword highlighting only works in the TinyMCE editor, so we've got to
     * check that it's active or has been activated.
     */
    function handleKeywordsTabSwitch()
    {
        var $tab = $postWidget.find('div.tab.keywords');

        if (SEOToolSet.posts.isGutenbergActive()) {
            //FIXME
        } else {
            var tiny = window.tinyMCE;

            // Seems `tiny.activeEditor` may still be null depending on when the tab
            // switch event is fired. Otherwise I'd be checking it too.
            if (tiny === null || !$('#wp-content-wrap').hasClass('tmce-active')) {
                $tab.find('p.warning').show();
            }
        }


        // Hide/show the warning when editor mode is switched.
        $('.wp-switch-editor.switch-tmce').on(
            'click.bcseo',
            function () {
                $tab.find('p.warning').hide();
            }
        );

        $('.wp-switch-editor.switch-html').on(
            'click.bcseo',
            function () {
                $tab.find('p.warning').show();
            }
        );
    }


    /**
     *
     */
    function checkRequiredFields($postbox)
    {
        var allgood = true;

        $postbox.find('.required').each(
            function () {
                if (!$(this).val()) {
                    $(this).addClass('empty');
                    allgood = false;
                }
            }
        );

        return allgood;
    }


    function refreshTarget(tabClass, data, action)
    {
        var $tab = $('.seotoolset .tab.' + tabClass);
        $(data.selector).addClass('loading');

        xhrLocal('GET', action, data, $tab).done(
            function (response) {
                obj = JSON.parse(response);
                SEOToolSet.log.debug(obj.data);

                $(data.selector).html(obj.data);
            }
        ).always(
            function () {
                    $(data.selector).removeClass('loading');
            }
        );
    }

    function xhrTemplate(template, data, $element, action)
    {
        var path = '/ajax/template';

        if (data === null || data === undefined) {
            data = {};
        }

        data.template = template;

        return makeApiCall('GET', path, data, $element, action);
    }

    function xhrLocal(method, path, data, $element, action)
    {
        return makeApiCall(method, path, data, $element, action);
    }


    function xhrRemote(method, path, data, $element, action)
    {
        if (data === null || data === undefined) {
            data = {};
        }

        data.remoteapi = true;

        return makeApiCall(method, path, data, $element, action);
    }

    function userAborted(xhr) {
        return !xhr.getAllResponseHeaders();
    }


    /**
     * Make an API call via Ajax. Returns the `jqXHR` object so the event that
     * made the call can await its completion and react accordingly -- note that
     * you'll need to parse the response again if attached to `.done()`.
     */
    var makeApiCall = function (method, path, data, $element, action) {
        var jqxhr, parsed;
        SEOToolSet.log.fn('makeApiCall(method=' + method + ', path=' + path + ', data=' + JSON.stringify(data) + ')');

        if (data === null || data === undefined) {
            data = {};
        }

        // Points the request at a specific hook and function.

        // `seotoolset_api_request` as needed by the API class. This includes
        // editing all `makeApiCall()` calls to send nothing for the action
        // unless there's a specific reason to call something else.
        data.action = action || 'seotoolset_apiRequest';
        data.method = method;
        data.path = path;

        if ($element) {
            if (!$element.find('.spinner').length) {
                $element.append('<div class="spinner" />');
            }

            $element.addClass('loading');
        }
        
        //log.debug('API Call:', path, 'Data:', data);
        jqxhr = $.post(ajaxurl, data, null, 'html');
        // API call succeeded -- in an HTTP 20X way.

        jqxhr.done(
            function (response) {
                SEOToolSet.log.debug('jqxr.done response:', response);

                if ($element) {
                    $element.removeClass('loading');
                }
                try {
                    parsed = response;
                    SEOToolSet.log.debug('jqxr.done parsed response:', parsed);

                    if (!parsed.success) {
                        //handleApiError(parsed, $element);
                    }

                    // Load the HTML into the element. If `selector` is false, it
                    // means use the current element. If it's instead undefined, it
                    // defaults to `.inside` which is what `.postbox` elements use.
                    if (parsed.template) {
                        if (data.selector === false) {
                            //$element.html(parsed.template);
                        } else {
                            //$element.find((data.selector) ? data.selector : '.inside').html(parsed.template);
                        }
                    }
                } catch (e) {
                    // Will we ever not get JSON back?
                    SEOToolSet.log.error(e.message);
                }
            }
        );

        // API call failed -- in an HTTP 50X way.
        jqxhr.error(
            function (xhr, textStatus, errorThrown) {
                if (!userAborted(xhr)) {
                    handleApiError({ message: wp.i18n.__('API responded with 50x error.', SEOTOOLSET_TEXTDOMAIN) }, $element);
                }
            }
        );

        // Return it so whoever made the call can also set events.
        //log.debug('tables: ' + JSON.stringify(jqxhr));
        return jqxhr;
    };


    /**
     *
     */
    function handleApiError(response, $element)
    {
        alert('handleApiError: ' + response.message);
        SEOToolSet.log.error(JSON.stringify(response));

        if ($element) {
            $element.removeClass('loading');
            //$postbox.addClass('api-error');
        }
    }


    /**
     *
     */
    function login($button)
    {
        var $postbox = $button.closest('.postbox');

        if (!checkRequiredFields($postbox)) {
            return false;
        }

        var data = {
            username: $postbox.find('input[name="login[username]"]').val(),
            password: $postbox.find('input[name="login[password]"]').val()
        };

        xhrLocal('POST', '/login', data).done(
            function (response) {
                if (response === null) {
                    alert(wp.i18n.__('Could not log in with those credentials.', SEOTOOLSET_TEXTDOMAIN));
                } else {
                    document.location.reload();
                }
            }
        )
            .fail(
                function () {
                    alert(wp.i18n.__('Terrible failure logging in!', SEOTOOLSET_TEXTDOMAIN));
                }
            );
    }


    /**
     *
     */
    function logout($button)
    {
        var $postbox = $button.closest('.postbox');

        xhrLocal('DELETE', '/login')
            .done(
                function (response) {
                    document.location.reload();
                }
            )
            .fail(
                function () {
                    alert(wp.i18n.__('Terrible failure logging out!', SEOTOOLSET_TEXTDOMAIN));
                }
            );
    }


    /**
     *
     */
    function saveChanges($postbox, setting, successCallback, errorCallback)
    {
        SEOToolSet.log.fn('saveChanges');
        return postFields($postbox, '/ajax/setting', { setting: setting }, successCallback, errorCallback);
    }

    function postFields($postbox, url, data, successCallback, errorCallback)
    {
        SEOToolSet.log.fn('postFields');

        if (!checkRequiredFields($postbox)) {
            SEOToolSet.log.debug('postFields missing required fields');
            return false;
        }

        // Populate data with post parameters from form.
        var data = data || {};

        if (typeof data.setting != 'undefined') {
            $postbox.find('input[name^="' + data.setting + '"],select[name^="' + data.setting + '"]')
                .filter(
                    function (idx, el) {
                        // Exclude unselected radio buttons and check boxes.
                        var type = $(el).attr('type');
                        if ($(el).is(':checked') || $(el).is(':selected')) {
                            return true;
                        }
                        if (type == 'checkbox' || type == 'radio') {
                            return false;
                        }
                        return true;
                    }
                )
                .each(
                    function () {
                        // Strip parameters from array. (e.g. project[new_name] -> new_name)
                        var key = $(this).attr('name');
                        if (!key) {
                            return;
                        }
                        key = key.replace(/^[^[]+\[(.*)\]$/, '$1');
                        var value = $(this).val();
                        data[key] = value;
                    }
                );
        }

        SEOToolSet.log.debug('postFields data=' + JSON.stringify(data));

        xhrLocal('POST', url, data)
            .done(
                function (data, textStatus, jqXHR) {
                    SEOToolSet.log.fn('postFields.xhrLocal.ajax.done');

                    try {
                        if (data === null) {
                            throw wp.i18n.__('There was an error reading the server response.', SEOTOOLSET_TEXTDOMAIN);
                        }

                        var obj = JSON.parse(data);

                        if (!obj.success) {
                            throw obj.data || wp.i18n.__('The server did not return a success response.', SEOTOOLSET_TEXTDOMAIN);
                        }

                        if (typeof obj.data == 'undefined' || obj.data == "") {
                            //throw 'The server returned a success response, but no data.';
                            return successCallback($postbox, obj);
                        }

                        if (typeof data.error_message != 'undefined' && data.error_message != "") {
                            throw wp.i18n.__('The server returned an error message.', SEOTOOLSET_TEXTDOMAIN) + ' ' + data.error_message;
                        }

                        SEOToolSet.log.debug('postFields.xhrLocal.ajax.done success!');

                        if (typeof successCallback == 'function') {
                            return successCallback($postbox, obj);
                        }
                    } catch (err) {
                        SEOToolSet.log.error('postFields.xhrLocal.ajax.done error "' + err + '"');

                        if (typeof errorCallback == 'function') {
                            return errorCallback($postbox, err);
                        }

                        alert(err);
                    }
                }
            )
            .fail(
                function (jqXHR, textStatus, errorThrown) {
                    SEOToolSet.log.error('postFields.xhrLocal.ajax.fail jqXHR.responseText = "' + jqXHR.responseText + '"');

                    if (typeof errorCallback == 'function') {
                        return errorCallback($postbox, textStatus);
                    }

                    alert(textStatus);
                }
            );
    }


    /**
     *
     */
    function selectProject($button)
    {
        SEOToolSet.log.fn('selectProject');

        var $postbox = $button.closest('.postbox');

        if (!checkRequiredFields($postbox)) {
            SEOToolSet.log.debug('selectProject missing required fields');
            return false;
        }

        var data = {};
        $('.postbox.authentication').find('input[name^="project["]')
            .filter(
                function (idx, el) {
                    var type = $(el).attr('type');
                    if ($(el).is(':checked') || $(el).is(':selected')) {
                        return true;
                    }
                    if (type == 'checkbox' || type == 'radio') {
                        return false;
                    }
                    return true;
                }
            )
            .each(
                function () {
                    var key = $(this).attr('name').replace(/^project\[(.*)\]$/, '$1');
                    if (!/^(id$|new_)/.test(key)) {
                        return;
                    }
                    key = key.replace(/^new_/, '');

                    var value = $(this).val();

                    data[key] = value;
                }
            );

        SEOToolSet.log.debug('selectProject data=' + JSON.stringify(data));

        xhrLocal('POST', '/projects', data)
            .done(
                function (json) {
                    SEOToolSet.log.fn('selectProject.xhrLocal.ajax.done');

                    if (json === null) {
                        alert(wp.i18n.__('There was an error reading the server response.', SEOTOOLSET_TEXTDOMAIN));
                        return;
                    }

                    var obj = JSON.parse(json);

                    if (!obj.success) {
                        SEOToolSet.log.error('selectProject.xhrLocal.ajax.done request unsuccessful.');
                        return;
                    }

                    if (typeof obj.data == 'undefined' || obj.data == "") {
                        SEOToolSet.log.error('selectProject.xhrLocal.ajax.done returned empty data set.');
                        return;
                    }

                    if (typeof data.error_message != 'undefined' && data.error_message != "") {
                        alert(data.error_message);
                        return;
                    }

                    getProjects($postbox);
                }
            )
            .fail(
                function () {
                    alert(wp.i18n.__('Terrible failure selecting project!', SEOTOOLSET_TEXTDOMAIN));
                }
            );
    }


    /**
     *
     */
    function getProjects($postbox)
    {
        SEOToolSet.log.fn('getProjects');

        return xhrLoadInside($postbox, 'choose-project');
    }


    /**
     *
     */
    function xhrLoadInside($elem, template, skipIfEmpty = false, data = null, callbackDone = null)
    {
        SEOToolSet.log.fn('xhrLoadInside');

        if ($elem) {
            if (!$elem.find('.inside').length) {
                $elem.html('<div class="inside" />');
            }
            if (!$elem.find('.spinner').length) {
                $elem.append('<div class="spinner" />');
            }
            $elem.addClass('loading');
        }
        xhrTemplate(template, data)
            .done(
                function (json) {
                    SEOToolSet.log.fn('xhrLoadInside.xhrLocal.ajax.done');

                    if ($elem) {
                        $elem.removeClass('loading');
                    }

                    if (json === "") {
                        SEOToolSet.log.error('xhrLoadInside.xhrLocal.ajax.done returned empty json response.');
                        return;
                    }

                    var obj = null;

                    try {
                        obj = JSON.parse(json);

                        if (!obj.success) {
                            SEOToolSet.log.error('xhrLoadInside.xhrLocal.ajax.done request unsuccessful.');
                            return;
                        }
                    } catch (err) {
                        obj = { data: json };
                    }

                    if (typeof obj.data == 'undefined' || obj.data == "") {
                        SEOToolSet.log.error('xhrLoadInside.xhrLocal.ajax.done returned empty data set.');
                        if (!skipIfEmpty) {
                            $elem.find('.inside').html('');
                        }
                        return;
                    }

                    SEOToolSet.log.debug('xhrLoadInside.xhrLocal.ajax.done populating obj.data');//' + obj.data);
                    var inside = $elem.find('.inside');
                    inside.html(obj.data);

                    if (typeof callbackDone == "function") {
                        callbackDone(inside);
                    }
                }
            )
            .fail(
                function (xhr, textStatus, errorThrown) {
                    if (!userAborted(xhr)) {
                        SEOToolSet.log.fn('xhrLoadInside.xhrLocal.ajax.fail');
                        alert(wp.i18n.__('Couldn\'t retrieve data!', SEOTOOLSET_TEXTDOMAIN));
                    }
                }
            );
    }

    function parseQuery(qs)
    {
        var arr = {};
        if (typeof qs == 'string' && qs.length > 0) {
            var tuples = (qs[0] === '?' ? qs.substr(1) : qs).split('&');

            for (var i = 0; i < tuples.length; i++) {
                var tuple = tuples[i].split('=');
                arr[decodeURIComponent(tuple[0])] = decodeURIComponent(tuple[1] || '');
            }
        }

        return arr;
    }

    function elementAjaxLoad()
    {
        SEOToolSet.log.fn('elementAjaxLoad()');
        if ($(this).attr('data-ajax-loaded') == 'true') {
            return;
        }

        var url = $(this).attr('data-ajax-load');
        SEOToolSet.log.debug('elementAjaxLoad url = ' + url);
        $(this).attr('data-ajax-loaded', 'true');
        xhrLoadInside(
            $(this),
            url,
            true,
            parseQuery($(this).attr('data-ajax-data')),
            function (el) {
                el.find('[data-ajax-load]:visible').each(elementAjaxLoad);
            }
        );
    }


    // ---------------------------------------------------------------------- //

    /**
     * Events that apply in various/multiple spots.
     */
    function setupGenericEvents()
    {
        SEOToolSet.log.fn('setupGenericEvents');

        /**
         * Automatically switch tabs on hash. May not be used in production but
         * it's good for testing due to constant page refreshing.
         */
        if (!this.tabNav) {
            if ($('.postbox').find('div.tab').length > 0) {
                this.tabNav = true;
                if (window.location.hash) {
                    var hash = window.location.hash.substr(1),
                        $tab = $('.postbox').find('div.tab.' + hash);

                    if ($tab.length) {
                        switchTab($tab.closest('.postbox').find('a[href="#' + hash + '"]'));
                    }
                }
            }
        }

        $('[data-ajax-load]:visible').each(elementAjaxLoad);

        $('[data-tab-target]').off('click.bcseo').on(
            'click.bcseo',
            function () {
                var target = $(this).attr('data-tab-target');
                SEOToolSet.log.debug('[data-tab-target](click) = ' + target);

                $('.tab.' + target + ' [data-ajax-load]').each(elementAjaxLoad);
            }
        );

        /**
         * Switches standard-style tabs in `.postbox` elements.
         */
        $('.seotoolset.postbox').off('click.bcseo', 'ul.tabs a').on(
            'click.bcseo',
            'ul.tabs a',
            function (e) {
                SEOToolSet.log.debug('CLICK .seotoolset.postbox ul.tabs a');

                e.preventDefault();
                switchTab($(this));
            }
        );


        /**
         * Switches select-style tabs in `.postbox` elements.
         */
        $('.seotoolset.postbox').off('change.bcseo', 'form.tabs select').on(
            'change.bcseo',
            'form.tabs select',
            function () {
                SEOToolSet.log.debug('CHANGE .seotoolset.postbox form.tabs select');

                var $postbox = $(this).closest('.postbox');

                $postbox.find('div.tab').hide();
                $postbox.find('div.tab.' + $(this).val()).show();
            }
        );


        /**
         * Toggles collapsible things in `.postbox` elements.
         */
        $('.seotoolset.postbox').off('click.bcseo', '.collapsible .title').on(
            'click.bcseo',
            '.collapsible .title',
            function () {
                SEOToolSet.log.debug('CLICK .seotoolset.postbox .collapsible .title');

                var $postbox = $(this).closest('.postbox');

                $postbox.find('.collapsible').addClass('closed');
                $(this).parent().toggleClass('closed');
            }
        );


        /**
         * Placeholder: Prevent empty links from doing anything.
         */
        $('.seotoolset').off('click.bcseo', 'a[href=""], a[href="@@"]').on(
            'click.bcseo',
            'a[href=""], a[href="@@"]',
            function (e) {
                SEOToolSet.log.debug('CLICK .seotoolset a[href=""], .seotoolset a[href="@@"]');

                e.preventDefault();
                return false;
            }
        );


        /**
         * Handle pop-ups for "What's this?" links.
         */
        $('.postbox').off('click.bcseo', 'a.whats-this').on(
            'click.bcseo',
            'a.whats-this',
            function (e) {
                SEOToolSet.log.debug('CLICK .postbox a.whats-this');

                e.preventDefault();

                var $this = $(this),
                $postbox = $this.closest('.postbox'),
                $popup = $postbox.find('.pop-up.' + $this.data('popup-target')),
                left = $this.position().left + $this.outerWidth(true) + 15,
                top = $this.position().top,
                $pointer = $popup.find('.pointer');

                if ($this.css('display') == 'block') {
                    top += $this.height() / 2;
                }

                // On small screens it should stay centered.
                if (viewportDimensions().width > 782) {
                    $popup.css({ left: left + 'px', top: top + 'px' });

                    if (!$pointer.length) {
                        $pointer = $popup.append('<span class="pointer" />');
                    }
                } else {
                    $popup.css({ left: 0, right: 0 });
                }

                ($popup.is(':visible')) ? $popup.hide(): $popup.show();
            }
        );


        /**
         *
         */
        $('.postbox').off('click.bcseo', '.pop-up').on(
            'click.bcseo',
            '.pop-up',
            function (e) {
                SEOToolSet.log.debug('CLICK .postbox .pop-up');

                if (!$(e.target).is('a')) {
                    $(this).hide();
                }
            }
        );
    }

    /**
     * Events that apply to the post editor screen.
     */
    function setupPostEvents()
    {
        SEOToolSet.log.fn('setupPostEvents');

        /**
         * Testing: Keyword highlighting.
         */
        $('.test-highlight a').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .test-highlight a');

                e.preventDefault();

                if ($(this).hasClass('clear-highlights')) {
                    SEOToolSet.posts.removeAllHighlights();
                } else {
                    SEOToolSet.posts.highlightKeyword($(this).data('word'), $(this).data('color'));
                }
            }
        );


        /**
         * Analyze Keywords - POST current post and retrieve results, refresh table and re-highlight.
         */
        $('.tab.keywords .analyze').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .tab.keywords .analyze');

                e.preventDefault();

                var content = SEOToolSet.posts.getPostContent(),
                    title = SEOToolSet.posts.getPostTitle(),
                    authorId = SEOToolSet.posts.getPostAuthorId(),
                    desc = SEOToolSet.posts.getPostDescription();

                var $tab = $(this).closest('.tab'),
                data = {
                    postId: $('input[name="post_ID"]').val(),
                    authorId: authorId,
                    title: title,
                    contentBody: content,
                    description: desc,
                    template: 'ajax-post-keywords',
                    selector: 'table'
                };
                
                SEOToolSet.posts.removeAllHighlights();

                xhrLocal('POST', '/posts', data, $tab).done(
                    function (response) {
                        var parsed = JSON.parse(response);
                
                        //$tab.find('table').removeClass('unanalyzed').addClass('analyzed');
                        var success = $(this).data('ajax-success');

                        xhrLoadInside($("#ajax-post-keywords"), 'post-keywords', true, parseQuery('post_id=' + data.postId));

                        $.each(
                            parsed.keywords,
                            function (keyword, counts) {
                                SEOToolSet.posts.highlightKeyword(keyword);
                            }
                        );
                    }
                );
            }
        );


        /**
         * Add Keywords
         */
        $('.tab.keywords .addkeywords').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .tab.keywords .addkeywords');

                e.preventDefault();

                var $tab = $(this).closest('.tab'),
                keywordsinput = $('input[id="keywordsinput"]').val(),
                keywordsarray = keywordsinput.split(','),
                post_id = $('input[name="post_ID"]').val(),
                data = {
                    keyword_title: keywordsarray[0],
                    post_id: post_id,
                    template: 'ajax-post-keywords',
                    selector: '#ajax-post-keywords'
                };

                SEOToolSet.posts.removeAllHighlights();
                $(data.selector).addClass('loading');

                xhrLocal('POST', '/keywords', data, $tab).done(
                    function (response) {
                        SEOToolSet.log.debug('$postWidget.addkeywords.on.click.xhrLocal.done');
                        var obj = JSON.parse(response);
                        SEOToolSet.log.debug(obj.data);

                        $(data.selector).html(obj.data);
                        $('#keywordsinput').val('');

                        data = {
                            post_id: post_id,
                            template: 'ajax-post-meta-keywords',
                            selector: '#ajax-post-meta-keywords'
                        };
                        refreshTarget('meta-description', data, '/metakeywords');
                        data = {
                            post_id: post_id,
                            template: 'ajax-post-summary-statistics',
                            selector: '#ajax-post-summary-statistics'
                        };
                        refreshTarget('summary', data, '/summarystatistics');
                    }
                ).always(
                    function () {
                        $('#ajax-post-keywords').removeClass('loading');
                    }
                );
            }
        );


        /**
         * Update Keywords
         */
        $('.tab.keywords .updatekeywords').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .tab.keywords .updatekeywords');

                e.preventDefault();

                var keywordsinput = $('input[id="keywordsinput"]').val(),
                keywordsarray = keywordsinput.split(','),
                data = {
                    keyword_title: keywordsarray[0],
                    post_id: $('input[name="post_ID"]').val()
                };

                SEOToolSet.posts.removeAllHighlights();

                xhrLocal('PATCH', '/keywords', data).done(
                    function (response) {
                        //log.debug(JSON.stringify(response));
                        var parsed = JSON.parse(response);
                        //log.debug(JSON.stringify(parsed));
                    }
                );
            
            }
        );


        /**
         * Update Keyword Colors
         */
        $postWidget.off('blur.bcseo', '.keywordhighlight').on(
            'blur.bcseo',
            '.keywordhighlight',
            function (e) {
                SEOToolSet.log.debug('BLUR .keywordhighlight');

                e.preventDefault();

                var keyword_id = $(this).attr('name'),
                //keyword = $(this).attr('data-word'),
                new_highlight_color = $(this).val().substr(1);
                data = {
                    keyword_id : keyword_id,
                    post_id: $('input[name="post_ID"]').val(),
                    highlight_color: new_highlight_color
                };

                SEOToolSet.posts.removeAllHighlights();
                $('#seotoolset_post_widget').addClass('loading');

                SEOToolSet.log.debug('$postWidget.keywordhighlight.on.blur keyword_id=' + keyword_id + ' new_highlight_color=' + new_highlight_color);

                xhrLocal('PATCH', '/keywords', data).done(
                    function (response) {
                        SEOToolSet.log.debug('$postWidget.keywordhighlight.on.blur.xhrLocal.done');
                        obj = JSON.parse(response);
                        SEOToolSet.log.debug(obj.data);
                        $('#ajax-post-keywords').html(obj.data);
                        $('#seotoolset_post_widget').removeClass('loading');

                        $('#highlight_swatch_' + keyword_id).css('background-color', '#' + new_highlight_color);
                        //$('mark.seotoolset[data-word="' + keyword + '"]').css('background-color', '#' + new_highlight_color);
                        $('.test-highlight li #highlight_test_' + keyword_id).attr("data-color", '#' + new_highlight_color);
                    }
                );
            
            }
        );
        

        /**
         * Delete Keyword
         */
        $('.tab.keywords .keyword-delete').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .tab.keywords .keyword-delete');

                e.preventDefault();

                var $tab = $(this).closest('.tab'),
                post_id = $('input[name="post_ID"]').val(),
                data = {
                    keyword_id: $(this).attr('id'),
                    post_id: post_id,
                    selector: '#ajax-post-keywords'
                };

                SEOToolSet.posts.removeAllHighlights();
                $(data.selector).addClass('loading');

                xhrLocal('DELETE', '/keywords', data, $tab).done(
                    function (response) {
                        SEOToolSet.log.debug('$postWidget.keyword-delete.on.click.xhrLocal.done');
                        var obj = JSON.parse(response);
                        SEOToolSet.log.debug(obj.data);

                        $(data.selector).html(obj.data);

                        data = {
                            post_id: post_id,
                            template: 'ajax-post-meta-keywords',
                            selector: '#ajax-post-meta-keywords'
                        };
                        refreshTarget('meta-description', data, '/metakeywords');
                        data = {
                            post_id: post_id,
                            template: 'ajax-post-summary-statistics',
                            selector: '#ajax-post-summary-statistics'
                        };
                        refreshTarget('summary', data, '/summarystatistics');
                    }
                ).always(
                    function () {
                        $('#ajax-post-keywords').removeClass('loading');
                    }
                );
            }
        );


        /**
         * Function to update Meta description
         */
        function updateMetaDescription() {
            const metaTitle = $('.tab.meta-description #seotoolset_meta_title').val();
            const metaDescription = $('.tab.meta-description #seotoolset_meta_description').val();
            $('.tab.meta-description .seotoolset_meta_title_label').html(metaTitle);
            $('.tab.meta-description .seotoolset_meta_description_label').html(metaDescription);
            $.ajax({
                url: seotoolset_vars.ajax_url,
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'save_meta_description',
                    seotoolset_post_id: seotoolset_vars.post_id,
                    seotoolset_meta_title: metaTitle,
                    seotoolset_meta_description: metaDescription
                },
                success: function(result) {
                    let className = '';
                    if (result.status === 200) {
                        className = ' met';
                    }
                    let resultMessage = '<span class="pill"><span class="goal' + className + '">' + result.message + '</span></span>';
                    $('.seo-meta-messages').html(resultMessage);
                }
            });
        }


        /**
         * Selector to call updateMetaDescription() function
         */
        $('.tab.meta-description #seotoolset_meta_title, .tab.meta-description #seotoolset_meta_description').on('input', function() {
            updateMetaDescription();
        });


        /**
         * Iterate "#check-*" and return array of :checked ids.
         */
        function getTrafficChecks()
        {
            SEOToolSet.log.fn('getTrafficChecks');

            var trafficChecks = {};

            // check- desktop, organic, direct, mobile, paid, social
            $('*[id^="check-"]:checked').each(
                function () {
                    var key = $(this).attr('id').replace(/^check-/, '');
                    trafficChecks[key] = true;
                }
            );

            return trafficChecks;
        }


        /**
         * Reload Traffic Chart
         */
        $('body').off('change.bcseo', '.tab.traffic #traffic-metric, .tab.traffic #traffic-view').on(
            'change.bcseo',
            '.tab.traffic #traffic-metric, .tab.traffic #traffic-view',
            function (e) {
                SEOToolSet.log.debug('CHANGE .tab.traffic #traffic-metric, .tab.traffic #traffic-view');

                e.preventDefault();

                var data = {
                    trafficMetric: $('#traffic-metric').val(),
                    trafficView: $('#traffic-view').val(),
                    post_id: $('input[name="post_ID"]').val(),
                    trafficCheck: JSON.stringify(getTrafficChecks())
                };

                //SEOToolSet.posts.removeAllHighlights();

                SEOToolSet.log.debug('xhrLocal GET /traffic data=' + JSON.stringify(data));
                xhrLocal('GET', '/traffic', data, $('#chart-traffic')).done(
                    function (response) {
                        SEOToolSet.log.debug('xhrLocal GET /traffic done');
                        //var parsed = JSON.parse(response);
                        //SEOToolSet.log.debug('response=' + response);

                        var html = response.replace(/{"success":(true|false)}/, ''),
                        regs = response.match(/NEWDATASTART (.*?) NEWDATAEND/m);
                        html = html.replace(/NEWDATASTART (.*?) NEWDATAEND/m, '');
                        if (regs && regs[1]) {// && $('.tab.traffic').length > 0) {
                            $(".tab.traffic").html(html);
                            SEOToolSet.draw.drawTrafficChart(JSON.parse(regs[1]));
                        } else {
                            $(".tab.traffic").html(html);
                        }
                        //SEOToolSet.draw.drawTrafficChart(response.traffic[0]);
                    }
                );
            
            }
        );


        /**
         * Select the "custom" radio when the input for it is focused.
         */
        $('body').off('focus.bcseo', 'input[name="seotoolset_attributes_custom"]').on(
            'focus.bcseo',
            'input[name="seotoolset_attributes_custom"]',
            function () {
                SEOToolSet.log.debug('FOCUS input[name="seotoolset_attributes_custom"]');

                $('#attributes-custom').attr('checked', 'checked');
            }
        );
    }

    /**
     * Events that apply to certain pages within the plugin (e.g. "Activity").
     */
    function setupPageEvents()
    {
        SEOToolSet.log.fn('setupPageEvents');

        /**
         * Placeholder: A fake status switcher for the activity page.
         */
        $('.page-activity .table-sorting .field.status a').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .page-activity .table-sorting .field.status a');

                e.preventDefault();

                var $this = $(this),
                $field = $this.parents('.field'),
                currstatus = $field.data('current'),
                newstatus = $this.data('status');

                $field.find('.circle').removeClass('active');
                $this.find('.circle').addClass('active');

                $('table.big-boy').find('.' + currstatus).removeClass(currstatus).addClass(newstatus);
                $field.data('current', newstatus);
            }
        );

        $('.page-activity').off('click.bcseo', '.unsee-item').on(
            'click.bcseo',
            '.unsee-item',
            function (e) {
                SEOToolSet.log.debug('CLICK .page-activity .unsee-item');

                e.preventDefault();

                var $this   = $(this),
                $alerts = $this.attr('data-alerts'),
                $tr     = $this.closest('tr');

                //

                var args = {
                    alerts: $alerts,
                    status: 'unseen'
                };

                xhrLocal('PATCH', '/activity', args).done(
                    function (response) {
                        $tr.hide();
                    }
                );
            }
        );

        $('.page-activity').off('click.bcseo', '.delete-item').on(
            'click.bcseo',
            '.delete-item',
            function (e) {
                SEOToolSet.log.debug('CLICK .page-activity .delete-item');
                e.preventDefault();

                var $this   = $(this),
                $alerts = $this.attr('data-alerts'),
                $tr     = $this.closest('tr');

                //

                var args = {
                    alerts: $alerts,
                    status: 'deleted'
                };

                xhrLocal('PATCH', '/activity', args).done(
                    function (response) {
                        $tr.hide();
                    }
                );
            }
        );

        $('.page-activity,.seotoolset.activity').off('click.bcseo', '.remove-item').on(
            'click.bcseo',
            '.remove-item',
            function (e) {
                SEOToolSet.log.debug('CLICK .page-activity .remove-item');

                e.preventDefault();

                var $this   = $(this),
                $alerts = $this.attr('data-alerts'),
                $tr     = $this.closest('tr');

                //

                var args = {
                    alerts: $alerts,
                    status: 'seen'
                };

                xhrLocal('PATCH', '/activity', args).done(
                    function (response) {
                        $tr.hide();
                    }
                );

            }
        );

        
        function escapeHtml(html)
        {
            var text = document.createTextNode(html);
            var p = document.createElement('span');
            p.appendChild(text);
            return p.innerHTML;
        }

        function ajaxDashboard(startDate, endDate)
        {
            SEOToolSet.log.fn('ajaxDashboard startDate=' + startDate + ' endDate=' + endDate);

            var data = {
                DateRangeStart: startDate,
                DateRangeEnd: endDate,
                template: 'dashboard'
            };

            var el = $('.seotoolset.page-dashboard');

            SEOToolSet.log.debug('xhrLocal GET /dashboard data=' + JSON.stringify(data));
            xhrLocal('GET', '/ajax/template', data, el).done(
                function (response) {
                    SEOToolSet.log.debug('xhrLocal GET /dashboard done');

                    var parsed = JSON.parse(response);
                    var html = parsed.data;

                    //var html = response.replace(/{"success":(true|false)}/, ''),
                    var regs = html.match(/NEWDATASTART (.*?) NEWDATAEND/m);
                    html = html.replace(/NEWDATASTART (.*?) NEWDATAEND/m, '');
                    $(".seotoolset .page-dashboard > div.inside").html(html);

                    $(".caleran-container").hide();

                    /*if (regs && regs[1]) {
                    setTimeout(function(){
                        SEOToolSet.draw.drawTrafficChart(JSON.parse(regs[1]));
                    }, 10);
                    }*/
                }
            );
        }


        $("#dashboard-content-sort:not(init)").attr('init', 'true').caleran(
            {
                calendarCount: 1,
                format: "YYYY-MM-DD",
                dateSeparator: ":",
                target: $("#dashboard-content-sort-target"),
                showOn:"right",
                autoAlign:true,
                onafterselect: function (instance, start, end) {
                    var startDate = start.format("YYYY-MM-DD");
                    var endDate = end.format("YYYY-MM-DD");

                    $('.target_date').html(startDate + ' - ' + endDate);

                    ajaxDashboard(startDate, endDate)
                },
                onrangeselect: function (instance, range) {
                    var startDate = range.endDate.format("YYYY-MM-DD");
                    var endDate = range.startDate.format("YYYY-MM-DD");

                    if (range.title) {
                        $('.target_date').html(range.title);
                    } else {
                        $('.target_date').html(startDate + ' - ' + endDate);
                    }

                    ajaxDashboard(startDate, endDate);

                }
            }
        );

        $("#caleran-table-sort:not(data-init)").attr('data-init', 'true').caleran(
            {
                calendarCount: 1,
                format: "YYYY-MM-DD",
                dateSeparator: ":",
                target: $("#caleran-table-sort-target"),
                showOn:"right",
                autoAlign:true,
                onafterselect: function (instance, start, end) {
                    var startDate = start.format("YYYY-MM-DD");
                    var endDate = end.format("YYYY-MM-DD");

                    $('.target_date').html(startDate + ' - ' + endDate);
                },
                onrangeselect: function (instance, range) {
                    var startDate = range.endDate.format("YYYY-MM-DD");
                    var endDate = range.startDate.format("YYYY-MM-DD");

                    if (range.title) {
                        $('.target_date').html(range.title);
                    } else {
                        $('.target_date').html(startDate + ' - ' + endDate);
                    }
                }
            }
        );
        
        $('input.caleran-snap-input').off('change.bcseo').on(
            'change.bcseo',
            function () {
                SEOToolSet.log.debug('CHANGE input.caleran-snap-input');

                var calendar = $("#dashboard-content-sort").data("caleran");
                SEOToolSet.log.debug($('.caleran-snap-input').val());
                var date = $('.caleran-snap-input').val();
                calendar.setDisplayDate(date);
                calendar.reDrawCalendars({ setStart: date, setEnd: date, format: "DD/MM/YYYY" });
                ajaxDashboard(date, date);
                $('.target_date').html(date);
                $('.caleran-snap-input').val(date);
            }
        );

        // Need to figure out a way to not trigger a calendar redraw until both fields below are entered and valid dates.
        $('input.caleran-manual-start').off('change.bcseo').on(
            'change.bcseo',
            function () {
                SEOToolSet.log.debug('CHANGE input.caleran-manual-start');

                var calendar = $("#dashboard-content-sort").data("caleran");
                var manualstartdate = $('.caleran-manual-start').val();
                var manualenddate = $('.caleran-manual-end').val();
                if (manualenddate.length > 6) {
                    calendar.reDrawCalendars({setStart: manualstartdate, setEnd: manualenddate, format: "DD/MM/YYYY"});
                    ajaxDashboard(manualstartdate, manualenddate);
                    $('.target_date').html(manualstartdate + ' - ' + manualenddate);
                    $('.caleran-manual-start').val(manualstartdate);
                    $('.caleran-manual-end').val(manualenddate);
                }

            }
        );

        $('input.caleran-manual-end').off('change.bcseo').on(
            'change.bcseo',
            function () {
                SEOToolSet.log.debug('CHANGE input.caleran-manual-end');

                var calendar = $("#dashboard-content-sort").data("caleran");
                var manualstartdate = $('.caleran-manual-start').val();
                var manualenddate = $('.caleran-manual-end').val();

                if (manualstartdate.length > 6) {
                    calendar.reDrawCalendars({setStart: manualstartdate, setEnd: manualenddate, format: "DD/MM/YYYY"});
                    ajaxDashboard(manualstartdate, manualenddate);
                    $('.target_date').html(manualstartdate + ' - ' + manualenddate);
                    $('.caleran-manual-start').val(manualstartdate);
                    $('.caleran-manual-end').val(manualenddate);
                }

            }
        );


        /**
         *  Big Table Sort - Where all the table sorting magic happens for activity/keyword/author/content dashboard pages.
         */

        $("form.table-sorting").off('submit.bcseo').on(
            'submit.bcseo',
            function (e) {
                SEOToolSet.log.debug('SUBMIT form.table-sorting');
                e.preventDefault();
            }
        );

        $('.table-sorting .search input:submit').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .table-sorting .search input:submit');

                e.preventDefault();

                var data = {
                    query: $('.table-sorting .search input.search').val(),
                    ranking: $('.table-sorting .ranking select').val(),
                    range: $('.table-sorting #caleran-table-sort-target').val(),
                    template: current_page_template,
                };

                // Additional controls depending on the page.
                switch (current_page_template) {
                    case 'activity':
                        data['severity'] = $('.page-activity .severity > span.active').attr('data-severity');
                        data['status'] = $('.page-activity .archive').attr('data-current');
                    break;

                    default:
                }

                //SEOToolSet.log.debug(JSON.stringify(data));

                xhrLocal('GET', '/ajax/tableSort', data).done(
                    function (response) {
                        SEOToolSet.log.fn('tableSort.ajax.done response=' + response);
                        var html = '';
                        try {
                            var parsed = JSON.parse(response);
                            html = parsed.data;
                            if (!parsed.success) {
                                alert(wp.i18n.__('An error occurred. ', SEOTOOLSET_TEXTDOMAIN) + parsed.data);
                                return;
                            }
                        } catch {
                            alert(wp.i18n.__('Invalid response from server.', SEOTOOLSET_TEXTDOMAIN));
                            html = response;
                        }
                
                        //$(".seotoolset.wrap.page-content table tbody tr:eq(1)").html(response);
                        SEOToolSet.log.debug('tableSort.ajax.done current_page_template=' + current_page_template);

                        $('.page-' + current_page_template + ' table').replaceWith(html);
                
                    }
                );
            }
        );

        /**
         *
         */
        $('.page-status input#suspend').off('click.bcseo').on(
            'click.bcseo',
            function (e) {
                SEOToolSet.log.debug('CLICK .page-status input#suspend');

                var $checkbox = $(this),
                $row = $checkbox.closest('tr');

                $row.toggleClass('loading');

                xhrLocal('POST', '/toggle-suspension', { checked: $checkbox.is(':checked') }).done(
                    function (response) {
                        $row.toggleClass('loading').toggleClass('suspended active')
                    }
                );
            }
        );
    }

    /**
     * Events that apply to the settings page.
     */
    function setupSettingsEvents()
    {
        SEOToolSet.log.fn('setupSettingsEvents');

        /**
         * Make passwords viewable. I'm not sure if this standards-breaking or
         * against some kind of security protocol, but it works.
         */
        if ($('.postbox input[type="password"]:not(.signup)').length) {
            $('.postbox input[type="password"]:not(.signup)').each(
                function (i, e) {
                    var $this = $(e),
                    top = ($this.position().top + 3) + 'px';

                    $this.after('<span class="toggle-password dashicons dashicons-visibility" style="top: ' + top + '" />');
                }
            );
        }

        $('.toggle-password').off('click.bcseo').on(
            'click.bcseo',
            function () {
                SEOToolSet.log.debug('CLICK .toggle-password');

                var $this = $(this),
                $input = $(this).prev('input');

                ($input.attr('type') == 'password') ? $input.attr('type', 'text'): $input.attr('type', 'password');
                $this.toggleClass('dashicons-visibility dashicons-hidden');
            }
        );



        /**
         * Handle ajax actions defined on postbox a elements.
         */
        $('.postbox').off('click.bcseo', 'a,input[type="button"],input[type="submit"]').on(
            'click.bcseo',
            'a,input[type="button"],input[type="submit"]',
            function (e) {
                var ajax = $(this).data('ajax');
                if (ajax != 'true' && ajax != true) {
                    return;
                }

                if ($(this).hasClass('savechanges')) {
                    return;
                }
                if ($(this).hasClass('signup-btn')) {
                    return;
                }

                var action = $(this).data('ajax-action');
                if (action == '') {
                    SEOToolSet.log.debug('CLICK .postbox a -- Missing data-ajax-action');
                    return;
                }

                SEOToolSet.log.debug('CLICK .postbox a data-ajax-action=' + action);

                e.preventDefault();

                var $postbox = $(this).closest('.postbox');

                return xhrLoadInside($postbox, action);
            }
        );


        /**
         * Handle ajax actions defined on postbox a elements.
         */
        $('.postbox').off('click.bcseo', '.signup-btn[data-ajax=true]').on(
            'click.bcseo',
            '.signup-btn[data-ajax=true]',
            function (e) {
                var action = $(this).data('ajax-action');
                if (action == '') {
                    SEOToolSet.log.debug('CLICK .signup-btn -- Missing data-ajax-action');
                    return;
                }

                SEOToolSet.log.debug('CLICK .signup-btn data-ajax-action=' + action);

                e.preventDefault();

                var $postbox = $(this).closest('.postbox');
                var success = $(this).data('ajax-success');

                if ($(this).hasClass('signup-btn-finish') && !$(this).hasClass('signup-btn-delete')) {
                    var form = $(this).closest('form');
                    return _recurly.token(
                        form,
                        function (err, token) {
                            return postFields(
                                $postbox,
                                '/ajax/template',
                                { setting: 'signup', template: action },
                                function () {
                                    SEOToolSet.log.fn('recurlySuccessCallback');
                                    SEOToolSet.log.debug('success=' + success);
                                    if (success != '') {
                                        xhrLoadInside($postbox, success, true);
                                    }
                                }
                            );
                        }
                    );
                }

                if (!$(this).hasClass('signup-btn-next') && !$(this).hasClass('signup-btn-delete')) {
                    return xhrLoadInside($postbox, action);
                }

                return postFields(
                    $postbox,
                    '/ajax/template',
                    { setting: 'signup', template: action },
                    function () {
                        SEOToolSet.log.fn('successCallback');
                        SEOToolSet.log.debug('success=' + success);
                        if (success != '') {
                            xhrLoadInside($postbox, success, true);
                        }
                    }
                );
            }
        );

        /**
         * Handle ajax actions defined on postbox input submit elements.
         */
        $('.postbox').off('click.bcseo', 'input[type="submit"].savechanges').on(
            'click.bcseo',
            'input[type="submit"].savechanges',
            function (e) {
                var ajax = $(this).data('ajax');
                if (ajax != 'true' && ajax != true) {
                    return;
                }

                var action = $(this).data('ajax-action');
                if (action == '') {
                    SEOToolSet.log.debug('CLICK .postbox input[type="submit"] -- Missing data-ajax-action');
                    return;
                }

                SEOToolSet.log.debug('CLICK .postbox input[type="submit"] data-ajax-action=' + action);

                e.preventDefault();

                var $postbox = $(this).closest('.postbox');

                var success = $(this).data('ajax-success');

                saveChanges(
                    $postbox,
                    action,
                    function () {
                        if (success != '') {
                            xhrLoadInside($postbox, success, true);
                        }
                    }
                );
            }
        );


        /**
         * Change the hidden project fields as the user selects one.
         */
        $('.postbox.authentication').off('change.bcseo', '.project-list input[type=text]').on(
            'change.bcseo',
            '.project-list input[type=text]',
            function () {
                SEOToolSet.log.debug('CHANGE .project-list input[type=text]');

                var $postbox = $(this).closest('.postbox');
                //val = $(this).val().split('|');

                //$postbox.find('input[name="project[id]"][value="-1"]').attr("checked", "checked");
                //$postbox.find('input[name="project[id]"]').val(val[0]);
                //$postbox.find('input[name="project[name]"]').val(val[1]);
                //$postbox.find('input[name="project[url]"]').val(val[2]);
            }
        );

        /**
         * Pop up a window and get authorization code from Google.
         */
        $('.postbox.google').off('click.bcseo', 'button.google').on(
            'click.bcseo',
            'button.google',
            function (e) {
                SEOToolSet.log.debug('CLICK button.google');

                e.preventDefault();

                SEOToolSet.analytics.getAuthCode();

                return false;
            }
        );

        /**
         * Validate authorization code when pasted into input.
         */
        $('.postbox.google').off('paste.bcseo', '#auth-code').on(
            'paste.bcseo',
            '#auth-code',
            function (e) {
                SEOToolSet.log.debug('PASTE .postbox.google #auth-code');

                var code, token, jqxhr,
                $this = $(this);

                // Paste events aren't instantaneous, so we wait a tick.
                setTimeout(
                    function () {
                        SEOToolSet.analytics.getAnalyticsSites($this.val());
                    },
                    200
                );
            }
        );

        /**
         *
         */
        $('.postbox.google').off('click.bcseo', '.icon.edit').on(
            'click.bcseo',
            '.icon.edit',
            function () {
                SEOToolSet.log.debug('CLICK .postbox.google .icon.edit');

                SEOToolSet.analytics.getAnalyticsSites();
                $(this).off('click');
            }
        );

        /**
         * When the relevant "Save Changes" button is clicked, we send off the
         * OAuth data pulled from Google to the API.
         */
        $('.postbox.google').off('click.bcseo', 'input[type="submit"]').on(
            'click.bcseo',
            'input[type="submit"]',
            function (e) {
                SEOToolSet.log.debug('CLICK .postbox.google input[type="submit"]');

                var $this = $(this);
                //var analytics_input = $('input[name="google[analytics_id]"]').val()
                //var analytics = analytics_input.split(/\|/);
                var data = {
                    email: $('input[name="google[username]"]').val(),
                    accessToken: $('input[name="google[access_token]"]').val(),
                    refreshToken: $('input[name="google[refresh_token]"]').val(),
                    analytics_id: $('input[name="google[analytics_id]"]').val(),
                    project_id: $('input[name="project[id]"]').val()

                };

                // Don't let the click go through until we've sent off `data`.
                e.preventDefault();

                xhrLocal('POST', '/oauth/google', data)
                .done(
                    function (response) {
                        $this.unbind('click.bcseo').click(); // Force the click to go through now that we know it's fine.
                    }
                )
                .fail(
                    function () {
                        alert(wp.i18n.__('Terrible failure sending OAuth tokens to the API!', SEOTOOLSET_TEXTDOMAIN));
                    }
                );
            }
        );
    }

    // Bind events once dom is ready.
    //$(document).ready(function() {
        //bind();
    //});

    // Expose makeApiCall at namespace level.
    SEOToolSet.makeApiCall = SEOToolSet.makeApiCall || makeApiCall;

    // Expose public methods.
    return {
        bind: bind,
        elementAjaxLoad: elementAjaxLoad,
        xhrLoadInside: xhrLoadInside
    };
})(jQuery);

