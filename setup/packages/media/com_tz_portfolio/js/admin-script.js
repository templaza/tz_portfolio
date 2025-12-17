(function ($) {
    "use strict";

    $(document).ready(function(){
        if($("#tmpl-tpPortfolio-footer").length) {
            if($("#j-main-container").length) {
                $("#j-main-container").append($("#tmpl-tpPortfolio-footer").html());
            }else{
                if($("form[name=adminForm]").length){
                    $("form[name=adminForm]").append($("#tmpl-tpPortfolio-footer").html());
                }
            }
        }

        // Remove addon_task value for draggable of add-on data list page
        if(typeof formData !== "undefined" && formData.has("addon_task")){
            formData.delete("addon_task");
        }
    });
})(jQuery);