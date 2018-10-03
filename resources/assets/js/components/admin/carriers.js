var $stringPrice = 'prix',
    $stringWeight = 'poids';


var changeTypeShipping = function(val) {
    if(val == 'price'){
        jQuery(".carriers-plages").find(".type-shipping").empty().append($stringPrice);
    } else {
        jQuery(".carriers-plages").find(".type-shipping").empty().append($stringWeight);
    }
};
changeTypeShipping(jQuery('input[name=type_shipping]:checked').val())

jQuery(document).on("click", "input[name=type_shipping]", function(){
    changeTypeShipping(jQuery(this).val());
});

/* Ajouter une nouvelle tranche */
jQuery(".carriers-plages").on("click",".add-new-plage",function(e){
    var $this = jQuery(this).parent().find(".plage-list"),
        $countPlage = $this.find(".col-plage").length,
        $lastCol = $this.find(".col-plage").last(),
        $maxPrice = $lastCol.find('input.price-max').val(),
        $key = $lastCol.attr("data-key"),
        $clone = $lastCol.clone();

    // Modification de l'attribut clé
    $clone.attr("data-key", $countPlage);
    // Champs à zéro
    $clone.find("input").val("");
    // Modification de la clé des inputs
    $clone.find("input").each(function(){
        this.name = this.name.replace($key, $countPlage);
    });
    // Valeur par défaut du prix minimum
    $clone.find("input.price-min").val($maxPrice);
    // Ajouter bouton Supprimer si premier clique
    if($key == 0){
        $clone.append('<a href="#" class="delete-plage">Supprimer</a>');
    }

    $this.append($clone);
    e.preventDefault();
});

/* Supprimer une plage */
jQuery(".carriers-plages").on("click", ".delete-plage", function(e){
    var $this = jQuery(this).parent(),
        $key = $this.attr("data-key"),
        $parent = $this.parent();
    $parent.find(".col-plage").each(function(){
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

/* Cocher tout les pays */
jQuery(".carriers-plages").on("click",".check-all", function(){
    var $this = jQuery(this).parent().parent().parent();
    if(jQuery(this).is(":checked")){
        $this.find(".country-plage").find('input[type=checkbox]').attr("checked","checked");
    } else {
        //$this.find(".country-plage").find('input[type=checkbox]').removeAttr("checked");
    }
});

/* Changer la valeur pour tout les pays */
jQuery(".carriers-plages").on("keyup",".value-all", function(){
    var $this = jQuery(this).parent().parent().parent();
    $this.find(".country-plage").find('input[type=text]').val(jQuery(this).val());
});