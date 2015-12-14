/*jslint browser: true */
/*global jQuery, STUDIP */
(function ($, STUDIP) {
    'use strict';

    STUDIP.FACULTYNEWS = {
        /**
         * url to ajax search
         */
        ajaxURL: '',
        setAjaxURL: function (string) {
            STUDIP.FACULTYNEWS.ajaxURL = string;
        },
        showNews: function (element) {
            STUDIP.FACULTYNEWS.setVisit(element);
            STUDIP.FACULTYNEWS.setRead(element);
        },
        setVisit: function (news_id) {
            var url = STUDIP.FACULTYNEWS.ajaxURL;
            $.post(url + '/setVisit/' + news_id);
        },

        setRead: function (news_id) {
            var url = STUDIP.FACULTYNEWS.ajaxURL;
            $.post(url + '/setRead/' + news_id);
        }
    };

}(jQuery, STUDIP));

