<script>
$(document).ready(function() {
    Sourcemap.loadSupplychain(<?= HTML::chars($supplychain_id) ?>, function(sc) {
        Sourcemap.log(sc);
        (new Sourcemap.FormEditor($('#supplychain-form-edit').get(0), sc.supplychain)).init();
    });
});
</script>
<div id="supplychain-form-edit"></div>
