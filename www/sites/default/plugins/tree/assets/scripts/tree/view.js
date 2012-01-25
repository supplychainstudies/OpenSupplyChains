$(document).ready(function() {
    var tree_element_id = '#tree-container';
    var passcode = ""; 
    var scid = Sourcemap.view_supplychain_id || location.pathname.split('/').pop();    

    Sourcemap.loadSupplychainToTree(scid, passcode, function(sc) { Sourcemap.buildTree(tree_element_id,sc); });
});
