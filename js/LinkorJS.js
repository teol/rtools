/**
 * LinkorJS - 1.0.0
 * @author Nicolas 'Neodern' Jamet
 * @about Javascript module to manager links from a kitmail.
 * @changelog Initial release.
 */

(function () {
    'use strict';
    var rmUtils = new RMUtils();

    /**
     *
     * @constructor
     */
    var Linkor = function () {
        this._links = null;
        this._elements = [];
        this._trackings = {
            'url': null,
            'variables': null
        };
        this._renderComponent = null;
        this._defaultURL = '';

        return this;
    };

    /**
     * @about links setter
     * @param links
     * @returns {Linkor}
     */
    Linkor.prototype.setLinks = function (content) {
        if (!content || typeof content !== 'object')
        {
            console.warn('setLinks need a JS-Object as parameter');
            return this;
        }
        if (content.value == 'undefined' || content.value == '')
        {
            console.warn('JS-Object content is empty or not found.');
            return this;
        }

        var tmp = document.createElement('div');
        tmp.innerHTML = content.value;
        var links = tmp.querySelectorAll('a');
        var metas = [];

        Array.prototype.forEach.call(links, function(elem) {
            var link = rmUtils.sanitizeLocalLinks(elem.href);
            if (!rmUtils.isVariable(elem.href))
            {
                link = decodeURI(link);
            }
            this._elements.push(elem);
            metas.push(link);
        }.bind(this));

        this._links = metas;
        console.log(this);
        return this;
    };

    /**
     * @about trackings setter
     * @param url
     * @param variables
     */
    Linkor.prototype.setTrackings = function (url, variables) {
        this._trackings.url = url && url !== '' ? url : '';
        this._trackings.variables = variables && variables !== '' ? variables : '';

        return this;
    };

    Linkor.prototype.setDefaultURL = function(e) {
        if (e.target.value !== 'undefined')
            this._defaultURL = e.target.value;
    };

    Linkor.prototype.setRenderComponent = function (renderComponent)
    {
        if (document.querySelector(renderComponent) == null)
        {
            console.warn('Render component missing. (' + renderComponent + ')');
            return;
        }
        this._renderComponent = document.querySelector(renderComponent);

        return this;
    };

    Linkor.prototype.getLinksComponents = function () {
        Array.prototype.forEach.call(this._links, function (elem, index) {
            var content = document.createElement('tbody');
            content.innerHTML = (
                '<tr>' +
                    '<td align="center">' + this._elements[index].innerHTML +
                        '<table>' +
                            '<tr>' +
                                '<td>' +
                                    '<label>url : <input class="inputLinks" attr-id="'+index+'" type="text" value="' + rmUtils.sanitizeLocalLinks(this._links[index]) + '" /></label>' +
                                '</td>' +
                            '</tr>' +
                        '</table>' +
                    '</td>' +
                '</tr>'
            );
            this._renderComponent.appendChild(content);
        }.bind(this));
    };

    Linkor.prototype.trackURL = function (cb) {
        if (this._trackings.url === '' && this._trackings.variables === '')
        {
            if (typeof cb === 'function')
                cb();
            return this;
        }
        var links = JSON.stringify(this._links);
        var tracking = this._trackings.url + '%url%' + this._trackings.variables;
        var data = {'links':links, 'tracking':tracking};

        $.ajax({
            type: 'POST',
            url: '../../scripts/linkor.php',
            data: { data:data },
            cache:false,

            success: function(response) {
                try {
                    var trackedLinks = JSON.parse(response);
                }catch(e){
                    console.warn(response);
                }

                console.log(trackedLinks)
                this._links = trackedLinks;
                if (typeof cb === 'function')
                {
                    cb();
                }
                return this;
            }.bind(this)
        });
    };


    window.Linkor = Linkor;
})();