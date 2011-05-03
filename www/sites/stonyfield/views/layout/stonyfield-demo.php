<!DOCTYPE html>
<html>
<head>
    <base href="<?= URL::base(true, true) ?>"></base>
<title>
Stonyfield Yogurt - Sourcemap
</title>
<style>
    body {
        font-family: sans-serif;
        background-color: #525b0c;
        padding: 0 1em;
        margin-top:0;
    }
    #wrapper {
        width: 957px;
        margin: 0 auto;
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
    #foot {}
    #foot img {
        float: right;
        padding-right: 1em;
    }

    .clear {
        width: 100%;
        clear: both;
    }
    ul.map-nav { width: 100%; background-color: white; margin: 0; padding: 0;}
    ul.map-nav li { 
        border-top-left-radius:3px;	      
        -moz-border-radius-topleft:3px;
        -webkit-border-top-left-radius:3px;
        border-top-right-radius:3px;	      
        -moz-border-radius-topright:3px;
        -webkit-border-top-right-radius:3px;
        -webkit-box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        margin-top: 0; display: inline; padding: .25em; background-color: #ccc; margin-left:0.25em 
    }
    ul.map-nav li a { color: black; text-decoration:none; }
    ul.map-nav li.dairy { background-color: #60cb59; }
    ul.map-nav.dairy li.dairy,
    ul.map-nav.sweeteners li.sweeteners,
    ul.map-nav.yogurt li.yogurt,
    ul.map-nav.other li.other
    { font-weight: bold; }
    ul.map-nav li.sweeteners { background-color: #f7c370; }
    ul.map-nav li.other { background-color: #70d0f8; }

    ul.map-nav li.dairy ul {
        display: none;
    }

    ul.map-nav li.dairy:hover ul {
        display: block;
        position: absolute;
    }
    ul.map-nav li.dairy:hover ul > li {
        border-top-left-radius:0;	      
        -moz-border-radius-topleft:0;
        -webkit-border-top-left-radius:0;
        border-top-right-radius:0;	      
        -moz-border-radius-topright:0;
        -webkit-border-top-right-radius:0;
        
        border-bottom-left-radius:3px;	      
        -moz-border-radius-bottomleft:3px;
        -webkit-border-bottom-left-radius:3px;
        border-bottom-right-radius:3px;	      
        -moz-border-radius-bottomright:3px;
        -webkit-border-bottom-right-radius:3px;
        display: block;
        float: left;
        position: relative;
        top: 100%;
        left: ;
    }
</style>
<?= isset($scripts) ? Sourcemap_JS::script_tags($scripts) : '' ?>
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
<script>
/*
$(document).ready(function() {


    // change default template path
    Sourcemap.TPL_PATH = "sites/stonyfield/assets/scripts/tpl/";

    // supplychains to map
    var scid = <?= isset($supplychain_id) ? $supplychain_id : false ?>;
    
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
*/
</script>
</body>
</html>
