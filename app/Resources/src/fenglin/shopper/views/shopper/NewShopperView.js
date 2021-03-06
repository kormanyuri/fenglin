/**
 * Created by korman on 27.01.17.
 */

define(['marionette', 'shopper/views/core/MenuHomeView'], function(Marionette, MenuHomeView){
    return Marionette.View.extend({
        el:'#contentContainer',
        template: '#newShopperView',
        onRender: function(){
            $('#searchBarContainer').empty();
            $('#paginationContainer').empty();
            $('#menuContainer').empty();

            var menu = new MenuHomeView();
            menu.render();
        }
    });
});