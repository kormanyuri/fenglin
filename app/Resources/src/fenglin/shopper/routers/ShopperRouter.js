/**
 * Created by korman on 27.01.17.
 */

define(['marionette', 'shopper/controllers/ShopperController'], function(Marionette, ShopperController){

    var ShopperRouter = Marionette.AppRouter.extend({
        controller: ShopperController,
        appRoutes: {
            'shopper/home':'homePage',
            'shopper/cash-back-statement': 'cashBackStatementPage',
            'shopper/setting': 'settingPage',
            'shopper/cash-back/confirm': 'cashBackConfirm',
            'shopper/change-password/:shopperId': 'changePasswordPage',
            'shopper/change-description/:shopperId': 'changeDescriptionPage',
            'shopper/qr/:shopperId': 'qrPage'
        }
    });

    return new ShopperRouter();
});