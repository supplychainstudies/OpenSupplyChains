<!DOCTYPE html>
<html>
<head>
    <base href="<?= URL::base(true, true) ?>"></base>
<title>
Stonyfield Yogurt - Sourcemap
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
    #map, .tabs-map {
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
        font-family: "Helvetica", "Arial", sans-serif;
    }
    #dialog h2, #dialog h3, #dialog h4 {
        margin: 0;
        padding: 0;
    }
    #dialog h2 {
        font-size: 1.8em;
        border-bottom: 1px solid #bbb;
        color: darkgreen;
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

    .ui-dialog-titlebar {
        background: none;
        border: none;
    }

    .ui-resizable-handle, .ui-resizable-handle * {
        background: none;
    }

    /* hock */
    .olPopup {
        border:1px solid #fff !important;
        height:90px !important; 
        background:#fff !important; 
        padding:5px 0 5px 5px !important; 
        border:none !important; 
        font-family:Helvetica, Arial, sans-serif !important;
        position:relative;
    } 
    .olPopupContent {
        width:215px !important;
        background:#fff; 
        margin-right:5px;
    } 
    .olPopupContent a { 
        display:block;
        text-align:center; -moz-border-radius:4px; -webkit-border-radius:4px;
        padding:5px; background:#eee;
    } 
    .olPopupCloseBox {display:none;} 
    .olPopup h4 {color:darkgreen;}
    ul.map-nav { width: 100%; background-color: white; margin: 0; padding: 0;}
    ul.map-nav li { margin-top: 0; display: inline; padding: .25em; background-color: #ccc; }
    ul.map-nav li a { color: black; }
    ul.map-nav li.dairy { background-color: #60cb59; }
    ul.map-nav.dairy li.dairy,
    ul.map-nav.sweeteners li.sweeteners,
    ul.map-nav.yogurt li.yogurt,
    ul.map-nav.other li.other
    { font-weight: bold; }
    ul.map-nav li.sweeteners { background-color: #f7c370; }
    ul.map-nav li.other { background-color: #70d0f8; }
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

    // supplychains to map
    var scid = <?= $supplychain_id ?>;
    
    // new map instance with custom stop and popup decoratorsa
    Sourcemap.map_instance = new Sourcemap.Map('map', {
        "layer_switcher": false,
        "prep_stop": function(stop, ftr) {
            var sz = 5;
            var vol = parseFloat(stop.getAttr("vol_pct"));
            if(!isNaN(vol)) {
                if(vol < 1) {
                    sz = 5;
                } else if(vol < 20) {
                    sz = 10;
                } else if(vol < 70) {
                    sz = 14;
                } else {
                    sz = 48;
                }
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
            html += '<a href="javascript: Sourcemap._showDialog(\''+st.instance_id+'\');">View details</a>';
            pop.setContentHTML(html);
        }
    });

    // function for opening stop detail dialog
    Sourcemap._showDialog = function(st) {
        for(var scid in Sourcemap.map_instance.supplychains) {
            if(Sourcemap.map_instance.supplychains[scid].findStop(st)) {
                break;
            }
        }
        Sourcemap.template('stop_details', function(p, txt, thtml) {
            Sourcemap.map_tour.stop();
            $(Sourcemap.map_dialog).html(thtml).dialog("open");
        }, Sourcemap.map_instance.supplychains[scid].findStop(st));
    }
    
    // load all supplychains
    Sourcemap.loadSupplychain(scid, function(sc) {
        var map = Sourcemap.map_instance;
        map.addSupplychain(sc);
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
        map.map.zoomOut();

        // set up tour
        Sourcemap.map_tour = new Sourcemap.MapTour(map, {"features": features, "interval": 4, "wait_interval": 0});

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
            Sourcemap.map_tour.stop();
        });
    });
});
</script>
</body>
</html>
