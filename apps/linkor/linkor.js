/**
 * Created by njamet on 28/11/2014.
 */

var rmUtils = new RMUtils();

$(document).ready(function() {

    var copyButton = $('#copy');
    var textButtonLinkor = "Copier";
    var content;
    var defaultUrl = document.querySelector('#defaultUrl');
    var rows = $('code');
    var links;
    var linkor = new Linkor();

    /**
     *
     */
    function refreshHLB()
    {
        $('render').each(function(i, e) {
            hljs.highlightBlock(e);
        });
    }

    /**
     *
     * @param container
     * @param attribute
     * @returns {*}
     */
    function removeTrackingURL(container, attribute)
    {
        container.each(function (index) {
            var value = this.getAttribute(attribute);
            var substringStart = _.max([ value.lastIndexOf('http'), value.lastIndexOf('https')]);
            if (substringStart === -1)
            {
                return this;
            }
            var sublink = value.substring(substringStart);
            if (sublink.length === value.length)
            {
                return this;
            }
            if (sublink.match(/%[0-9A-F]{2}/i)) //isEncoded ?
            {
                sublink = decodeURIComponent(sublink);
            }
            this.setAttribute(attribute, sublink);
            return this;
        });
        return container;
    }

    /**
     *
     * @param container
     * @param attribute
     * @returns {*}
     */
    function    removeTrackingVAR(container, attribute)
    {
        container.each(function (index) {
            var value = this.getAttribute(attribute);
            var substringStart = _.max([ value.lastIndexOf('http'), value.lastIndexOf('https') ]);
            var isEncoded = false;

            if (substringStart === -1)
            {
                return this;
            }
            var base = value.substring(0, substringStart);
            var sublink = value.substring(substringStart);

            if (sublink.match(/%[0-9A-F]{2}/i)) //isEncoded ?
            {
                isEncoded = true;
                sublink = decodeURIComponent(sublink);
            }
            var link = new Url(sublink);
            link.query = '';
            if (isEncoded)
            {
                link = base + encodeURIComponent(link.toString());
            }
            else
                link = base + link.toString();
            this.setAttribute(attribute, link);
            return this;
        });
        return container;
    }

    /**
     *
     * @param func
     */
    function    removeFunction(func)
    {
        if (!(func && typeof func === "function"))
            return;

        links = func(links, 'href');
        func($('.inputLinks'), 'value');
        render();
    }

    /**
     *
     */
    var render = function () {
        content = document.createElement('div');
        content.innerHTML = rmUtils.sanitizeLocalLinks($('#kitmail').val());

        $(content).find('a').each(function(index) {
            this.href = rmUtils.sanitizeLocalLinks(linkor._links[index]);
            return this;
        });
        document.querySelector('render').innerText = content.innerHTML;
        copyButton.text(textButtonLinkor);
        refreshHLB();
    }

    var tracking_callback = function () {
        linkor
            .setTrackings(
                document.getElementById('tracking_url').value,
                document.getElementById('tracking_variables').value
            )
            .trackURL();
        render();
        console.log(linkor);
    }

    $('#tracking_variables').on('blur', tracking_callback);
    $('#tracking_url').on('blur', tracking_callback);
    $('#defaultUrl').on('blur', function(e) {linkor.setDefaultURL(e)});
    $('#delete_trackingURL').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Êtes-vous sur ?'))
            return false;

        removeFunction(removeTrackingURL);
        render();
        refreshHLB();

    });

    $('#delete_trackingVAR').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Êtes-vous sur ?'))
            return false;

        removeFunction(removeTrackingVAR);
        render();
        refreshHLB();
    });

    $('#delete_trackings').on('click', function(e) {
        e.preventDefault()
        if (!confirm('Êtes-vous sur ?'))
            return false;

        removeFunction(removeTrackingURL);
        removeFunction(removeTrackingVAR);
        render();
        refreshHLB();
    });

    var resetContainer = function(container, attribute) {
        container.each(function (index) {
            this.setAttribute(attribute, '#');
            return this;
        });
        return container;
    };

    $('#deleteHref').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Êtes-vous sur ?'))
            return false;

        links = resetContainer(links, 'href');
        resetContainer($('.inputLinks'), 'value');

        render();
        refreshHLB();
    });


    /**
     *
     */
    $('#kitmail').on('input', function () {
        var togglableBlock = $('#togglable');
        if (togglableBlock.css('display') === "none")
        {
            togglableBlock.css('display', 'block');
        }

        rows.html("<table id='container' />");

        linkor
            .setLinks(document.querySelector('#kitmail'))
            .setTrackings(
                    document.getElementById('tracking_url').value,
                    document.getElementById('tracking_variables').value
            )
            .setRenderComponent('code table')
            .trackURL(function () {linkor.getLinksComponents()});

        $('#anchors').css('display', 'block');
        copyButton.css('display', 'block');
        render();
    });

    /**
     * Reset de la page.
     */
    $("#reset").on('click', function() {
        var togglableBlock = $('#togglable');
        if (togglableBlock.css('display') === "block")
            togglableBlock.css('display', 'none');

        $("#kitmail").val('');
        $('render').val('');
        $('#anchors').css('display', 'none');
        copyButton.css('display', 'none');
    });

    /**
     * Edition d'un lien.
     *
     */
    $('#linkored').on('blur', '.inputLinks', function() {
        linkor._links[this.getAttribute('attr-id')] = this.value;
        linkor.trackURL();
        render();
    });

    /**
     *
     * @type {ZeroClipboard}
     */

    var client = new ZeroClipboard( copyButton , {forceEnhancedClipboard: true, debug:true});
    client.on("ready", function() {
        client.on("copy", function(event) {
            event.clipboardData.setData('text/plain', rmUtils.sanitizeLocalLinks(content.innerHTML));
            copyButton.html('Copied ! &#x2713;');
        });
    });
});

