// Affichage date/datetime picker
import flatpickr from "../../../vendors/flatpickr/dist/flatpickr.min";
import { French } from "../../../vendors/flatpickr/dist/l10n/fr";
if(jQuery(".date-picker").length > 0) {
    flatpickr('.date-picker', {
        locale: French,
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });
}
if(jQuery(".datetime-picker").length > 0) {
    flatpickr('.datetime-picker', {
        locale: French,
        enableTime :true,
        time_24hr: true,
        altInput : true,
        altFormat : 'j F Y, H:i',
        dateFormat : 'Y-m-d H:i:S'
    });
}
// Calcul prix produits
if(jQuery("input[name=price_ht]").length > 0 && jQuery("input[name=price_ttc]").length > 0 && jQuery("select[name=vat_id]").length > 0) {
    var ht_val = parseFloat(jQuery("input[name=price_ht]").val());
    var ttc_val = parseFloat(jQuery("input[name=price_ttc]").val());
    var percent_val = jQuery("select[name=vat_id]").val();

    /* Prix HT */
    jQuery("input[name=price_ht]").on("change keyup paste", function(){
        ht_val = parseFloat(jQuery("input[name=price_ht]").val());
        ttc_val = parseFloat(jQuery("input[name=price_ttc]").val());
        percent_val = jQuery("select[name=vat_id]").val();

        var taux = 0;
        jQuery('select[name=vat_id] option').each(function(){
            if(jQuery(this).attr("value") == percent_val){
                taux = jQuery(this).attr("data-taux");
            }
        });

        var taxe = ht_val*(1+(taux/100));
        jQuery("input[name=price_ttc]").val(taxe.toFixed(2));
    });

    /* Prix TTC */
    jQuery("input[name=price_ttc]").on("change keyup paste", function(){
        ht_val = parseFloat(jQuery("input[name=price_ht]").val());
        ttc_val = parseFloat(jQuery("input[name=price_ttc]").val());
        percent_val = jQuery("select[name=vat_id]").val();

        var taux = 0;
        jQuery('select[name=vat_id] option').each(function(){
            if(jQuery(this).attr("value") == percent_val){
                taux = jQuery(this).attr("data-taux");
            }
        });
        var taxe = ttc_val/(1+(taux/100));
        jQuery("input[name=price_ht]").val(taxe.toFixed(2));

    });
}

/* Gestion des déclinaisons */
if(jQuery("#attributes").length > 0) {
    var $div = jQuery("#attributes"),
        $modal = jQuery('[data-remodal-id=modal-attributes]'),
        $modalContainer = $modal.find(".modal-container"),
        $modalInst = $modal.remodal(),
        $table = $div.find('.table-declinaisons'),
        $line = "";

    /* Afficher le formulaire d'ajout */
    $div.on('click',"a.add-new-declinaison", function(e){
        $line = "";
        axios({
            method: 'get',
            url: jQuery(this).attr('href')
        })
            .then(function (response) {
                $modalContainer.empty().append(response.data);
                $modalInst.open();
            })
            .catch(function (error) {
                $modalInst.close();
                var errorMsg = "";
                if (error.response) {
                    errorMsg = error.response.data;
                } else if (error.request) {
                    errorMsg  = error.request;
                } else {
                    errorMsg = error.message;
                }
                $.toast({
                    heading: 'Erreur !',
                    text:  'Une erreur s\'est produite...' + errorMsg,
                    showHideTransition: 'slide',
                    icon: 'error',
                    position:'top-right'
                });
            });
        e.preventDefault();
        e.stopPropagation();
        return $line;
    });

    /* Ajouter un attribut */
    $modal.on("click", '.attribute .add-new-attribute', function(e){

        var $this = jQuery(this),
            $parent = jQuery(this).parent().parent(),
            $container = $parent.parent(),
            $key = $parent.attr("data-key"),
            $countAttribute = $modal.find('.attribute').length,
            $clone = $parent.clone();

        // Modification de l'attribut clé
        $clone.attr("data-key", $countAttribute);
        // Champs à zéro
        $clone.find("input").val("");
        // Modification de la clé des inputs
        $clone.find("input").each(function(){
            this.name = this.name.replace($key, $countAttribute);
        });
        // Ajouter bouton Supprimer si premier clique
        if($key == 0){
            $clone.find(".attribute-actions").append(
                '<span class="delete-attribute">\n' +
                '<i class="fa fa-trash"></i>\n' +
                '</span>'
            );
        }

        $container.append($clone);

        e.preventDefault();
    });

    /* Supprimer un attribut */
    $modal.on("click", ".delete-attribute", function(e){
        var $this = jQuery(this).parent().parent(),
            $key = $this.attr("data-key"),
            $parent = $this.parent();
        $parent.find(".attribute").each(function(){
            if(jQuery(this).attr("data-key") > $key){
                var $currentKey = jQuery(this).attr("data-key"),
                    $newKey = parseInt($currentKey) -1;
                jQuery(this).attr("data-key", $newKey);
                jQuery(this).find("input").each(function(){
                    this.name = this.name.replace($currentKey, $newKey);
                });
            }
        });
        $this.empty().remove();
        e.preventDefault();
    });

    $modal.on("click", ".is-image", function(e){
        var $this = jQuery(this);
        if($this.find("img").attr("class") == "is-selected") {
            $this.find("img").removeClass("is-selected");
            $this.find("input").prop( "checked", false );
        } else {
            $this.find("img").addClass("is-selected");
            $this.find("input").prop( "checked", true );
        }
        e.preventDefault();
    });

    /* Sauvegarde du formulaire */
    $modal.on("click", ".remodal-confirm", function(e){

        var $form = $modal.find("form"),
            $button = jQuery(this),
            $label = $button.text(),
            $uri = $form.attr("action"),
            $postData = $form.serialize();


        $button.empty().append('...');
        axios({
            method: 'post',
            url: $uri,
            data: $postData
        })
            .then(function (response) {
                $button.empty().append($label);
                $modalInst.close();
                if($line != ""){
                    console.log($line);
                    $line.empty().append(response.data);
                } else {
                    $table.find('tbody').append(response.data);
                }
                toastr.success("La déclinaison a bien été sauvegardée");


            })
            .catch(function (error) {
                $button.empty().append($label);
                var errorMsg = "";
                if (error.response) {
                    errorMsg = error.response.data;
                } else if (error.request) {
                    errorMsg  = error.request;
                } else {
                    errorMsg = error.message;
                }
                toastr.error(errorMsg);
            });


        e.preventDefault();
        e.stopPropagation();
    });

    /* Modification d'une déclinaison */
    $div.on('click',"a.edit-declinaison", function(e){

        $line = jQuery(this).parent().parent();

        axios({
            method: 'get',
            url: jQuery(this).attr('href')
        })
            .then(function (response) {
                $modalContainer.empty().append(response.data);
                $modalInst.open();
            })
            .catch(function (error) {
                $modalInst.close();
                var errorMsg = "";
                if (error.response) {
                    errorMsg = error.response.data;
                } else if (error.request) {
                    errorMsg  = error.request;
                } else {
                    errorMsg = error.message;
                }
                toastr.error(errorMsg);
            });
        e.preventDefault();
        e.stopPropagation();

        return $line;
    });

    /* Supprimer une declinaison */
    $div.on("click", "a.delete-declinaison", function(e){

        $line = jQuery(this).parent().parent();
        var $link = jQuery(this).attr('href');
        swal({
            title: "Attention",
            text: "Voulez-vous supprimer cet élément ?",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Annuler",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Confirmer",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                }
            },
            dangerMode: true,
        }).then((willDelete) => {
            axios({
                      method: 'get',
                      url: $link
                  })
            .then(function (response) {
                $line.empty().remove()
            })
            .catch(function (error) {
                var errorMsg = "";
                if (error.response) {
                    errorMsg = error.response.data;
                } else if (error.request) {
                    errorMsg  = error.request;
                } else {
                    errorMsg = error.message;
                }
                toastr.error(errorMsg);
            });
    });

        e.preventDefault();
        e.stopPropagation();

        return $line;
    });

}