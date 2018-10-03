import "./bootstrap";
import flatpickr from "../vendors/flatpickr/dist/flatpickr.min";
import { French } from "../vendors/flatpickr/dist/l10n/fr";
jQuery(document).ready(function(){
    // Navigation
    require("./components/admin/navigation");
    require('../vendors/remodal/remodal');
    // Products
    require("./components/admin/products");

    // DataTable
    require('../vendors/datatable/JSZip-2.5.0/jszip.min');
    require('../vendors/datatable/pdfmake-0.1.36/pdfmake.min');
    require('../vendors/datatable/pdfmake-0.1.36/vfs_fonts');
    var dataTable = require('../vendors/datatable/DataTables-1.10.18/js/jquery.dataTables.min');
    require('../vendors/datatable/Responsive-2.2.2/js/dataTables.responsive.min');
    require('../vendors/datatable/Scroller-1.5.0/js/dataTables.scroller.min');
    $.fn.DataTable = dataTable;

    // Submit Form
    jQuery("a.submit-form").on("click",function(e){
        jQuery(this).parent().parent().parent().find(".admin-form").submit();
        e.preventDefault();
    })

    // CKEditor
    if(jQuery(".html-editor")){
        window.CKEDITOR_BASEPATH = '/vendors/ckeditor/';
        require("../vendors/ckeditor/ckeditor");
        jQuery('.html-editor').each(function(e) {
            var config = {
                extraPlugins: 'colorbutton,font',
                filebrowserBrowseUrl : window.CKEDITOR_BASEPATH + 'kcfinder/browse.php?opener=ckeditor&type=files',
                filebrowserImageBrowseUrl : window.CKEDITOR_BASEPATH + 'kcfinder/browse.php?opener=ckeditor&type=images',
                filebrowserFlashBrowseUrl : window.CKEDITOR_BASEPATH + 'kcfinder/browse.php?opener=ckeditor&type=flash',
                filebrowserUploadUrl : window.CKEDITOR_BASEPATH + 'kcfinder/upload.php?opener=ckeditor&type=files',
                filebrowserImageUploadUrl : window.CKEDITOR_BASEPATH + 'kcfinder/upload.php?opener=ckeditor&type=images',
                filebrowserFlashUploadUrl : window.CKEDITOR_BASEPATH + 'kcfinder/upload.php?opener=ckeditor&type=flash',
            };
            CKEDITOR.replace(this.id,config);
        });
    }

    // Remodal
    jQuery(document).on("click",".remodalImg", function(e){
        var $this = jQuery(this);
        jQuery(".remodal .forImg").empty().append('<img src="'+ $this.attr('data-src') +'" />');
        var inst = jQuery('[data-remodal-id=remodal]').remodal();
        inst.open();
        e.preventDefault();
    });

    // Select2 - Multiple
    if(jQuery("select.multiple-select").length > 0) {
        require("../vendors/select2/dist/js/select2.full.min");
        jQuery("select.multiple-select").select2();
    }

    // Affichage date/datetime picker */
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

    // Update media
    if(jQuery(".edit-media").length > 0) {
        jQuery(".edit-media").on("click",function(e){
            var $this = jQuery(this),
                $uri = $this.attr("href"),
                $alt = $this.parent().parent().find('input[name=medias_alt]'),
                $description = $this.parent().parent().find('input[name=medias_description]');

            var formData = new FormData();
            formData.append('alt', $alt.val());
            formData.append('description', $description.val());

            axios({
                method: 'post',
                url: $uri,
                data: formData
            })
            .then(function (response) {
                toastr.success(response.data.message);
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
            e.preventDefault();
        });
    }

    // Delete confirmation
    var swal  = require('../vendors/sweetalert/dist/sweetalert.min');
    jQuery(document).on("click",'.confirm-alert',function(e){
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
            if (willDelete) {
                window.location.replace($link);
            }
        });
        e.preventDefault();
    });

    jQuery(document).on("click",'.duplicate-alert',function(e){
        var $link = jQuery(this).attr('href');
        swal({
            title: "Attention",
            text: "Voulez-vous dupliquer cet élément ?",
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
            if (willDelete) {
                window.location.replace($link);
            }
        });
        e.preventDefault();
    });

    // Sortable
    if(jQuery(".nested-section").length > 0) {

        require('./components/admin/sortable');
        var $sort = jQuery('.sortable');
        var $uri = jQuery(".nested-section").attr("data-url");

        $sort.on('click', 'a', function (e) {
            e.preventDefault();
        });

        $sort.nestedSortable({
            items: 'li',
            listType: 'ul',
            forcePlaceholderSize: true,
            helper: 'clone',
            isTree: true,
            update: function () {
                var serialized = $sort.nestedSortable('toArray');
                axios({
                    method: 'post',
                    url: $uri,
                    data: {orders: serialized}
                })
                .then(function (response) {
                    toastr.success(response.data);
                })
                .catch(function (error) {
                    var errorMsg = "";
                    if (error.response) {
                        errorMsg = error.response.data;
                    } else if (error.request) {
                        errorMsg = error.request;
                    } else {
                        errorMsg = error.message;
                    }
                    toastr.error(errorMsg);
                });
            }
        });
    }

    // Translate
    if (jQuery(".translate-actions").length > 0) {
        jQuery(".translate-actions a").on("click", function(e){
            var $lang = jQuery(this).attr('data-lang'),
                $route = jQuery(".modal-translate form").attr("data-action"),
                $form = jQuery(".modal-translate form"),
                $modal = jQuery('[data-remodal-id=translate-modal]').remodal();
            $form.attr("action",$route + "&lang=" + $lang);
            axios({
                method: 'get',
                url: $route + "&lang=" + $lang,
            })
            .then(function (response) {
                jQuery.each(response.data, function($key, object) {
                    jQuery.each(object, function($field, $value) {
                        $form.find('#'+$field).val($value);
                    });
                });
                $form.find('.ajax-editor').each(function(e){
                    var instance = CKEDITOR.instances.content;
                    instance.destroy();
                    CKEDITOR.replace( this.id, {});
                });
                $form.find("span.lang").empty().append($lang);
                $modal.open();
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
            e.preventDefault();
        });

        jQuery(".modal-translate .remodal-confirm").on("click", function(e){
            var instance = CKEDITOR.instances.content;
            for ( instance in CKEDITOR.instances ) {
                CKEDITOR.instances[instance].updateElement();
            }

            var $form = jQuery(".modal-translate form"),
                $postData = $form.serialize();

            axios({
                method: 'post',
                url: $form.attr("action"),
                data: $postData
            })
            .then(function (response) {
                toastr.success(response.data.message);
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

            e.preventDefault();

        });

    }

    if(jQuery("#seo").length > 0){
        require("./components/admin/seo");

    }
    if(jQuery(".carriers-plages").length > 0){
        require("./components/admin/carriers");
    }
});