/**
 * Craft Flowplayer Drive plugin for Craft CMS
 *
 * VideoField Field JS
 *
 * @author    Lucas Bares
 * @copyright Copyright (c) 2018 Lucas Bares
 * @link      http://luke.nehemedia.de
 * @package   CraftFlowplayerDrive
 * @since     1.0.0CraftFlowplayerDriveVideoField
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "CraftFlowplayerDriveVideoField",
        defaults = {
        };

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function(id) {
            var _this = this;

            $(function () {

/* -- _this.options gives us access to the $jsonVars that our FieldType passed down to us */

            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };


})( jQuery, window, document );

function openVideoSelectModal(id){
        var modal = new Garnish.Modal($('#fields-'+id),{autoShow: true, draggable: true}); //create a new modal
    }
