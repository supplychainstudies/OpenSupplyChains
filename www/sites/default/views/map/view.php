<script>
$(document).ready(function() {
    Sourcemap.map_instance = new Sourcemap.Map('sourcemap-map-view');
    Sourcemap.loadSupplychain(<?= $supplychain_id ?>, function(data) {
        Sourcemap.map_instance.mapSupplychain(Sourcemap.factory('supplychain', data.supplychain));
    });
});
</script>
<div id="sourcemap-map-view" style="width: 100%; height: 100%; background-color: #ddd;"></div>
