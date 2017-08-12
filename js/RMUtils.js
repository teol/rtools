/*
 * RMUtils
 * @author: Nicolas (@Neodern) Jamet
 */

(function() {
    /**
     *
     * @constructor
     */
    var RMUtils = function() {
        this._string = null;
        this._appLocation = null;
    };

    /**
     *
     * @param string
     * @returns {boolean}
     */
    RMUtils.prototype.isVariable = function(string) {
        return !!string.match(/%\S+%/g) || !!string.match(/\{{2}\S+\}{2}/g);
    };

    /**
     *
     * @param string
     * @returns {XML|*|string|void}
     */
    RMUtils.prototype.escapeRegExp = function(string) {
        return string.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    };

    /**
     *
     * @param encode
     * @returns {string}
     */
    RMUtils.prototype.getAppLocation = function(encode) {
        var appLocation = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
        return !!encode ? encodeURIComponent(appLocation) : appLocation;
    };

    /**
     *
     * @param kitmail
     * @returns {string}
     */
    RMUtils.prototype.kitmailDecode = function(kitmail) {
        var toReplace = {
            "%3A":":",
            "%2F":"/",
            "%3F":"?",
            "%7B":"{",
            "%7D":"}"
        };
        for (var val in toReplace)
            kitmail = kitmail.replace(new RegExp(val, "g"), toReplace[val]);
        return kitmail;
    };

    /**
     *
     * @param id
     * @returns {string}
     */
    RMUtils.prototype.sanitizeLocalLinks = function(id) {
        var selector = null, string;

        if (id.indexOf('#') === 0)
             selector = document.querySelector(id);
        if (selector === null || typeof selector == 'undefined')
        {
            string = new String(id);
        }
        else
        {
            string = new String(document.querySelector(id).innerText);
        }
        string = this.kitmailDecode(string);
        return string
            .replace(new RegExp(this.escapeRegExp(window.location.href), "g"), '')
            .replace(new RegExp(this.escapeRegExp(this.getAppLocation()), "g"), '')
            .replace(new RegExp(this.escapeRegExp(this.getAppLocation(true)), "g"), '')
            .replace(new RegExp(this.escapeRegExp('linkor.php'), "g"), '')
            .replace(/&amp;/g, '&');
    };

    window.RMUtils = RMUtils;
})();