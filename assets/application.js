
STUDIP.FACULTYNEWS = {
    /**
     * url to ajax search
     */
    ajaxURL : '',
    
    setAjaxURL : function(string) {
       STUDIP.FACULTYNEWS.ajaxURL = string;
    },
    showNews : function(element){
        STUDIP.FACULTYNEWS.setVisit(element);
        STUDIP.FACULTYNEWS.setRead(element);
    },
    
    setVisit : function(news_id){
        var url = STUDIP.FACULTYNEWS.ajaxURL;
        jQuery.ajax({
            type: "POST",
            url: url + '/setVisit/' + news_id
        });
    },
    
    setRead : function(news_id){
        var url = STUDIP.FACULTYNEWS.ajaxURL;
        jQuery.ajax({
            type: "POST",
            url: url + '/setRead/' + news_id
        });

    }
};
