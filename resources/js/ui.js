function button_click(url) {
    url = normalizeUrl(url);
    document.location = url;
}

function button_submit(url) {
    var form = document.getElementById("theForm");
    form.method = "post";
    url = normalizeUrl(url);
    form.action = url;
    form.submit();
}

function normalizeUrl(url) {
    if (url.charAt(0) == '/') {
      return appBase + url.substring(1);
    }
    else return url;
}

function focusOnFirst() {
    var elements = document.getElementsByTagName('input');
    for (var i=0; i<elements.length; i++) {
        var elem = elements[i];
        if (elem.type == 'text') {
            try {
                elem.focus();
            }
            catch (e) {
                // IE refuses to focus on hidden elements
            }
            return;
        }
    }
}

function formKeyPress(e) {
    var key = null;
    var control = null;
    if (e.which) { // FireFox
        key = e.which;
        control = e.target;
    }
    else { //IE
        key = window.event.keyCode;
        control = e.srcElement;
    }
//    alert(control.nodeName);
//    alert(control.type);
    if (control.nodeName == 'INPUT' && (control.type == 'text' || control.type == 'password')) {
        if (key == 13 || key == 3) {
            var button = getFirstButton(control);
            if (button) {
                button.onclick();
            }
            return false;
        }
    }
    return true;
}

/**
 * Get the first button in the same container as the given element.
 * A container, in this context, is either a form or an element that has the
 * 'group' attribute.
 * 
 * @param HTMLElement element
 * @return the first button, or null if no button was found.
 */
function getFirstButton(element) {
    var container = findContainer(element);
    if (!container) {
        return null;
    }
    return getFirstButtonChild(container);
}

/**
 * Find the first child button of the given element.
 * 
 * @param HTMLElement element
 * @return the first button.
 */
function getFirstButtonChild(element) {
    var nodes = element.getElementsByTagName('button');
    if (nodes.length > 0) {
        return nodes[0];
    }
    return null;
}

/**
 * Find the container of the given element. A container, in this context, is
 * either a form or an element that has the 'group' attribute.
 * 
 * @param HTMLElement element
 * @return the container of the given element.
 */
function findContainer(element) {
    while ((element = element.parentNode) != null) {
        if (element.nodeName == 'FORM') {
            return element;
        }
    }
    return null;
}