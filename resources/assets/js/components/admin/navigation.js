if (jQuery(".main-navigation").length > 0) {
    jQuery(".main-navigation").on("click", "a", function(e){
        var $this = jQuery(this);
        if($this.parent().find("ul").length > 0){
            if($this.parent().find("ul").is(":visible")) {
                $this.parent().find("ul").slideUp(300);
                $this.find("span.caret").removeClass("is-down").addClass("is-left");
            } else {
                $this.parent().find("ul").slideDown(300);
                $this.find("span.caret").removeClass("is-left").addClass("is-down");
            }
            e.preventDefault();
        }
    });
}