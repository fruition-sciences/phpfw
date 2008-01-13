function cb_verifySelectedItems(name) {
    if (!cb_hasSelectedCheckbox(document, name)) {
        alert("No items selected.");
        return false;
    }
    return true;
}

function cb_hasSelectedCheckbox(node, childName) {
    if (node.nodeName == "INPUT" && node.type == "checkbox") {
        if (node.checked) {
            return true;
        }
    }
    var nodes = node.childNodes;
    for (var i=0; i<nodes.length; i++) {
        var child = nodes.item(i);
        if (cb_hasSelectedCheckbox(child, childName)) {
            return true;
        }
    }
    return false;
}
