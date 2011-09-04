function SrcClient() {
	
	this.API_VERSION = '1.0';
	this.API_ENDPOINT = 'http://www.sourcemap.com/services/';
	this.apikey = '';
	this.apisecret = '';
	
	this.init = function(key, secret) {
		this.apikey = key;
		this.apisecret = secret;
	}

	this.available = function(cbname) {
		this._get('', cbname);
	}

	this.supplychain = function(id, cbname) {
		this._get('supplychains/'+id, cbname);
	}

	this.supplychains = function(args, cbname) {
		if(args[0] && typeof(args[0]) == 'number') {
			var l = args[0];
			if(args[1]) { var o = args[1]; } else { var o = 0; }
			this._get('supplychains/', cbname, '?l='+l+'&o='+o+'&');		
		} else if(typeof(args[0]) == 'string') {
			this._get('search/', cbname, '?q='+args[0]+'&');		
		} else { throw 'Invalid Arguments'; }
	}

	this._get = function(service, cbname, get) {
		if(typeof(get) == 'undefined') { get = '?'; }
		var script = document.createElement('script');
		script.setAttribute('src', this.API_ENDPOINT+service+get+'f=jsonp&callback='+cbname);
		document.getElementsByTagName('head')[0].appendChild(script);
	}
}