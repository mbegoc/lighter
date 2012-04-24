
(function() {
    var divDebug = document.createElement('div');
    divDebug.setAttribute('class', 'debug');
    
    divDebug.innerHTML = "{debugInfo}";
    divDebug.onmouseover = function(){
        divDebug.style.width = 'auto';
        divDebug.style.height = 'auto';
        divDebug.style.padding = '10px';
        divDebug.style.overflow = 'auto';
    };
    divDebug.onmouseout = function(){
        divDebug.style.width = '10px';
        divDebug.style.height = '10px';
        divDebug.style.padding = '0';
        divDebug.style.overflow = 'hidden';
    };
    document.body.appendChild(divDebug);
})();

