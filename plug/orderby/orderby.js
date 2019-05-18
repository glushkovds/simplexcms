
var JSOrderBy = function(){

    var instance = this;
    
    this.init = function(){
        
    }
    
    this.exec = function(a,sysname){
        var toDirection = $(a).attr('next');
        if(!toDirection){
            toDirection = 'asc';
        }
        location.href = this.addtouri('orderby',sysname+':'+toDirection);
    }
    
    // преобразовывает p1=2&p2=ert => {p1 : 2, p2 : 'ert'}
    this.decodeuri = function(query){
        if(!query){
            query = location.search;
        }
        query = query.replace("?","");
        var urlParams = {};
        var e,
            a = /\+/g,  // Regex for replacing addition symbol with a space
            r = /([^&=]+)=?([^&]*)/g,
            d = function (s) {return decodeURIComponent(s.replace(a, " "));},
            q = query;
        while (e = r.exec(q))urlParams[d(e[1])] = d(e[2]);
        return urlParams;
    }

    // преобразовывает {p1 : 2, p2 : 'ert'} => p1=2&p2=ert
    this.encodeuri = function(urlParams){
        var qs = "";
        for (i in urlParams)qs += "&" + i + "=" + urlParams[i];
        qs = qs.substring(1);
        return qs;
    }

    // добавляет к параметрам запроса еще параметр, если он пустой, удаляет его
    this.addtouri = function(param,value){
        var urlParams = this.decodeuri(location.search.replace('?',''));
        value ? urlParams[param] = value : delete urlParams[param];
        
        delete urlParams['page'];
        var uristr = this.encodeuri(urlParams);
        return location.pathname + (uristr ? '?'+uristr : '');
    }
    
    this.init();
    
}

var OrderBy;
$(function(){
    OrderBy = new JSOrderBy();
})
