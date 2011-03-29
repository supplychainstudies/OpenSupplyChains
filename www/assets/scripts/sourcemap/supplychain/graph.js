Sourcemap.Supplychain.Graph = function(sc, o) {
    if(!(sc instanceof Sourcemap.Supplychain))
        throw new Error("Graph requires instance of supplychain.");
    this.supplychain = sc;
    Sourcemap.Configurable.call(this, o);
}

Sourcemap.Supplychain.Graph.prototype.defaults = {
    "auto_init": true
};

Sourcemap.Supplychain.Graph.prototype.init = function() {
    this.nodes = this.getNodes();
    this.edges = this.getEdges();
}

Sourcemap.Supplychain.Graph.prototype.getNodes = function() {
    var stops = this.supplychain.stops;
    var nodes = [];
    for(var i=0; i<stops.length; i++) nodes.push(stops[i].instance_id);
    return nodes;
}

Sourcemap.Supplychain.Graph.prototype.getEdges = function() {
    var hops = this.supplychain.hops;
    var edges = [];
    for(var i=0; i<hops.length; i++) {
        var hop = hops[i];
        var edge = {
            "to": hop.to_stop_id, "from": hop.from_stop_id, 
            "id": hop.instance_id
        }
        edges.push(edge);
    }
    return edges;
}

Sourcemap.Supplychain.Graph.prototype.nodeEdges = function(node_id) {
    var node_edges = {"in": [], "out": []};
    for(var i=0; i<this.edges.length; i++) {
        var edge = this.edges[i];
        if(node_id == edge.from) node_edges.out.push(edge);
        else if(node_id == edge.to) node_edges["in"].push(edge);
    }
    return node_edges;
}

Sourcemap.Supplychain.Graph.prototype.outboundEdges = function(node_id) {
   var edges = this.nodeEdges(node_id);
   return edges.out;
}

Sourcemap.Supplychain.Graph.prototype.inboundEdges = function(node_id) {
   var edges = this.nodeEdges(node_id);
   return edges["in"];
}

Sourcemap.Supplychain.Graph.prototype.roots = function() {
    var nodes = Sourcemap.deep_clone(this.nodes);
    var edges = Sourcemap.deep_clone(this.edges);
    var sinks = [];
    var sources = [];
    for(var i=0; i<edges.length; i++) {
        var idx = -1;
        var edge = edges[i];
        if((idx = sources.indexOf(edge.from)) < 0)
            sources.push(edge.from);
        if(!(idx = sinks.indexOf(edge.to)))
            sinks.push(edge.to);
    }
    var roots = [];
    for(var i=0; i<sources.length; i++) 
        if(sinks.indexOf(sources[i]) < 0) 
            roots.push(sources[i]);
    return roots;
}

Sourcemap.Supplychain.Graph.prototype.leaves = function() {
    var nodes = Sourcemap.deep_clone(this.nodes);
    var edges = Sourcemap.deep_clone(this.edges);
    var sources = []
    var sinks = [];
    for(var i=0; i<edges.length; i++) {
        var idx = -1;
        var edge = edges[i];
        if((idx = sinks.indexOf(edge.to)) < 0)
            sinks.push(edge.to);
        if((idx = sources.indexOf(edge.from)) < 0)
            sources.push(edge.from);
    }
    var leaves = [];
    for(var i=0; i<sinks.length; i++) 
        if(sources.indexOf(sinks[i]) < 0) 
            leaves.push(sinks[i]);
    return leaves;
}   

Sourcemap.Supplychain.Graph.prototype.islands = function() {
    var nodes = Sourcemap.deep_clone(this.nodes);
    var edges = Sourcemap.deep_clone(this.edges);
    for(var i=0; i<edges.length; i++) {
        var idx = -1;
        var edge = edges[i];
        if((idx = nodes.indexOf(edge.from)) >= 0)
            nodes.splice(idx, 1);
        if((idx = nodes.indexOf(edge.to)) >= 0)
            nodes.splice(idx, 1);
    }
    return nodes;
}

Sourcemap.Supplychain.Graph.prototype.depthFirstOrder = function(upstream) {
    var order = [];
    var stack = upstream ? this.leaves() : this.roots();
    var cur = null;
    var traverse_fn = upstream ? 'inboundEdges' : 'outboundEdges';
    var traverse_dir = upstream ? 'from' : 'to';
    while(cur = stack.pop()) {
        if(order.indexOf(cur) >= 0)
            continue;
        else
            order.push(cur);
        var out = this[traverse_fn](cur);
        for(var i=0; i<out.length; i++) {
            if(stack.indexOf(out[i][traverse_dir]) < 0) {
                stack.push(out[i][traverse_dir]);
            }
        }
    }
    return order;
}

Sourcemap.Supplychain.Graph.prototype.fromClosestLeafOrder = function(from_stop) {
    var leaves = this.leaves();
    var wkt = new OpenLayers.Format.WKT();
    var from_stop_id = from_stop.instance_id;
    var from_point = wkt.read(from_stop.geometry).geometry;
    var leaf_dist = {};
    for(var li=0; li<leaves.length; li++) {
        var l_point = wkt.read(this.supplychain.findStop(leaves[li]).geometry).geometry;
        leaf_dist[leaves[li]] = from_point.distanceTo(l_point);
    }
    var korder = Sourcemap.oksort(leaf_dist);
    var closest_leaf = korder[0];
    var stack = [closest_leaf];
    var order = [];
    while(cur = stack.pop()) {
        if(order.indexOf(cur) >= 0)
            continue;
        else
            order.push(cur);
        var inbound = this.inboundEdges(cur);
        for(var i=0; i<inbound.length; i++) {
            if(stack.indexOf(inbound[i]['from']) < 0) {
                stack.push(inbound[i]['from']);
            }
        }
    }
    return order;
}
