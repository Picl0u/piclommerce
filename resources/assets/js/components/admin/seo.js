if(jQuery(".seo-notifications").length > 0){
    var $keyword = jQuery("#form-seo-keywords"),
        $title = jQuery("#form-seo-title"),
        $description = jQuery("#form-seo-description"),
        $name = jQuery("input[name=name]"),
        $slug = jQuery("input[name=slug]"),
        $content = jQuery("textarea[name=description]"),
        $previewTitle = jQuery(".seo-preview .seo-title span"),
        $previewSlug = jQuery(".seo-preview .seo-url span"),
        $previewDescription = jQuery(".seo-preview .seo-description");

    $name.on("change",function(e){
       console.log("Titre ! :"+jQuery(this).val());
    });

    var slugify = function (text)
    {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    // Test si le mot clé est dans la méta Title
    var testKeywordsInTitle = function()
    {
        var $keywordWord = $keyword.val().toLowerCase();
        var $titleWord = $title.val().toLowerCase();
        if($keywordWord && $titleWord) {
            if($titleWord.indexOf($keywordWord) >= 0){
                jQuery('li.seo-ok-title').show();
                jQuery('li.seo-keywords-no-title').hide();
            } else {
                jQuery('li.seo-ok-title').hide();
                jQuery('li.seo-keywords-no-title').show();
                jQuery('li.seo-keywords-no-title span.keyword').empty().append($keyword.val());
            }
        } else{
            if($keywordWord && !$titleWord){
                jQuery('li.seo-ok-title').hide();
                jQuery('li.seo-keywords-no-title').show();
                jQuery('li.seo-keywords-no-title span.keyword').empty().append($keyword.val());
            }
        }
    };

    // Test si le mot clé est dans le slug de page
    var testKeywordsInUrl = function()
    {
        var $keywordWord = slugify($keyword.val());
        var $slugWord = slugify($slug.val());
        if($slugWord && $keywordWord) {
            if($slugWord.indexOf($keywordWord) >= 0){
                jQuery('li.seo-ok-url').show();
                jQuery('li.seo-keywords-no-url').hide();
            } else {
                jQuery('li.seo-ok-url').hide();
                jQuery('li.seo-keywords-no-url').show();
                jQuery('li.seo-keywords-no-url span.keyword').empty().append($keyword.val());
            }
        } else{
            if($keywordWord && !$slugWord){
                jQuery('li.seo-ok-url').hide();
                jQuery('li.seo-keywords-no-url').show();
                jQuery('li.seo-keywords-no-url span.keyword').empty().append($keyword.val());
            }
        }
    };

    // Test de la méta description
    var testDescription = function()
    {
        if($description.val()) {
            $previewDescription.empty().append($description.val());
            jQuery('li.seo-ok-description').show();
            jQuery('li.seo-keywords-no-description').hide();
        } else{
            $previewDescription.empty().append($previewDescription.attr("data-text"));
            jQuery('li.seo-ok-description').hide();
            jQuery('li.seo-keywords-no-description').show();
        }
    };

    // Test l'existance du mot clé principal
    var testKeywords = function()
    {
        if(!$keyword.val()){
            jQuery('.problem li').hide();
            jQuery('.good li').hide();
            jQuery('li.seo-keywords-no-keywords').show();
            //jQuery(".seo-notifications .good").hide();
        } else {
            jQuery('.problem li').hide();
            jQuery('li.seo-keywords-no-keywords').hide();
            testKeywordsInTitle();
            testKeywordsInUrl();
            testDescription();
        }
    };

    // Lancement des fonctions
    testKeywords();
    $title.on("keyup",function(e){
        $previewTitle.empty().append($title.val());
        testKeywordsInTitle();
    });
    $title.on("change",function(e){
        $previewTitle.empty().append($title.val());
        testKeywordsInTitle();
    });
    if($title.val()){
        $previewTitle.empty().append($title.val());
    }

    $slug.on("keyup",function(e){
        $previewSlug.empty().append(slugify($slug.val()));
        testKeywordsInUrl();
    });
    $slug.on("change",function(e){
        $previewSlug.empty().append(slugify($slug.val()));
        testKeywordsInUrl();
    });
    if($slug.val()){
        $previewSlug.empty().append(slugify($slug.val()));
    }

    $description.on("keyup",function(e){
        $previewDescription.empty().append($description.val());
        testDescription();
    });
    $description.on("change",function(e){
        testDescription();
    });
    if($description.val()){
        $previewDescription.empty().append($description.val());
    }

    $keyword.on("change",function(e){
        testKeywords();
    });

}