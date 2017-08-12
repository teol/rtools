
/**
 * Sexy progress bar
 * @param e
 */
function progressHandlingFunction(e)
{
    if (e.lengthComputable)
    {
        var percent = (e.loaded / e.total) * 100;
        $('.meter').css('width', percent + '%');
    }
}

$(document).ready(function () {
    $("#step-one a").click(function(e) {
        e.stopPropagation;

        if ($(this).attr('disabled'))
            return;

        $("#results").show();
        $("#upload").show();
        $('#informationsBlock').show();
        var type = $(this).attr("type");
        if (type === "convert")
        {
            $('#concateBlock').show();
            $('#optionsBlock').show();
            $('#outputBlock').show();
            $('#keyBlock').show();
            $('#autoCompleteBlock').show();
            $('#partnerFileBlock').hide()
            $('#sendForm').attr('name', 'envoyer_ref').attr('value', 'Convertir');
        }
        else if (type === "nows")
        {
            $('#concateBlock').show();
            $('#optionsBlock').show();
            $('#outputBlock').show();
            $('#keyBlock').show()
            $('#autoCompleteBlock').show();
            $('#partnerFileBlock').show();
            $('#sendForm').attr('name', 'no_webservice').attr('value', 'Comparer');
        }
        else if (type === "extract")
        {
            $('#concateBlock').hide();
            $('#outputBlock').hide();
            $('#optionsBlock').hide();
            $('#autoCompleteBlock').hide();
            $('#keyBlock').show();
            $('#partnerFileBlock').hide();
            $('#sendForm').attr('name', 'extraire_md5').attr('value', 'Re-extraire les md5');
        }
        else if (type === "compare")
        {
            $('#concateBlock').hide();
            $('#optionsBlock').hide();
            $('#outputBlock').hide();
            $('#keyBlock').hide();
            $('#autoCompleteBlock').hide();
            $('#partnerFileBlock').hide();
            $('#sendForm').attr('name', 'comparaison').attr('value', 'Comparer');
        }
    });


    $('#sendForm').click(function (e) {
        e.preventDefault();

        var formData = new FormData($('form')[0]);
        formData.append(this.name, true);

        $.ajax({
            url: '/scripts/scripts.php',
            type: 'post',
            data: formData,

            cache: false,
            contentType: false,
            processData: false,

            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
                }
                return myXhr;
            },

            beforeSend: function () {
                $('#answer').html('in progress');
                $('#sendForm').prop('disabled', 'disabled');
            },

            error: function () {
                $('#answer').html("Erreur lors de l'envoi AJAX du fichier.");
            },

            success: function (answer) {
                $('#answer').html(answer);
                $('#sendForm').removeAttr('disabled');
            }
        });
    });

    /**
     * Switch class first step.
     */
    $("#step-one a").click(function () {
        if ($(this).attr('disabled'))
            return;
        $("#step-one a").each(function() {
           $(this).addClass("secondary");
        });
        $(this).removeClass("secondary");
    });

    /**
     * Display / hide concat options.
     * @param e
     */
    $("#concate").click(function() {
        var checked = $(this).is(':checked');
        if (checked)
            $('#concatBlock').show();
        else
            $('#concatBlock').hide();
    });


    //Auto Completion pour les bases déjà enregistrées en bdd.
    $(".autoComplete").click(function (e) {
        e.preventDefault();
        var splited = this.id.split('_');

        $('#base').val(splited[0]);
        $('#table').val(splited[1]);
    });

    $('.completeBase').on('click', function(e) {
        e.preventDefault();
        var base = $(this).text();

        $('#base').val(base);
    });


    var getCurrentMonth = function () {
        var months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
        var j = new Date().getMonth();
        return months[j];
    };

    var currentMonth = getCurrentMonth();

    //So sexy thx @OtaK_
    var completionData = {
        edf: {
            table: 'edf',
            uol: 'lowercase',
            isupper: true,
            concate: false,
            concatField1 : null,
            concatField2 : null,
            field2IsCp: false,
            key: "DéduPlicationENr" + currentMonth + new Date().getFullYear() + "@public-Idees.com"
        },
        meetic: {
            table: 'meetic',
            uol: 'lowercase',
            isupper: true,
            concate: false,
            concatField1 : null,
            concatField2 : null,
            field2IsCp: false,
            key: "DédupLicationMe" + currentMonth + "@public-ideeS.com"
        },
        fortuneo: {
            table: 'fortuneo',
            uol: 'lowercase',
            isupper: true,
            concate: false,
            concatField1 : null,
            concatField2 : null,
            field2IsCp: false,
            key: "DédupLicationFortuneo" + currentMonth + "@public-ideeS.com"
        },
        savoirmaigrir: {
            table: 'savoirmaigrir',
            uol: 'lowercase',
            isupper: true,
            concate: false,
            concatField1 : null,
            concatField2 : null,
            field2IsCp: false,
            key: "DédupLicationSM" + currentMonth + "@public-ideeS.com"
        },
        hellobank: {
            table: 'hellobank',
            uol: 'uppercase',
            isupper: false,
            concate: true,
            concatField1: 'nom',
            concatField2: 'code_postal',
            field2IsCp: true,
            key: "PuBliCIDéesHb 92300"
        },
        allianz: {
            table: 'allianz',
            uol: 'uppercase',
            isupper: true,
            concate: false,
            concatField1 : null,
            concatField2 : null,
            field2IsCp: false,
            key: "DéduPlicationENr" + currentMonth + new Date().getFullYear() + "@public-Idees.com"
        },
        securitas: {
            table: 'securitas',
            uol: 'lowercase',
            isupper: true,
            concate: false,
            concatField1 : null,
            concatField2 : null,
            field2IsCp: false,
            key: "EMAIL"
        },
        sephora : {
            table: 'sephora',
            uol: 'lowercase',
            isupper: false,
            concate: false,
            concatField1 : null,
            concatField2 : null,
            field2IsCp: false,
            key: null
        }
    };

    //Auto Complétion pour les bases réccurentes.
    var $table = $("#table"),
        $uol = $("#uol"),
        $isupper = $("#isUpper"),
        $concate = $("#concate"),
        $concatField1 = $("#concat_field1"),
        $concatField2 = $("#concat_field2"),
        $field2IsCp = $("#field2IsCP"),
        $key = $("#key");

    // wow, so such magic
    $('.completion-button[data-dedup-completion]').click(function (e) {
        e.preventDefault();
        var type = $(this).attr('data-dedup-completion');
        var currentCompletionData = completionData[type];
        if (typeof currentCompletionData === 'undefined')
            return alert('Completion type not found!');

        if (currentCompletionData.concate)
            $('#concatBlock').css('display', 'block');
        else
            $('#concatBlock').css('display', 'none');

        $table.val(currentCompletionData.table);
        $uol.val(currentCompletionData.uol);
        $isupper.prop('checked', currentCompletionData.isupper);
        $concate.prop('checked', currentCompletionData.concate);
        $concatField1.val(currentCompletionData.concatField1);
        $concatField2.val(currentCompletionData.concatField2);
        $field2IsCp.prop('checked', currentCompletionData.field2IsCp);
        $key.val(currentCompletionData.key);
        return true;
    });

    var emailsCommerciaux = {
        'michaelH'  : 'michael.hanen@rentabiliweb.com',
        'yakare'    : 'yakare.diarra@rentabiliweb.com',
        'michaelD'  : 'michael.drouhin@rentabiliweb.com',
        'veronique' : 'veronique.marin@rentabiliweb.com'
    };

    $('#base').on('change', function() {
        var base = $("#base option:selected").val();
        var emailSelector = $("#receiver");
        switch (base)
        {
            case "consoclient":
                emailSelector.val(emailsCommerciaux.michaelH);
                break;
            case "onlinevoyance":
                emailSelector.val(emailsCommerciaux.yakare);
                break;
            case "prpsycho":
                emailSelector.val(emailsCommerciaux.yakare);
                break;
            case "prformation":
                emailSelector.val(emailsCommerciaux.michaelH);
                break;
            case "r1e":
                emailSelector.val(emailsCommerciaux.michaelH);
                break;
            case "skyrock":
                emailSelector.val(emailsCommerciaux.veronique);
                break;
            case "toox":
                emailSelector.val(emailsCommerciaux.veronique);
                break;
            case "tp":
                emailSelector.val(emailsCommerciaux.michaelD);
                break;
        }
    });
});