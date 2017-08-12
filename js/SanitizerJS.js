/*
 * SanitizerJS - 1.0.0
 * @author: Nicolas (@Neodern) Jamet
 * @about: JavaScript Class to sanitize kitmail.
 * @todo:
    * remove jQuery.
 * @changelog
  *    1.0.0 : Initial release
 */

(function() {
    var rmUtils = new RMUtils();


    /**
     *
     * @constructor
     */
    var Sanitizer = function () {
        this._content = null;
        this._specificsTags = ['title', 'script', 'meta', 'link'];
        this._renderElement = 'nominified';
        this._variables = [];
        this._renderVariables = 'variablesBlock';
    };

    /**
     *
     * @param renderElement
     */
    Sanitizer.prototype.setRenderElement = function(renderElement) {
        if (!!document.querySelector('#' + renderElement))
            this._renderElement = renderElement;
    }

    /**
     *
     * @param content
     */
    Sanitizer.prototype.init = function (element, tags) {
        var _tags = (typeof tags === 'undefined' || tags === null) ? this._specificsTags : tags;

        this._content = $('<div />').html(element.value || element.innerText);
        this.removeSpecificsTags(_tags);
        this.sanitizeTagA();
        this.sanitizeTagIMG();
        this.sanitizeAccents();
        this.trim();
    };

    /**
     *
     * @param tags
     */
    Sanitizer.prototype.removeSpecificsTags = function (tags) {
        var _tags = (tags === 'undefined' || tags === null) ? this._specificsTags : tags;
        _tags.forEach(function (tag) {
            this._content.find(tag).remove();
        }.bind(this));
    };

    /**
     *
     */
    Sanitizer.prototype.sanitizeTagA = function () {
        this._content.find('a').each(function () {
            if (this.style.textDecoration === "")
                this.style.textDecoration = "none";
            this.href = this.href.trim();
        });
    };

    /**
     *
     */
    Sanitizer.prototype.sanitizeTagIMG = function () {
        this._content.find('img').each(function() {
            this.border = '0';
            this.style.display = "block";
            this.src = this.src.trim();
        });
    };

    /**
     *
     */
    Sanitizer.prototype.sanitizeAccents = function () {
        var content = this._content.html();
        content = content.replace(/[^\w ]/g, function (char) {
            return sanitizeAccents[char] || char;
        });
        this._content.val(content);
    };

    /**
     *
     * @param tagname
     * @param attribute
     * @param value
     */
    Sanitizer.prototype.setAttribute = function (tagname, attribute, value) {
        var content = this._content.html();
        this._content.find(tagname).attr(attribute, value);
        this._content.val(content);
    };

    /**
     *
     * @param element
     */
    Sanitizer.prototype.setRenderVariables = function (element) {
        this._renderVariables = document.getElementById(element);
        if (typeof this._renderVariables == "undefined") {
            console.warn('renderVariabler: Render element undefined (' + element + ')');
            return;
        }
    };

    /**
     *
     * @param startVar
     * @param endVar
     * @returns {Array}
     */
    Sanitizer.prototype.getVariables = function (startVar, endVar) {
        var content = rmUtils.kitmailDecode(this._content.html()),
            variables = [];
        this._variables = [];

        if (startVar.length !== endVar.length)
        {
            console.warn('getVariables: params length differ.');
            return;
        }
        for (var i in startVar)
        {
            var regExp = new RegExp(startVar[i] + "\\S+" + endVar[i], "g");
            var tmp = content.match(regExp);
            if (!!tmp)
                variables = variables.concat(tmp);
        }
        // Delete duplicate variables.
        variables = variables.filter(function(elem, index, self) {
            return index == self.indexOf(elem);
        })
        this._variables = variables;
        return variables;
    };

    /**
     *
     */
    Sanitizer.prototype.trim = function() {
        var content = this._content.val()
            .split('\n')
            .filter(function(line) { return line.trim() !== ""; })
            .join('\n');
        this._content.val(content);
    };

    /**
     *
     */
    Sanitizer.prototype.renderVariables = function() {
        var renderContainer = document.getElementById(this._renderVariables);
        if (!!document.getElementById('variables'))
            document.getElementById('variables').remove();
        if (this._variables.length === 0)
        {
            renderContainer.style.display = "none";
            return;
        }
        renderContainer.style.display = "block";
        var container = document.createElement('div');
        for (var i in this._variables)
        {
            var vcontainer = document.createElement('span');
            vcontainer.className = "alert-box info";
            vcontainer.innerHTML = "<strong>" + rmUtils.sanitizeLocalLinks(this._variables[i]) + "</strong><br />";
            container.appendChild(vcontainer);
        }
        container.id = 'variables';
        renderContainer.appendChild(container);
    };

    /**
     *
     * @param sanitizeLinks
     */
    Sanitizer.prototype.render = function (sanitizeLinks) {
        var string = this._content.val();
        if (!sanitizeLinks)
        {
            string = rmUtils.sanitizeLocalLinks(this._content.val());
        }
        document.getElementById(this._renderElement).innerText = string;
        refreshHLB();
        this.renderVariables();
    };

    window.Sanitizer = Sanitizer;
})();