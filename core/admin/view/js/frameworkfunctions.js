const Ajax = (set) => {

    if (typeof set === 'undefined'){
        set = {};
    }
    if (typeof set.url === 'undefined' || !set.url){
        set.url = typeof PATH !== 'undefined' ? PATH : '/';
    }
    if (typeof set.ajax === 'undefined'){
        set.ajax = true;
    }
    if (typeof set.type === 'undefined' || !set.type){
        set.type = 'GET';
    }

    set.type = set.type.toUpperCase();

    let body = '';

    if (typeof set.data !== 'undefined' && set.data){

        if (typeof set.processData !== 'undefined' && !set.processData){
            body = set.data
        }else{
            for (let i in set.data){
                if (set.data.hasOwnProperty(i)){
                    body += '&' + i + '=' + set.data[i];
                }
            }
            body = body.substr(1);

            if (typeof ADMIN_MODE !== 'undefined'){
                body += body ? '&' : '';
                body += 'ADMIN_MODE=' + ADMIN_MODE;
            }
        }
    }

    if (set.type === 'GET'){
        set.url += '?' + body;
        body = null;
    }

    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
        xhr.open(set.type, set.url, true);
        let contentType = false;
        if (typeof set.headers !== 'undefined' && set.headers){
            for (let i in set.headers) {
                if (set.headers.hasOwnProperty(i)){
                    xhr.setRequestHeader(i, set.headers[i]);
                    if (i.toLowerCase() === 'content-type') contentType = true;
                }
            }
        }
        if (!contentType && typeof set.contentType === 'undefined' || set.contentType) xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

        if (set.ajax){
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        }

        xhr.onload = function (){
            if (this.status >= 200 && this.status < 300){
                if (/fatal\s+?error/ui.test(this.response)){
                    reject(this.response);
                }
                resolve(this.response);
            }
            reject(this.response);
        }
        xhr.onerror = function () {
            reject(this.response);
        }

        xhr.send(body);
    });
}

function isEmpty(arr){
    for (let i in arr){
        return false
    }
    return true
}

