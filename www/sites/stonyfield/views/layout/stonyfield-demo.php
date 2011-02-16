<!DOCTYPE html>
<html>
<head>
    <base href="<?= URL::base(true, true) ?>"></base>
<title>
||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||stonyfield demo
</title>
<?= isset($styles) ? Sourcemap_CSS::link_tags($styles) : '' ?>
<style>
    body {
        font-family: sans-serif;
        background-color: #525b0c;
        padding: 1em;
    }
    #wrapper {
        width: 957px;
        margin: auto;
        overflow: hidden;
        background-color: #85951b;
    }
    #wrapper > div {
        margin: 0;
        padding: 0;
    }
    #head {
        width: 957px;
        height: 88px;
        background-image: url("sites/stonyfield/assets/images/stonyfield-demo-banner.png");
    }
    #content, #map {
        min-height: 400px;
        height: 600px;
        width: 100%;
        background-color: #fff;
    }
    #content > * {
        padding: 1em;
    }
    #foot {}
    #foot img {
        float: right;
        padding-right: 1em;
    }

    .clear {
        width: 100%;
        clear: both;
    }
    .olPopup {
        -moz-border-radius: 0 10px 10px 10px;
        -webkit-border-radius: 0 10px 10px 10px;
        border: 1px solid #333 !important;
        color: #333;
        font-family: "Palatino", "Georgia", serif;
        padding: 1em;
    }
    .olPopup h4, h5, a {
        margin: 0;
        padding: 0;
    }
    .olPopup h5 {
        font-weight: normal;
        margin-bottom: .25em;
    }
    .olPopup a {
        color: #525b0c;
        color: #362a76;
        font-family: "Helvetica", "Arial", sans-serif;
        font-size: .8em;
    }
    #dialog {
        color: #333;
        font-family: "Palatino", "Georgia", serif;
    }
    #dialog h2, #dialog h3, #dialog h4 {
        margin: 0;
        padding: 0;
    }
    #dialog h2 {
        font-size: 1.8em;
        border-bottom: 1px solid #bbb;
    }
    #dialog h3 {
        font-size: 1.2em;
        font-weight: normal;
    }
    #dialog h3 .placename {
        font-weight: bold;
        padding-top: 0;
    }
    #dialog a {
        border: none;
        outline: none;
    }
    #dialog p {
        padding: 0;
        margin: 1em 0 1em 0;
        font-family: "Helvetica", "Arial", sans-serif;
        font-size: .9em;
    }
    #dialog .fun-fact {
        padding: 0;
        padding-top: 1em;
        font-style: italic;
    }
    #dialog .fun-fact h4 {
        border-bottom: 1px solid #ccc;
    }
</style>
</head>
<body>
<div id="wrapper">
    <div id="head">
    </div>
    <div id="content"><?= $content ?>
        <div class="clear">&nbsp;</div>
    </div>
    <div class="clear">&nbsp;</div>
    <div id="foot">
        <div class="clear">&nbsp;</div>
    </div>
</div>
<?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : '' ?>
<script>
$(document).ready(function() {

    // change default template path
    Sourcemap.TPL_PATH = "sites/stonyfield/assets/scripts/tpl/";

    // new map instance with custom stop and popup decorators
    Sourcemap.map_instance = new Sourcemap.Map('map', {
        "prep_stop": function(stop, ftr) {
            var sz = 8;
            var vol = parseFloat(stop.getAttr("vol_pct"));
            if(vol < 1) {
                sz = 5;
            } else if(vol < 20) {
                sz = 10;
            } else if(vol < 70) {
                sz = 14;
            } else {
                sz = 48;
            }
            ftr.attributes.size = sz;
            var color = 'green';
            var cat = stop.getAttr('category');
            switch(cat) {
                case "FS":
                    color = "#66cc33";
                    break;
                case "D":
                    color = "#339933";
                    break;
                default:
                    color = "#006633";
                    break;
            }
            ftr.attributes.color = color;
        },
        "prep_popup": function(st, ftr, pop) {
            var html = '<h4>'+st.getAttr("name", st.getLabel())+'</h4>';
            html += '<h5>from <span class="placename">'+st.getAttr("org.sourcemap.placename")+'</span></h5>';
            html += '&raquo;<a href="javascript: Sourcemap._showDialog(\''+ftr.attributes.supplychain_instance_id+'\',\''+st.instance_id+'\');">View details</a>';
            pop.setContentHTML(html);
        }
    });

    // function for opening stop detail dialog
    Sourcemap._showDialog = function(sc, st) {
        Sourcemap.template('stop_details', function(p, txt, thtml) {
            Sourcemap.map_tour.stop();
            $(Sourcemap.map_dialog).html(thtml).dialog("open");
        }, Sourcemap.map_instance.findSupplychain(sc).findStop(st));
    }
    
    // supplychains to map
    var scids = [<?= join(',', $supplychain_ids) ?>];
    
    // counter
    Sourcemap.map_sc_count = scids.length;

    // load all supplychains
    for(var i=0; i<scids.length; i++) { 
        var scid = scids[i];
        Sourcemap.loadSupplychain(scid, function(sc) {
            Sourcemap.map_instance.addSupplychain(sc);
            if(!(--Sourcemap.map_sc_count)) {
                var map = Sourcemap.map_instance;
                // get features in a natural order:
                //      from upstream to downstream
                var features = [];
                for(var k in map.supplychains) {
                    var sc = map.supplychains[k];
                    var g = new Sourcemap.Supplychain.Graph(map.supplychains[k]);
                    var order = g.depthFirstOrder();
                    order = order.concat(g.islands());
                    for(var i=0; i<order.length; i++)
                        features.push(map.mapped_features[order[i]]);
                }

                // back off a little
                Sourcemap.map_instance.map.zoomOut();
                Sourcemap.map_instance.map.zoomOut();

                // set up tour
                Sourcemap.map_tour = new Sourcemap.MapTour(map, {"features": features, "interval": 5});

                // set up details dialog
                var d_el = $('<div id="dialog"></div>');
                $(document.body).append(d_el);
                $(d_el).dialog({"width": 600, "height": 600, "zIndex": 3000, "close": function(evt, ui) {
                    $(Sourcemap.map_dialog).html('');
                    Sourcemap.map_tour.wait();
                }}).dialog("close");
                Sourcemap.map_dialog = d_el;

                // pause tour on click anywhere on map
                $(Sourcemap.map_instance.map.div).mouseup(function() {
                    for(var i=0; i<Sourcemap.map_tour.features; i++)
                        Sourcemap.map_instance.controls.select.unselect(Sourcemap.map_tour.features[i]);
                    Sourcemap.map_tour.wait();
                });
            }
        });
    }
});
</script>
</body>
</html>
