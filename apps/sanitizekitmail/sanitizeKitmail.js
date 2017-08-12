
var sanitizeAccents = {
    '«': '&laquo;',	 '»': '&raquo;',  '·': '&middot;', '•': '&bull;',	'©': '&copy;',	 '€': '&euro;',	  'á': '&aacute;',
    'Á': '&Aacute;', 'â': '&acirc;',  'Â': '&Acirc;',  'à': '&agrave;', 'À': '&Agrave;', 'å': '&aring;',  'Å': '&Aring;',
    'ã': '&atilde;', 'Ã': '&Atilde;', 'ä': '&auml;',   'Ä': '&Auml;',   'æ': '&aelig;',  'Æ': '&AElig;',  'ç': '&ccedil;',
    'Ç': '&Ccedil;', 'é': '&eacute;', 'É': '&Eacute;', 'ê': '&ecirc;',  'Ê': '&Ecirc;',  'è': '&egrave;', 'È': '&Egrave;',
    'ë': '&euml;',   'Ë': '&Euml;',   'í': '&iacute;', 'Í': '&Iacute;', 'î': '&icirc;',  'Î': '&Icirc;',  'ì': '&igrave;',
    'Ì': '&Igrave;', 'ï': '&iuml;',   'Ï': '&Iuml;',   'ñ': '&ntilde;', 'Ñ': '&Ntilde;', 'ó': '&oacute;', 'Ó': '&Oacute;',
    'ô': '&ocirc;',  'Ô': '&Ocirc;',  'ò': '&ograve;', 'Ò': '&Ograve;', 'ø': '&oslash;', 'Ø': '&Oslash;', 'õ': '&otilde;',
    'Õ': '&Otilde;', 'ö': '&ouml;',   'Ö': '&Ouml;',   'œ': '&oelig;',  'Œ': '&OElig;',  'š': '&scaron;', 'Š': '&Scaron;',
    'ß': '&szlig;',  'ð': '&eth;',    'Ð': '&ETH;',    'þ': '&thorn;',  'Þ': '&THORN;',  'ú': '&uacute;', 'Ú': '&Uacute;',
    'û': '&ucirc;',  'Û': '&Ucirc;',  'ù': '&ugrave;', 'Ù': '&Ugrave;', 'ü': '&uuml;',   'Ü': '&Uuml;',   'ý': '&yacute;',
    'Ý': '&Yacute;', 'ÿ': '&yuml;',   'Ÿ': '&Yuml;',   '°': '&deg;',    '’': '\'',       '–': '&ndash;',  '—': '&mdash;',
    '…' : '&hellip;'
};

function refreshHLB()
{
   $('code').each(function(i, e) {
       hljs.highlightBlock(e);
   });
}

var textButtonMinified = "Copier la version minifiée";
var textButtonSanitized = "Copier";

var sanitizer = new Sanitizer(),
    rmUtils = new RMUtils(),
    renameAlt = document.querySelector('#renameAlt'),
    copyMinified = document.querySelector('#copy-minified .button-txt'),
    copyNoMinified = document.querySelector('#copy-nominified .button-txt');

$(document).ready(function(){

    $('#toSanitize').on('input', function () {
        var sanitizedBlock = $('#sanitized');
        if (sanitizedBlock.css('display') === "none")
            sanitizedBlock.css('display', 'block');

        renameAlt.value = '';
        copyMinified.innerText = textButtonMinified;
        copyNoMinified.innerText = textButtonSanitized;

        sanitizer.init(document.querySelector('#toSanitize'));
        sanitizer.getVariables(["%", "{{"], ["%", "}}"]);
        sanitizer.render();
    });

    $('#deleteHref').on('click', function() {
        copyMinified.innerText = textButtonMinified;
        copyNoMinified.innerText = textButtonSanitized;

        sanitizer.setAttribute('a', 'href', "#");
        sanitizer.render();
    });

    $("#renameAlt").on('input', function() {
        copyMinified.innerText = textButtonMinified;
        copyNoMinified.innerText = textButtonSanitized;

        sanitizer.setAttribute('img', 'alt', this.value);
        sanitizer.render();
    });

    $("#resetToSanitize").on('click', function() {
        var sanitizedBlock = $('#sanitized');
        if (sanitizedBlock.css('display') === "block")
            sanitizedBlock.css('display', 'none');

        $("#toSanitize").val('');
        sanitizer.init(null);
    });

    var client          = new ZeroClipboard( $('#copy-nominified') ),
        clientMinified  = new ZeroClipboard( $('#copy-minified')),
        nominified_txt  = $('#nominified').parent().find('.button-txt'),
        minified_txt    = $('#copy-minified').find('.button-txt');

    client.on("ready", function(event) {
        client.on("copy", function(event) {
            event.clipboardData.setData('text/plain', rmUtils.sanitizeLocalLinks('#nominified'));
            nominified_txt.html('Copied ! &#x2713;');
            minified_txt.html(textButtonMinified);
        });
    });

    clientMinified.on("ready", function(event) {
        clientMinified.on("copy", function(event) {
            var compressedKit = rmUtils.sanitizeLocalLinks('#nominified');
            compressedKit = compressedKit
                .replace(/\n|\t/g, ' ')
                .replace(/>\s+</g, '><')
                .trim()
                .replace(/\s{2,}/g, ' ')
                .replace(/<!--.*?-->/g, '');
            $('#minified').text(compressedKit);

            event.clipboardData.setData('text/plain', rmUtils.sanitizeLocalLinks('#minified'));
            minified_txt.html('Copied ! &#x2713;');
            nominified_txt.html(textButtonSanitized);
        });
    });
});